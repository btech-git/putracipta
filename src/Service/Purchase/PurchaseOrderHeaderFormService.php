<?php

namespace App\Service\Purchase;

use App\Common\Idempotent\IdempotentUtility;
use App\Entity\Purchase\PurchaseOrderDetail;
use App\Entity\Purchase\PurchaseOrderHeader;
use App\Entity\Purchase\PurchaseRequestDetail;
use App\Entity\Support\Idempotent;
use App\Repository\Purchase\PurchaseOrderDetailRepository;
use App\Repository\Purchase\PurchaseOrderHeaderRepository;
use App\Sync\Purchase\PurchaseOrderHeaderFormSync;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class PurchaseOrderHeaderFormService
{
    private PurchaseOrderHeaderFormSync $formSync;
    private EntityManagerInterface $entityManager;
    private PurchaseOrderHeaderRepository $purchaseOrderHeaderRepository;
    private PurchaseOrderDetailRepository $purchaseOrderDetailRepository;

    public function __construct(RequestStack $requestStack, PurchaseOrderHeaderFormSync $formSync, EntityManagerInterface $entityManager)
    {
        $this->formSync = $formSync;
        $this->requestStack = $requestStack;
        $this->entityManager = $entityManager;
        $this->idempotentRepository = $entityManager->getRepository(Idempotent::class);
        $this->purchaseOrderHeaderRepository = $entityManager->getRepository(PurchaseOrderHeader::class);
        $this->purchaseOrderDetailRepository = $entityManager->getRepository(PurchaseOrderDetail::class);
    }

    public function initialize(PurchaseOrderHeader $purchaseOrderHeader, array $options = []): void
    {
        list($datetime, $user) = [$options['datetime'], $options['user']];

        if (empty($purchaseOrderHeader->getId())) {
            $purchaseOrderHeader->setCreatedTransactionDateTime($datetime);
            $purchaseOrderHeader->setCreatedTransactionUser($user);
        } else {
            $purchaseOrderHeader->setModifiedTransactionDateTime($datetime);
            $purchaseOrderHeader->setModifiedTransactionUser($user);
        }
        
        $purchaseOrderHeader->setCodeNumberVersion($purchaseOrderHeader->getCodeNumberVersion() + 1);
    }

    public function finalize(PurchaseOrderHeader $purchaseOrderHeader, array $options = []): void
    {
        if ($purchaseOrderHeader->getTransactionDate() !== null && $purchaseOrderHeader->getId() === null) {
            $year = $purchaseOrderHeader->getTransactionDate()->format('y');
            $month = $purchaseOrderHeader->getTransactionDate()->format('m');
            $lastPurchaseOrderHeader = $this->purchaseOrderHeaderRepository->findRecentBy($year, $month);
            $currentPurchaseOrderHeader = ($lastPurchaseOrderHeader === null) ? $purchaseOrderHeader : $lastPurchaseOrderHeader;
            $purchaseOrderHeader->setCodeNumberToNext($currentPurchaseOrderHeader->getCodeNumber(), $year, $month);

        }
        
        if ($purchaseOrderHeader->getTaxMode() !== $purchaseOrderHeader::TAX_MODE_NON_TAX) {
            $purchaseOrderHeader->setTaxPercentage($options['vatPercentage']);
        } else {
            $purchaseOrderHeader->setTaxPercentage(0);
        }
        
        foreach ($purchaseOrderHeader->getPurchaseOrderDetails() as $purchaseOrderDetail) {
            $purchaseOrderDetail->setIsCanceled($purchaseOrderDetail->getSyncIsCanceled());
            $purchaseOrderDetail->setIsTransactionClosed($purchaseOrderDetail->getSyncIsTransactionClosed());
            if ($purchaseOrderDetail->isIsCanceled()) {
                $purchaseOrderDetail->setPurchaseRequestDetail(null);
            }
            
            $purchaseOrderDetail->setRemainingReceive($purchaseOrderDetail->getSyncRemainingReceive());
            $purchaseOrderDetail->setUnitPriceBeforeTax($purchaseOrderDetail->getSyncUnitPriceBeforeTax());
            $purchaseOrderDetail->setTotal($purchaseOrderDetail->getSyncTotal());
            
            if ($purchaseOrderDetail->getRemainingReceive() === 0) {
                $purchaseOrderDetail->setIsTransactionClosed(true);
            }
            
            if ($purchaseOrderDetail->isIsTransactionClosed() === true or $purchaseOrderDetail->isIsCanceled() === true) {
                $purchaseOrderDetail->setRemainingReceive(0);
            }
            
            $purchaseRequestDetail = $purchaseOrderDetail->getPurchaseRequestDetail();
            if ($purchaseRequestDetail !== null && $purchaseOrderHeader->getId() === null) {
                $purchaseRequestDetail->setTransactionStatus(PurchaseRequestDetail::TRANSACTION_STATUS_PURCHASE);
            }
            
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
        $idempotent = IdempotentUtility::create(Idempotent::class, $this->requestStack->getCurrentRequest());
        $this->idempotentRepository->add($idempotent);
        $this->purchaseOrderHeaderRepository->add($purchaseOrderHeader);
        foreach ($purchaseOrderHeader->getPurchaseOrderDetails() as $purchaseOrderDetail) {
            $this->purchaseOrderDetailRepository->add($purchaseOrderDetail);
        }
        $this->entityManager->flush();
    }

    public function createSyncView(): array
    {
        return $this->formSync->getView();
    }

    public function copyFrom(PurchaseOrderHeader $sourcePurchaseOrderHeader): PurchaseOrderHeader
    {
        $purchaseOrderHeader = new PurchaseOrderHeader();
        $purchaseOrderHeader->setSupplier($sourcePurchaseOrderHeader->getSupplier());
        foreach ($sourcePurchaseOrderHeader->getPurchaseOrderDetails() as $sourcePurchaseOrderDetail) {
            $purchaseOrderDetail = new PurchaseOrderDetail();
            $purchaseOrderDetail->setMaterial($sourcePurchaseOrderDetail->getMaterial());
            $purchaseOrderDetail->setQuantity($sourcePurchaseOrderDetail->getQuantity());
            $purchaseOrderDetail->setUnit($sourcePurchaseOrderDetail->getUnit());
            $purchaseOrderDetail->setUnitPrice($sourcePurchaseOrderDetail->getUnitPrice());
            $purchaseOrderHeader->addPurchaseOrderDetail($purchaseOrderDetail);
        }
        return $purchaseOrderHeader;
    }
}
