<?php

namespace App\Service\Transaction;

use App\Entity\Transaction\PurchaseReturnDetail;
use App\Entity\Transaction\PurchaseReturnHeader;
use App\Repository\Transaction\PurchaseReturnDetailRepository;
use App\Repository\Transaction\PurchaseReturnHeaderRepository;
use Doctrine\ORM\EntityManagerInterface;

class PurchaseReturnHeaderFormService
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
        list($year, $month, $datetime, $user) = [$options['year'], $options['month'], $options['datetime'], $options['user']];

        if (empty($purchaseReturnHeader->getId())) {
            $lastPurchaseReturnHeader = $this->purchaseReturnHeaderRepository->findRecentBy($year, $month);
            $currentPurchaseReturnHeader = ($lastPurchaseReturnHeader === null) ? $purchaseReturnHeader : $lastPurchaseReturnHeader;
            $purchaseReturnHeader->setCodeNumberToNext($currentPurchaseReturnHeader->getCodeNumber(), $year, $month);

            $purchaseReturnHeader->setCreatedTransactionDateTime($datetime);
            $purchaseReturnHeader->setCreatedTransactionUser($user);
        } else {
            $purchaseReturnHeader->setModifiedTransactionDateTime($datetime);
            $purchaseReturnHeader->setModifiedTransactionUser($user);
        }
    }

    public function finalize(PurchaseReturnHeader $purchaseReturnHeader, array $options = []): void
    {
        $receiveHeader = $purchaseReturnHeader->getReceiveHeader();
        $purchaseOrderHeader = $receiveHeader === null ? null : $receiveHeader->getPurchaseOrderHeader();
        $purchaseReturnHeader->setSupplier($receiveHeader === null ? null : $receiveHeader->getSupplier());
        $purchaseReturnHeader->setTaxMode($purchaseOrderHeader === null ? PurchaseReturnHeader::TAX_MODE_NON_TAX : $purchaseOrderHeader->getTaxMode());
        foreach ($purchaseReturnHeader->getPurchaseReturnDetails() as $purchaseReturnDetail) {
            $purchaseReturnDetail->setIsCanceled($purchaseReturnDetail->getSyncIsCanceled());
            $receiveDetail = $purchaseReturnDetail->getReceiveDetail();
            $purchaseOrderDetail = empty($receiveDetail->getPurchaseOrderDetail()) ? $receiveDetail->getPurchaseOrderPaperDetail(): $receiveDetail->getPurchaseOrderDetail();
            $purchaseReturnDetail->setMaterial($receiveDetail->getMaterial());
            $purchaseReturnDetail->setPaper($receiveDetail->getPaper());
            $purchaseReturnDetail->setUnitPrice($purchaseOrderDetail->getUnitPriceBeforeTax());
            $purchaseReturnDetail->setUnit($receiveDetail === null ? null : $receiveDetail->getUnit());
        }
        $purchaseReturnHeader->setSubTotal($purchaseReturnHeader->getSyncSubTotal());
        if ($purchaseReturnHeader->getTaxMode() !== $purchaseReturnHeader::TAX_MODE_NON_TAX) {
            $purchaseReturnHeader->setTaxPercentage($options['vatPercentage']);
        }
        $purchaseReturnHeader->setTaxNominal($purchaseReturnHeader->getSyncTaxNominal());
        $purchaseReturnHeader->setGrandTotal($purchaseReturnHeader->getSyncGrandTotal());
    }

    public function save(PurchaseReturnHeader $purchaseReturnHeader, array $options = []): void
    {
        $this->purchaseReturnHeaderRepository->add($purchaseReturnHeader);
        foreach ($purchaseReturnHeader->getPurchaseReturnDetails() as $purchaseReturnDetail) {
            $this->purchaseReturnDetailRepository->add($purchaseReturnDetail);
        }
        $this->entityManager->flush();
    }
}
