<?php

namespace App\Service\Purchase;

use App\Entity\Purchase\PurchaseOrderDetail;
use App\Entity\Purchase\PurchaseOrderPaperDetail;
use App\Entity\Purchase\PurchaseReturnDetail;
use App\Entity\Purchase\PurchaseReturnHeader;
use App\Entity\Purchase\ReceiveDetail;
use App\Entity\Purchase\ReceiveHeader;
use App\Repository\Purchase\PurchaseReturnDetailRepository;
use App\Repository\Purchase\PurchaseReturnHeaderRepository;
use App\Repository\Purchase\ReceiveDetailRepository;
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
        $purchaseReturnHeader->setSupplier($receiveHeader === null ? null : $receiveHeader->getSupplier());
        
        if ($receiveHeader !== null) {
            $receiveHeader->setHasReturnTransaction(true);
            $purchaseOrderHeaderForMaterialOrPaper = $this->getPurchaseOrderHeaderForMaterialOrPaper($receiveHeader);
            
            if ($purchaseOrderHeaderForMaterialOrPaper !== null) {
                $purchaseOrderHeaderForMaterialOrPaper->setHasReturnTransaction(true);
            }
        }
        
        foreach ($purchaseReturnHeader->getPurchaseReturnDetails() as $purchaseReturnDetail) {
            $purchaseReturnDetail->setIsCanceled($purchaseReturnDetail->getSyncIsCanceled());
            $receiveDetail = $purchaseReturnDetail->getReceiveDetail();
            $purchaseOrderDetail = empty($receiveDetail->getPurchaseOrderDetail()) ? $receiveDetail->getPurchaseOrderPaperDetail(): $receiveDetail->getPurchaseOrderDetail();
            $purchaseReturnDetail->setMaterial($receiveDetail->getMaterial());
            $purchaseReturnDetail->setPaper($receiveDetail->getPaper());
            $purchaseReturnDetail->setUnitPrice($purchaseOrderDetail->getUnitPriceBeforeTax());
            $purchaseReturnDetail->setUnit($receiveDetail === null ? null : $receiveDetail->getUnit());
            
            if ($purchaseReturnHeader->isIsProductExchange()) {
                $totalReturn = $purchaseOrderDetail->getTotalReturn();
                if ($purchaseReturnDetail->isIsCanceled() === false) {
                    $totalReturn += $purchaseReturnDetail->getQuantity();
                }

                $purchaseOrderDetail->setTotalReturn($totalReturn);
                $purchaseOrderDetail->setRemainingReceive($purchaseOrderDetail->getSyncRemainingReceive());
            }
            
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
        
        $purchaseInvoiceHeaders = $receiveHeader === null ? null : $receiveHeader->getPurchaseInvoiceHeaders();
        if ($purchaseInvoiceHeaders !== null) {
            foreach ($purchaseInvoiceHeaders as $purchaseInvoiceHeader) {
                $purchaseInvoiceHeader->setTotalReturn($purchaseReturnHeader->getGrandTotal());
                $purchaseInvoiceHeader->setRemainingPayment($purchaseInvoiceHeader->getSyncRemainingPayment());
            }
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
}
