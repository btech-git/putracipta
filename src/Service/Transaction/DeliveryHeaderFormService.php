<?php

namespace App\Service\Transaction;

use App\Entity\Transaction\SaleOrderDetail;
use App\Entity\Transaction\SaleOrderHeader;
use App\Entity\Transaction\DeliveryDetail;
use App\Entity\Transaction\DeliveryHeader;
use App\Repository\Transaction\SaleOrderDetailRepository;
use App\Repository\Transaction\SaleOrderHeaderRepository;
use App\Repository\Transaction\DeliveryDetailRepository;
use App\Repository\Transaction\DeliveryHeaderRepository;
use Doctrine\ORM\EntityManagerInterface;

class DeliveryHeaderFormService
{
    private EntityManagerInterface $entityManager;
    private DeliveryHeaderRepository $deliveryHeaderRepository;
    private DeliveryDetailRepository $deliveryDetailRepository;
    private SaleOrderHeaderRepository $saleOrderHeaderRepository;
    private SaleOrderDetailRepository $saleOrderDetailRepository;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->deliveryHeaderRepository = $entityManager->getRepository(DeliveryHeader::class);
        $this->deliveryDetailRepository = $entityManager->getRepository(DeliveryDetail::class);
        $this->saleOrderHeaderRepository = $entityManager->getRepository(SaleOrderHeader::class);
        $this->saleOrderDetailRepository = $entityManager->getRepository(SaleOrderDetail::class);
    }

    public function initialize(DeliveryHeader $deliveryHeader, array $options = []): void
    {
        list($year, $month, $datetime, $user) = [$options['year'], $options['month'], $options['datetime'], $options['user']];

        if (empty($deliveryHeader->getId())) {
            $lastDeliveryHeader = $this->deliveryHeaderRepository->findRecentBy($year, $month);
            $currentDeliveryHeader = ($lastDeliveryHeader === null) ? $deliveryHeader : $lastDeliveryHeader;
            $deliveryHeader->setCodeNumberToNext($currentDeliveryHeader->getCodeNumber(), $year, $month);

            $deliveryHeader->setCreatedTransactionDateTime($datetime);
            $deliveryHeader->setCreatedTransactionUser($user);
        } else {
            $deliveryHeader->setModifiedTransactionDateTime($datetime);
            $deliveryHeader->setModifiedTransactionUser($user);
        }
    }

    public function finalize(DeliveryHeader $deliveryHeader, array $options = []): void
    {
//        $deliveryHeader->setCustomer($saleOrderHeader === null ? null : $saleOrderHeader->getCustomer());
        foreach ($deliveryHeader->getDeliveryDetails() as $deliveryDetail) {
            $saleOrderDetail = $deliveryDetail->getSaleOrderDetail();
            $deliveryDetail->setProduct($saleOrderDetail->getProduct());
            $deliveryDetail->setUnit($saleOrderDetail->getUnit());
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
                    $totalDelivery += $oldDeliveryDetail->getDeliveredQuantity();
                }
            }
            if ($deliveryDetail->isIsCanceled() === false) {
                $totalDelivery += $deliveryDetail->getDeliveredQuantity();
            }
            $saleOrderDetail->setTotalDelivery($totalDelivery);
            $saleOrderDetail->setRemainingDelivery($saleOrderDetail->getSyncRemainingDelivery());
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
