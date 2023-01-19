<?php

namespace App\Service\Transaction;

use App\Entity\Transaction\SaleReturnDetail;
use App\Entity\Transaction\SaleReturnHeader;
use App\Repository\Transaction\SaleReturnDetailRepository;
use App\Repository\Transaction\SaleReturnHeaderRepository;
use Doctrine\ORM\EntityManagerInterface;

class SaleReturnHeaderFormService
{
    private EntityManagerInterface $entityManager;
    private SaleReturnHeaderRepository $saleReturnHeaderRepository;
    private SaleReturnDetailRepository $saleReturnDetailRepository;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->saleReturnHeaderRepository = $entityManager->getRepository(SaleReturnHeader::class);
        $this->saleReturnDetailRepository = $entityManager->getRepository(SaleReturnDetail::class);
    }

    public function initialize(SaleReturnHeader $saleReturnHeader, array $options = []): void
    {
        list($year, $month, $datetime, $user) = [$options['year'], $options['month'], $options['datetime'], $options['user']];

        if (empty($saleReturnHeader->getId())) {
            $lastSaleReturnHeader = $this->saleReturnHeaderRepository->findRecentBy($year, $month);
            $currentSaleReturnHeader = ($lastSaleReturnHeader === null) ? $saleReturnHeader : $lastSaleReturnHeader;
            $saleReturnHeader->setCodeNumberToNext($currentSaleReturnHeader->getCodeNumber(), $year, $month);

            $saleReturnHeader->setCreatedTransactionDateTime($datetime);
            $saleReturnHeader->setCreatedTransactionUser($user);
        } else {
            $saleReturnHeader->setModifiedTransactionDateTime($datetime);
            $saleReturnHeader->setModifiedTransactionUser($user);
        }
    }

    public function finalize(SaleReturnHeader $saleReturnHeader, array $options = []): void
    {
        foreach ($saleReturnHeader->getSaleReturnDetails() as $saleReturnDetail) {
            $saleReturnDetail->setIsCanceled($saleReturnDetail->getSyncIsCanceled());
        }
        $saleReturnHeader->setSubTotal($saleReturnHeader->getSyncSubTotal());
        $saleReturnHeader->setTaxPercentage($saleReturnHeader->getSyncTaxPercentage());
        $saleReturnHeader->setSubTotalAfterTaxInclusion($saleReturnHeader->getSyncSubTotalAfterTaxInclusion());
        $saleReturnHeader->setTaxNominal($saleReturnHeader->getSyncTaxNominal());
        $saleReturnHeader->setGrandTotal($saleReturnHeader->getSyncGrandTotal());
    }

    public function save(SaleReturnHeader $saleReturnHeader, array $options = []): void
    {
        $this->saleReturnHeaderRepository->add($saleReturnHeader);
        foreach ($saleReturnHeader->getSaleReturnDetails() as $saleReturnDetail) {
            $this->saleReturnDetailRepository->add($saleReturnDetail);
        }
        $this->entityManager->flush();
    }
}
