<?php

namespace App\Service\Transaction;

use App\Entity\Transaction\SaleOrderDetail;
use App\Entity\Transaction\SaleOrderHeader;
use App\Repository\Transaction\SaleOrderDetailRepository;
use App\Repository\Transaction\SaleOrderHeaderRepository;
use Doctrine\ORM\EntityManagerInterface;

class SaleOrderHeaderFormService
{
    private EntityManagerInterface $entityManager;
    private SaleOrderHeaderRepository $saleOrderHeaderRepository;
    private SaleOrderDetailRepository $saleOrderDetailRepository;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->saleOrderHeaderRepository = $entityManager->getRepository(SaleOrderHeader::class);
        $this->saleOrderDetailRepository = $entityManager->getRepository(SaleOrderDetail::class);
    }

    public function initialize(SaleOrderHeader $saleOrderHeader, array $options = []): void
    {
        list($year, $month, $datetime, $user) = [$options['year'], $options['month'], $options['datetime'], $options['user']];

        if (empty($saleOrderHeader->getId())) {
            $lastSaleOrderHeader = $this->saleOrderHeaderRepository->findRecentBy($year, $month);
            $currentSaleOrderHeader = ($lastSaleOrderHeader === null) ? $saleOrderHeader : $lastSaleOrderHeader;
            $saleOrderHeader->setCodeNumberToNext($currentSaleOrderHeader->getCodeNumber(), $year, $month);

            $saleOrderHeader->setCreatedTransactionDateTime($datetime);
            $saleOrderHeader->setCreatedTransactionUser($user);
        } else {
            $saleOrderHeader->setModifiedTransactionDateTime($datetime);
            $saleOrderHeader->setModifiedTransactionUser($user);
        }
    }

    public function finalize(SaleOrderHeader $saleOrderHeader, array $options = []): void
    {
        foreach ($saleOrderHeader->getSaleOrderDetails() as $saleOrderDetail) {
            $saleOrderDetail->setIsCanceled($saleOrderDetail->getSyncIsCanceled());
        }
        
        if ($saleOrderHeader->getTaxMode() !== $saleOrderHeader::TAX_MODE_NON_TAX) {
            $saleOrderHeader->setTaxPercentage($options['vatPercentage']);
        } else {
            $saleOrderHeader->setTaxPercentage(0);
        }
        
        foreach ($saleOrderHeader->getSaleOrderDetails() as $saleOrderDetail) {
            $saleOrderDetail->setRemainingDelivery($saleOrderDetail->getSyncRemainingDelivery());
            $saleOrderDetail->setUnitPriceBeforeTax($saleOrderDetail->getSyncUnitPriceBeforeTax());
        }
        $saleOrderHeader->setSubTotal($saleOrderHeader->getSyncSubTotal());
        $saleOrderHeader->setTaxNominal($saleOrderHeader->getSyncTaxNominal());
        $saleOrderHeader->setGrandTotal($saleOrderHeader->getSyncGrandTotal());
        $saleOrderHeader->setTotalRemainingDelivery($saleOrderHeader->getSyncTotalRemainingDelivery());
    }

    public function save(SaleOrderHeader $saleOrderHeader, array $options = []): void
    {
        $this->saleOrderHeaderRepository->add($saleOrderHeader);
        foreach ($saleOrderHeader->getSaleOrderDetails() as $saleOrderDetail) {
            $this->saleOrderDetailRepository->add($saleOrderDetail);
        }
        $this->entityManager->flush();
    }
}
