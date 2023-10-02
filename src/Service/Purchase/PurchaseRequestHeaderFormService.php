<?php

namespace App\Service\Purchase;

use App\Common\Idempotent\IdempotentUtility;
use App\Entity\Purchase\PurchaseRequestDetail;
use App\Entity\Purchase\PurchaseRequestHeader;
use App\Entity\Support\Idempotent;
use App\Repository\Purchase\PurchaseRequestDetailRepository;
use App\Repository\Purchase\PurchaseRequestHeaderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class PurchaseRequestHeaderFormService
{
    private EntityManagerInterface $entityManager;
    private PurchaseRequestHeaderRepository $purchaseRequestHeaderRepository;
    private PurchaseRequestDetailRepository $purchaseRequestDetailRepository;

    public function __construct(RequestStack $requestStack, EntityManagerInterface $entityManager)
    {
        $this->requestStack = $requestStack;
        $this->entityManager = $entityManager;
        $this->idempotentRepository = $entityManager->getRepository(Idempotent::class);
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
        $idempotent = IdempotentUtility::create(Idempotent::class, $this->requestStack->getCurrentRequest());
        $this->idempotentRepository->add($idempotent);
        $this->purchaseRequestHeaderRepository->add($purchaseRequestHeader);
        foreach ($purchaseRequestHeader->getPurchaseRequestDetails() as $purchaseRequestDetail) {
            $this->purchaseRequestDetailRepository->add($purchaseRequestDetail);
        }
        $this->entityManager->flush();
    }
}
