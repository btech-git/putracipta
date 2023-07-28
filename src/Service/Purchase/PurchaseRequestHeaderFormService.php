<?php

namespace App\Service\Purchase;

use App\Entity\Purchase\PurchaseRequestDetail;
use App\Entity\Purchase\PurchaseRequestHeader;
use App\Repository\Purchase\PurchaseRequestDetailRepository;
use App\Repository\Purchase\PurchaseRequestHeaderRepository;
use Doctrine\ORM\EntityManagerInterface;

class PurchaseRequestHeaderFormService
{
    private EntityManagerInterface $entityManager;
    private PurchaseRequestHeaderRepository $purchaseRequestHeaderRepository;
    private PurchaseRequestDetailRepository $purchaseRequestDetailRepository;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->purchaseRequestHeaderRepository = $entityManager->getRepository(PurchaseRequestHeader::class);
        $this->purchaseRequestDetailRepository = $entityManager->getRepository(PurchaseRequestDetail::class);
    }

    public function initialize(PurchaseRequestHeader $purchaseRequestHeader, array $options = []): void
    {
        list($datetime, $user) = [$options['datetime'], $options['user']];

        if (empty($purchaseRequestHeader->getId())) {
            $purchaseRequestHeader->setCreatedTransactionDateTime($datetime);
            $purchaseRequestHeader->setCreatedTransactionUser($user);
        } else {
            $purchaseRequestHeader->setModifiedTransactionDateTime($datetime);
            $purchaseRequestHeader->setModifiedTransactionUser($user);
        }
        
        $purchaseRequestHeader->setCodeNumberVersion($purchaseRequestHeader->getCodeNumberVersion() + 1);
    }

    public function finalize(PurchaseRequestHeader $purchaseRequestHeader, array $options = []): void
    {
        if ($purchaseRequestHeader->getTransactionDate() !== null && $purchaseRequestHeader->getId() === null) {
            $year = $purchaseRequestHeader->getTransactionDate()->format('y');
            $month = $purchaseRequestHeader->getTransactionDate()->format('m');
            $lastPurchaseRequestHeader = $this->purchaseRequestHeaderRepository->findRecentBy($year, $month);
            $currentPurchaseRequestHeader = ($lastPurchaseRequestHeader === null) ? $purchaseRequestHeader : $lastPurchaseRequestHeader;
            $purchaseRequestHeader->setCodeNumberToNext($currentPurchaseRequestHeader->getCodeNumber(), $year, $month);

        }
        foreach ($purchaseRequestHeader->getPurchaseRequestDetails() as $purchaseRequestDetail) {
            $purchaseRequestDetail->setIsCanceled($purchaseRequestDetail->getSyncIsCanceled());
            $purchaseRequestDetail->setTransactionStatus(PurchaseRequestDetail::TRANSACTION_STATUS_OPEN);
        }
        $purchaseRequestHeader->setTotalQuantity($purchaseRequestHeader->getSyncTotalQuantity());
    }

    public function save(PurchaseRequestHeader $purchaseRequestHeader, array $options = []): void
    {
        $this->purchaseRequestHeaderRepository->add($purchaseRequestHeader);
        foreach ($purchaseRequestHeader->getPurchaseRequestDetails() as $purchaseRequestDetail) {
            $this->purchaseRequestDetailRepository->add($purchaseRequestDetail);
        }
        $this->entityManager->flush();
    }
}
