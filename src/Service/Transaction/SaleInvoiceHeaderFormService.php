<?php

namespace App\Service\Transaction;

use App\Entity\Transaction\SaleInvoiceDetail;
use App\Entity\Transaction\SaleInvoiceHeader;
use App\Repository\Transaction\SaleInvoiceDetailRepository;
use App\Repository\Transaction\SaleInvoiceHeaderRepository;
use Doctrine\ORM\EntityManagerInterface;

class SaleInvoiceHeaderFormService
{
    private EntityManagerInterface $entityManager;
    private SaleInvoiceHeaderRepository $saleInvoiceHeaderRepository;
    private SaleInvoiceDetailRepository $saleInvoiceDetailRepository;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->saleInvoiceHeaderRepository = $entityManager->getRepository(SaleInvoiceHeader::class);
        $this->saleInvoiceDetailRepository = $entityManager->getRepository(SaleInvoiceDetail::class);
    }

    public function initialize(SaleInvoiceHeader $saleInvoiceHeader, array $options = []): void
    {
        list($year, $month, $datetime, $user) = [$options['year'], $options['month'], $options['datetime'], $options['user']];

        if (empty($saleInvoiceHeader->getId())) {
            $lastSaleInvoiceHeader = $this->saleInvoiceHeaderRepository->findRecentBy($year, $month);
            $currentSaleInvoiceHeader = ($lastSaleInvoiceHeader === null) ? $saleInvoiceHeader : $lastSaleInvoiceHeader;
            $saleInvoiceHeader->setCodeNumberToNext($currentSaleInvoiceHeader->getCodeNumber(), $year, $month);

            $saleInvoiceHeader->setCreatedTransactionDateTime($datetime);
            $saleInvoiceHeader->setCreatedTransactionUser($user);
        } else {
            $saleInvoiceHeader->setModifiedTransactionDateTime($datetime);
            $saleInvoiceHeader->setModifiedTransactionUser($user);
        }
    }

    public function finalize(SaleInvoiceHeader $saleInvoiceHeader, array $options = []): void
    {
        $deliveryHeader = $saleInvoiceHeader->getDeliveryHeader();
        $saleOrderHeader = $deliveryHeader === null ? null : $deliveryHeader->getSaleOrderHeader();
        $saleInvoiceHeader->setCustomer($deliveryHeader === null ? null : $deliveryHeader->getCustomer());
        $saleInvoiceHeader->setDiscountValueType($saleOrderHeader === null ? SaleInvoiceHeader::DISCOUNT_VALUE_TYPE_PERCENTAGE : $saleOrderHeader->getDiscountValueType());
        $saleInvoiceHeader->setDiscountValue($saleOrderHeader === null ? '0.00' : $saleOrderHeader->getDiscountValue());
        $saleInvoiceHeader->setTaxMode($saleOrderHeader === null ? SaleInvoiceHeader::TAX_MODE_NON_TAX : $saleOrderHeader->getTaxMode());
        $saleInvoiceHeader->setDueDate($saleInvoiceHeader->getSyncDueDate());
        foreach ($saleInvoiceHeader->getSaleInvoiceDetails() as $saleInvoiceDetail) {
            $deliveryDetail = $saleInvoiceDetail->getDeliveryDetail();
            $saleOrderDetail = $deliveryDetail->getSaleOrderDetail();
            $saleInvoiceDetail->setProduct($deliveryDetail->getProduct());
            $saleInvoiceDetail->setQuantity($deliveryDetail->getDeliveredQuantity());
            $saleInvoiceDetail->setUnitPrice($saleOrderDetail->getUnitPrice());
            $saleInvoiceDetail->setUnit($deliveryDetail === null ? null : $deliveryDetail->getUnit());
        }
        foreach ($saleInvoiceHeader->getSaleInvoiceDetails() as $saleInvoiceDetail) {
            $saleInvoiceDetail->setIsCanceled($saleInvoiceDetail->getSyncIsCanceled());
        }
        $saleInvoiceHeader->setSubTotal($saleInvoiceHeader->getSyncSubTotal());
        $saleInvoiceHeader->setTaxPercentage($saleInvoiceHeader->getSyncTaxPercentage());
        $saleInvoiceHeader->setSubTotalAfterTaxInclusion($saleInvoiceHeader->getSyncSubTotalAfterTaxInclusion());
        $saleInvoiceHeader->setTaxNominal($saleInvoiceHeader->getSyncTaxNominal());
        $saleInvoiceHeader->setGrandTotal($saleInvoiceHeader->getSyncGrandTotal());
        $saleInvoiceHeader->setRemainingPayment($saleInvoiceHeader->getSyncRemainingPayment());
    }

    public function save(SaleInvoiceHeader $saleInvoiceHeader, array $options = []): void
    {
        $this->saleInvoiceHeaderRepository->add($saleInvoiceHeader);
        foreach ($saleInvoiceHeader->getSaleInvoiceDetails() as $saleInvoiceDetail) {
            $this->saleInvoiceDetailRepository->add($saleInvoiceDetail);
        }
        $this->entityManager->flush();
    }
}
