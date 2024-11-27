<?php

namespace App\Repository\Purchase;

use App\Common\Doctrine\Repository\EntityAdd;
use App\Common\Doctrine\Repository\EntityDataFetch;
use App\Common\Doctrine\Repository\EntityRemove;
use App\Entity\Purchase\PurchaseOrderDetail;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class PurchaseOrderDetailRepository extends ServiceEntityRepository
{
    use EntityDataFetch, EntityAdd, EntityRemove;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PurchaseOrderDetail::class);
    }

    public function getAveragePriceList(array $materials): array
    {
        $dql = 'SELECT IDENTITY(e.material) AS materialId, SUM(e.quantity * e.unitPrice) / SUM(e.quantity) AS averagePrice
                FROM ' . PurchaseOrderDetail::class . ' e
                WHERE e.material IN (:materials) AND e.isCanceled = false
                GROUP BY e.material';

        $query = $this->getEntityManager()->createQuery($dql);
        $query->setParameter('materials', $materials);
        $averagePriceList = $query->getScalarResult();

        return $averagePriceList;
    }
    
    public function findMaterialPurchaseOrderDetails(array $materials, $startDate, $endDate, $supplier, $codeNumberOrdinal, $codeNumberMonth, $codeNumberYear): array
    {
        $codeNumberOrdinalConditionString = !empty($codeNumberOrdinal) ? ' AND s.codeNumberOrdinal LIKE :codeNumberOrdinal' : '';
        $codeNumberMonthConditionString = !empty($codeNumberMonth) ? ' AND s.codeNumberMonth = :codeNumberMonth' : '';
        $codeNumberYearConditionString = !empty($codeNumberYear) ? ' AND s.codeNumberYear = :codeNumberYear' : '';
        $supplierConditionString = !empty($supplier) ? ' AND s.supplier = :supplier' : '';
        $dql = "SELECT e
                FROM " . PurchaseOrderDetail::class . " e
                INNER JOIN e.purchaseOrderHeader s
                WHERE e.material IN (:materials) AND s.transactionDate BETWEEN :startDate AND :endDate{$supplierConditionString}{$codeNumberOrdinalConditionString}{$codeNumberMonthConditionString}{$codeNumberYearConditionString}
                ORDER BY e.material ASC, s.transactionDate ASC";

        $query = $this->getEntityManager()->createQuery($dql);
        $query->setParameter('materials', $materials);
        $query->setParameter('startDate', $startDate);
        $query->setParameter('endDate', $endDate);
        if (!empty($supplier)) {
            $query->setParameter('supplier', $supplier);
        }
        if (!empty($codeNumberOrdinal)) {
            $query->setParameter('codeNumberOrdinal', '%' . $codeNumberOrdinal . '%');
        }
        if (!empty($codeNumberMonth)) {
            $query->setParameter('codeNumberMonth', $codeNumberMonth);
        }
        if (!empty($codeNumberYear)) {
            $query->setParameter('codeNumberYear', $codeNumberYear);
        }
        $purchaseOrderDetails = $query->getResult();

        return $purchaseOrderDetails;
    }
    
    public function findPurchaseOrderHeaderDetails(array $purchaseOrderHeaders, $startDate, $endDate, $materialCode, $materialName): array
    {
        $materialCodeConditionString = !empty($materialCode) ? ' AND p.code LIKE :materialCode' : '';
        $materialNameConditionString = !empty($materialName) ? ' AND p.name LIKE :materialName' : '';
        $dql = "SELECT e
                FROM " . PurchaseOrderDetail::class . " e
                JOIN e.purchaseOrderHeader h
                JOIN e.material p
                WHERE e.purchaseOrderHeader IN (:purchaseOrderHeaders) AND h.isCanceled = false AND h.transactionDate BETWEEN :startDate AND :endDate{$materialCodeConditionString}{$materialNameConditionString}
                ORDER BY h.id ASC, h.transactionDate ASC";

        $query = $this->getEntityManager()->createQuery($dql);
        $query->setParameter('purchaseOrderHeaders', $purchaseOrderHeaders);
        $query->setParameter('startDate', $startDate);
        $query->setParameter('endDate', $endDate);
        if (!empty($materialCode)) {
            $query->setParameter('materialCode', '%' . $materialCode . '%');
        }
        if (!empty($materialName)) {
            $query->setParameter('materialName', '%' . $materialName . '%');
        }
        $purchaseOrderDetails = $query->getResult();

        return $purchaseOrderDetails;
    }
}
