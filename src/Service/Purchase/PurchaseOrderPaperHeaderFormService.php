<?php

namespace App\Service\Purchase;

use App\Common\Idempotent\IdempotentUtility;
use App\Entity\Purchase\PurchaseOrderPaperDetail;
use App\Entity\Purchase\PurchaseOrderPaperHeader;
use App\Entity\Purchase\PurchaseRequestPaperDetail;
use App\Entity\Support\Idempotent;
use App\Repository\Purchase\PurchaseOrderPaperDetailRepository;
use App\Repository\Purchase\PurchaseOrderPaperHeaderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class PurchaseOrderPaperHeaderFormService
{
    private EntityManagerInterface $entityManager;
    private PurchaseOrderPaperHeaderRepository $purchaseOrderPaperHeaderRepository;
    private PurchaseOrderPaperDetailRepository $purchaseOrderPaperDetailRepository;

    public function __construct(RequestStack $requestStack, EntityManagerInterface $entityManager)
    {
        $this->requestStack = $requestStack;
        $this->entityManager = $entityManager;
        $this->idempotentRepository = $entityManager->getRepository(Idempotent::class);
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
        
        $purchaseOrderPaperHeader->setCodeNumberVersion($purchaseOrderPaperHeader->getCodeNumberVersion() + 1);
    }

    public function finalize(PurchaseOrderPaperHeader $purchaseOrderPaperHeader, array $options = []): void
    {
        if ($purchaseOrderPaperHeader->getTransactionDate() !== null && $purchaseOrderPaperHeader->getId() === null) {
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
            
            $purchaseRequestPaperDetail = $purchaseOrderPaperDetail->getPurchaseRequestPaperDetail();
            if ($purchaseRequestPaperDetail !== null) {
                $purchaseRequestPaperDetail->setTransactionStatus(PurchaseRequestPaperDetail::TRANSACTION_STATUS_PURCHASE);
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
        $idempotent = IdempotentUtility::create(Idempotent::class, $this->requestStack->getCurrentRequest());
        $this->idempotentRepository->add($idempotent);
        $this->purchaseOrderPaperHeaderRepository->add($purchaseOrderPaperHeader);
        foreach ($purchaseOrderPaperHeader->getPurchaseOrderPaperDetails() as $purchaseOrderPaperDetail) {
            $this->purchaseOrderPaperDetailRepository->add($purchaseOrderPaperDetail);
        }
        $this->entityManager->flush();
    }

    public function copyFrom(PurchaseOrderPaperHeader $sourcePurchaseOrderPaperHeader): PurchaseOrderPaperHeader
    {
        $purchaseOrderPaperHeader = new PurchaseOrderPaperHeader();
        $purchaseOrderPaperHeader->setSupplier($sourcePurchaseOrderPaperHeader->getSupplier());
        foreach ($sourcePurchaseOrderPaperHeader->getPurchaseOrderPaperDetails() as $sourcePurchaseOrderPaperDetail) {
            $purchaseOrderPaperDetail = new PurchaseOrderPaperDetail();
            $purchaseOrderPaperDetail->setPaper($sourcePurchaseOrderPaperDetail->getPaper());
            $purchaseOrderPaperDetail->setLength($sourcePurchaseOrderPaperDetail->getLength());
            $purchaseOrderPaperDetail->setWidth($sourcePurchaseOrderPaperDetail->getWidth());
            $purchaseOrderPaperDetail->setWeight($sourcePurchaseOrderPaperDetail->getWeight());
            $purchaseOrderPaperDetail->setQuantity($sourcePurchaseOrderPaperDetail->getQuantity());
            $purchaseOrderPaperDetail->setUnit($sourcePurchaseOrderPaperDetail->getUnit());
            $purchaseOrderPaperDetail->setApkiValue($sourcePurchaseOrderPaperDetail->getApkiValue());
            $purchaseOrderPaperDetail->setAssociationPrice($sourcePurchaseOrderPaperDetail->getAssociationPrice());
            $purchaseOrderPaperDetail->setWeightPrice($sourcePurchaseOrderPaperDetail->getWeightPrice());
            $purchaseOrderPaperDetail->setUnitPrice($sourcePurchaseOrderPaperDetail->getUnitPrice());
            $purchaseOrderPaperHeader->addPurchaseOrderPaperDetail($purchaseOrderPaperDetail);
        }
        return $purchaseOrderPaperHeader;
    }
}
