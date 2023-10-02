<?php

namespace App\Service\Sale;

use App\Common\Idempotent\IdempotentUtility;
use App\Entity\Sale\SaleReturnDetail;
use App\Entity\Sale\SaleReturnHeader;
use App\Entity\Support\Idempotent;
use App\Repository\Sale\SaleReturnDetailRepository;
use App\Repository\Sale\SaleReturnHeaderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class SaleReturnHeaderFormService
{
    private EntityManagerInterface $entityManager;
    private SaleReturnHeaderRepository $saleReturnHeaderRepository;
    private SaleReturnDetailRepository $saleReturnDetailRepository;

    public function __construct(RequestStack $requestStack, EntityManagerInterface $entityManager)
    {
        $this->requestStack = $requestStack;
        $this->entityManager = $entityManager;
        $this->idempotentRepository = $entityManager->getRepository(Idempotent::class);
        $this->saleReturnHeaderRepository = $entityManager->getRepository(SaleReturnHeader::class);
        $this->saleReturnDetailRepository = $entityManager->getRepository(SaleReturnDetail::class);
    }

    public function initialize(SaleReturnHeader $saleReturnHeader, array $options = []): void
    {
        list($datetime, $user) = [$options['datetime'], $options['user']];

        if (empty($saleReturnHeader->getId())) {
            $saleReturnHeader->setCreatedTransactionDateTime($datetime);
            $saleReturnHeader->setCreatedTransactionUser($user);
        } else {
            $saleReturnHeader->setModifiedTransactionDateTime($datetime);
            $saleReturnHeader->setModifiedTransactionUser($user);
        }
        
        $saleReturnHeader->setCodeNumberVersion($saleReturnHeader->getCodeNumberVersion() + 1);
    }

    public function finalize(SaleReturnHeader $saleReturnHeader, array $options = []): void
    {
        if ($saleReturnHeader->getTransactionDate() !== null && $saleReturnHeader->getId() === null) {
            $year = $saleReturnHeader->getTransactionDate()->format('y');
            $month = $saleReturnHeader->getTransactionDate()->format('m');
            $lastSaleReturnHeader = $this->saleReturnHeaderRepository->findRecentBy($year, $month);
            $currentSaleReturnHeader = ($lastSaleReturnHeader === null) ? $saleReturnHeader : $lastSaleReturnHeader;
            $saleReturnHeader->setCodeNumberToNext($currentSaleReturnHeader->getCodeNumber(), $year, $month);

        }
        $deliveryHeader = $saleReturnHeader->getDeliveryHeader();
        if ($deliveryHeader !== null) {
            $saleReturnHeader->setSaleOrderReferenceNumbers($deliveryHeader->getSaleOrderReferenceNumbers());
            $deliveryHeader->setHasReturnTransaction(true);
        }
        
        $saleReturnHeader->setCustomer($deliveryHeader === null ? null : $deliveryHeader->getCustomer());
        foreach ($saleReturnHeader->getSaleReturnDetails() as $saleReturnDetail) {
            $saleReturnDetail->setIsCanceled($saleReturnDetail->getSyncIsCanceled());
            $deliveryDetail = $saleReturnDetail->getDeliveryDetail();
            $saleInvoiceDetails = $deliveryDetail->getSaleInvoiceDetails();
            $saleOrderDetail = $deliveryDetail->getSaleOrderDetail();
            $saleOrderHeader = $saleOrderDetail->getSaleOrderHeader();
            
            $saleReturnDetail->setProduct($deliveryDetail->getProduct());
            $saleReturnDetail->setUnitPrice($saleOrderDetail->getUnitPriceBeforeTax());
            $saleReturnDetail->setUnit($deliveryDetail === null ? null : $deliveryDetail->getUnit());
            $saleOrderHeader->setHasReturnTransaction(true);
            
            if ($saleReturnHeader->isIsProductExchange()) {
                $totalReturn = $saleOrderDetail->getTotalReturn();
                if ($saleReturnDetail->isIsCanceled() === false) {
                    $totalReturn += $saleReturnDetail->getQuantity();
                }

                $saleOrderDetail->setTotalReturn($totalReturn);
                $saleOrderDetail->setRemainingDelivery($saleOrderDetail->getSyncRemainingDelivery());
            }
            
            if ($saleInvoiceDetails !== null) {
                foreach($saleInvoiceDetails as $saleInvoiceDetail) {
                    $saleInvoiceDetail->setReturnAmount($deliveryDetail->getSyncTotalReturn());
                    $saleInvoiceHeader = $saleInvoiceDetail->getSaleInvoiceHeader();
                    $saleInvoiceHeader->setTotalReturn($saleInvoiceHeader->getSyncTotalReturn());
                    $saleInvoiceHeader->setRemainingPayment($saleInvoiceHeader->getSyncRemainingPayment());
                }
            }
        }
        $saleReturnHeader->setSubTotal($saleReturnHeader->getSyncSubTotal());
        
        if ($saleReturnHeader->getTaxMode() !== $saleReturnHeader::TAX_MODE_NON_TAX) {
            $saleReturnHeader->setTaxPercentage($options['vatPercentage']);
        } else {
            $saleReturnHeader->setTaxPercentage(0);
        }
        
        $saleReturnHeader->setTaxNominal($saleReturnHeader->getSyncTaxNominal());
        $saleReturnHeader->setGrandTotal($saleReturnHeader->getSyncGrandTotal());
        
    }

    public function save(SaleReturnHeader $saleReturnHeader, array $options = []): void
    {
        $idempotent = IdempotentUtility::create(Idempotent::class, $this->requestStack->getCurrentRequest());
        $this->idempotentRepository->add($idempotent);
        $this->saleReturnHeaderRepository->add($saleReturnHeader);
        foreach ($saleReturnHeader->getSaleReturnDetails() as $saleReturnDetail) {
            $this->saleReturnDetailRepository->add($saleReturnDetail);
        }
        $this->entityManager->flush();
    }
}
