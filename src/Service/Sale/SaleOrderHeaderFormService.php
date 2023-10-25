<?php

namespace App\Service\Sale;

use App\Common\Idempotent\IdempotentUtility;
use App\Entity\Sale\SaleOrderDetail;
use App\Entity\Sale\SaleOrderHeader;
use App\Entity\Support\Idempotent;
use App\Repository\Sale\SaleOrderDetailRepository;
use App\Repository\Sale\SaleOrderHeaderRepository;
use App\Repository\Support\IdempotentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class SaleOrderHeaderFormService
{
    private EntityManagerInterface $entityManager;
    private IdempotentRepository $idempotentRepository;
    private SaleOrderHeaderRepository $saleOrderHeaderRepository;
    private SaleOrderDetailRepository $saleOrderDetailRepository;

    public function __construct(RequestStack $requestStack, EntityManagerInterface $entityManager)
    {
        $this->requestStack = $requestStack;
        $this->entityManager = $entityManager;
        $this->idempotentRepository = $entityManager->getRepository(Idempotent::class);
        $this->saleOrderHeaderRepository = $entityManager->getRepository(SaleOrderHeader::class);
        $this->saleOrderDetailRepository = $entityManager->getRepository(SaleOrderDetail::class);
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
            
            if ($saleOrderDetail->getRemainingDelivery() <= 0) {
                $saleOrderDetail->setIsTransactionClosed(true);
            }
            
            if ($saleOrderDetail->isIsTransactionClosed() === true or $saleOrderDetail->isIsCanceled() === true) {
                $saleOrderDetail->setRemainingDelivery(0);
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
        $idempotent = IdempotentUtility::create(Idempotent::class, $this->requestStack->getCurrentRequest());
        $this->idempotentRepository->add($idempotent);
        $this->saleOrderHeaderRepository->add($saleOrderHeader);
        foreach ($saleOrderHeader->getSaleOrderDetails() as $saleOrderDetail) {
            $this->saleOrderDetailRepository->add($saleOrderDetail);
        }
        $this->entityManager->flush();
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
