<?php

namespace App\Repository\Admin;

use App\Common\Doctrine\Repository\EntityAdd;
use App\Common\Doctrine\Repository\EntityDataFetch;
use App\Common\Doctrine\Repository\EntityRemove;
use App\Entity\Admin\LiteralConfig;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<LiteralConfig>
 *
 * @method LiteralConfig|null find($id, $lockMode = null, $lockVersion = null)
 * @method LiteralConfig|null findOneBy(array $criteria, array $orderBy = null)
 * @method LiteralConfig[]    findAll()
 * @method LiteralConfig[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LiteralConfigRepository extends ServiceEntityRepository
{
    use EntityDataFetch, EntityAdd, EntityRemove;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LiteralConfig::class);
    }
}
