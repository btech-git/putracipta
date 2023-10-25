<?php

namespace App\Service\Stock;

use App\Common\Idempotent\IdempotentUtility;
use App\Entity\Purchase\PurchaseOrderDetail;
use App\Entity\Purchase\PurchaseOrderPaperDetail;
use App\Entity\Sale\SaleOrderDetail;
use App\Entity\Stock\StockTransferMaterialDetail;
use App\Entity\Stock\StockTransferPaperDetail;
use App\Entity\Stock\StockTransferProductDetail;
use App\Entity\Stock\StockTransferHeader;
use App\Entity\Stock\Inventory;
use App\Entity\Support\Idempotent;
use App\Repository\Purchase\PurchaseOrderDetailRepository;
use App\Repository\Purchase\PurchaseOrderPaperDetailRepository;
use App\Repository\Sale\SaleOrderDetailRepository;
use App\Repository\Stock\StockTransferMaterialDetailRepository;
use App\Repository\Stock\StockTransferPaperDetailRepository;
use App\Repository\Stock\StockTransferProductDetailRepository;
use App\Repository\Stock\StockTransferHeaderRepository;
use App\Repository\Stock\InventoryRepository;
use App\Repository\Support\IdempotentRepository;
use App\Util\Service\InventoryUtil;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class StockTransferHeaderFormService
{
    private EntityManagerInterface $entityManager;
    private IdempotentRepository $idempotentRepository;
    private StockTransferHeaderRepository $stockTransferHeaderRepository;
    private StockTransferMaterialDetailRepository $stockTransferMaterialDetailRepository;
    private StockTransferPaperDetailRepository $stockTransferPaperDetailRepository;
    private StockTransferProductDetailRepository $stockTransferProductDetailRepository;
    private PurchaseOrderDetailRepository $purchaseOrderDetailRepository;
    private PurchaseOrderPaperDetailRepository $purchaseOrderPaperDetailRepository;
    private SaleOrderDetailRepository $saleOrderDetailRepository;
    private InventoryRepository $inventoryRepository;

    public function __construct(RequestStack $requestStack, EntityManagerInterface $entityManager)
    {
        $this->requestStack = $requestStack;
        $this->entityManager = $entityManager;
        $this->idempotentRepository = $entityManager->getRepository(Idempotent::class);
        $this->stockTransferHeaderRepository = $entityManager->getRepository(StockTransferHeader::class);
        $this->stockTransferMaterialDetailRepository = $entityManager->getRepository(StockTransferMaterialDetail::class);
        $this->stockTransferPaperDetailRepository = $entityManager->getRepository(StockTransferPaperDetail::class);
        $this->stockTransferProductDetailRepository = $entityManager->getRepository(StockTransferProductDetail::class);
        $this->purchaseOrderDetailRepository = $entityManager->getRepository(PurchaseOrderDetail::class);
        $this->purchaseOrderPaperDetailRepository = $entityManager->getRepository(PurchaseOrderPaperDetail::class);
        $this->saleOrderDetailRepository = $entityManager->getRepository(SaleOrderDetail::class);
        $this->inventoryRepository = $entityManager->getRepository(Inventory::class);
    }

    public function initialize(StockTransferHeader $stockTransferHeader, array $options = []): void
    {
        list($datetime, $user) = [$options['datetime'], $options['user']];

        if (empty($stockTransferHeader->getId())) {
            $stockTransferHeader->setCreatedTransactionDateTime($datetime);
            $stockTransferHeader->setCreatedTransactionUser($user);
        } else {
            $stockTransferHeader->setModifiedTransactionDateTime($datetime);
            $stockTransferHeader->setModifiedTransactionUser($user);
        }
    }

    public function finalize(StockTransferHeader $stockTransferHeader, array $options = []): void
    {
        if ($stockTransferHeader->getTransactionDate() !== null && $stockTransferHeader->getId() === null) {
            $year = $stockTransferHeader->getTransactionDate()->format('y');
            $month = $stockTransferHeader->getTransactionDate()->format('m');
            $lastStockTransferHeader = $this->stockTransferHeaderRepository->findRecentBy($year, $month);
            $currentStockTransferHeader = ($lastStockTransferHeader === null) ? $stockTransferHeader : $lastStockTransferHeader;
            $stockTransferHeader->setCodeNumberToNext($currentStockTransferHeader->getCodeNumber(), $year, $month);

        }
        foreach ($stockTransferHeader->getStockTransferMaterialDetails() as $stockTransferMaterialDetail) {
            $stockTransferMaterialDetail->setIsCanceled($stockTransferMaterialDetail->getSyncIsCanceled());
        }
        foreach ($stockTransferHeader->getStockTransferPaperDetails() as $stockTransferPaperDetail) {
            $stockTransferPaperDetail->setIsCanceled($stockTransferPaperDetail->getSyncIsCanceled());
        }
        foreach ($stockTransferHeader->getStockTransferProductDetails() as $stockTransferProductDetail) {
            $stockTransferProductDetail->setIsCanceled($stockTransferProductDetail->getSyncIsCanceled());
        }
        $stockTransferHeader->setTotalQuantity($stockTransferHeader->getSyncTotalQuantity());
    }

    public function save(StockTransferHeader $stockTransferHeader, array $options = []): void
    {
        $idempotent = IdempotentUtility::create(Idempotent::class, $this->requestStack->getCurrentRequest());
        $this->idempotentRepository->add($idempotent);
        $this->stockTransferHeaderRepository->add($stockTransferHeader);
        foreach ($stockTransferHeader->getStockTransferMaterialDetails() as $stockTransferMaterialDetail) {
            $this->stockTransferMaterialDetailRepository->add($stockTransferMaterialDetail);
        }
        foreach ($stockTransferHeader->getStockTransferPaperDetails() as $stockTransferPaperDetail) {
            $this->stockTransferPaperDetailRepository->add($stockTransferPaperDetail);
        }
        foreach ($stockTransferHeader->getStockTransferProductDetails() as $stockTransferProductDetail) {
            $this->stockTransferProductDetailRepository->add($stockTransferProductDetail);
        }
        $this->addInventories($stockTransferHeader);
        $this->entityManager->flush();
    }

    private function addInventories(StockTransferHeader $stockTransferHeader): void
    {
        InventoryUtil::reverseOldData($this->inventoryRepository, $stockTransferHeader);
        if ($stockTransferHeader->getTransferMode() === StockTransferHeader::TRANSFER_MODE_MATERIAL) {
            $stockTransferMaterialDetails = $stockTransferHeader->getStockTransferMaterialDetails()->toArray();
            $averagePriceList = InventoryUtil::getAveragePriceList('material', $this->purchaseOrderDetailRepository, $stockTransferMaterialDetails);
            InventoryUtil::addNewData($this->inventoryRepository, $stockTransferHeader, $stockTransferMaterialDetails, function($newInventory, $stockTransferMaterialDetail) use ($averagePriceList, $stockTransferHeader) {
                $material = $stockTransferMaterialDetail->getMaterial();
                $purchasePrice = isset($averagePriceList[$material->getId()]) ? $averagePriceList[$material->getId()] : '0.00';
                $newInventory->setTransactionSubject($stockTransferMaterialDetail->getMemo());
                $newInventory->setPurchasePrice($purchasePrice);
                $newInventory->setMaterial($material);
                $newInventory->setWarehouse($stockTransferHeader->getWarehouseFrom());
                $newInventory->setInventoryMode($stockTransferHeader->getTransferMode());
                $newInventory->setQuantityOut($stockTransferMaterialDetail->getQuantity());
            });
            InventoryUtil::addNewData($this->inventoryRepository, $stockTransferHeader, $stockTransferMaterialDetails, function($newInventory, $stockTransferMaterialDetail) use ($averagePriceList, $stockTransferHeader) {
                $material = $stockTransferMaterialDetail->getMaterial();
                $purchasePrice = isset($averagePriceList[$material->getId()]) ? $averagePriceList[$material->getId()] : '0.00';
                $newInventory->setTransactionSubject($stockTransferMaterialDetail->getMemo());
                $newInventory->setPurchasePrice($purchasePrice);
                $newInventory->setMaterial($material);
                $newInventory->setWarehouse($stockTransferHeader->getWarehouseTo());
                $newInventory->setInventoryMode($stockTransferHeader->getTransferMode());
                $newInventory->setQuantityIn($stockTransferMaterialDetail->getQuantity());
            });
        } else if ($stockTransferHeader->getTransferMode() === StockTransferHeader::TRANSFER_MODE_PAPER) {
            $stockTransferPaperDetails = $stockTransferHeader->getStockTransferPaperDetails()->toArray();
            $averagePriceList = InventoryUtil::getAveragePriceList('paper', $this->purchaseOrderPaperDetailRepository, $stockTransferPaperDetails);
            InventoryUtil::addNewData($this->inventoryRepository, $stockTransferHeader, $stockTransferPaperDetails, function($newInventory, $stockTransferPaperDetail) use ($averagePriceList, $stockTransferHeader) {
                $paper = $stockTransferPaperDetail->getPaper();
                $purchasePrice = isset($averagePriceList[$paper->getId()]) ? $averagePriceList[$paper->getId()] : '0.00';
                $newInventory->setTransactionSubject($stockTransferPaperDetail->getMemo());
                $newInventory->setPurchasePrice($purchasePrice);
                $newInventory->setPaper($paper);
                $newInventory->setWarehouse($stockTransferHeader->getWarehouseFrom());
                $newInventory->setInventoryMode($stockTransferHeader->getTransferMode());
                $newInventory->setQuantityOut($stockTransferPaperDetail->getQuantity());
            });
            InventoryUtil::addNewData($this->inventoryRepository, $stockTransferHeader, $stockTransferPaperDetails, function($newInventory, $stockTransferPaperDetail) use ($averagePriceList, $stockTransferHeader) {
                $paper = $stockTransferPaperDetail->getPaper();
                $purchasePrice = isset($averagePriceList[$paper->getId()]) ? $averagePriceList[$paper->getId()] : '0.00';
                $newInventory->setTransactionSubject($stockTransferPaperDetail->getMemo());
                $newInventory->setPurchasePrice($purchasePrice);
                $newInventory->setPaper($paper);
                $newInventory->setWarehouse($stockTransferHeader->getWarehouseTo());
                $newInventory->setInventoryMode($stockTransferHeader->getTransferMode());
                $newInventory->setQuantityIn($stockTransferPaperDetail->getQuantity());
            });
        } else if ($stockTransferHeader->getTransferMode() === StockTransferHeader::TRANSFER_MODE_PRODUCT) {
            $stockTransferProductDetails = $stockTransferHeader->getStockTransferProductDetails()->toArray();
            $averagePriceList = InventoryUtil::getAveragePriceList('product', $this->saleOrderDetailRepository, $stockTransferProductDetails);
            InventoryUtil::addNewData($this->inventoryRepository, $stockTransferHeader, $stockTransferProductDetails, function($newInventory, $stockTransferProductDetail) use ($averagePriceList, $stockTransferHeader) {
                $product = $stockTransferProductDetail->getProduct();
                $purchasePrice = isset($averagePriceList[$product->getId()]) ? $averagePriceList[$product->getId()] : '0.00';
                $newInventory->setTransactionSubject($stockTransferProductDetail->getMemo());
                $newInventory->setPurchasePrice($purchasePrice);
                $newInventory->setProduct($product);
                $newInventory->setWarehouse($stockTransferHeader->getWarehouseFrom());
                $newInventory->setInventoryMode($stockTransferHeader->getTransferMode());
                $newInventory->setQuantityOut($stockTransferProductDetail->getQuantity());
            });
            InventoryUtil::addNewData($this->inventoryRepository, $stockTransferHeader, $stockTransferProductDetails, function($newInventory, $stockTransferProductDetail) use ($averagePriceList, $stockTransferHeader) {
                $product = $stockTransferProductDetail->getProduct();
                $purchasePrice = isset($averagePriceList[$product->getId()]) ? $averagePriceList[$product->getId()] : '0.00';
                $newInventory->setTransactionSubject($stockTransferProductDetail->getMemo());
                $newInventory->setPurchasePrice($purchasePrice);
                $newInventory->setProduct($product);
                $newInventory->setWarehouse($stockTransferHeader->getWarehouseTo());
                $newInventory->setInventoryMode($stockTransferHeader->getTransferMode());
                $newInventory->setQuantityIn($stockTransferProductDetail->getQuantity());
            });
        }
    }
}
