<?php

namespace App\Repository\Transaction;

use App\Entity\Transaction\PurchaseRequestHeader;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PurchaseRequestHeader>
 *
 * @method PurchaseRequestHeader|null find($id, $lockMode = null, $lockVersion = null)
 * @method PurchaseRequestHeader|null findOneBy(array $criteria, array $orderBy = null)
 * @method PurchaseRequestHeader[]    findAll()
 * @method PurchaseRequestHeader[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PurchaseRequestHeaderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PurchaseRequestHeader::class);
    }

    public function add(PurchaseRequestHeader $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(PurchaseRequestHeader $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return PurchaseRequestHeader[] Returns an array of PurchaseRequestHeader objects
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

//    public function findOneBySomeField($value): ?PurchaseRequestHeader
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
