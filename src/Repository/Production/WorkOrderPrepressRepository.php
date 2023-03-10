<?php

namespace App\Repository\Production;

use App\Common\Doctrine\Repository\EntityAdd;
use App\Common\Doctrine\Repository\EntityDataFetch;
use App\Common\Doctrine\Repository\EntityRemove;
use App\Entity\Production\WorkOrderPrepress;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class WorkOrderPrepressRepository extends ServiceEntityRepository
{
    use EntityDataFetch, EntityAdd, EntityRemove;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, WorkOrderPrepress::class);
    }

    public function findRecentBy($year, $month)
    {
        $dql = 'SELECT e FROM ' . WorkOrderPrepress::class . ' e WHERE e.codeNumberMonth = :codeNumberMonth AND e.codeNumberYear = :codeNumberYear ORDER BY e.codeNumberOrdinal DESC';

        $query = $this->getEntityManager()->createQuery($dql);
        $query->setParameter('codeNumberMonth', $month);
        $query->setParameter('codeNumberYear', $year);
        $query->setMaxResults(1);
        $lastWorkOrderPrepress = $query->getOneOrNullResult();

        return $lastWorkOrderPrepress;
    }
}
