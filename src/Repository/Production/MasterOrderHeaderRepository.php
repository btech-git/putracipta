<?php

namespace App\Repository\Production;

use App\Common\Doctrine\Repository\EntityAdd;
use App\Common\Doctrine\Repository\EntityDataFetch;
use App\Common\Doctrine\Repository\EntityRemove;
use App\Entity\Production\MasterOrderHeader;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class MasterOrderHeaderRepository extends ServiceEntityRepository
{
    use EntityDataFetch, EntityAdd, EntityRemove;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MasterOrderHeader::class);
    }

    public function findRecentBy($year)
    {
        $dql = 'SELECT e FROM ' . MasterOrderHeader::class . ' e WHERE e.codeNumberYear = :codeNumberYear ORDER BY e.codeNumberOrdinal DESC';

        $query = $this->getEntityManager()->createQuery($dql);
        $query->setParameter('codeNumberYear', $year);
        $query->setMaxResults(1);
        $lastMasterOrder = $query->getOneOrNullResult();

        return $lastMasterOrder;
    }
    
    public function findPaperFscMasterOrderHeaders(array $papers, $startDate, $endDate): array
    {
        $dql = "SELECT e
                FROM " . MasterOrderHeader::class . " e
                WHERE e.paper IN (:papers) AND e.transactionDate BETWEEN :startDate AND :endDate
                ORDER BY e.paper ASC, e.transactionDate ASC";

        $query = $this->getEntityManager()->createQuery($dql);
        $query->setParameter('papers', $papers);
        $query->setParameter('startDate', $startDate);
        $query->setParameter('endDate', $endDate);
        $masterOrderHeaders = $query->getResult();

        return $masterOrderHeaders;
    }
}
