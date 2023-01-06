<?php

namespace App\Service\Transaction;

use App\Entity\Transaction\PurchaseInvoiceHeader;
use App\Entity\Transaction\PurchasePaymentDetail;
use App\Entity\Transaction\PurchasePaymentHeader;
use App\Repository\Transaction\PurchasePaymentDetailRepository;
use App\Repository\Transaction\PurchasePaymentHeaderRepository;
use Doctrine\ORM\EntityManagerInterface;

class PurchasePaymentHeaderFormService
{
    private EntityManagerInterface $entityManager;
    private PurchasePaymentHeaderRepository $purchasePaymentHeaderRepository;
    private PurchasePaymentDetailRepository $purchasePaymentDetailRepository;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->purchasePaymentHeaderRepository = $entityManager->getRepository(PurchasePaymentHeader::class);
        $this->purchasePaymentDetailRepository = $entityManager->getRepository(PurchasePaymentDetail::class);
        $this->purchaseInvoiceHeaderRepository = $entityManager->getRepository(PurchaseInvoiceHeader::class);
    }

    public function initialize(PurchasePaymentHeader $purchasePaymentHeader, array $options = []): void
    {
        list($year, $month, $datetime, $user) = [$options['year'], $options['month'], $options['datetime'], $options['user']];

        if (empty($purchasePaymentHeader->getId())) {
            $lastPurchasePaymentHeader = $this->purchasePaymentHeaderRepository->findRecentBy($year, $month);
            $currentPurchasePaymentHeader = ($lastPurchasePaymentHeader === null) ? $purchasePaymentHeader : $lastPurchasePaymentHeader;
            $purchasePaymentHeader->setCodeNumberToNext($currentPurchasePaymentHeader->getCodeNumber(), $year, $month);

            $purchasePaymentHeader->setCreatedTransactionDateTime($datetime);
            $purchasePaymentHeader->setCreatedTransactionUser($user);
        } else {
            $purchasePaymentHeader->setModifiedTransactionDateTime($datetime);
            $purchasePaymentHeader->setModifiedTransactionUser($user);
        }
    }

    public function finalize(PurchasePaymentHeader $purchasePaymentHeader, array $options = []): void
    {
        foreach ($purchasePaymentHeader->getPurchasePaymentDetails() as $purchasePaymentDetail) {
            $purchasePaymentDetail->setIsCanceled($purchasePaymentDetail->getSyncIsCanceled());
        }
        $purchasePaymentHeader->setTotalAmount($purchasePaymentHeader->getSyncTotalAmount());
        foreach ($purchasePaymentHeader->getPurchasePaymentDetails() as $purchasePaymentDetail) {
            $purchaseInvoiceHeader = $purchasePaymentDetail->getPurchaseInvoiceHeader();
            $oldPurchasePaymentDetails = $this->purchasePaymentDetailRepository->findByPurchaseInvoiceHeader($purchaseInvoiceHeader);
            $totalPayment = '0.00';
            foreach ($oldPurchasePaymentDetails as $oldPurchasePaymentDetail) {
                if ($oldPurchasePaymentDetail->getId() !== $purchasePaymentDetail->getId()) {
                    $totalPayment += $oldPurchasePaymentDetail->getAmount();
                }
            }
            $totalPayment += $purchasePaymentDetail->getAmount();
            $purchaseInvoiceHeader->setTotalPayment($totalPayment);
            $purchaseInvoiceHeader->setRemainingPayment($purchaseInvoiceHeader->getSyncRemainingPayment());
            if ($purchaseInvoiceHeader->getRemainingPayment() > '0.00') {
                $purchaseInvoiceHeader->setTransactionStatus(PurchaseInvoiceHeader::TRANSACTION_STATUS_PARTIAL_PAYMENT);
            } else {
                $purchaseInvoiceHeader->setTransactionStatus(PurchaseInvoiceHeader::TRANSACTION_STATUS_FULL_PAYMENT);
            }
        }
    }

    public function save(PurchasePaymentHeader $purchasePaymentHeader, array $options = []): void
    {
        $this->purchasePaymentHeaderRepository->add($purchasePaymentHeader);
        foreach ($purchasePaymentHeader->getPurchasePaymentDetails() as $purchasePaymentDetail) {
            $purchaseInvoiceHeader = $purchasePaymentDetail->getPurchaseInvoiceHeader();
            $this->purchasePaymentDetailRepository->add($purchasePaymentDetail);
            $this->purchaseInvoiceHeaderRepository->add($purchaseInvoiceHeader);
        }
        $this->entityManager->flush();
    }
}
