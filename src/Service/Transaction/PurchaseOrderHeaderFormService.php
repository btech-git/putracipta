<?php

namespace App\Service\Transaction;

use App\Entity\Transaction\PurchaseOrderDetail;
use App\Entity\Transaction\PurchaseOrderHeader;
use App\Repository\Transaction\PurchaseOrderDetailRepository;
use App\Repository\Transaction\PurchaseOrderHeaderRepository;
use Doctrine\ORM\EntityManagerInterface;

class PurchaseOrderHeaderFormService
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
        list($year, $month, $datetime, $user) = [$options['year'], $options['month'], $options['datetime'], $options['user']];

        if (empty($purchaseOrderHeader->getId())) {
            $lastPurchaseOrderHeader = $this->purchaseOrderHeaderRepository->findRecentBy($year, $month);
            $currentPurchaseOrderHeader = ($lastPurchaseOrderHeader === null) ? $purchaseOrderHeader : $lastPurchaseOrderHeader;
            $purchaseOrderHeader->setCodeNumberToNext($currentPurchaseOrderHeader->getCodeNumber(), $year, $month);

            $purchaseOrderHeader->setCreatedTransactionDateTime($datetime);
            $purchaseOrderHeader->setCreatedTransactionUser($user);
        } else {
            $purchaseOrderHeader->setModifiedTransactionDateTime($datetime);
            $purchaseOrderHeader->setModifiedTransactionUser($user);
        }
    }

    public function finalize(PurchaseOrderHeader $purchaseOrderHeader, array $options = []): void
    {
        if ($purchaseOrderHeader->getTaxMode() !== $purchaseOrderHeader::TAX_MODE_NON_TAX) {
            $purchaseOrderHeader->setTaxPercentage($options['vatPercentage']);
        } else {
            $purchaseOrderHeader->setTaxPercentage(0);
        }
        
        foreach ($purchaseOrderHeader->getPurchaseOrderDetails() as $purchaseOrderDetail) {
            $purchaseOrderDetail->setIsCanceled($purchaseOrderDetail->getSyncIsCanceled());
            $purchaseOrderDetail->setRemainingReceive($purchaseOrderDetail->getSyncRemainingReceive());
            $purchaseOrderDetail->setUnitPriceBeforeTax($purchaseOrderDetail->getSyncUnitPriceBeforeTax());
            $purchaseOrderDetail->setTotal($purchaseOrderDetail->getSyncTotal());
        }
        $supplier = $purchaseOrderHeader->getSupplier();
        $purchaseOrderHeader->setCurrency($supplier === null ? null : $supplier->getCurrency());
        $purchaseOrderHeader->setSubTotal($purchaseOrderHeader->getSyncSubTotal());
        $purchaseOrderHeader->setTaxNominal($purchaseOrderHeader->getSyncTaxNominal());
        $purchaseOrderHeader->setGrandTotal($purchaseOrderHeader->getSyncGrandTotal());
        $purchaseOrderHeader->setTotalRemainingReceive($purchaseOrderHeader->getSyncTotalRemainingReceive());
    }

    public function save(PurchaseOrderHeader $purchaseOrderHeader, array $options = []): void
    {
        $this->purchaseOrderHeaderRepository->add($purchaseOrderHeader);
        foreach ($purchaseOrderHeader->getPurchaseOrderDetails() as $purchaseOrderDetail) {
            $this->purchaseOrderDetailRepository->add($purchaseOrderDetail);
        }
        $this->entityManager->flush();
    }
}
