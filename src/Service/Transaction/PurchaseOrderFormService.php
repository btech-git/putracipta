<?php

namespace App\Service\Transaction;

use App\Entity\Transaction\PurchaseOrderDetail;
use App\Entity\Transaction\PurchaseOrderHeader;
use App\Repository\Transaction\PurchaseOrderDetailRepository;
use App\Repository\Transaction\PurchaseOrderHeaderRepository;
use Doctrine\ORM\EntityManagerInterface;

class PurchaseOrderFormService
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
//        list($month, $year, $staff) = array($params['month'], $params['year'], $params['staff']);
        
        if (empty($purchaseOrderHeader->getId())) {
//            $lastPurchaseInvoiceHeader = $this->purchaseInvoiceHeaderRepository->findRecentBy($year, $month);
//            $currentPurchaseInvoiceHeader = ($lastPurchaseInvoiceHeader === null) ? $purchaseInvoiceHeader : $lastPurchaseInvoiceHeader;
//            $purchaseInvoiceHeader->setCodeNumberToNext($currentPurchaseInvoiceHeader->getCodeNumber(), $year, $month);
            
//            $purchaseInvoiceHeader->setStaffCreated($staff);
//            $purchaseInvoiceHeader->setTotalPayment(0.00);
//            $purchaseInvoiceHeader->setTotalReturn(0.00);
        }
    }

    public function finalize(PurchaseOrderHeader $purchaseOrderHeader, array $options = []): void
    {
        foreach ($purchaseOrderHeader->getPurchaseOrderDetails() as $purchaseOrderDetail) {
            $purchaseOrderDetail->setPurchaseOrderHeader($purchaseOrderHeader);
        }
        $this->sync($purchaseOrderHeader);
    }

    public function save(PurchaseOrderHeader $purchaseOrderHeader, array $options = []): void
    {
        $this->purchaseOrderHeaderRepository->add($purchaseOrderHeader);
        foreach ($purchaseOrderHeader->getPurchaseOrderDetails() as $purchaseOrderDetail) {
            $this->purchaseOrderDetailRepository->add($purchaseOrderDetail);
        }
        $this->entityManager->flush();
    }

    private function sync(PurchaseOrderHeader $purchaseOrderHeader): void
    {
        foreach ($purchaseOrderHeader->getPurchaseOrderDetails() as $purchaseOrderDetail) {
            $purchaseOrderDetail->setIsCanceled($purchaseOrderHeader->isIsCanceled());
        }
        $subTotal = '0.00';
        foreach ($purchaseOrderHeader->getPurchaseOrderDetails() as $purchaseOrderDetail) {
            if (!$purchaseOrderDetail->isIsCanceled()) {
                $subTotal += $purchaseOrderDetail->getTotal();
            }
        }
        $subTotalAfterTaxInclusion = $subTotal;
        $taxPercentage = '0.00';
        $taxNominal = '0.00';
        $discountNominal = $purchaseOrderHeader->discountValueType === PurchaseOrderHeader::DISCOUNT_VALUE_TYPE_NOMINAL ? $purchaseOrderHeader->discountValue : $subTotal * $purchaseOrderHeader->discountValue / 100;
        $subTotalAfterDiscount = $subTotal - $discountNominal;
        $taxMode = $purchaseOrderHeader->getTaxMode();
        if ($taxMode !== PurchaseOrderHeader::TAX_MODE_NON_TAX) {
            $taxPercentage = PurchaseOrderHeader::VAT_PERCENTAGE;
            if ($taxMode === PurchaseOrderHeader::TAX_MODE_TAX_INCLUSION) {
                $subTotalAfterTaxInclusion /= 1 + $taxPercentage / 100;
            }
            $taxNominal = $subTotalAfterDiscount * $taxPercentage / 100;
        }
        $purchaseOrderHeader->setSubTotal($subTotal);
        $purchaseOrderHeader->setSubTotalAfterTaxInclusion($subTotalAfterTaxInclusion);
        $purchaseOrderHeader->setTaxPercentage($taxPercentage);
        $purchaseOrderHeader->setTaxNominal($taxNominal);
        $grandTotal = $subTotalAfterDiscount + $taxNominal + $purchaseOrderHeader->getShippingFee();
        $purchaseOrderHeader->setGrandTotal($grandTotal);
    }
}
