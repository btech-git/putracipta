<?php

namespace App\Service\Transaction;

use App\Entity\Transaction\SalePaymentDetail;
use App\Entity\Transaction\SalePaymentHeader;
use App\Repository\Transaction\SalePaymentDetailRepository;
use App\Repository\Transaction\SalePaymentHeaderRepository;
use Doctrine\ORM\EntityManagerInterface;

class SalePaymentHeaderFormService
{
    private EntityManagerInterface $entityManager;
    private SalePaymentHeaderRepository $salePaymentHeaderRepository;
    private SalePaymentDetailRepository $salePaymentDetailRepository;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->salePaymentHeaderRepository = $entityManager->getRepository(SalePaymentHeader::class);
        $this->salePaymentDetailRepository = $entityManager->getRepository(SalePaymentDetail::class);
    }

    public function initialize(SalePaymentHeader $salePaymentHeader, array $options = []): void
    {
        list($year, $month, $datetime, $user) = [$options['year'], $options['month'], $options['datetime'], $options['user']];

        if (empty($salePaymentHeader->getId())) {
            $lastSalePaymentHeader = $this->salePaymentHeaderRepository->findRecentBy($year, $month);
            $currentSalePaymentHeader = ($lastSalePaymentHeader === null) ? $salePaymentHeader : $lastSalePaymentHeader;
            $salePaymentHeader->setCodeNumberToNext($currentSalePaymentHeader->getCodeNumber(), $year, $month);

            $salePaymentHeader->setCreatedTransactionDateTime($datetime);
            $salePaymentHeader->setCreatedTransactionUser($user);
        } else {
            $salePaymentHeader->setModifiedTransactionDateTime($datetime);
            $salePaymentHeader->setModifiedTransactionUser($user);
        }
    }

    public function finalize(SalePaymentHeader $salePaymentHeader, array $options = []): void
    {
        foreach ($salePaymentHeader->getSalePaymentDetails() as $salePaymentDetail) {
            $salePaymentDetail->setIsCanceled($salePaymentDetail->getSyncIsCanceled());
        }
        $salePaymentHeader->setTotalAmount($salePaymentHeader->getSyncTotalAmount());
    }

    public function save(SalePaymentHeader $salePaymentHeader, array $options = []): void
    {
        $this->salePaymentHeaderRepository->add($salePaymentHeader);
        foreach ($salePaymentHeader->getSalePaymentDetails() as $salePaymentDetail) {
            $this->salePaymentDetailRepository->add($salePaymentDetail);
        }
        $this->entityManager->flush();
    }
}
