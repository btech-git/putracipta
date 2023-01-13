<?php

namespace App\Service\Transaction;

use App\Entity\Transaction\PurchaseOrderPaperDetail;
use App\Entity\Transaction\PurchaseOrderPaperHeader;
use App\Repository\Transaction\PurchaseOrderPaperDetailRepository;
use App\Repository\Transaction\PurchaseOrderPaperHeaderRepository;
use Doctrine\ORM\EntityManagerInterface;

class PurchaseOrderPaperHeaderFormService
{
    private EntityManagerInterface $entityManager;
    private PurchaseOrderPaperHeaderRepository $purchaseOrderPaperHeaderRepository;
    private PurchaseOrderPaperDetailRepository $purchaseOrderPaperDetailRepository;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->purchaseOrderPaperHeaderRepository = $entityManager->getRepository(PurchaseOrderPaperHeader::class);
        $this->purchaseOrderPaperDetailRepository = $entityManager->getRepository(PurchaseOrderPaperDetail::class);
    }

    public function initialize(PurchaseOrderPaperHeader $purchaseOrderPaperHeader, array $options = []): void
    {
        list($year, $month, $datetime, $user) = [$options['year'], $options['month'], $options['datetime'], $options['user']];

        if (empty($purchaseOrderPaperHeader->getId())) {
            $lastPurchaseOrderPaperHeader = $this->purchaseOrderPaperHeaderRepository->findRecentBy($year, $month);
            $currentPurchaseOrderPaperHeader = ($lastPurchaseOrderPaperHeader === null) ? $purchaseOrderPaperHeader : $lastPurchaseOrderPaperHeader;
            $purchaseOrderPaperHeader->setCodeNumberToNext($currentPurchaseOrderPaperHeader->getCodeNumber(), $year, $month);

            $purchaseOrderPaperHeader->setCreatedTransactionDateTime($datetime);
            $purchaseOrderPaperHeader->setCreatedTransactionUser($user);
        } else {
            $purchaseOrderPaperHeader->setModifiedTransactionDateTime($datetime);
            $purchaseOrderPaperHeader->setModifiedTransactionUser($user);
        }
    }

    public function finalize(PurchaseOrderPaperHeader $purchaseOrderPaperHeader, array $options = []): void
    {
        foreach ($purchaseOrderPaperHeader->getPurchaseOrderPaperDetails() as $purchaseOrderPaperDetail) {
            $material = $purchaseOrderPaperDetail->getMaterial();
            $purchaseOrderPaperDetail->setLength($material->getLength());
            $purchaseOrderPaperDetail->setWidth($material->getWidth());
            $purchaseOrderPaperDetail->setWeight($material->getWeight());
            $purchaseOrderPaperDetail->setWeightPrice($purchaseOrderPaperDetail->getAssociationPrice() == 0.00 ? $purchaseOrderPaperDetail->getWeightPrice() : $purchaseOrderPaperDetail->getSyncWeightPrice());
            $purchaseOrderPaperDetail->setUnitPrice($purchaseOrderPaperDetail->getSyncUnitPrice());
            $purchaseOrderPaperDetail->setIsCanceled($purchaseOrderPaperDetail->getSyncIsCanceled());
            $purchaseOrderPaperDetail->setRemainingReceive($purchaseOrderPaperDetail->getSyncRemainingReceive());
        }
        $supplier = $purchaseOrderPaperHeader->getSupplier();
        $purchaseOrderPaperHeader->setCurrency($supplier === null ? null : $supplier->getCurrency());
        $purchaseOrderPaperHeader->setSubTotal($purchaseOrderPaperHeader->getSyncSubTotal());
        $purchaseOrderPaperHeader->setTaxPercentage($purchaseOrderPaperHeader->getSyncTaxPercentage());
        $purchaseOrderPaperHeader->setSubTotalAfterTaxInclusion($purchaseOrderPaperHeader->getSyncSubTotalAfterTaxInclusion());
        $purchaseOrderPaperHeader->setTaxNominal($purchaseOrderPaperHeader->getSyncTaxNominal());
        $purchaseOrderPaperHeader->setGrandTotal($purchaseOrderPaperHeader->getSyncGrandTotal());
        $purchaseOrderPaperHeader->setTotalRemainingReceive($purchaseOrderPaperHeader->getSyncTotalRemainingReceive());
    }

    public function save(PurchaseOrderPaperHeader $purchaseOrderPaperHeader, array $options = []): void
    {
        $this->purchaseOrderPaperHeaderRepository->add($purchaseOrderPaperHeader);
        foreach ($purchaseOrderPaperHeader->getPurchaseOrderPaperDetails() as $purchaseOrderPaperDetail) {
            $this->purchaseOrderPaperDetailRepository->add($purchaseOrderPaperDetail);
        }
        $this->entityManager->flush();
    }
}
