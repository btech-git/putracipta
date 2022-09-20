<?php

namespace App\Repository\Transaction;

use App\Entity\Transaction\PurchaseReturnHeader;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PurchaseReturnHeader>
 *
 * @method PurchaseReturnHeader|null find($id, $lockMode = null, $lockVersion = null)
 * @method PurchaseReturnHeader|null findOneBy(array $criteria, array $orderBy = null)
 * @method PurchaseReturnHeader[]    findAll()
 * @method PurchaseReturnHeader[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PurchaseReturnHeaderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PurchaseReturnHeader::class);
    }

    public function add(PurchaseReturnHeader $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(PurchaseReturnHeader $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return PurchaseReturnHeader[] Returns an array of PurchaseReturnHeader objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?PurchaseReturnHeader
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
