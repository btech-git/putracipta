<?php

namespace App\Repository\Transaction;

use App\Entity\Transaction\ReceiveHeader;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ReceiveHeader>
 *
 * @method ReceiveHeader|null find($id, $lockMode = null, $lockVersion = null)
 * @method ReceiveHeader|null findOneBy(array $criteria, array $orderBy = null)
 * @method ReceiveHeader[]    findAll()
 * @method ReceiveHeader[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReceiveHeaderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ReceiveHeader::class);
    }

    public function add(ReceiveHeader $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(ReceiveHeader $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return ReceiveHeader[] Returns an array of ReceiveHeader objects
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

//    public function findOneBySomeField($value): ?ReceiveHeader
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
