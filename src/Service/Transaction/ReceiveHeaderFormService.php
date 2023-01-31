<?php

namespace App\Service\Transaction;

use App\Entity\Transaction\PurchaseOrderDetail;
use App\Entity\Transaction\PurchaseOrderHeader;
use App\Entity\Transaction\PurchaseOrderPaperDetail;
use App\Entity\Transaction\PurchaseOrderPaperHeader;
use App\Entity\Transaction\ReceiveDetail;
use App\Entity\Transaction\ReceiveHeader;
use App\Repository\Transaction\PurchaseOrderDetailRepository;
use App\Repository\Transaction\PurchaseOrderHeaderRepository;
use App\Repository\Transaction\PurchaseOrderPaperDetailRepository;
use App\Repository\Transaction\PurchaseOrderPaperHeaderRepository;
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
    private PurchaseOrderPaperHeaderRepository $purchaseOrderPaperHeaderRepository;
    private PurchaseOrderPaperDetailRepository $purchaseOrderPaperDetailRepository;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->receiveHeaderRepository = $entityManager->getRepository(ReceiveHeader::class);
        $this->receiveDetailRepository = $entityManager->getRepository(ReceiveDetail::class);
        $this->purchaseOrderHeaderRepository = $entityManager->getRepository(PurchaseOrderHeader::class);
        $this->purchaseOrderDetailRepository = $entityManager->getRepository(PurchaseOrderDetail::class);
        $this->purchaseOrderPaperHeaderRepository = $entityManager->getRepository(PurchaseOrderPaperHeader::class);
        $this->purchaseOrderPaperDetailRepository = $entityManager->getRepository(PurchaseOrderPaperDetail::class);
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
        $purchaseOrderHeaderForMaterialOrPaper = $this->getPurchaseOrderHeaderForMaterialOrPaper($receiveHeader);
        $receiveHeader->setSupplier($purchaseOrderHeaderForMaterialOrPaper === null ? null : $purchaseOrderHeaderForMaterialOrPaper->getSupplier());
        foreach ($receiveHeader->getReceiveDetails() as $receiveDetail) {
            $purchaseOrderDetailForMaterialOrPaper = $this->getPurchaseOrderDetailForMaterialOrPaper($receiveDetail);
            $this->setMaterialOrPaper($receiveDetail, $purchaseOrderDetailForMaterialOrPaper);
        }
        foreach ($receiveHeader->getReceiveDetails() as $receiveDetail) {
            $purchaseOrderDetailForMaterialOrPaper = $this->getPurchaseOrderDetailForMaterialOrPaper($receiveDetail);
            $receiveDetail->setIsCanceled($receiveDetail->getSyncIsCanceled());
            $receiveDetail->setUnit($purchaseOrderDetailForMaterialOrPaper === null ? null : $purchaseOrderDetailForMaterialOrPaper->getUnit());
        }
        $receiveHeader->setTotalQuantity($receiveHeader->getSyncTotalQuantity());
        foreach ($receiveHeader->getReceiveDetails() as $receiveDetail) {
            $purchaseOrderDetailForMaterialOrPaper = $this->getPurchaseOrderDetailForMaterialOrPaper($receiveDetail);
            $oldReceiveDetails = $this->receiveDetailRepository->findByPurchaseOrderDetail($purchaseOrderDetailForMaterialOrPaper);
            $totalReceive = 0;
            foreach ($oldReceiveDetails as $oldReceiveDetail) {
                if ($oldReceiveDetail->getId() !== $receiveDetail->getId()) {
                    $totalReceive += $oldReceiveDetail->getReceivedQuantity();
                }
            }
            $totalReceive += $receiveDetail->getReceivedQuantity();
            $purchaseOrderDetailForMaterialOrPaper->setTotalReceive($totalReceive);
            $purchaseOrderDetailForMaterialOrPaper->setRemainingReceive($purchaseOrderDetailForMaterialOrPaper->getSyncRemainingReceive());
        }
        $totalRemaining = 0;
        foreach ($receiveHeader->getReceiveDetails() as $receiveDetail) {
            $purchaseOrderDetailForMaterialOrPaper = $this->getPurchaseOrderDetailForMaterialOrPaper($receiveDetail);
            $totalRemaining += $purchaseOrderDetailForMaterialOrPaper->getRemainingReceive();
        }
        if ($purchaseOrderHeaderForMaterialOrPaper !== null) {
            if ($totalRemaining > 0) {
                $purchaseOrderHeaderForMaterialOrPaper->setTransactionStatus(PurchaseOrderHeader::TRANSACTION_STATUS_PARTIAL_RECEIVE);
            } else {
                $purchaseOrderHeaderForMaterialOrPaper->setTransactionStatus(PurchaseOrderHeader::TRANSACTION_STATUS_FULL_RECEIVE);
            }
            $purchaseOrderHeaderForMaterialOrPaper->setTotalRemainingReceive($purchaseOrderHeaderForMaterialOrPaper->getSyncTotalRemainingReceive());
        }
    }

    public function save(ReceiveHeader $receiveHeader, array $options = []): void
    {
        $purchaseOrderHeader = $receiveHeader->getPurchaseOrderHeader();
        $purchaseOrderPaperHeader = $receiveHeader->getPurchaseOrderPaperHeader();
        $this->receiveHeaderRepository->add($receiveHeader);
        
        if ($purchaseOrderHeader !== null) {
            $this->purchaseOrderHeaderRepository->add($purchaseOrderHeader);
        } else {
            $this->purchaseOrderPaperHeaderRepository->add($purchaseOrderPaperHeader);
        }
        
        foreach ($receiveHeader->getReceiveDetails() as $receiveDetail) {
            $purchaseOrderDetail = $receiveDetail->getPurchaseOrderDetail();
            $purchaseOrderPaperDetail = $receiveDetail->getPurchaseOrderPaperDetail();
            $this->receiveDetailRepository->add($receiveDetail);
            
            if ($purchaseOrderHeader !== null) {
                $this->purchaseOrderDetailRepository->add($purchaseOrderDetail);
            } else {
                $this->purchaseOrderPaperDetailRepository->add($purchaseOrderPaperDetail);
            }
        }
        $this->entityManager->flush();
    }

    private function getPurchaseOrderHeaderForMaterialOrPaper(ReceiveHeader $receiveHeader)
    {
        $purchaseOrderHeader = $receiveHeader->getPurchaseOrderHeader();
        $purchaseOrderPaperHeader = $receiveHeader->getPurchaseOrderPaperHeader();
        if ($purchaseOrderHeader === null && $purchaseOrderPaperHeader === null) {
            return null;
        } else if ($purchaseOrderPaperHeader === null && $purchaseOrderHeader !== null) {
            return $purchaseOrderHeader;
        } else if ($purchaseOrderHeader === null && $purchaseOrderPaperHeader !== null) {
            return $purchaseOrderPaperHeader;
        }
    }

    private function getPurchaseOrderDetailForMaterialOrPaper(ReceiveDetail $receiveDetail)
    {
        $purchaseOrderDetail = $receiveDetail->getPurchaseOrderDetail();
        $purchaseOrderPaperDetail = $receiveDetail->getPurchaseOrderPaperDetail();
        if ($purchaseOrderDetail === null && $purchaseOrderPaperDetail === null) {
            return null;
        } else if ($purchaseOrderPaperDetail === null && $purchaseOrderDetail !== null) {
            return $purchaseOrderDetail;
        } else if ($purchaseOrderDetail === null && $purchaseOrderPaperDetail !== null) {
            return $purchaseOrderPaperDetail;
        }
    }

    private function setMaterialOrPaper(ReceiveDetail $receiveDetail, $purchaseOrderDetailForMaterialOrPaper): void
    {
        $purchaseOrderDetail = $receiveDetail->getPurchaseOrderDetail();
        $purchaseOrderPaperDetail = $receiveDetail->getPurchaseOrderPaperDetail();
        if ($purchaseOrderDetail === null && $purchaseOrderPaperDetail === null) {
            $receiveDetail->setMaterial(null);
            $receiveDetail->setPaper(null);
        } else if ($purchaseOrderPaperDetail === null && $purchaseOrderDetail !== null) {
            $receiveDetail->setMaterial($purchaseOrderDetailForMaterialOrPaper->getMaterial());
            $receiveDetail->setPaper(null);
        } else if ($purchaseOrderDetail === null && $purchaseOrderPaperDetail !== null) {
            $receiveDetail->setMaterial(null);
            $receiveDetail->setPaper($purchaseOrderDetailForMaterialOrPaper->getPaper());
        }
    }
}
