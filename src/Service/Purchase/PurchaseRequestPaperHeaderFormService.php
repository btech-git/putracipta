<?php

namespace App\Service\Purchase;

use App\Entity\Purchase\PurchaseRequestPaperDetail;
use App\Entity\Purchase\PurchaseRequestPaperHeader;
use App\Repository\Purchase\PurchaseRequestPaperDetailRepository;
use App\Repository\Purchase\PurchaseRequestPaperHeaderRepository;
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
        list($datetime, $user) = [$options['datetime'], $options['user']];

        if (empty($purchaseRequestPaperHeader->getId())) {
            $purchaseRequestPaperHeader->setCreatedTransactionDateTime($datetime);
            $purchaseRequestPaperHeader->setCreatedTransactionUser($user);
        } else {
            $purchaseRequestPaperHeader->setModifiedTransactionDateTime($datetime);
            $purchaseRequestPaperHeader->setModifiedTransactionUser($user);
        }
        
        $purchaseRequestPaperHeader->setCodeNumberVersion($purchaseRequestPaperHeader->getCodeNumberVersion() + 1);
    }

    public function finalize(PurchaseRequestPaperHeader $purchaseRequestPaperHeader, array $options = []): void
    {
        if ($purchaseRequestPaperHeader->getTransactionDate() !== null && $purchaseRequestPaperHeader->getId() === null) {
            $year = $purchaseRequestPaperHeader->getTransactionDate()->format('y');
            $month = $purchaseRequestPaperHeader->getTransactionDate()->format('m');
            $lastPurchaseRequestPaperHeader = $this->purchaseRequestPaperHeaderRepository->findRecentBy($year, $month);
            $currentPurchaseRequestPaperHeader = ($lastPurchaseRequestPaperHeader === null) ? $purchaseRequestPaperHeader : $lastPurchaseRequestPaperHeader;
            $purchaseRequestPaperHeader->setCodeNumberToNext($currentPurchaseRequestPaperHeader->getCodeNumber(), $year, $month);

        }
        foreach ($purchaseRequestPaperHeader->getPurchaseRequestPaperDetails() as $purchaseRequestPaperDetail) {
            $purchaseRequestPaperDetail->setIsCanceled($purchaseRequestPaperDetail->getSyncIsCanceled());
            $purchaseRequestPaperDetail->setTransactionStatus(PurchaseRequestPaperDetail::TRANSACTION_STATUS_OPEN);
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