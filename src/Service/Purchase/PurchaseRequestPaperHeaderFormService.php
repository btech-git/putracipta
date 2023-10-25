<?php

namespace App\Service\Purchase;

use App\Common\Idempotent\IdempotentUtility;
use App\Entity\Purchase\PurchaseRequestPaperDetail;
use App\Entity\Purchase\PurchaseRequestPaperHeader;
use App\Entity\Support\Idempotent;
use App\Repository\Purchase\PurchaseRequestPaperDetailRepository;
use App\Repository\Purchase\PurchaseRequestPaperHeaderRepository;
use App\Repository\Support\IdempotentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class PurchaseRequestPaperHeaderFormService
{
    private EntityManagerInterface $entityManager;
    private IdempotentRepository $idempotentRepository;
    private PurchaseRequestPaperHeaderRepository $purchaseRequestPaperHeaderRepository;
    private PurchaseRequestPaperDetailRepository $purchaseRequestPaperDetailRepository;

    public function __construct(RequestStack $requestStack, EntityManagerInterface $entityManager)
    {
        $this->requestStack = $requestStack;
        $this->entityManager = $entityManager;
        $this->idempotentRepository = $entityManager->getRepository(Idempotent::class);
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
        $idempotent = IdempotentUtility::create(Idempotent::class, $this->requestStack->getCurrentRequest());
        $this->idempotentRepository->add($idempotent);
        $this->purchaseRequestPaperHeaderRepository->add($purchaseRequestPaperHeader);
        foreach ($purchaseRequestPaperHeader->getPurchaseRequestPaperDetails() as $purchaseRequestPaperDetail) {
            $this->purchaseRequestPaperDetailRepository->add($purchaseRequestPaperDetail);
        }
        $this->entityManager->flush();
    }
}
