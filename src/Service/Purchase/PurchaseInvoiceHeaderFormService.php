<?php

namespace App\Service\Purchase;

use App\Common\Idempotent\IdempotentUtility;
use App\Entity\Purchase\PurchaseInvoiceDetail;
use App\Entity\Purchase\PurchaseInvoiceHeader;
use App\Entity\Support\Idempotent;
use App\Entity\Support\TransactionLog;
use App\Repository\Purchase\PurchaseInvoiceDetailRepository;
use App\Repository\Purchase\PurchaseInvoiceHeaderRepository;
use App\Repository\Support\IdempotentRepository;
use App\Repository\Support\TransactionLogRepository;
use App\Support\Purchase\PurchaseInvoiceHeaderFormSupport;
use App\Sync\Purchase\PurchaseInvoiceHeaderFormSync;
use App\Util\Service\EntityResetUtil;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class PurchaseInvoiceHeaderFormService
{
    use PurchaseInvoiceHeaderFormSupport;
    
    private PurchaseInvoiceHeaderFormSync $formSync;
    private EntityManagerInterface $entityManager;
    private TransactionLogRepository $transactionLogRepository;
    private IdempotentRepository $idempotentRepository;
    private PurchaseInvoiceHeaderRepository $purchaseInvoiceHeaderRepository;
    private PurchaseInvoiceDetailRepository $purchaseInvoiceDetailRepository;

    public function __construct(RequestStack $requestStack, PurchaseInvoiceHeaderFormSync $formSync, EntityManagerInterface $entityManager)
    {
        $this->formSync = $formSync;
        $this->requestStack = $requestStack;
        $this->entityManager = $entityManager;
        $this->transactionLogRepository = $entityManager->getRepository(TransactionLog::class);
        $this->idempotentRepository = $entityManager->getRepository(Idempotent::class);
        $this->purchaseInvoiceHeaderRepository = $entityManager->getRepository(PurchaseInvoiceHeader::class);
        $this->purchaseInvoiceDetailRepository = $entityManager->getRepository(PurchaseInvoiceDetail::class);
    }

    public function initialize(PurchaseInvoiceHeader $purchaseInvoiceHeader, array $options = []): void
    {
        list($datetime, $user) = [$options['datetime'], $options['user']];

        if (isset($options['cancelTransaction']) && $options['cancelTransaction'] === true) {
            $purchaseInvoiceHeader->setTransactionStatus(PurchaseInvoiceHeader::TRANSACTION_STATUS_CANCEL);
            $purchaseInvoiceHeader->setIsCanceled(true);
            $purchaseInvoiceHeader->setCancelledTransactionDateTime($datetime);
            $purchaseInvoiceHeader->setCancelledTransactionUser($user);
        } else {
            if (empty($purchaseInvoiceHeader->getId())) {
                $purchaseInvoiceHeader->setCreatedTransactionDateTime($datetime);
                $purchaseInvoiceHeader->setCreatedTransactionUser($user);
            } else {
                $purchaseInvoiceHeader->setModifiedTransactionDateTime($datetime);
                $purchaseInvoiceHeader->setModifiedTransactionUser($user);
            }

            $purchaseInvoiceHeader->setCodeNumberVersion($purchaseInvoiceHeader->getCodeNumberVersion() + 1);
        }
    }

    public function finalize(PurchaseInvoiceHeader $purchaseInvoiceHeader, array $options = []): void
    {
        if (isset($options['cancelTransaction']) && $options['cancelTransaction'] === true) {
            EntityResetUtil::reset($this->formSync, $purchaseInvoiceHeader);
        } else {
            foreach ($purchaseInvoiceHeader->getPurchaseInvoiceDetails() as $purchaseInvoiceDetail) {
                EntityResetUtil::reset($this->formSync, $purchaseInvoiceDetail);
            }
        }
        
        if ($purchaseInvoiceHeader->getTransactionDate() !== null && $purchaseInvoiceHeader->getId() === null) {
            $year = $purchaseInvoiceHeader->getTransactionDate()->format('y');
            $month = $purchaseInvoiceHeader->getTransactionDate()->format('m');
            $lastPurchaseInvoiceHeader = $this->purchaseInvoiceHeaderRepository->findRecentBy($year);
            $currentPurchaseInvoiceHeader = ($lastPurchaseInvoiceHeader === null) ? $purchaseInvoiceHeader : $lastPurchaseInvoiceHeader;
            $purchaseInvoiceHeader->setCodeNumberToNext($currentPurchaseInvoiceHeader->getCodeNumber(), $year, $month);
        }
        
        if ($purchaseInvoiceHeader->getTaxMode() !== $purchaseInvoiceHeader::TAX_MODE_NON_TAX) {
            $purchaseInvoiceHeader->setTaxPercentage($options['vatPercentage']);
        } else {
            $purchaseInvoiceHeader->setTaxPercentage(0);
        }
        
//        $purchaseInvoiceHeader->setSupplier($receiveHeader === null ? null : $receiveHeader->getSupplier());
        $purchaseInvoiceHeader->setDueDate($purchaseInvoiceHeader->getSyncDueDate());
        foreach ($purchaseInvoiceHeader->getPurchaseInvoiceDetails() as $purchaseInvoiceDetail) {
            $purchaseInvoiceDetail->setIsCanceled($purchaseInvoiceDetail->getSyncIsCanceled());
        }
        
        foreach ($purchaseInvoiceHeader->getPurchaseInvoiceDetails() as $purchaseInvoiceDetail) {
            $receiveDetail = $purchaseInvoiceDetail->getReceiveDetail();
//            $purchaseOrderDetail = empty($receiveDetail->getPurchaseOrderDetail()) ? $receiveDetail->getPurchaseOrderPaperDetail(): $receiveDetail->getPurchaseOrderDetail();
            $purchaseInvoiceDetail->setMaterial($receiveDetail->getMaterial());
            $purchaseInvoiceDetail->setPaper($receiveDetail->getPaper());
            $purchaseInvoiceDetail->setQuantity($receiveDetail->getReceivedQuantity());
//            $purchaseInvoiceDetail->setUnitPrice($purchaseOrderDetail->getUnitPriceBeforeTax());
            $purchaseInvoiceDetail->setUnit($receiveDetail === null ? null : $receiveDetail->getUnit());
        }
        
        $purchaseInvoiceHeader->setSubTotal($purchaseInvoiceHeader->getSyncSubTotal());
        $purchaseInvoiceHeader->setSubTotalCoretax($purchaseInvoiceHeader->getSyncSubTotalCoretax());
        $purchaseInvoiceHeader->setTaxNominal($purchaseInvoiceHeader->getSyncTaxNominal());
        $purchaseInvoiceHeader->setGrandTotal($purchaseInvoiceHeader->getSyncGrandTotal());
        
//        $purchaseReturnHeaders = $receiveHeader === null ? null : $receiveHeader->getPurchaseReturnHeaders();
//        if ($purchaseReturnHeaders !== null) {
//            foreach ($purchaseReturnHeaders as $purchaseReturnHeader) {
//                if ($purchaseReturnHeader->isIsProductExchange() === false) {
//                    $purchaseInvoiceHeader->setTotalReturn($purchaseReturnHeader->getGrandTotal());
//                }
//            }
//        }
        
        $purchaseInvoiceHeader->setRemainingPayment($purchaseInvoiceHeader->getSyncRemainingPayment());
    }

    public function save(PurchaseInvoiceHeader $purchaseInvoiceHeader, array $options = []): void
    {
        $this->entityManager->wrapInTransaction(function($entityManager) use ($purchaseInvoiceHeader) {
            $idempotent = IdempotentUtility::create(Idempotent::class, $this->requestStack->getCurrentRequest());
            $this->idempotentRepository->add($idempotent);
            $this->purchaseInvoiceHeaderRepository->add($purchaseInvoiceHeader);
            foreach ($purchaseInvoiceHeader->getPurchaseInvoiceDetails() as $purchaseInvoiceDetail) {
                $this->purchaseInvoiceDetailRepository->add($purchaseInvoiceDetail);
            }
            $this->entityManager->flush();
            $transactionLog = $this->buildTransactionLog($purchaseInvoiceHeader);
            $this->transactionLogRepository->add($transactionLog);
            $entityManager->flush();
        });
    }
}
