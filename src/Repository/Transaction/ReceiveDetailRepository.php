<?php

namespace App\Repository\Transaction;

use App\Entity\Transaction\ReceiveDetail;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ReceiveDetail>
 *
 * @method ReceiveDetail|null find($id, $lockMode = null, $lockVersion = null)
 * @method ReceiveDetail|null findOneBy(array $criteria, array $orderBy = null)
 * @method ReceiveDetail[]    findAll()
 * @method ReceiveDetail[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReceiveDetailRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ReceiveDetail::class);
    }

    public function add(ReceiveDetail $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(ReceiveDetail $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return ReceiveDetail[] Returns an array of ReceiveDetail objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('r.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?ReceiveDetail
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
