<?php

namespace App\Service\Transaction;

use App\Entity\Transaction\PurchaseOrderDetail;
use App\Entity\Transaction\PurchaseOrderHeader;
use App\Entity\Transaction\ReceiveDetail;
use App\Entity\Transaction\ReceiveHeader;
use App\Repository\Transaction\PurchaseOrderDetailRepository;
use App\Repository\Transaction\PurchaseOrderHeaderRepository;
use App\Repository\Transaction\ReceiveDetailRepository;
use App\Repository\Transaction\ReceiveHeaderRepository;
use Doctrine\ORM\EntityManagerInterface;

class ReceiveHeaderFormService
{
    private EntityManagerInterface $entityManager;
    private ReceiveHeaderRepository $receiveHeaderRepository;
    private ReceiveDetailRepository $receiveDetailRepository;
    private PurchaseOrderHeaderRepository $purchaseOrderHeaderRepository;
    private PurchaseOrderDetailRepository $purchaseOrderDetailRepository;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->receiveHeaderRepository = $entityManager->getRepository(ReceiveHeader::class);
        $this->receiveDetailRepository = $entityManager->getRepository(ReceiveDetail::class);
        $this->purchaseOrderHeaderRepository = $entityManager->getRepository(PurchaseOrderHeader::class);
        $this->purchaseOrderDetailRepository = $entityManager->getRepository(PurchaseOrderDetail::class);
    }

    public function initialize(ReceiveHeader $receiveHeader, array $options = []): void
    {
        list($year, $month, $datetime, $user) = [$options['year'], $options['month'], $options['datetime'], $options['user']];

        if (empty($receiveHeader->getId())) {
            $lastReceiveHeader = $this->receiveHeaderRepository->findRecentBy($year, $month);
            $currentReceiveHeader = ($lastReceiveHeader === null) ? $receiveHeader : $lastReceiveHeader;
            $receiveHeader->setCodeNumberToNext($currentReceiveHeader->getCodeNumber(), $year, $month);

            $receiveHeader->setCreatedTransactionDateTime($datetime);
            $receiveHeader->setCreatedTransactionUser($user);
        } else {
            $receiveHeader->setModifiedTransactionDateTime($datetime);
            $receiveHeader->setModifiedTransactionUser($user);
        }
    }

    public function finalize(ReceiveHeader $receiveHeader, array $options = []): void
    {
        $purchaseOrderHeader = $receiveHeader->getPurchaseOrderHeader();
        $receiveHeader->setSupplier($purchaseOrderHeader === null ? null : $purchaseOrderHeader->getSupplier());
        foreach ($receiveHeader->getReceiveDetails() as $receiveDetail) {
            $purchaseOrderDetail = $receiveDetail->getPurchaseOrderDetail();
            $receiveDetail->setMaterial($purchaseOrderDetail->getMaterial());
        }
        foreach ($receiveHeader->getReceiveDetails() as $receiveDetail) {
            $purchaseOrderDetail = $receiveDetail->getPurchaseOrderDetail();
            $receiveDetail->setIsCanceled($receiveDetail->getSyncIsCanceled());
            $receiveDetail->setUnit($purchaseOrderDetail === null ? null : $purchaseOrderDetail->getUnit());
        }
        $receiveHeader->setTotalQuantity($receiveHeader->getSyncTotalQuantity());
        foreach ($receiveHeader->getReceiveDetails() as $receiveDetail) {
            $purchaseOrderDetail = $receiveDetail->getPurchaseOrderDetail();
            $oldReceiveDetails = $this->receiveDetailRepository->findByPurchaseOrderDetail($purchaseOrderDetail);
            $totalReceive = 0;
            foreach ($oldReceiveDetails as $oldReceiveDetail) {
                if ($oldReceiveDetail->getId() !== $receiveDetail->getId()) {
                    $totalReceive += $oldReceiveDetail->getReceivedQuantity();
                }
            }
            $totalReceive += $receiveDetail->getReceivedQuantity();
            $purchaseOrderDetail->setTotalReceive($totalReceive);
            $purchaseOrderDetail->setRemainingReceive($purchaseOrderDetail->getSyncRemainingReceive());
        }
        $totalRemaining = 0;
        foreach ($receiveHeader->getReceiveDetails() as $receiveDetail) {
            $purchaseOrderDetail = $receiveDetail->getPurchaseOrderDetail();
            $totalRemaining += $purchaseOrderDetail->getRemainingReceive();
        }
        if ($purchaseOrderHeader !== null) {
            if ($totalRemaining > 0) {
                $purchaseOrderHeader->setTransactionStatus(PurchaseOrderHeader::TRANSACTION_STATUS_PARTIAL_RECEIVE);
            } else {
                $purchaseOrderHeader->setTransactionStatus(PurchaseOrderHeader::TRANSACTION_STATUS_FULL_RECEIVE);
            }
            $purchaseOrderHeader->setTotalRemainingReceive($purchaseOrderHeader->getSyncTotalRemainingReceive());
        }
    }

    public function save(ReceiveHeader $receiveHeader, array $options = []): void
    {
        $purchaseOrderHeader = $receiveHeader->getPurchaseOrderHeader();
        $this->receiveHeaderRepository->add($receiveHeader);
        $this->purchaseOrderHeaderRepository->add($purchaseOrderHeader);
        foreach ($receiveHeader->getReceiveDetails() as $receiveDetail) {
            $purchaseOrderDetail = $receiveDetail->getPurchaseOrderDetail();
            $this->receiveDetailRepository->add($receiveDetail);
            $this->purchaseOrderDetailRepository->add($purchaseOrderDetail);
        }
        $this->entityManager->flush();
    }
}
