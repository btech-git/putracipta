<?php

namespace App\Service\Transaction;

use App\Entity\Transaction\PurchaseOrderDetail;
use App\Entity\Transaction\PurchaseOrderPaperDetail;
use App\Entity\Transaction\PurchaseReturnDetail;
use App\Entity\Transaction\PurchaseReturnHeader;
use App\Entity\Transaction\ReceiveDetail;
use App\Repository\Transaction\PurchaseReturnDetailRepository;
use App\Repository\Transaction\PurchaseReturnHeaderRepository;
use App\Repository\Transaction\ReceiveDetailRepository;
use Doctrine\ORM\EntityManagerInterface;

class PurchaseReturnHeaderFormService
{
    private EntityManagerInterface $entityManager;
    private PurchaseReturnHeaderRepository $purchaseReturnHeaderRepository;
    private PurchaseReturnDetailRepository $purchaseReturnDetailRepository;
    private ReceiveDetailRepository $receiveDetailRepository;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->purchaseReturnHeaderRepository = $entityManager->getRepository(PurchaseReturnHeader::class);
        $this->purchaseReturnDetailRepository = $entityManager->getRepository(PurchaseReturnDetail::class);
        $this->receiveDetailRepository = $entityManager->getRepository(ReceiveDetail::class);
    }

    public function initialize(PurchaseReturnHeader $purchaseReturnHeader, array $options = []): void
    {
        list($datetime, $user) = [$options['datetime'], $options['user']];

        if (empty($purchaseReturnHeader->getId())) {
            $purchaseReturnHeader->setCreatedTransactionDateTime($datetime);
            $purchaseReturnHeader->setCreatedTransactionUser($user);
        } else {
            $purchaseReturnHeader->setModifiedTransactionDateTime($datetime);
            $purchaseReturnHeader->setModifiedTransactionUser($user);
        }
    }

    public function finalize(PurchaseReturnHeader $purchaseReturnHeader, array $options = []): void
    {
        if ($purchaseReturnHeader->getTransactionDate() !== null && $purchaseReturnHeader->getId() === null) {
            $year = $purchaseReturnHeader->getTransactionDate()->format('y');
            $month = $purchaseReturnHeader->getTransactionDate()->format('m');
            $lastPurchaseReturnHeader = $this->purchaseReturnHeaderRepository->findRecentBy($year, $month);
            $currentPurchaseReturnHeader = ($lastPurchaseReturnHeader === null) ? $purchaseReturnHeader : $lastPurchaseReturnHeader;
            $purchaseReturnHeader->setCodeNumberToNext($currentPurchaseReturnHeader->getCodeNumber(), $year, $month);

        }
        $receiveHeader = $purchaseReturnHeader->getReceiveHeader();
        $purchaseOrderHeader = empty($receiveHeader->getPurchaseOrderHeader()) ? $receiveHeader->getPurchaseOrderPaperHeader() : $receiveHeader->getPurchaseOrderHeader();
        $purchaseReturnHeader->setSupplier($receiveHeader === null ? null : $receiveHeader->getSupplier());
        $receiveHeader->setHasReturnTransaction(true);
        
        if ($purchaseOrderHeader !== null) {
            $purchaseOrderHeader->setHasReturnTransaction(true);
        }
        
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
        } else {
            $purchaseReturnHeader->setTaxPercentage(0);
        }
        $purchaseReturnHeader->setTaxNominal($purchaseReturnHeader->getSyncTaxNominal());
        $purchaseReturnHeader->setGrandTotal($purchaseReturnHeader->getSyncGrandTotal());
        foreach ($purchaseReturnHeader->getPurchaseReturnDetails() as $purchaseReturnDetail) {
            $receiveDetail = $purchaseReturnDetail->getReceiveDetail();
            $purchaseOrderDetailForMaterialOrPaper = $this->getPurchaseOrderDetailForMaterialOrPaper($receiveDetail);
            $oldReceiveDetails = [];
            if ($purchaseOrderDetailForMaterialOrPaper instanceof PurchaseOrderDetail) {
                $oldReceiveDetails = $this->receiveDetailRepository->findByPurchaseOrderDetail($purchaseOrderDetailForMaterialOrPaper);
            } else if ($purchaseOrderDetailForMaterialOrPaper instanceof PurchaseOrderPaperDetail) {
                $oldReceiveDetails = $this->receiveDetailRepository->findByPurchaseOrderPaperDetail($purchaseOrderDetailForMaterialOrPaper);
            }
            $oldPurchaseReturnDetailsList = [];
            foreach ($oldReceiveDetails as $oldReceiveDetail) {
                $oldPurchaseReturnDetailsList[] = $this->purchaseReturnDetailRepository->findByReceiveDetail($oldReceiveDetail);
            }
            $totalReturn = 0;
            foreach ($oldPurchaseReturnDetailsList as $oldPurchaseReturnDetailsItem) {
                foreach ($oldPurchaseReturnDetailsItem as $oldPurchaseReturnDetail) {
                    if ($oldPurchaseReturnDetail->getId() !== $purchaseReturnDetail->getId()) {
                        $totalReturn += $oldPurchaseReturnDetail->getQuantity();
                    }
                }
            }
            $totalReturn += $purchaseReturnDetail->getQuantity();
            $purchaseOrderDetailForMaterialOrPaper->setTotalReturn($totalReturn);
            $purchaseOrderDetailForMaterialOrPaper->setRemainingReceive($purchaseOrderDetailForMaterialOrPaper->getSyncRemainingReceive());
        }
        
        $purchaseInvoiceHeader = $receiveHeader === null ? null : $receiveHeader->getPurchaseInvoiceHeader();
        if ($purchaseInvoiceHeader !== null) {
            $purchaseInvoiceHeader->setTotalReturn($purchaseReturnHeader->getGrandTotal());
            $purchaseInvoiceHeader->setRemainingPayment($purchaseInvoiceHeader->getSyncRemainingPayment());
        }
    }

    public function save(PurchaseReturnHeader $purchaseReturnHeader, array $options = []): void
    {
        $this->purchaseReturnHeaderRepository->add($purchaseReturnHeader);
        foreach ($purchaseReturnHeader->getPurchaseReturnDetails() as $purchaseReturnDetail) {
            $this->purchaseReturnDetailRepository->add($purchaseReturnDetail);
        }
        $this->entityManager->flush();
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
}
