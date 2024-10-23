<?php

namespace App\Repository\Purchase;

use App\Common\Doctrine\Repository\EntityAdd;
use App\Common\Doctrine\Repository\EntityDataFetch;
use App\Common\Doctrine\Repository\EntityRemove;
use App\Entity\Purchase\PurchaseOrderPaperDetail;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class PurchaseOrderPaperDetailRepository extends ServiceEntityRepository
{
    use EntityDataFetch, EntityAdd, EntityRemove;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PurchaseOrderPaperDetail::class);
    }

    public function getAveragePriceList(array $papers): array
    {
        $dql = 'SELECT IDENTITY(e.paper) AS paperId, SUM(e.quantity * e.unitPrice) / SUM(e.quantity) AS averagePrice
                FROM ' . PurchaseOrderPaperDetail::class . ' e
                WHERE e.paper IN (:papers) AND e.isCanceled = false
                GROUP BY e.paper';

        $query = $this->getEntityManager()->createQuery($dql);
        $query->setParameter('papers', $papers);
        $averagePriceList = $query->getScalarResult();

        return $averagePriceList;
    }
    
    public function findPaperPurchaseOrderPaperDetails(array $papers, $startDate, $endDate, $supplierCompany, $materialSubCategoryCode): array
    {
        $supplierNameConditionString = !empty($supplierCompany) ? ' AND s.company LIKE :supplierCompany' : '';
        $materialSubCategoryCodeConditionString = !empty($materialSubCategoryCode) ? ' AND c.code LIKE :materialSubCategoryCode' : '';
        $dql = "SELECT e
                FROM " . PurchaseOrderPaperDetail::class . " e
                JOIN e.purchaseOrderPaperHeader h
                JOIN e.paper p 
                JOIN p.materialSubCategory c
                JOIN h.supplier s
                WHERE e.paper IN (:papers) AND h.transactionDate BETWEEN :startDate AND :endDate{$supplierNameConditionString}{$materialSubCategoryCodeConditionString}
                ORDER BY e.paper ASC, h.transactionDate ASC";

        $query = $this->getEntityManager()->createQuery($dql);
        $query->setParameter('papers', $papers);
        $query->setParameter('startDate', $startDate);
        $query->setParameter('endDate', $endDate);
        if (!empty($supplierCompany)) {
            $query->setParameter('supplierCompany', '%' . $supplierCompany . '%');
        }
        if (!empty($materialSubCategoryCode)) {
            $query->setParameter('materialSubCategoryCode', '%' . $materialSubCategoryCode . '%');
        }
        $purchaseOrderDetails = $query->getResult();

        return $purchaseOrderDetails;
    }
    
    public function findPurchaseOrderPaperHeaderDetails(array $purchaseOrderPaperHeaders, $startDate, $endDate, $paperCode, $paperName, $paperType, $paperWeight, $materialSubCategoryCode): array
    {
        $paperCodeConditionString = !empty($paperCode) ? ' AND p.code = :paperCode' : '';
        $paperNameConditionString = !empty($paperName) ? ' AND p.name LIKE :paperName' : '';
        $paperTypeConditionString = !empty($paperType) ? ' AND p.type = :paperType' : '';
        $paperWeightConditionString = !empty($paperWeight) ? ' AND p.weight = :paperWeight' : '';
        $materialSubCategoryCodeConditionString = !empty($materialSubCategoryCode) ? ' AND c.code LIKE :materialSubCategoryCode' : '';
        $dql = "SELECT e
                FROM " . PurchaseOrderPaperDetail::class . " e
                JOIN e.purchaseOrderPaperHeader h
                JOIN e.paper p
                JOIN p.materialSubCategory c 
                WHERE e.purchaseOrderPaperHeader IN (:purchaseOrderPaperHeaders) AND h.isCanceled = false AND h.transactionDate BETWEEN :startDate AND :endDate{$paperCodeConditionString}{$paperNameConditionString}{$paperTypeConditionString}{$paperWeightConditionString}{$materialSubCategoryCodeConditionString}
                ORDER BY h.id ASC, h.transactionDate ASC";

        $query = $this->getEntityManager()->createQuery($dql);
        $query->setParameter('purchaseOrderPaperHeaders', $purchaseOrderPaperHeaders);
        $query->setParameter('startDate', $startDate);
        $query->setParameter('endDate', $endDate);
        if (!empty($paperCode)) {
            $query->setParameter('paperCode', $paperCode);
        }
        if (!empty($paperName)) {
            $query->setParameter('paperName', '%' . $paperName . '%');
        }
        if (!empty($paperType)) {
            $query->setParameter('paperType', $paperType);
        }
        if (!empty($paperWeight)) {
            $query->setParameter('paperWeight', $paperWeight);
        }
        if (!empty($materialSubCategoryCode)) {
            $query->setParameter('materialSubCategoryCode', '%' . $materialSubCategoryCode . '%');
        }
        $purchaseOrderPaperDetails = $query->getResult();

        return $purchaseOrderPaperDetails;
    }
}
