<?php

namespace App\Repository\Sale;

use App\Common\Doctrine\Repository\EntityAdd;
use App\Common\Doctrine\Repository\EntityDataFetch;
use App\Common\Doctrine\Repository\EntityRemove;
use App\Entity\Sale\SaleOrderDetailLogData;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class SaleOrderDetailLogDataRepository extends ServiceEntityRepository
{
    use EntityDataFetch, EntityAdd, EntityRemove;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SaleOrderDetailLogData::class);
    }
    
    public function findSaleOrderHeaderDetailLogDatas(array $saleOrderHeaders, $startDate, $endDate, $productCode, $productName): array
    {
        $productCodeConditionString = !empty($productCode) ? ' AND p.code LIKE :productCode' : '';
        $productNameConditionString = !empty($productName) ? ' AND p.name LIKE :productName' : '';
        $dql = "SELECT e
                FROM " . SaleOrderDetailLogData::class . " e
                JOIN e.saleOrderHeader h
                JOIN e.product p
                WHERE e.saleOrderHeader IN (:saleOrderHeaders) AND h.isCanceled = false AND h.orderReceiveDate BETWEEN :startDate AND :endDate{$productCodeConditionString}{$productNameConditionString}
                ORDER BY h.id ASC, h.orderReceiveDate ASC";

        $query = $this->getEntityManager()->createQuery($dql);
        $query->setParameter('saleOrderHeaders', $saleOrderHeaders);
        $query->setParameter('startDate', $startDate);
        $query->setParameter('endDate', $endDate);
        if (!empty($productCode)) {
            $query->setParameter('productCode', '%' . $productCode . '%');
        }
        if (!empty($productName)) {
            $query->setParameter('productName', '%' . $productName . '%');
        }
        $saleOrderDetails = $query->getResult();

        return $saleOrderDetails;
    }
}
