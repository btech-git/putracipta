<?php

namespace App\Service\Transaction;

use App\Entity\Transaction\PurchaseOrderDetail;
use App\Entity\Transaction\PurchaseOrderHeader;
use App\Repository\Transaction\PurchaseOrderDetailRepository;
use App\Repository\Transaction\PurchaseOrderHeaderRepository;
use Doctrine\ORM\EntityManagerInterface;

class PurchaseOrderFormService
{
    private EntityManagerInterface $entityManager;
    private PurchaseOrderHeaderRepository $purchaseOrderHeaderRepository;
    private PurchaseOrderDetailRepository $purchaseOrderDetailRepository;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->purchaseOrderHeaderRepository = $entityManager->getRepository(PurchaseOrderHeader::class);
        $this->purchaseOrderDetailRepository = $entityManager->getRepository(PurchaseOrderDetail::class);
    }

    public function initialize(PurchaseOrderHeader $purchaseOrderHeader, array $options = []): void
    {
//        list($month, $year, $staff) = array($params['month'], $params['year'], $params['staff']);
        
        if (empty($purchaseOrderHeader->getId())) {
//            $lastPurchaseOrderHeader = $this->purchaseOrderHeaderRepository->findRecentBy($year, $month);
//            $currentPurchaseOrderHeader = ($lastPurchaseOrderHeader === null) ? $purchaseOrderHeader : $lastPurchaseOrderHeader;
//            $purchaseOrderHeader->setCodeNumberToNext($currentPurchaseOrderHeader->getCodeNumber(), $year, $month);
            
//            $purchaseOrderHeader->setStaffCreated($staff);
//            $purchaseOrderHeader->setTotalPayment(0.00);
//            $purchaseOrderHeader->setTotalReturn(0.00);
        }
    }

    public function finalize(PurchaseOrderHeader $purchaseOrderHeader, array $options = []): void
    {
//        foreach ($purchaseOrderHeader->getPurchaseOrderDetails() as $purchaseOrderDetail) {
//            $purchaseOrderDetail->setPurchaseOrderHeader($purchaseOrderHeader);
//        }
        $this->sync($purchaseOrderHeader);
    }

    public function save(PurchaseOrderHeader $purchaseOrderHeader, array $options = []): void
    {
        $this->purchaseOrderHeaderRepository->add($purchaseOrderHeader);
        foreach ($purchaseOrderHeader->getPurchaseOrderDetails() as $purchaseOrderDetail) {
            $this->purchaseOrderDetailRepository->add($purchaseOrderDetail);
        }
        $this->entityManager->flush();
    }

    public function sync(PurchaseOrderHeader $purchaseOrderHeader): void
    {
        foreach ($purchaseOrderHeader->getPurchaseOrderDetails() as $purchaseOrderDetail) {
            $purchaseOrderDetail->sync();
        }
        $purchaseOrderHeader->sync();
    }
}
