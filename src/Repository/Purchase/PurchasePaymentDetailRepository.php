<?php

namespace App\Repository\Purchase;

use App\Common\Doctrine\Repository\EntityAdd;
use App\Common\Doctrine\Repository\EntityDataFetch;
use App\Common\Doctrine\Repository\EntityRemove;
use App\Entity\Purchase\PurchasePaymentDetail;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class PurchasePaymentDetailRepository extends ServiceEntityRepository
{
    use EntityDataFetch, EntityAdd, EntityRemove;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PurchasePaymentDetail::class);
    }
    
    public function findPurchasePaymentHeaderDetails(array $purchasePaymentHeaders, $startDate, $endDate, $supplierInvoiceCodeNumber): array
    {
        $supplierInvoiceConditionString = !empty($supplierInvoiceCodeNumber) ? ' AND i.supplierInvoiceCodeNumber LIKE :supplierInvoiceCodeNumber' : '';
        $dql = "SELECT e
                FROM " . PurchasePaymentDetail::class . " e
                JOIN e.purchasePaymentHeader h
                JOIN e.purchaseInvoiceHeader i
                WHERE e.purchasePaymentHeader IN (:purchasePaymentHeaders) AND h.isCanceled = false AND h.transactionDate BETWEEN :startDate AND :endDate{$supplierInvoiceConditionString}
                ORDER BY h.id ASC, h.transactionDate ASC";

        $query = $this->getEntityManager()->createQuery($dql);
        $query->setParameter('purchasePaymentHeaders', $purchasePaymentHeaders);
        $query->setParameter('startDate', $startDate);
        $query->setParameter('endDate', $endDate);
        if (!empty($supplierInvoiceCodeNumber)) {
            $query->setParameter('supplierInvoiceCodeNumber', '%' . $supplierInvoiceCodeNumber . '%');
        }
        $purchasePaymentDetails = $query->getResult();

        return $purchasePaymentDetails;
    }
}
