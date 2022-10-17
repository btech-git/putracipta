<?php

namespace App\Service\Transaction;

use App\Entity\Transaction\PurchaseReturnDetail;
use App\Entity\Transaction\PurchaseReturnHeader;
use App\Repository\Transaction\PurchaseReturnDetailRepository;
use App\Repository\Transaction\PurchaseReturnHeaderRepository;
use Doctrine\ORM\EntityManagerInterface;

class PurchaseReturnFormService
{
    private EntityManagerInterface $entityManager;
    private PurchaseReturnHeaderRepository $purchaseReturnHeaderRepository;
    private PurchaseReturnDetailRepository $purchaseReturnDetailRepository;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->purchaseReturnHeaderRepository = $entityManager->getRepository(PurchaseReturnHeader::class);
        $this->purchaseReturnDetailRepository = $entityManager->getRepository(PurchaseReturnDetail::class);
    }

    public function initialize(PurchaseReturnHeader $purchaseReturnHeader, array $options = []): void
    {
//        list($month, $year, $staff) = array($params['month'], $params['year'], $params['staff']);
        
        if (empty($purchaseReturnHeader->getId())) {
//            $lastPurchaseReturnHeader = $this->purchaseReturnHeaderRepository->findRecentBy($year, $month);
//            $currentPurchaseReturnHeader = ($lastPurchaseReturnHeader === null) ? $purchaseReturnHeader : $lastPurchaseReturnHeader;
//            $purchaseReturnHeader->setCodeNumberToNext($currentPurchaseReturnHeader->getCodeNumber(), $year, $month);
            
//            $purchaseReturnHeader->setStaffCreated($staff);
//            $purchaseReturnHeader->setTotalPayment(0.00);
//            $purchaseReturnHeader->setTotalReturn(0.00);
        }
    }

    public function finalize(PurchaseReturnHeader $purchaseReturnHeader, array $options = []): void
    {
//        foreach ($purchaseReturnHeader->getPurchaseReturnDetails() as $purchaseReturnDetail) {
//            $purchaseReturnDetail->setPurchaseReturnHeader($purchaseReturnHeader);
//        }
        $this->sync($purchaseReturnHeader);
    }

    public function save(PurchaseReturnHeader $purchaseReturnHeader, array $options = []): void
    {
        $this->purchaseReturnHeaderRepository->add($purchaseReturnHeader);
        foreach ($purchaseReturnHeader->getPurchaseReturnDetails() as $purchaseReturnDetail) {
            $this->purchaseReturnDetailRepository->add($purchaseReturnDetail);
        }
        $this->entityManager->flush();
    }

    public function sync(PurchaseReturnHeader $purchaseReturnHeader): void
    {
        foreach ($purchaseReturnHeader->getPurchaseReturnDetails() as $purchaseReturnDetail) {
            $purchaseReturnDetail->sync();
        }
        $purchaseReturnHeader->sync();
    }
}
