<?php

namespace App\Service\Sale;

use App\Entity\Sale\SaleOrderHeader;
use App\Entity\Sale\DeliveryDetail;
use App\Entity\Sale\DeliveryHeader;
use App\Repository\Sale\DeliveryDetailRepository;
use App\Repository\Sale\DeliveryHeaderRepository;
use Doctrine\ORM\EntityManagerInterface;

class DeliveryHeaderFormService
{
    private EntityManagerInterface $entityManager;
    private DeliveryHeaderRepository $deliveryHeaderRepository;
    private DeliveryDetailRepository $deliveryDetailRepository;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->deliveryHeaderRepository = $entityManager->getRepository(DeliveryHeader::class);
        $this->deliveryDetailRepository = $entityManager->getRepository(DeliveryDetail::class);
    }

    public function initialize(DeliveryHeader $deliveryHeader, array $options = []): void
    {
        list($datetime, $user) = [$options['datetime'], $options['user']];

        if (empty($deliveryHeader->getId())) {
            $deliveryHeader->setCreatedTransactionDateTime($datetime);
            $deliveryHeader->setCreatedTransactionUser($user);
        } else {
            $deliveryHeader->setModifiedTransactionDateTime($datetime);
            $deliveryHeader->setModifiedTransactionUser($user);
        }
        
        $deliveryHeader->setCodeNumberVersion($deliveryHeader->getCodeNumberVersion() + 1);
    }

    public function finalize(DeliveryHeader $deliveryHeader, array $options = []): void
    {
        if ($deliveryHeader->getTransactionDate() !== null && $deliveryHeader->getId() === null) {
            $year = $deliveryHeader->getTransactionDate()->format('y');
            $month = $deliveryHeader->getTransactionDate()->format('m');
            $lastDeliveryHeader = $this->deliveryHeaderRepository->findRecentBy($year, $month);
            $currentDeliveryHeader = ($lastDeliveryHeader === null) ? $deliveryHeader : $lastDeliveryHeader;
            $deliveryHeader->setCodeNumberToNext($currentDeliveryHeader->getCodeNumber(), $year, $month);
        }
        foreach ($deliveryHeader->getDeliveryDetails() as $deliveryDetail) {
            $saleOrderDetail = $deliveryDetail->getSaleOrderDetail();
            $deliveryDetail->setProduct($saleOrderDetail->getProduct());
            $deliveryDetail->setUnit($saleOrderDetail->getUnit());
            $deliveryHeader->setDeliveryAddressOrdinal($saleOrderDetail->getSaleOrderHeader()->getDeliveryAddressOrdinal());
        }
        foreach ($deliveryHeader->getDeliveryDetails() as $deliveryDetail) {
            $deliveryDetail->setIsCanceled($deliveryDetail->getSyncIsCanceled());
        }
        $deliveryHeader->setTotalQuantity($deliveryHeader->getSyncTotalQuantity());
        foreach ($deliveryHeader->getDeliveryDetails() as $deliveryDetail) {
            $saleOrderDetail = $deliveryDetail->getSaleOrderDetail();
            $oldDeliveryDetails = $this->deliveryDetailRepository->findBySaleOrderDetail($saleOrderDetail);
            $totalDelivery = 0;
            foreach ($oldDeliveryDetails as $oldDeliveryDetail) {
                if ($oldDeliveryDetail->getId() !== $deliveryDetail->getId() && $oldDeliveryDetail->isIsCanceled() === false) {
                    $totalDelivery += $oldDeliveryDetail->getQuantity();
                }
            }
            if ($deliveryDetail->isIsCanceled() === false) {
                $totalDelivery += $deliveryDetail->getQuantity();
            }
            $saleOrderDetail->setTotalDelivery($totalDelivery);
            $saleOrderDetail->setRemainingDelivery($saleOrderDetail->getSyncRemainingDelivery());
            
//            if ($deliveryHeader->getId() === null) {
                $deliveryDetail->setDeliveredQuantity($saleOrderDetail->getTotalDelivery());
                $deliveryDetail->setRemainingQuantity($saleOrderDetail->getRemainingDelivery());
//            }
        }
        
        foreach ($deliveryHeader->getDeliveryDetails() as $deliveryDetail) {
            $saleOrderDetail = $deliveryDetail->getSaleOrderDetail();
            $saleOrderHeader = $saleOrderDetail->getSaleOrderHeader();
            $deliveryHeader->setIsUsingFscPaper($saleOrderHeader->isIsUsingFscPaper());
            
            $totalRemainingDelivery = 0;
            foreach ($saleOrderHeader->getSaleOrderDetails() as $saleOrderDetail) {
                $totalRemainingDelivery += $saleOrderDetail->getRemainingDelivery();
            }
            $saleOrderHeader->setTotalRemainingDelivery($totalRemainingDelivery);
            
            if ($totalRemainingDelivery > 0) {
                $saleOrderHeader->setTransactionStatus(SaleOrderHeader::TRANSACTION_STATUS_PARTIAL_DELIVERY);
            } else {
                $saleOrderHeader->setTransactionStatus(SaleOrderHeader::TRANSACTION_STATUS_FULL_DELIVERY);
            }
        }
        $saleOrderReferenceNumberList = array();
        foreach ($deliveryHeader->getDeliveryDetails() as $deliveryDetail) {
            $saleOrderDetail = $deliveryDetail->getSaleOrderDetail();
            $saleOrderHeader = $saleOrderDetail->getSaleOrderHeader();
            $saleOrderReferenceNumberList[] = $saleOrderHeader->getReferenceNumber();
        }
        $deliveryHeader->setSaleOrderReferenceNumbers(implode(', ', $saleOrderReferenceNumberList));
    }

    public function save(DeliveryHeader $deliveryHeader, array $options = []): void
    {
        $this->deliveryHeaderRepository->add($deliveryHeader);
        foreach ($deliveryHeader->getDeliveryDetails() as $deliveryDetail) {
            $this->deliveryDetailRepository->add($deliveryDetail);
        }
        $this->entityManager->flush();
    }
}