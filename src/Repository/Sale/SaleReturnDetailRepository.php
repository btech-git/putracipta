<?php

namespace App\Repository\Sale;

use App\Common\Doctrine\Repository\EntityAdd;
use App\Common\Doctrine\Repository\EntityDataFetch;
use App\Common\Doctrine\Repository\EntityRemove;
use App\Entity\Sale\SaleReturnDetail;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class SaleReturnDetailRepository extends ServiceEntityRepository
{
    use EntityDataFetch, EntityAdd, EntityRemove;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SaleReturnDetail::class);
    }
    
    public function findSaleReturnHeaderDetails(array $saleReturnHeaders, $startDate, $endDate, $productCode, $productName): array
    {
        $productCodeConditionString = !empty($productCode) ? ' AND p.code LIKE :productCode' : '';
        $productNameConditionString = !empty($productName) ? ' AND p.name LIKE :productName' : '';
        $dql = "SELECT e
                FROM " . SaleReturnDetail::class . " e
                JOIN e.saleReturnHeader h
                JOIN e.product p
                WHERE e.saleReturnHeader IN (:saleReturnHeaders) AND h.isCanceled = false AND h.transactionDate BETWEEN :startDate AND :endDate{$productCodeConditionString}{$productNameConditionString}
                ORDER BY h.id ASC, h.transactionDate ASC";

        $query = $this->getEntityManager()->createQuery($dql);
        $query->setParameter('saleReturnHeaders', $saleReturnHeaders);
        $query->setParameter('startDate', $startDate);
        $query->setParameter('endDate', $endDate);
        if (!empty($productCode)) {
            $query->setParameter('productCode', '%' . $productCode . '%');
        }
        if (!empty($productName)) {
            $query->setParameter('productName', '%' . $productName . '%');
        }
        $saleReturnDetails = $query->getResult();

        return $saleReturnDetails;
    }
}
