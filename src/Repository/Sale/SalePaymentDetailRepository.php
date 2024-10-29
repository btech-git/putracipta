<?php

namespace App\Repository\Sale;

use App\Common\Doctrine\Repository\EntityAdd;
use App\Common\Doctrine\Repository\EntityDataFetch;
use App\Common\Doctrine\Repository\EntityRemove;
use App\Entity\Sale\SalePaymentDetail;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class SalePaymentDetailRepository extends ServiceEntityRepository
{
    use EntityDataFetch, EntityAdd, EntityRemove;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SalePaymentDetail::class);
    }
    
    public function findSalePaymentHeaderDetails(array $salePaymentHeaders, $startDate, $endDate, $invoiceOrdinal, $invoiceMonth, $invoiceYear): array
    {
        $invoiceOrdinalConditionString = !empty($invoiceOrdinal) ? ' AND i.codeNumberOrdinal = :codeNumberOrdinal' : '';
        $invoiceMonthConditionString = !empty($invoiceMonth) ? ' AND i.codeNumberMonth = :codeNumberMonth' : '';
        $invoiceYearConditionString = !empty($invoiceYear) ? ' AND i.codeNumberYear = :codeNumberYear' : '';
        $dql = "SELECT e
                FROM " . SalePaymentDetail::class . " e
                JOIN e.salePaymentHeader h
                JOIN e.saleInvoiceHeader i
                WHERE e.salePaymentHeader IN (:salePaymentHeaders) AND h.isCanceled = false AND h.transactionDate BETWEEN :startDate AND :endDate{$invoiceOrdinalConditionString}{$invoiceMonthConditionString}{$invoiceYearConditionString}
                ORDER BY h.id ASC, h.transactionDate ASC";

        $query = $this->getEntityManager()->createQuery($dql);
        $query->setParameter('salePaymentHeaders', $salePaymentHeaders);
        $query->setParameter('startDate', $startDate);
        $query->setParameter('endDate', $endDate);
        if (!empty($invoiceOrdinal)) {
            $query->setParameter('codeNumberOrdinal', $invoiceOrdinal);
        }
        if (!empty($invoiceMonth)) {
            $query->setParameter('codeNumberMonth', $invoiceMonth);
        }
        if (!empty($invoiceYear)) {
            $query->setParameter('codeNumberYear', $invoiceYear);
        }
        $salePaymentDetails = $query->getResult();

        return $salePaymentDetails;
    }
}
