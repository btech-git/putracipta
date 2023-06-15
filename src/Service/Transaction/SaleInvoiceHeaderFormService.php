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
        list($datetime, $user) = [$options['datetime'], $options['user']];

        if (empty($saleInvoiceHeader->getId())) {
            $saleInvoiceHeader->setCreatedTransactionDateTime($datetime);
            $saleInvoiceHeader->setCreatedTransactionUser($user);
        } else {
            $saleInvoiceHeader->setModifiedTransactionDateTime($datetime);
            $saleInvoiceHeader->setModifiedTransactionUser($user);
        }
    }

    public function finalize(SaleInvoiceHeader $saleInvoiceHeader, array $options = []): void
    {
        if ($saleInvoiceHeader->getTransactionDate() !== null && $saleInvoiceHeader->getId() === null) {
            $year = $saleInvoiceHeader->getTransactionDate()->format('y');
            $month = $saleInvoiceHeader->getTransactionDate()->format('m');
            $lastSaleInvoiceHeader = $this->saleInvoiceHeaderRepository->findRecentBy($year, $month);
            $currentSaleInvoiceHeader = ($lastSaleInvoiceHeader === null) ? $saleInvoiceHeader : $lastSaleInvoiceHeader;
            $saleInvoiceHeader->setCodeNumberToNext($currentSaleInvoiceHeader->getCodeNumber(), $year, $month);

        }
        $saleInvoiceHeader->setDueDate($saleInvoiceHeader->getSyncDueDate());
        foreach ($saleInvoiceHeader->getSaleInvoiceDetails() as $saleInvoiceDetail) {
            $deliveryDetail = $saleInvoiceDetail->getDeliveryDetail();
            $deliveryHeader = $deliveryDetail->getDeliveryHeader();
            $saleInvoiceHeader->setIsUsingFscPaper($deliveryHeader->isIsUsingFscPaper());
            
            $saleOrderDetail = $deliveryDetail->getSaleOrderDetail();
            $saleInvoiceDetail->setIsCanceled($saleInvoiceDetail->getSyncIsCanceled());
            $saleInvoiceDetail->setProduct($deliveryDetail->getProduct());
            $saleInvoiceDetail->setQuantity($deliveryDetail->getQuantity());
            $saleInvoiceDetail->setUnitPrice($saleOrderDetail->getUnitPriceBeforeTax());
            $saleInvoiceDetail->setUnit($deliveryDetail === null ? null : $deliveryDetail->getUnit());
            $saleInvoiceDetail->setReturnAmount($deliveryDetail->getSyncTotalReturn());
        }
        $saleInvoiceHeader->setSubTotal($saleInvoiceHeader->getSyncSubTotal());
        $saleInvoiceHeader->setTotalReturn($saleInvoiceHeader->getSyncTotalReturn());
        if ($saleInvoiceHeader->getTaxMode() !== $saleInvoiceHeader::TAX_MODE_NON_TAX) {
            $saleInvoiceHeader->setTaxPercentage($options['vatPercentage']);
        } else {
            $saleInvoiceHeader->setTaxPercentage(0);
        }
        $saleInvoiceHeader->setTaxNominal($saleInvoiceHeader->getSyncTaxNominal());
        if ($saleInvoiceHeader->getServiceTaxMode() !== $saleInvoiceHeader::SERVICE_TAX_MODE_NON_TAX) {
            $saleInvoiceHeader->setServiceTaxPercentage($options['serviceTaxPercentage']);
        } else {
            $saleInvoiceHeader->setServiceTaxPercentage(0);
        }
        $saleInvoiceHeader->setServiceTaxNominal($saleInvoiceHeader->getSyncServiceTaxNominal());
        $saleInvoiceHeader->setGrandTotal($saleInvoiceHeader->getSyncGrandTotal());
        $saleInvoiceHeader->setRemainingPayment($saleInvoiceHeader->getSyncRemainingPayment());
        
        $saleOrderReferenceNumberList = array();
        foreach ($saleInvoiceHeader->getSaleInvoiceDetails() as $saleInvoiceDetail) {
            $deliveryDetail = $saleInvoiceDetail->getDeliveryDetail();
            $saleOrderDetail = $deliveryDetail->getSaleOrderDetail();
            $saleOrderHeader = $saleOrderDetail->getSaleOrderHeader();
            $saleOrderReferenceNumberList[] = $saleOrderHeader->getReferenceNumber();
        }
        $saleInvoiceHeader->setSaleOrderReferenceNumbers(implode(', ', $saleOrderReferenceNumberList));
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
