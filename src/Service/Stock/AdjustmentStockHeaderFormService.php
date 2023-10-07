<?php

namespace App\Service\Stock;

use App\Common\Idempotent\IdempotentUtility;
use App\Entity\Purchase\PurchaseOrderDetail;
use App\Entity\Purchase\PurchaseOrderPaperDetail;
use App\Entity\Sale\SaleOrderDetail;
use App\Entity\Stock\AdjustmentStockMaterialDetail;
use App\Entity\Stock\AdjustmentStockPaperDetail;
use App\Entity\Stock\AdjustmentStockProductDetail;
use App\Entity\Stock\AdjustmentStockHeader;
use App\Entity\Stock\Inventory;
use App\Entity\Support\Idempotent;
use App\Repository\Purchase\PurchaseOrderDetailRepository;
use App\Repository\Purchase\PurchaseOrderPaperDetailRepository;
use App\Repository\Sale\SaleOrderDetailRepository;
use App\Repository\Stock\AdjustmentStockMaterialDetailRepository;
use App\Repository\Stock\AdjustmentStockPaperDetailRepository;
use App\Repository\Stock\AdjustmentStockProductDetailRepository;
use App\Repository\Stock\AdjustmentStockHeaderRepository;
use App\Repository\Stock\InventoryRepository;
use App\Util\Service\InventoryUtil;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class AdjustmentStockHeaderFormService
{
    private EntityManagerInterface $entityManager;
    private AdjustmentStockHeaderRepository $adjustmentStockHeaderRepository;
    private AdjustmentStockMaterialDetailRepository $adjustmentStockMaterialDetailRepository;
    private AdjustmentStockPaperDetailRepository $adjustmentStockPaperDetailRepository;
    private AdjustmentStockProductDetailRepository $adjustmentStockProductDetailRepository;
    private PurchaseOrderDetailRepository $purchaseOrderDetailRepository;
    private PurchaseOrderPaperDetailRepository $purchaseOrderPaperDetailRepository;
    private SaleOrderDetailRepository $saleOrderDetailRepository;
    private InventoryRepository $inventoryRepository;

    public function __construct(RequestStack $requestStack, EntityManagerInterface $entityManager)
    {
        $this->requestStack = $requestStack;
        $this->entityManager = $entityManager;
        $this->idempotentRepository = $entityManager->getRepository(Idempotent::class);
        $this->adjustmentStockHeaderRepository = $entityManager->getRepository(AdjustmentStockHeader::class);
        $this->adjustmentStockMaterialDetailRepository = $entityManager->getRepository(AdjustmentStockMaterialDetail::class);
        $this->adjustmentStockPaperDetailRepository = $entityManager->getRepository(AdjustmentStockPaperDetail::class);
        $this->adjustmentStockProductDetailRepository = $entityManager->getRepository(AdjustmentStockProductDetail::class);
        $this->purchaseOrderDetailRepository = $entityManager->getRepository(PurchaseOrderDetail::class);
        $this->purchaseOrderPaperDetailRepository = $entityManager->getRepository(PurchaseOrderPaperDetail::class);
        $this->saleOrderDetailRepository = $entityManager->getRepository(SaleOrderDetail::class);
        $this->inventoryRepository = $entityManager->getRepository(Inventory::class);
    }

    public function initialize(AdjustmentStockHeader $adjustmentStockHeader, array $options = []): void
    {
        list($datetime, $user) = [$options['datetime'], $options['user']];

        if (empty($adjustmentStockHeader->getId())) {
            $adjustmentStockHeader->setCreatedTransactionDateTime($datetime);
            $adjustmentStockHeader->setCreatedTransactionUser($user);
        } else {
            $adjustmentStockHeader->setModifiedTransactionDateTime($datetime);
            $adjustmentStockHeader->setModifiedTransactionUser($user);
        }
    }

    public function finalize(AdjustmentStockHeader $adjustmentStockHeader, array $options = []): void
    {
        if ($adjustmentStockHeader->getTransactionDate() !== null && $adjustmentStockHeader->getId() === null) {
            $year = $adjustmentStockHeader->getTransactionDate()->format('y');
            $month = $adjustmentStockHeader->getTransactionDate()->format('m');
            $lastAdjustmentStockHeader = $this->adjustmentStockHeaderRepository->findRecentBy($year, $month);
            $currentAdjustmentStockHeader = ($lastAdjustmentStockHeader === null) ? $adjustmentStockHeader : $lastAdjustmentStockHeader;
            $adjustmentStockHeader->setCodeNumberToNext($currentAdjustmentStockHeader->getCodeNumber(), $year, $month);
        }
        
        foreach ($adjustmentStockHeader->getAdjustmentStockMaterialDetails() as $adjustmentStockMaterialDetail) {
            $adjustmentStockMaterialDetail->setIsCanceled($adjustmentStockMaterialDetail->getSyncIsCanceled());
            $adjustmentStockMaterialDetail->setQuantityDifference($adjustmentStockMaterialDetail->getSyncQuantityDifference());
        }
        foreach ($adjustmentStockHeader->getAdjustmentStockPaperDetails() as $adjustmentStockPaperDetail) {
            $adjustmentStockPaperDetail->setIsCanceled($adjustmentStockPaperDetail->getSyncIsCanceled());
            $adjustmentStockPaperDetail->setQuantityDifference($adjustmentStockPaperDetail->getSyncQuantityDifference());
        }
        foreach ($adjustmentStockHeader->getAdjustmentStockProductDetails() as $adjustmentStockProductDetail) {
            $adjustmentStockProductDetail->setIsCanceled($adjustmentStockProductDetail->getSyncIsCanceled());
            $adjustmentStockProductDetail->setQuantityDifference($adjustmentStockProductDetail->getSyncQuantityDifference());
        }
    }

    public function save(AdjustmentStockHeader $adjustmentStockHeader, array $options = []): void
    {
        $idempotent = IdempotentUtility::create(Idempotent::class, $this->requestStack->getCurrentRequest());
        $this->idempotentRepository->add($idempotent);
        $this->adjustmentStockHeaderRepository->add($adjustmentStockHeader);
        foreach ($adjustmentStockHeader->getAdjustmentStockMaterialDetails() as $adjustmentStockMaterialDetail) {
            $this->adjustmentStockMaterialDetailRepository->add($adjustmentStockMaterialDetail);
        }
        foreach ($adjustmentStockHeader->getAdjustmentStockPaperDetails() as $adjustmentStockPaperDetail) {
            $this->adjustmentStockPaperDetailRepository->add($adjustmentStockPaperDetail);
        }
        foreach ($adjustmentStockHeader->getAdjustmentStockProductDetails() as $adjustmentStockProductDetail) {
            $this->adjustmentStockProductDetailRepository->add($adjustmentStockProductDetail);
        }
        $this->addInventories($adjustmentStockHeader);
        $this->entityManager->flush();
    }

    private function addInventories(AdjustmentStockHeader $adjustmentStockHeader): void
    {
        InventoryUtil::reverseOldData($this->inventoryRepository, $adjustmentStockHeader);
        if ($adjustmentStockHeader->getAdjustmentMode() === AdjustmentStockHeader::ADJUSTMENT_MODE_MATERIAL) {
            $adjustmentStockMaterialDetails = $adjustmentStockHeader->getAdjustmentStockMaterialDetails()->toArray();
            $averagePriceList = InventoryUtil::getAveragePriceList('material', $this->purchaseOrderDetailRepository, $adjustmentStockMaterialDetails);
            InventoryUtil::addNewData($this->inventoryRepository, $adjustmentStockHeader, $adjustmentStockMaterialDetails, function($newInventory, $adjustmentStockMaterialDetail) use ($averagePriceList, $adjustmentStockHeader) {
                $material = $adjustmentStockMaterialDetail->getMaterial();
                $purchasePrice = isset($averagePriceList[$material->getId()]) ? $averagePriceList[$material->getId()] : '0.00';
                $newInventory->setTransactionSubject($adjustmentStockMaterialDetail->getMemo());
                $newInventory->setPurchasePrice($purchasePrice);
                $newInventory->setMaterial($material);
                $newInventory->setInventoryMode($adjustmentStockHeader->getAdjustmentMode());
                $newInventory->setQuantityIn($adjustmentStockMaterialDetail->getQuantityAdjustment());
            });
        } else if ($adjustmentStockHeader->getAdjustmentMode() === AdjustmentStockHeader::ADJUSTMENT_MODE_PAPER) {
            $adjustmentStockPaperDetails = $adjustmentStockHeader->getAdjustmentStockPaperDetails()->toArray();
            $averagePriceList = InventoryUtil::getAveragePriceList('paper', $this->purchaseOrderPaperDetailRepository, $adjustmentStockPaperDetails);
            InventoryUtil::addNewData($this->inventoryRepository, $adjustmentStockHeader, $adjustmentStockPaperDetails, function($newInventory, $adjustmentStockPaperDetail) use ($averagePriceList, $adjustmentStockHeader) {
                $paper = $adjustmentStockPaperDetail->getPaper();
                $purchasePrice = isset($averagePriceList[$paper->getId()]) ? $averagePriceList[$paper->getId()] : '0.00';
                $newInventory->setTransactionSubject($adjustmentStockPaperDetail->getMemo());
                $newInventory->setPurchasePrice($purchasePrice);
                $newInventory->setPaper($paper);
                $newInventory->setInventoryMode($adjustmentStockHeader->getAdjustmentMode());
                $newInventory->setQuantityIn($adjustmentStockPaperDetail->getQuantityAdjustment());
            });
        } else if ($adjustmentStockHeader->getAdjustmentMode() === AdjustmentStockHeader::ADJUSTMENT_MODE_PRODUCT) {
            $adjustmentStockProductDetails = $adjustmentStockHeader->getAdjustmentStockProductDetails()->toArray();
            $averagePriceList = InventoryUtil::getAveragePriceList('product', $this->saleOrderDetailRepository, $adjustmentStockProductDetails);
            InventoryUtil::addNewData($this->inventoryRepository, $adjustmentStockHeader, $adjustmentStockProductDetails, function($newInventory, $adjustmentStockProductDetail) use ($averagePriceList, $adjustmentStockHeader) {
                $product = $adjustmentStockProductDetail->getProduct();
                $purchasePrice = isset($averagePriceList[$product->getId()]) ? $averagePriceList[$product->getId()] : '0.00';
                $newInventory->setTransactionSubject($adjustmentStockProductDetail->getMemo());
                $newInventory->setPurchasePrice($purchasePrice);
                $newInventory->setProduct($product);
                $newInventory->setInventoryMode($adjustmentStockHeader->getAdjustmentMode());
                $newInventory->setQuantityIn($adjustmentStockProductDetail->getQuantityAdjustment());
            });
        }
    }
}
