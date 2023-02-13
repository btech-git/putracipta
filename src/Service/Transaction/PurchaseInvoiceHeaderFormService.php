<?php

namespace App\Service\Transaction;

use App\Entity\Transaction\PurchaseInvoiceDetail;
use App\Entity\Transaction\PurchaseInvoiceHeader;
use App\Repository\Transaction\PurchaseInvoiceDetailRepository;
use App\Repository\Transaction\PurchaseInvoiceHeaderRepository;
use Doctrine\ORM\EntityManagerInterface;

class PurchaseInvoiceHeaderFormService
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
        list($year, $month, $datetime, $user) = [$options['year'], $options['month'], $options['datetime'], $options['user']];

        if (empty($purchaseInvoiceHeader->getId())) {
            $lastPurchaseInvoiceHeader = $this->purchaseInvoiceHeaderRepository->findRecentBy($year, $month);
            $currentPurchaseInvoiceHeader = ($lastPurchaseInvoiceHeader === null) ? $purchaseInvoiceHeader : $lastPurchaseInvoiceHeader;
            $purchaseInvoiceHeader->setCodeNumberToNext($currentPurchaseInvoiceHeader->getCodeNumber(), $year, $month);

            $purchaseInvoiceHeader->setCreatedTransactionDateTime($datetime);
            $purchaseInvoiceHeader->setCreatedTransactionUser($user);
        } else {
            $purchaseInvoiceHeader->setModifiedTransactionDateTime($datetime);
            $purchaseInvoiceHeader->setModifiedTransactionUser($user);
        }
    }

    public function finalize(PurchaseInvoiceHeader $purchaseInvoiceHeader, array $options = []): void
    {
        $receiveHeader = $purchaseInvoiceHeader->getReceiveHeader();
        if ($receiveHeader !== null) {
            $purchaseOrderHeader = $receiveHeader->getPurchaseOrderHeader() === null ? $receiveHeader->getPurchaseOrderPaperHeader() : $receiveHeader->getPurchaseOrderHeader();
            $purchaseInvoiceHeader->setDiscountValueType($purchaseOrderHeader === null ? PurchaseInvoiceHeader::DISCOUNT_VALUE_TYPE_PERCENTAGE : $purchaseOrderHeader->getDiscountValueType());
            $purchaseInvoiceHeader->setDiscountValue($purchaseOrderHeader === null ? '0.00' : $purchaseOrderHeader->getDiscountValue());
            $purchaseInvoiceHeader->setTaxMode($purchaseOrderHeader === null ? PurchaseInvoiceHeader::TAX_MODE_NON_TAX : $purchaseOrderHeader->getTaxMode());
        }
        
        $purchaseInvoiceHeader->setSupplier($receiveHeader === null ? null : $receiveHeader->getSupplier());
        $purchaseInvoiceHeader->setDueDate($purchaseInvoiceHeader->getSyncDueDate());
        foreach ($purchaseInvoiceHeader->getPurchaseInvoiceDetails() as $purchaseInvoiceDetail) {
            $purchaseInvoiceDetail->setIsCanceled($purchaseInvoiceDetail->getSyncIsCanceled());
        }
        
        foreach ($purchaseInvoiceHeader->getPurchaseInvoiceDetails() as $purchaseInvoiceDetail) {
            $receiveDetail = $purchaseInvoiceDetail->getReceiveDetail();
            $purchaseOrderDetail = empty($receiveDetail->getPurchaseOrderDetail()) ? $receiveDetail->getPurchaseOrderPaperDetail(): $receiveDetail->getPurchaseOrderDetail();
            $purchaseInvoiceDetail->setMaterial($receiveDetail->getMaterial());
            $purchaseInvoiceDetail->setPaper($receiveDetail->getPaper());
            $purchaseInvoiceDetail->setQuantity($receiveDetail->getReceivedQuantity());
            $purchaseInvoiceDetail->setUnitPrice($purchaseOrderDetail->getUnitPriceBeforeTax());
            $purchaseInvoiceDetail->setUnit($receiveDetail === null ? null : $receiveDetail->getUnit());
        }
        
        $purchaseInvoiceHeader->setSubTotal($purchaseInvoiceHeader->getSyncSubTotal());
        if ($purchaseInvoiceHeader->getTaxMode() !== $purchaseInvoiceHeader::TAX_MODE_NON_TAX) {
            $purchaseInvoiceHeader->setTaxPercentage($options['vatPercentage']);
        }
        $purchaseInvoiceHeader->setTaxNominal($purchaseInvoiceHeader->getSyncTaxNominal());
        $purchaseInvoiceHeader->setGrandTotal($purchaseInvoiceHeader->getSyncGrandTotal());
        
        $purchaseReturnHeader = $receiveHeader === null ? null : $receiveHeader->getPurchaseReturnHeader();
        if ($purchaseReturnHeader !== null) {
            $purchaseInvoiceHeader->setTotalReturn($purchaseReturnHeader->getGrandTotal());
        }
        
        $purchaseInvoiceHeader->setRemainingPayment($purchaseInvoiceHeader->getSyncRemainingPayment());
    }

    public function save(PurchaseInvoiceHeader $purchaseInvoiceHeader, array $options = []): void
    {
        $this->purchaseInvoiceHeaderRepository->add($purchaseInvoiceHeader);
        foreach ($purchaseInvoiceHeader->getPurchaseInvoiceDetails() as $purchaseInvoiceDetail) {
            $this->purchaseInvoiceDetailRepository->add($purchaseInvoiceDetail);
        }
        $this->entityManager->flush();
    }
}
