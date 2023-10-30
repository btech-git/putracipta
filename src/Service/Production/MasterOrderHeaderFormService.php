<?php

namespace App\Service\Production;

use App\Common\Idempotent\IdempotentUtility;
use App\Entity\Master\Warehouse;
use App\Entity\Production\MasterOrderCheckSheetDetail;
use App\Entity\Production\MasterOrderDistributionDetail;
use App\Entity\Production\MasterOrderHeader;
use App\Entity\Production\MasterOrderProcessDetail;
use App\Entity\Production\MasterOrderProductDetail;
use App\Entity\Stock\Inventory;
use App\Entity\Support\Idempotent;
use App\Repository\Master\WarehouseRepository;
use App\Repository\Production\MasterOrderCheckSheetDetailRepository;
use App\Repository\Production\MasterOrderDistributionDetailRepository;
use App\Repository\Production\MasterOrderHeaderRepository;
use App\Repository\Production\MasterOrderProcessDetailRepository;
use App\Repository\Production\MasterOrderProductDetailRepository;
use App\Repository\Stock\InventoryRepository;
use App\Repository\Support\IdempotentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class MasterOrderHeaderFormService
{
    private EntityManagerInterface $entityManager;
    private IdempotentRepository $idempotentRepository;
    private InventoryRepository $inventoryRepository;
    private WarehouseRepository $warehouseRepository;
    private MasterOrderHeaderRepository $masterOrderHeaderRepository;
    private MasterOrderProductDetailRepository $masterOrderProductDetailRepository;
    private MasterOrderProcessDetailRepository $masterOrderProcessDetailRepository;
    private MasterOrderDistributionDetailRepository $masterOrderDistributionDetailRepository;
    private MasterOrderCheckSheetDetailRepository $masterOrderCheckSheetDetailRepository;

    public function __construct(RequestStack $requestStack, EntityManagerInterface $entityManager)
    {
        $this->requestStack = $requestStack;
        $this->entityManager = $entityManager;
        $this->idempotentRepository = $entityManager->getRepository(Idempotent::class);
        $this->inventoryRepository = $entityManager->getRepository(Inventory::class);
        $this->masterOrderHeaderRepository = $entityManager->getRepository(MasterOrderHeader::class);
        $this->masterOrderProductDetailRepository = $entityManager->getRepository(MasterOrderProductDetail::class);
        $this->masterOrderProcessDetailRepository = $entityManager->getRepository(MasterOrderProcessDetail::class);
        $this->masterOrderDistributionDetailRepository = $entityManager->getRepository(MasterOrderDistributionDetail::class);
        $this->masterOrderCheckSheetDetailRepository = $entityManager->getRepository(MasterOrderCheckSheetDetail::class);
        $this->warehouseRepository = $entityManager->getRepository(Warehouse::class);
    }

    public function initialize(MasterOrderHeader $masterOrderHeader, array $options = []): void
    {
        list($datetime, $user) = [$options['datetime'], $options['user']];

        if (empty($masterOrderHeader->getId())) {
            $masterOrderHeader->setCreatedTransactionDateTime($datetime);
            $masterOrderHeader->setCreatedTransactionUser($user);
        } else {
            $masterOrderHeader->setModifiedTransactionDateTime($datetime);
            $masterOrderHeader->setModifiedTransactionUser($user);
        }
    }

    public function finalize(MasterOrderHeader $masterOrderHeader, array $options = []): void
    {
        if ($masterOrderHeader->getTransactionDate() !== null && $masterOrderHeader->getId() === null) {
            $year = $masterOrderHeader->getTransactionDate()->format('y');
            $month = $masterOrderHeader->getTransactionDate()->format('m');
            $lastMasterOrderHeader = $this->masterOrderHeaderRepository->findRecentBy($year, $month);
            $currentMasterOrderHeader = ($lastMasterOrderHeader === null) ? $masterOrderHeader : $lastMasterOrderHeader;
            $masterOrderHeader->setCodeNumberToNext($currentMasterOrderHeader->getCodeNumber(), $year, $month);
        }
        
        foreach ($masterOrderHeader->getMasterOrderProductDetails() as $masterOrderProductDetail) {
            $saleOrderDetail = $masterOrderProductDetail->getSaleOrderDetail();
            if (!empty($saleOrderDetail)) {
                $masterOrderProductDetail->setProduct($saleOrderDetail->getProduct());
                $masterOrderProductDetail->setQuantityOrder($saleOrderDetail->getQuantity());
                $masterOrderProductDetail->setQuantityShortage($masterOrderProductDetail->getSyncQuantityShortage());
            }
        }
        $masterOrderHeader->setTotalQuantityOrder($masterOrderHeader->getSyncTotalQuantityOrder());
        $masterOrderHeader->setTotalQuantityStock($masterOrderHeader->getSyncTotalQuantityStock());
        $masterOrderHeader->setTotalQuantityShortage($masterOrderHeader->getSyncTotalQuantityShortage());
        
        $paper = $masterOrderHeader->getPaper();
        if (!empty($paper)) {
            $masterOrderHeader->setPaperPlanoLength($paper->getLength());
            $masterOrderHeader->setPaperPlanoWidth($paper->getWidth());
        }
        $masterOrderHeader->setQuantityPaper($masterOrderHeader->getSyncQuantityPaper());
        $masterOrderHeader->setPaperTotal($masterOrderHeader->getSyncPaperTotal());
        $masterOrderHeader->setInsitPrintingQuantity($masterOrderHeader->getSyncInsitPrintingQuantity());
        $masterOrderHeader->setInsitSortingQuantity($masterOrderHeader->getSyncInsitSortingQuantity());
        $masterOrderHeader->setPaperRequirement($masterOrderHeader->getSyncPaperRequirement());
        $masterOrderHeader->setInkCyanWeight($masterOrderHeader->getSyncInkCyanWeight());
        $masterOrderHeader->setInkMagentaWeight($masterOrderHeader->getSyncInkMagentaWeight());
        $masterOrderHeader->setInkYellowWeight($masterOrderHeader->getSyncInkYellowWeight());
        $masterOrderHeader->setInkBlackWeight($masterOrderHeader->getSyncInkBlackWeight());
        $masterOrderHeader->setInkOpvWeight($masterOrderHeader->getSyncInkOpvWeight());
        $masterOrderHeader->setInkK1Weight($masterOrderHeader->getSyncInkK1Weight());
        $masterOrderHeader->setInkK2Weight($masterOrderHeader->getSyncInkK2Weight());
        $masterOrderHeader->setInkK3Weight($masterOrderHeader->getSyncInkK3Weight());
        $masterOrderHeader->setInkK4Weight($masterOrderHeader->getSyncInkK4Weight());
        $masterOrderHeader->setInkLaminatingQuantity($masterOrderHeader->getSyncInkLaminatingQuantity());
        $masterOrderHeader->setPackagingGlueWeight($masterOrderHeader->getSyncPackagingGlueWeight());
        $masterOrderHeader->setPackagingRubberWeight($masterOrderHeader->getSyncPackagingRubberWeight());
        $masterOrderHeader->setPackagingPaperWeight($masterOrderHeader->getSyncPackagingPaperWeight());
        $masterOrderHeader->setPackagingBoxWeight($masterOrderHeader->getSyncPackagingBoxWeight());
        $masterOrderHeader->setPackagingTapeLargeSize($masterOrderHeader->getSyncPackagingTapeLargeSize());
        $masterOrderHeader->setPackagingTapeSmallSize($masterOrderHeader->getSyncPackagingTapeSmallSize());
        $masterOrderHeader->setPackagingPlasticSize($masterOrderHeader->getSyncPackagingPlasticSize());
        
        $finishedGoodsWarehouse = $this->warehouseRepository->find(2);
        $masterOrderHeader->setWarehouse($finishedGoodsWarehouse);
        if ($masterOrderHeader->getWarehouse() !== null) {
            $products = array_map(fn($masterOrderProductDetail) => $masterOrderProductDetail->getProduct(), $masterOrderHeader->getMasterOrderProductDetails()->toArray());
            $stockQuantityList = $this->inventoryRepository->getProductStockQuantityList($masterOrderHeader->getWarehouse(), $products);
            $stockQuantityListIndexed = array_column($stockQuantityList, 'stockQuantity', 'productId');
            foreach ($masterOrderHeader->getMasterOrderProductDetails() as $masterOrderProductDetail) {
                $masterOrderProductDetail->setIsCanceled($masterOrderProductDetail->getSyncIsCanceled());
                $product = $masterOrderProductDetail->getProduct();
                $stockQuantity = isset($stockQuantityListIndexed[$product->getId()]) ? $stockQuantityListIndexed[$product->getId()] : 0;
                $masterOrderProductDetail->setQuantityStock($stockQuantity);
            }
        }
        
        if ($options['transactionFile']) {
            $masterOrderHeader->setLayoutModelFileExtension($options['transactionFile']->guessExtension());
        }
    }

    public function save(MasterOrderHeader $masterOrderHeader, array $options = []): void
    {
        $idempotent = IdempotentUtility::create(Idempotent::class, $this->requestStack->getCurrentRequest());
        $this->idempotentRepository->add($idempotent);
        $this->masterOrderHeaderRepository->add($masterOrderHeader);
        foreach ($masterOrderHeader->getMasterOrderProductDetails() as $masterOrderProductDetail) {
            $this->masterOrderProductDetailRepository->add($masterOrderProductDetail);
        }
        foreach ($masterOrderHeader->getMasterOrderProcessDetails() as $masterOrderProcessDetail) {
            $this->masterOrderProcessDetailRepository->add($masterOrderProcessDetail);
        }
        foreach ($masterOrderHeader->getMasterOrderDistributionDetails() as $masterOrderDistributionDetail) {
            $this->masterOrderDistributionDetailRepository->add($masterOrderDistributionDetail);
        }
        foreach ($masterOrderHeader->getMasterOrderCheckSheetDetails() as $masterOrderCheckSheetDetail) {
            $this->masterOrderCheckSheetDetailRepository->add($masterOrderCheckSheetDetail);
        }
        $this->entityManager->flush();
    }

    public function uploadFile(MasterOrderHeader $masterOrderHeader, $transactionFile, $uploadDirectory): void
    {
        if ($transactionFile) {
            try {
                $filename = $masterOrderHeader->getId() . '.' . $masterOrderHeader->getLayoutModelFileExtension();
                $transactionFile->move($uploadDirectory, $filename);
            } catch (FileException $e) {
            }
        }
    }
}
