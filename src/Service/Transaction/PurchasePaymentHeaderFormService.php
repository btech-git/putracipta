<?php

namespace App\Service\Transaction;

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
    }

    public function save(PurchasePaymentHeader $purchasePaymentHeader, array $options = []): void
    {
        $this->purchasePaymentHeaderRepository->add($purchasePaymentHeader);
        foreach ($purchasePaymentHeader->getPurchasePaymentDetails() as $purchasePaymentDetail) {
            $this->purchasePaymentDetailRepository->add($purchasePaymentDetail);
        }
        $this->entityManager->flush();
    }
}
