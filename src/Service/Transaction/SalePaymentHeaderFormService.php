<?php

namespace App\Service\Transaction;

use App\Entity\Admin\LiteralConfig;
use App\Entity\Transaction\SaleInvoiceHeader;
use App\Entity\Transaction\SalePaymentDetail;
use App\Entity\Transaction\SalePaymentHeader;
use App\Repository\Admin\LiteralConfigRepository;
use App\Repository\Transaction\SalePaymentDetailRepository;
use App\Repository\Transaction\SalePaymentHeaderRepository;
use Doctrine\ORM\EntityManagerInterface;

class SalePaymentHeaderFormService
{
    private EntityManagerInterface $entityManager;
    private SalePaymentHeaderRepository $salePaymentHeaderRepository;
    private SalePaymentDetailRepository $salePaymentDetailRepository;
    private LiteralConfigRepository $literalConfigRepository;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->salePaymentHeaderRepository = $entityManager->getRepository(SalePaymentHeader::class);
        $this->salePaymentDetailRepository = $entityManager->getRepository(SalePaymentDetail::class);
        $this->literalConfigRepository = $entityManager->getRepository(LiteralConfig::class);
    }

    public function initialize(SalePaymentHeader $salePaymentHeader, array $options = []): void
    {
        list($datetime, $user) = [$options['datetime'], $options['user']];

        if (empty($salePaymentHeader->getId())) {
            $salePaymentHeader->setCreatedTransactionDateTime($datetime);
            $salePaymentHeader->setCreatedTransactionUser($user);
        } else {
            $salePaymentHeader->setModifiedTransactionDateTime($datetime);
            $salePaymentHeader->setModifiedTransactionUser($user);
        }
    }

    public function finalize(SalePaymentHeader $salePaymentHeader, array $options = []): void
    {
        if ($salePaymentHeader->getTransactionDate() !== null && $salePaymentHeader->getId() === null) {
            $year = $salePaymentHeader->getTransactionDate()->format('y');
            $month = $salePaymentHeader->getTransactionDate()->format('m');
            $lastSalePaymentHeader = $this->salePaymentHeaderRepository->findRecentBy($year, $month);
            $currentSalePaymentHeader = ($lastSalePaymentHeader === null) ? $salePaymentHeader : $lastSalePaymentHeader;
            $salePaymentHeader->setCodeNumberToNext($currentSalePaymentHeader->getCodeNumber(), $year, $month);

        }
        foreach ($salePaymentHeader->getSalePaymentDetails() as $salePaymentDetail) {
            $salePaymentDetail->setIsCanceled($salePaymentDetail->getSyncIsCanceled());
            $saleInvoiceHeader = $salePaymentDetail->getSaleInvoiceHeader();
            $oldSalePaymentDetails = $this->salePaymentDetailRepository->findBySaleInvoiceHeader($saleInvoiceHeader);
            $totalPayment = '0.00';
            foreach ($oldSalePaymentDetails as $oldSalePaymentDetail) {
                if ($oldSalePaymentDetail->getId() !== $salePaymentDetail->getId()) {
                    $totalPayment += $oldSalePaymentDetail->getAmount();
                }
            }
            $totalPayment += $salePaymentDetail->getAmount();
            $saleInvoiceHeader->setTotalPayment($totalPayment);
            $saleInvoiceHeader->setRemainingPayment($saleInvoiceHeader->getSyncRemainingPayment());
            $remainingTolerance = $this->literalConfigRepository->findLiteralValue('paymentRemainingTolerance');
            if ($saleInvoiceHeader->getRemainingPayment() > $remainingTolerance) {
                $saleInvoiceHeader->setTransactionStatus(SaleInvoiceHeader::TRANSACTION_STATUS_PARTIAL_PAYMENT);
            } else {
                $saleInvoiceHeader->setTransactionStatus(SaleInvoiceHeader::TRANSACTION_STATUS_FULL_PAYMENT);
            }
        }
        $salePaymentHeader->setTotalAmount($salePaymentHeader->getSyncTotalAmount());
        $salePaymentHeader->setReceivedAmount($salePaymentHeader->getSyncReceivedAmount());
        
        $saleOrderReferenceNumberList = array();
        foreach ($salePaymentHeader->getSalePaymentDetails() as $salePaymentDetail) {
            $saleInvoiceHeader = $salePaymentDetail->getSaleInvoiceHeader();
            $saleOrderReferenceNumberList[] = $saleInvoiceHeader->getSaleOrderReferenceNumbers();
        }
        $salePaymentHeader->setSaleOrderReferenceNumbers(implode(', ', $saleOrderReferenceNumberList));
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
