<?php

namespace App\Service\Transaction;

use App\Entity\Transaction\PurchaseRequestPaperDetail;
use App\Entity\Transaction\PurchaseRequestPaperHeader;
use App\Repository\Transaction\PurchaseRequestPaperDetailRepository;
use App\Repository\Transaction\PurchaseRequestPaperHeaderRepository;
use Doctrine\ORM\EntityManagerInterface;

class PurchaseRequestPaperHeaderFormService
{
    private EntityManagerInterface $entityManager;
    private PurchaseRequestPaperHeaderRepository $purchaseRequestPaperHeaderRepository;
    private PurchaseRequestPaperDetailRepository $purchaseRequestPaperDetailRepository;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->purchaseRequestPaperHeaderRepository = $entityManager->getRepository(PurchaseRequestPaperHeader::class);
        $this->purchaseRequestPaperDetailRepository = $entityManager->getRepository(PurchaseRequestPaperDetail::class);
    }

    public function initialize(PurchaseRequestPaperHeader $purchaseRequestPaperHeader, array $options = []): void
    {
        list($year, $month, $datetime, $user) = [$options['year'], $options['month'], $options['datetime'], $options['user']];

        if (empty($purchaseRequestPaperHeader->getId())) {
            $lastPurchaseRequestPaperHeader = $this->purchaseRequestPaperHeaderRepository->findRecentBy($year, $month);
            $currentPurchaseRequestPaperHeader = ($lastPurchaseRequestPaperHeader === null) ? $purchaseRequestPaperHeader : $lastPurchaseRequestPaperHeader;
            $purchaseRequestPaperHeader->setCodeNumberToNext($currentPurchaseRequestPaperHeader->getCodeNumber(), $year, $month);

            $purchaseRequestPaperHeader->setCreatedTransactionDateTime($datetime);
            $purchaseRequestPaperHeader->setCreatedTransactionUser($user);
        } else {
            $purchaseRequestPaperHeader->setModifiedTransactionDateTime($datetime);
            $purchaseRequestPaperHeader->setModifiedTransactionUser($user);
        }
    }

    public function finalize(PurchaseRequestPaperHeader $purchaseRequestPaperHeader, array $options = []): void
    {
        foreach ($purchaseRequestPaperHeader->getPurchaseRequestPaperDetails() as $purchaseRequestPaperDetail) {
            $purchaseRequestPaperDetail->setIsCanceled($purchaseRequestPaperDetail->getSyncIsCanceled());
        }
        $purchaseRequestPaperHeader->setTotalQuantity($purchaseRequestPaperHeader->getSyncTotalQuantity());
    }

    public function save(PurchaseRequestPaperHeader $purchaseRequestPaperHeader, array $options = []): void
    {
        $this->purchaseRequestPaperHeaderRepository->add($purchaseRequestPaperHeader);
        foreach ($purchaseRequestPaperHeader->getPurchaseRequestPaperDetails() as $purchaseRequestPaperDetail) {
            $this->purchaseRequestPaperDetailRepository->add($purchaseRequestPaperDetail);
        }
        $this->entityManager->flush();
    }
}
