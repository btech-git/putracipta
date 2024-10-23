<?php

namespace App\Repository\Sale;

use App\Entity\Sale\SaleOrderDetailLogData;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<SaleOrderDetailLogData>
 *
 * @method SaleOrderDetailLogData|null find($id, $lockMode = null, $lockVersion = null)
 * @method SaleOrderDetailLogData|null findOneBy(array $criteria, array $orderBy = null)
 * @method SaleOrderDetailLogData[]    findAll()
 * @method SaleOrderDetailLogData[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SaleOrderDetailLogDataRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SaleOrderDetailLogData::class);
    }

    public function save(SaleOrderDetailLogData $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(SaleOrderDetailLogData $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return SaleOrderDetailLogData[] Returns an array of SaleOrderDetailLogData objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('s.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?SaleOrderDetailLogData
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
