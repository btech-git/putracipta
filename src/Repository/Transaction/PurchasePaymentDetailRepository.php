<?php

namespace App\Repository\Transaction;

use App\Entity\Transaction\PurchasePaymentDetail;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PurchasePaymentDetail>
 *
 * @method PurchasePaymentDetail|null find($id, $lockMode = null, $lockVersion = null)
 * @method PurchasePaymentDetail|null findOneBy(array $criteria, array $orderBy = null)
 * @method PurchasePaymentDetail[]    findAll()
 * @method PurchasePaymentDetail[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PurchasePaymentDetailRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PurchasePaymentDetail::class);
    }

    public function add(PurchasePaymentDetail $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(PurchasePaymentDetail $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return PurchasePaymentDetail[] Returns an array of PurchasePaymentDetail objects
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

//    public function findOneBySomeField($value): ?PurchasePaymentDetail
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
