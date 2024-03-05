<?php

namespace App\Service\Sale;

use App\Common\Idempotent\IdempotentUtility;
use App\Entity\Sale\SaleOrderDetail;
use App\Entity\Sale\SaleOrderHeader;
use App\Entity\Stock\Inventory;
use App\Entity\Support\Idempotent;
use App\Entity\Support\TransactionLog;
use App\Repository\Sale\SaleOrderDetailRepository;
use App\Repository\Sale\SaleOrderHeaderRepository;
use App\Repository\Stock\InventoryRepository;
use App\Repository\Support\IdempotentRepository;
use App\Repository\Support\TransactionLogRepository;
use App\Support\Sale\SaleOrderHeaderFormSupport;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class SaleOrderHeaderFormService
{
    use SaleOrderHeaderFormSupport;

    private EntityManagerInterface $entityManager;
    private TransactionLogRepository $transactionLogRepository;
    private IdempotentRepository $idempotentRepository;
    private SaleOrderHeaderRepository $saleOrderHeaderRepository;
    private SaleOrderDetailRepository $saleOrderDetailRepository;
    private InventoryRepository $inventoryRepository;

    public function __construct(RequestStack $requestStack, EntityManagerInterface $entityManager)
    {
        $this->requestStack = $requestStack;
        $this->entityManager = $entityManager;
        $this->transactionLogRepository = $entityManager->getRepository(TransactionLog::class);
        $this->idempotentRepository = $entityManager->getRepository(Idempotent::class);
        $this->saleOrderHeaderRepository = $entityManager->getRepository(SaleOrderHeader::class);
        $this->saleOrderDetailRepository = $entityManager->getRepository(SaleOrderDetail::class);
        $this->inventoryRepository = $entityManager->getRepository(Inventory::class);
    }

    public function initialize(SaleOrderHeader $saleOrderHeader, array $options = []): void
    {
        list($datetime, $user) = [$options['datetime'], $options['user']];

        if (empty($saleOrderHeader->getId())) {
            $saleOrderHeader->setCreatedTransactionDateTime($datetime);
            $saleOrderHeader->setCreatedTransactionUser($user);
        } else {
            $saleOrderHeader->setModifiedTransactionDateTime($datetime);
            $saleOrderHeader->setModifiedTransactionUser($user);
        }
        
        $saleOrderHeader->setCodeNumberVersion($saleOrderHeader->getCodeNumberVersion() + 1);
    }

    public function finalize(SaleOrderHeader $saleOrderHeader, array $options = []): void
    {
        if ($saleOrderHeader->getTransactionDate() !== null && $saleOrderHeader->getId() === null) {
            $year = $saleOrderHeader->getTransactionDate()->format('y');
            $month = $saleOrderHeader->getTransactionDate()->format('m');
            $lastSaleOrderHeader = $this->saleOrderHeaderRepository->findRecentBy($year, $month);
            $currentSaleOrderHeader = ($lastSaleOrderHeader === null) ? $saleOrderHeader : $lastSaleOrderHeader;
            $saleOrderHeader->setCodeNumberToNext($currentSaleOrderHeader->getCodeNumber(), $year, $month);
        }
        
        foreach ($saleOrderHeader->getSaleOrderDetails() as $i => $saleOrderDetail) {
            $saleOrderDetail->setIsCanceled($saleOrderDetail->getSyncIsCanceled());
            $saleOrderDetail->setRemainingDelivery($saleOrderDetail->getSyncRemainingDelivery());
            $saleOrderDetail->setUnitPriceBeforeTax($saleOrderDetail->getSyncUnitPriceBeforeTax());
            $saleOrderDetail->setLinePo($i + 1);
            $saleOrderDetail->setQuantityProductionRemaining($saleOrderDetail->getSyncRemainingProduction());
            
            if ($saleOrderDetail->getRemainingDelivery() <= 0) {
                $saleOrderDetail->setIsTransactionClosed(true);
            }
            
            if ($saleOrderDetail->isIsTransactionClosed() === true or $saleOrderDetail->isIsCanceled() === true) {
                $saleOrderDetail->setRemainingDelivery(0);
            }
        }
        
        if ($saleOrderHeader->getId() === null) {
            $products = array_map(fn($saleOrderDetail) => $saleOrderDetail->getProduct(), $saleOrderHeader->getSaleOrderDetails()->toArray());
            $stockQuantityList = $this->inventoryRepository->getAllWarehouseProductStockQuantityList($products);
            $stockQuantityListIndexed = array_column($stockQuantityList, 'stockQuantity', 'productId');
            foreach ($saleOrderHeader->getSaleOrderDetails() as $saleOrderDetail) {
                $product = $saleOrderDetail->getProduct();
                $stockQuantity = isset($stockQuantityListIndexed[$product->getId()]) ? $stockQuantityListIndexed[$product->getId()] : 0;
                $saleOrderDetail->setQuantityStock($stockQuantity);
            }
        }

        if ($saleOrderHeader->getTaxMode() !== $saleOrderHeader::TAX_MODE_NON_TAX) {
            $saleOrderHeader->setTaxPercentage($options['vatPercentage']);
        } else {
            $saleOrderHeader->setTaxPercentage(0);
        }
        
        $saleOrderHeader->setTotalQuantity($saleOrderHeader->getSyncTotalQuantity());
        $saleOrderHeader->setSubTotal($saleOrderHeader->getSyncSubTotal());
        $saleOrderHeader->setTaxNominal($saleOrderHeader->getSyncTaxNominal());
        $saleOrderHeader->setGrandTotal($saleOrderHeader->getSyncGrandTotal());
        $saleOrderHeader->setTotalRemainingDelivery($saleOrderHeader->getSyncTotalRemainingDelivery());

        if ($options['transactionFile']) {
            $saleOrderHeader->setTransactionFileExtension($options['transactionFile']->guessExtension());
        }
    }

    public function save(SaleOrderHeader $saleOrderHeader, array $options = []): void
    {
        $this->entityManager->wrapInTransaction(function($entityManager) use ($saleOrderHeader) {
            $idempotent = IdempotentUtility::create(Idempotent::class, $this->requestStack->getCurrentRequest());
            $this->idempotentRepository->add($idempotent);
            $this->saleOrderHeaderRepository->add($saleOrderHeader);
            foreach ($saleOrderHeader->getSaleOrderDetails() as $saleOrderDetail) {
                $this->saleOrderDetailRepository->add($saleOrderDetail);
            }
            $this->entityManager->flush();
            $transactionLog = $this->buildTransactionLog($saleOrderHeader);
            $this->transactionLogRepository->add($transactionLog);
            $entityManager->flush();
        });
    }

    public function uploadFile(SaleOrderHeader $saleOrderHeader, $transactionFile, $uploadDirectory): void
    {
        if ($transactionFile) {
            try {
                $filename = $saleOrderHeader->getId() . '.' . $saleOrderHeader->getTransactionFileExtension();
                $transactionFile->move($uploadDirectory, $filename);
            } catch (FileException $e) {
            }
        }
    }

    public function copyFrom(SaleOrderHeader $sourceSaleOrderHeader): SaleOrderHeader
    {
        $saleOrderHeader = new SaleOrderHeader();
        $saleOrderHeader->setCustomer($sourceSaleOrderHeader->getCustomer());
        $saleOrderHeader->setIsUsingFscPaper($sourceSaleOrderHeader->isIsUsingFscPaper());
        foreach ($sourceSaleOrderHeader->getSaleOrderDetails() as $sourceSaleOrderDetail) {
            $saleOrderDetail = new SaleOrderDetail();
            $saleOrderDetail->setProduct($sourceSaleOrderDetail->getProduct());
            $saleOrderDetail->setQuantity($sourceSaleOrderDetail->getQuantity());
            $saleOrderDetail->setUnit($sourceSaleOrderDetail->getUnit());
            $saleOrderDetail->setUnitPrice($sourceSaleOrderDetail->getUnitPrice());
            $saleOrderHeader->addSaleOrderDetail($saleOrderDetail);
        }
        return $saleOrderHeader;
    }
}
