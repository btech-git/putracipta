<?php

namespace App\Service\Transaction;

use App\Entity\Transaction\PurchaseInvoiceDetail;
use App\Entity\Transaction\PurchaseInvoiceHeader;
use App\Repository\Transaction\PurchaseInvoiceDetailRepository;
use App\Repository\Transaction\PurchaseInvoiceHeaderRepository;
use Doctrine\ORM\EntityManagerInterface;

class PurchaseInvoiceFormService
{
    private EntityManagerInterface $entityManager;
    private PurchaseInvoiceHeaderRepository $purchaseInvoiceHeaderRepository;
    private PurchaseInvoiceDetailRepository $purchaseInvoiceDetailRepository;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->purchaseInvoiceHeaderRepository = $entityManager->getRepository(PurchaseInvoiceHeader::class);
        $this->purchaseInvoiceDetailRepository = $entityManager->getRepository(PurchaseInvoiceDetail::class);
    }

    public function initialize(PurchaseInvoiceHeader $purchaseInvoiceHeader, array $options = []): void
    {
//        list($month, $year, $staff) = array($params['month'], $params['year'], $params['staff']);
        
        if (empty($purchaseInvoiceHeader->getId())) {
//            $lastPurchaseInvoiceHeader = $this->purchaseInvoiceHeaderRepository->findRecentBy($year, $month);
//            $currentPurchaseInvoiceHeader = ($lastPurchaseInvoiceHeader === null) ? $purchaseInvoiceHeader : $lastPurchaseInvoiceHeader;
//            $purchaseInvoiceHeader->setCodeNumberToNext($currentPurchaseInvoiceHeader->getCodeNumber(), $year, $month);
            
//            $purchaseInvoiceHeader->setStaffCreated($staff);
//            $purchaseInvoiceHeader->setTotalPayment(0.00);
//            $purchaseInvoiceHeader->setTotalReturn(0.00);
        }
    }

    public function finalize(PurchaseInvoiceHeader $purchaseInvoiceHeader, array $options = []): void
    {
//        foreach ($purchaseInvoiceHeader->getPurchaseInvoiceDetails() as $purchaseInvoiceDetail) {
//            $purchaseInvoiceDetail->setPurchaseInvoiceHeader($purchaseInvoiceHeader);
//        }
        $this->sync($purchaseInvoiceHeader);
    }

    public function save(PurchaseInvoiceHeader $purchaseInvoiceHeader, array $options = []): void
    {
        $this->purchaseInvoiceHeaderRepository->add($purchaseInvoiceHeader);
        foreach ($purchaseInvoiceHeader->getPurchaseInvoiceDetails() as $purchaseInvoiceDetail) {
            $this->purchaseInvoiceDetailRepository->add($purchaseInvoiceDetail);
        }
        $this->entityManager->flush();
    }

    public function sync(PurchaseInvoiceHeader $purchaseInvoiceHeader): void
    {
        foreach ($purchaseInvoiceHeader->getPurchaseInvoiceDetails() as $purchaseInvoiceDetail) {
            $purchaseInvoiceDetail->sync();
        }
        $purchaseInvoiceHeader->sync();
    }
}
