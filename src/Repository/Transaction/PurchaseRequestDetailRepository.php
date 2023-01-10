<?php

namespace App\Repository\Transaction;

use App\Common\Doctrine\Repository\EntityAdd;
use App\Common\Doctrine\Repository\EntityDataFetch;
use App\Common\Doctrine\Repository\EntityRemove;
use App\Entity\Transaction\PurchaseRequestDetail;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PurchaseRequestDetail>
 *
 * @method PurchaseRequestDetail|null find($id, $lockMode = null, $lockVersion = null)
 * @method PurchaseRequestDetail|null findOneBy(array $criteria, array $orderBy = null)
 * @method PurchaseRequestDetail[]    findAll()
 * @method PurchaseRequestDetail[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PurchaseRequestDetailRepository extends ServiceEntityRepository
{
    use EntityDataFetch, EntityAdd, EntityRemove;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PurchaseRequestDetail::class);
    }
}
