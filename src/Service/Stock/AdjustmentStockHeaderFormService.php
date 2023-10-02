<?php

namespace App\Service\Stock;

use App\Common\Idempotent\IdempotentUtility;
use App\Entity\Purchase\PurchaseOrderDetail;
use App\Entity\Stock\AdjustmentStockMaterialDetail;
use App\Entity\Stock\AdjustmentStockPaperDetail;
use App\Entity\Stock\AdjustmentStockProductDetail;
use App\Entity\Stock\AdjustmentStockHeader;
use App\Entity\Stock\Inventory;
use App\Entity\Support\Idempotent;
use App\Repository\Purchase\PurchaseOrderDetailRepository;
use App\Repository\Stock\AdjustmentStockMaterialDetailRepository;
use App\Repository\Stock\AdjustmentStockPaperDetailRepository;
use App\Repository\Stock\AdjustmentStockProductDetailRepository;
use App\Repository\Stock\AdjustmentStockHeaderRepository;
use App\Repository\Stock\InventoryRepository;
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
        if ($adjustmentStockHeader->getTransactionDate() !== null) {
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
        $this->entityManager->flush();
    }

    public function addInventories(AdjustmentStockHeader $adjustmentStockHeader): void
    {
        $lastInventoryItems = $this->inventoryRepository->findBy([
            'transactionCodeNumberOrdinal' => $adjustmentStockHeader->getCodeNumberOrdinal(),
            'transactionCodeNumberMonth' => $adjustmentStockHeader->getCodeNumberMonth(),
            'transactionCodeNumberYear' => $adjustmentStockHeader->getCodeNumberYear(),
            'transactionType' => $adjustmentStockHeader->getCodeNumberConstant(),
            'isReversed' => false,
        ]);
        
        foreach ($lastInventoryItems as $lastInventoryItem) {
            $lastInventoryItem->setIsReversed(true);
            $this->inventoryRepository->add($lastInventoryItem);
            $reversedInventory = new Inventory();
            $reversedInventory->setTransactionCodeNumberOrdinal($lastInventoryItem->getTransactionCodeNumberOrdinal());
            $reversedInventory->setTransactionCodeNumberMonth($lastInventoryItem->getTransactionCodeNumberMonth());
            $reversedInventory->setTransactionCodeNumberYear($lastInventoryItem->getTransactionCodeNumberYear());
            $reversedInventory->setTransactionDate($lastInventoryItem->getTransactionDate());
            $reversedInventory->setTransactionType($lastInventoryItem->getTransactionType());
            $reversedInventory->setTransactionSubject($lastInventoryItem->getTransactionSubject());
            $reversedInventory->setMaterial($lastInventoryItem->getMaterial());
            $reversedInventory->setPaper($lastInventoryItem->getPaper());
            $reversedInventory->setProduct($lastInventoryItem->getProduct());
            $reversedInventory->setInventoryMode($lastInventoryItem->getInventoryMode());
            $reversedInventory->setCreatedInventoryDateTime($lastInventoryItem->getCreatedInventoryDateTime());
            $reversedInventory->setNote($lastInventoryItem->getNote());
            $reversedInventory->setPurchasePrice(-($lastInventoryItem->getPurchasePrice()));
            $reversedInventory->setQuantityIn(-($lastInventoryItem->getQuantityIn()));
            $reversedInventory->setQuantityOut(-($lastInventoryItem->getQuantityOut()));
            $reversedInventory->setIsReversed(true);
            $this->inventoryRepository->add($reversedInventory);
        }
        if ($adjustmentStockHeader->getAdjustmentMode() === AdjustmentStockHeader::ADJUSTMENT_MODE_MATERIAL) {
            foreach ($adjustmentStockHeader->getAdjustmentStockMaterialDetails() as $adjustmentStockMaterialDetail) {
                if (!$adjustmentStockMaterialDetail->isCanceled()) {
                    $newInventory = new Inventory();
                    $newInventory->setTransactionCodeNumberOrdinal($adjustmentStockHeader->getCodeNumberOrdinal());
                    $newInventory->setTransactionCodeNumberMonth($adjustmentStockHeader->getCodeNumberMonth());
                    $newInventory->setTransactionCodeNumberYear($adjustmentStockHeader->getCodeNumberYear());
                    $newInventory->setTransactionDate($adjustmentStockHeader->getTransactionDate());
                    $newInventory->setTransactionType($adjustmentStockHeader->getCodeNumberConstant());
                    $newInventory->setTransactionSubject($adjustmentStockMaterialDetail->getMemo());
                    $newInventory->setNote($adjustmentStockHeader->getNote());
//                    $newInventory->setPurchasePrice($adjustmentStockMaterialDetail->getMaterial());
                    $newInventory->setMaterial($adjustmentStockMaterialDetail->getMaterial());
                    $newInventory->setInventoryMode($adjustmentStockHeader->getAdjustmentMode());
                    $newInventory->setCreatedInventoryDateTime(new \DateTime());
                    $newInventory->setQuantityIn($adjustmentStockMaterialDetail->getQuantityAdjustment());
                    $this->inventoryRepository->add($newInventory);
                }
            }
        } else if ($adjustmentStockHeader->getAdjustmentMode() === AdjustmentStockHeader::ADJUSTMENT_MODE_PAPER) {
            
        } else if ($adjustmentStockHeader->getAdjustmentMode() === AdjustmentStockHeader::ADJUSTMENT_MODE_PRODUCT) {
            
        }
    }
}
