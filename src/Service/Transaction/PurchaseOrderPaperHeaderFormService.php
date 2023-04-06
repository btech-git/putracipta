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
        list($datetime, $user) = [$options['datetime'], $options['user']];

        if (empty($purchaseOrderPaperHeader->getId())) {
            $purchaseOrderPaperHeader->setCreatedTransactionDateTime($datetime);
            $purchaseOrderPaperHeader->setCreatedTransactionUser($user);
        } else {
            $purchaseOrderPaperHeader->setModifiedTransactionDateTime($datetime);
            $purchaseOrderPaperHeader->setModifiedTransactionUser($user);
        }
    }

    public function finalize(PurchaseOrderPaperHeader $purchaseOrderPaperHeader, array $options = []): void
    {
        if ($purchaseOrderPaperHeader->getTransactionDate() !== null) {
            $year = $purchaseOrderPaperHeader->getTransactionDate()->format('y');
            $month = $purchaseOrderPaperHeader->getTransactionDate()->format('m');
            $lastPurchaseOrderPaperHeader = $this->purchaseOrderPaperHeaderRepository->findRecentBy($year, $month);
            $currentPurchaseOrderPaperHeader = ($lastPurchaseOrderPaperHeader === null) ? $purchaseOrderPaperHeader : $lastPurchaseOrderPaperHeader;
            $purchaseOrderPaperHeader->setCodeNumberToNext($currentPurchaseOrderPaperHeader->getCodeNumber(), $year, $month);

        }
        if ($purchaseOrderPaperHeader->getTaxMode() !== $purchaseOrderPaperHeader::TAX_MODE_NON_TAX) {
            $purchaseOrderPaperHeader->setTaxPercentage($options['vatPercentage']);
        } else {
            $purchaseOrderPaperHeader->setTaxPercentage(0);
        }
        
        foreach ($purchaseOrderPaperHeader->getPurchaseOrderPaperDetails() as $purchaseOrderPaperDetail) {
            $paper = $purchaseOrderPaperDetail->getPaper();
            $purchaseOrderPaperDetail->setIsCanceled($purchaseOrderPaperDetail->getSyncIsCanceled());
            $purchaseOrderPaperDetail->setLength($paper->getLength());
            $purchaseOrderPaperDetail->setWidth($paper->getWidth());
            $purchaseOrderPaperDetail->setWeight($paper->getWeight());
            if ($purchaseOrderPaperDetail->getApkiValue() > '0.00' || $purchaseOrderPaperDetail->getAssociationPrice() > '0.00') {
                $purchaseOrderPaperDetail->setWeightPrice($purchaseOrderPaperDetail->getSyncWeightPrice());
            }
            if ($purchaseOrderPaperDetail->getApkiValue() > '0.00' || $purchaseOrderPaperDetail->getAssociationPrice() > '0.00' || $purchaseOrderPaperDetail->getWeightPrice() > '0.00') {
                $purchaseOrderPaperDetail->setUnitPrice($purchaseOrderPaperDetail->getSyncUnitPrice());
            }
            $purchaseOrderPaperDetail->setRemainingReceive($purchaseOrderPaperDetail->getSyncRemainingReceive());
            $purchaseOrderPaperDetail->setUnitPriceBeforeTax($purchaseOrderPaperDetail->getSyncUnitPriceBeforeTax());
            $purchaseOrderPaperDetail->setTotal($purchaseOrderPaperDetail->getSyncTotal());
            
            if ($purchaseOrderPaperDetail->getRemainingReceive() === 0) {
                $purchaseOrderPaperDetail->setIsTransactionClosed(true);
            }
            
            if ($purchaseOrderPaperDetail->isIsTransactionClosed() === true or $purchaseOrderPaperDetail->isIsCanceled() === true) {
                $purchaseOrderPaperDetail->setRemainingReceive(0);
            }
        }
        $supplier = $purchaseOrderPaperHeader->getSupplier();
        $purchaseOrderPaperHeader->setCurrency($supplier === null ? null : $supplier->getCurrency());
        $purchaseOrderPaperHeader->setSubTotal($purchaseOrderPaperHeader->getSyncSubTotal());
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
