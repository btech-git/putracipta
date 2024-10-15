<?php

namespace App\Repository\Sale;

use App\Common\Doctrine\Repository\EntityAdd;
use App\Common\Doctrine\Repository\EntityDataFetch;
use App\Common\Doctrine\Repository\EntityRemove;
use App\Entity\Sale\SaleOrderDetail;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class SaleOrderDetailRepository extends ServiceEntityRepository
{
    use EntityDataFetch, EntityAdd, EntityRemove;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SaleOrderDetail::class);
    }

    public function getAveragePriceList(array $products): array
    {
        $dql = 'SELECT IDENTITY(e.product) AS productId, SUM(e.quantity * e.unitPrice) / SUM(e.quantity) AS averagePrice
                FROM ' . SaleOrderDetail::class . ' e
                WHERE e.product IN (:products) AND e.isCanceled = false
                GROUP BY e.product';

        $query = $this->getEntityManager()->createQuery($dql);
        $query->setParameter('products', $products);
        $averagePriceList = $query->getScalarResult();

        return $averagePriceList;
    }
    
    public function findProductSaleOrderDetails(array $products, $startDate, $endDate): array
    {
        $dql = "SELECT e
                FROM " . SaleOrderDetail::class . " e
                JOIN e.saleOrderHeader s
                WHERE e.product IN (:products) AND s.transactionDate BETWEEN :startDate AND :endDate
                ORDER BY e.product ASC, s.transactionDate ASC";

        $query = $this->getEntityManager()->createQuery($dql);
        $query->setParameter('products', $products);
        $query->setParameter('startDate', $startDate);
        $query->setParameter('endDate', $endDate);
        $saleOrderDetails = $query->getResult();

        return $saleOrderDetails;
    }
    
    public function findCustomerSaleOrderDetails(array $customers, $startDate, $endDate, $productCode, $productName, $referenceNumber): array
    {
        $productCodeConditionString = !empty($productCode) ? ' AND p.code LIKE :productCode' : '';
        $productNameConditionString = !empty($productName) ? ' AND p.name LIKE :productName' : '';
        $referenceNumberConditionString = !empty($referenceNumber) ? ' AND h.referenceNumber LIKE :referenceNumber' : '';
        $dql = "SELECT e
                FROM " . SaleOrderDetail::class . " e
                JOIN e.saleOrderHeader h
                JOIN e.product p
                WHERE h.customer IN (:customers) AND h.isCanceled = false AND h.orderReceiveDate BETWEEN :startDate AND :endDate{$productCodeConditionString}{$productNameConditionString}{$referenceNumberConditionString}
                ORDER BY h.customer ASC, h.orderReceiveDate ASC";

        $query = $this->getEntityManager()->createQuery($dql);
        $query->setParameter('customers', $customers);
        $query->setParameter('startDate', $startDate);
        $query->setParameter('endDate', $endDate);
        if (!empty($productCode)) {
            $query->setParameter('productCode', '%' . $productCode . '%');
        }
        if (!empty($productName)) {
            $query->setParameter('productName', '%' . $productName . '%');
        }
        if (!empty($referenceNumber)) {
            $query->setParameter('referenceNumber', '%' . $referenceNumber . '%');
        }
        $saleOrderDetails = $query->getResult();

        return $saleOrderDetails;
    }
    
    public function findSaleOrderHeaderDetails(array $saleOrderHeaders, $startDate, $endDate, $productCode, $productName): array
    {
        $productCodeConditionString = !empty($productCode) ? ' AND p.code LIKE :productCode' : '';
        $productNameConditionString = !empty($productName) ? ' AND p.name LIKE :productName' : '';
        $dql = "SELECT e
                FROM " . SaleOrderDetail::class . " e
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
