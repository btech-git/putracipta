<?php

namespace App\Repository\Stock;

use App\Common\Doctrine\Repository\EntityAdd;
use App\Common\Doctrine\Repository\EntityDataFetch;
use App\Common\Doctrine\Repository\EntityRemove;
use App\Entity\Stock\MaterialRequestHeader;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class MaterialRequestHeaderRepository extends ServiceEntityRepository
{
    use EntityDataFetch, EntityAdd, EntityRemove;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MaterialRequestHeader::class);
    }

    public function findRecentBy($year, $month)
    {
        $dql = 'SELECT e FROM ' . MaterialRequestHeader::class . ' e WHERE e.codeNumberMonth = :codeNumberMonth AND e.codeNumberYear = :codeNumberYear ORDER BY e.codeNumberOrdinal DESC';

        $query = $this->getEntityManager()->createQuery($dql);
        $query->setParameter('codeNumberMonth', $month);
        $query->setParameter('codeNumberYear', $year);
        $query->setMaxResults(1);
        $lastMaterialRequestHeader = $query->getOneOrNullResult();

        return $lastMaterialRequestHeader;
    }
}
