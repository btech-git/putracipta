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
        foreach ($purchaseOrderHeader->getPurchaseOrderDetails() as $purchaseOrderDetail) {
            $material = $purchaseOrderDetail->getMaterial();
            $purchaseOrderDetail->setLength($material->getLength());
            $purchaseOrderDetail->setWidth($material->getWidth());
            $purchaseOrderDetail->setWeight($material->getWeight());
            $purchaseOrderDetail->setWeightPrice($purchaseOrderDetail->getWeightPaperPrice());
            $purchaseOrderDetail->setUnitPrice($purchaseOrderDetail->getUnitPrice() === 0.00 ? $purchaseOrderDetail->getUnitPaperPrice() : $purchaseOrderDetail->getUnitPrice());
            $purchaseOrderDetail->setIsCanceled($purchaseOrderDetail->getSyncIsCanceled());
            $purchaseOrderDetail->setRemainingReceive($purchaseOrderDetail->getSyncRemainingReceive());
        }
        $supplier = $purchaseOrderHeader->getSupplier();
        $purchaseOrderHeader->setCurrency($supplier === null ? null : $supplier->getCurrency());
        $purchaseOrderHeader->setSubTotal($purchaseOrderHeader->getSyncSubTotal() == 0.00 ? $purchaseOrderHeader->getSyncSubTotalPaper() : $purchaseOrderHeader->getSyncSubTotal());
        $purchaseOrderHeader->setTaxPercentage($purchaseOrderHeader->getSyncTaxPercentage());
        $purchaseOrderHeader->setSubTotalAfterTaxInclusion($purchaseOrderHeader->getSyncSubTotalAfterTaxInclusion());
        $purchaseOrderHeader->setTaxNominal($purchaseOrderHeader->getSyncTaxNominal());
        $purchaseOrderHeader->setGrandTotal($purchaseOrderHeader->getSyncGrandTotal());
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
