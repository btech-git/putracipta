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
//        list($month, $year, $staff) = array($params['month'], $params['year'], $params['staff']);
        
        if (empty($purchasePaymentHeader->getId())) {
//            $lastPurchasePaymentHeader = $this->purchasePaymentHeaderRepository->findRecentBy($year, $month);
//            $currentPurchasePaymentHeader = ($lastPurchasePaymentHeader === null) ? $purchasePaymentHeader : $lastPurchasePaymentHeader;
//            $purchasePaymentHeader->setCodeNumberToNext($currentPurchasePaymentHeader->getCodeNumber(), $year, $month);
            
//            $purchasePaymentHeader->setStaffCreated($staff);
//            $purchasePaymentHeader->setTotalPayment(0.00);
//            $purchasePaymentHeader->setTotalReturn(0.00);
        }
    }

    public function finalize(PurchasePaymentHeader $purchasePaymentHeader, array $options = []): void
    {
//        foreach ($purchasePaymentHeader->getPurchasePaymentDetails() as $purchasePaymentDetail) {
//            $purchasePaymentDetail->setPurchasePaymentHeader($purchasePaymentHeader);
//        }
        $this->sync($purchasePaymentHeader);
    }

    public function save(PurchasePaymentHeader $purchasePaymentHeader, array $options = []): void
    {
        $this->purchasePaymentHeaderRepository->add($purchasePaymentHeader);
        foreach ($purchasePaymentHeader->getPurchasePaymentDetails() as $purchasePaymentDetail) {
            $this->purchasePaymentDetailRepository->add($purchasePaymentDetail);
        }
        $this->entityManager->flush();
    }

    public function sync(PurchasePaymentHeader $purchasePaymentHeader): void
    {
        foreach ($purchasePaymentHeader->getPurchasePaymentDetails() as $purchasePaymentDetail) {
            $purchasePaymentDetail->sync();
        }
        $purchasePaymentHeader->sync();
    }
}
