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
}
