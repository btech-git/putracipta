<?php

namespace App\Repository\Stock;

use App\Entity\Stock\AdjustmentStockDetail;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<AdjustmentStockDetail>
 *
 * @method AdjustmentStockDetail|null find($id, $lockMode = null, $lockVersion = null)
 * @method AdjustmentStockDetail|null findOneBy(array $criteria, array $orderBy = null)
 * @method AdjustmentStockDetail[]    findAll()
 * @method AdjustmentStockDetail[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AdjustmentStockDetailRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AdjustmentStockDetail::class);
    }

    public function add(AdjustmentStockDetail $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(AdjustmentStockDetail $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return AdjustmentStockDetail[] Returns an array of AdjustmentStockDetail objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('a.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?AdjustmentStockDetail
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
