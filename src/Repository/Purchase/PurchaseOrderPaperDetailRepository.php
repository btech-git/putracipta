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
}
