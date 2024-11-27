<?php

namespace App\Repository\Purchase;

use App\Common\Doctrine\Repository\EntityAdd;
use App\Common\Doctrine\Repository\EntityDataFetch;
use App\Common\Doctrine\Repository\EntityRemove;
use App\Entity\Purchase\PurchaseOrderHeader;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class PurchaseOrderHeaderRepository extends ServiceEntityRepository
{
    use EntityDataFetch, EntityAdd, EntityRemove;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PurchaseOrderHeader::class);
    }

    public function findRecentBy($year)
    {
        $dql = 'SELECT e FROM ' . PurchaseOrderHeader::class . ' e WHERE e.codeNumberYear = :codeNumberYear ORDER BY e.codeNumberOrdinal DESC';

        $query = $this->getEntityManager()->createQuery($dql);
//        $query->setParameter('codeNumberMonth', $month);
        $query->setParameter('codeNumberYear', $year);
        $query->setMaxResults(1);
        $lastPurchaseOrderHeader = $query->getOneOrNullResult();

        return $lastPurchaseOrderHeader;
    }
    
    public function findSupplierPurchaseOrderHeaders(array $suppliers, $startDate, $endDate, $codeNumberOrdinal, $codeNumberMonth, $codeNumberYear): array
    {
        $codeNumberOrdinalConditionString = !empty($codeNumberOrdinal) ? ' AND e.codeNumberOrdinal LIKE :codeNumberOrdinal' : '';
        $codeNumberMonthConditionString = !empty($codeNumberMonth) ? ' AND e.codeNumberMonth = :codeNumberMonth' : '';
        $codeNumberYearConditionString = !empty($codeNumberYear) ? ' AND e.codeNumberYear = :codeNumberYear' : '';
        $dql = "SELECT e
                FROM " . PurchaseOrderHeader::class . " e
                WHERE e.supplier IN (:suppliers) AND e.transactionDate BETWEEN :startDate AND :endDate{$codeNumberOrdinalConditionString}{$codeNumberMonthConditionString}{$codeNumberYearConditionString}
                ORDER BY e.supplier ASC, e.transactionDate ASC";

        $query = $this->getEntityManager()->createQuery($dql);
        $query->setParameter('suppliers', $suppliers);
        $query->setParameter('startDate', $startDate);
        $query->setParameter('endDate', $endDate);
        if (!empty($codeNumberOrdinal)) {
            $query->setParameter('codeNumberOrdinal', '%' . $codeNumberOrdinal . '%');
        }
        if (!empty($codeNumberMonth)) {
            $query->setParameter('codeNumberMonth', $codeNumberMonth);
        }
        if (!empty($codeNumberYear)) {
            $query->setParameter('codeNumberYear', $codeNumberYear);
        }
        $purchaseOrderHeaders = $query->getResult();

        return $purchaseOrderHeaders;
    }
    
}
