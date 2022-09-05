<?php

namespace App\Repository\Transaction;

use App\Entity\Transaction\PurchasePaymentHeader;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PurchasePaymentHeader>
 *
 * @method PurchasePaymentHeader|null find($id, $lockMode = null, $lockVersion = null)
 * @method PurchasePaymentHeader|null findOneBy(array $criteria, array $orderBy = null)
 * @method PurchasePaymentHeader[]    findAll()
 * @method PurchasePaymentHeader[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PurchasePaymentHeaderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PurchasePaymentHeader::class);
    }

    public function add(PurchasePaymentHeader $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(PurchasePaymentHeader $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return PurchasePaymentHeader[] Returns an array of PurchasePaymentHeader objects
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

//    public function findOneBySomeField($value): ?PurchasePaymentHeader
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
