<?php

namespace App\Repository\Transaction;

use App\Entity\Transaction\PurchaseInvoiceDetail;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PurchaseInvoiceDetail>
 *
 * @method PurchaseInvoiceDetail|null find($id, $lockMode = null, $lockVersion = null)
 * @method PurchaseInvoiceDetail|null findOneBy(array $criteria, array $orderBy = null)
 * @method PurchaseInvoiceDetail[]    findAll()
 * @method PurchaseInvoiceDetail[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PurchaseInvoiceDetailRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PurchaseInvoiceDetail::class);
    }

    public function add(PurchaseInvoiceDetail $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(PurchaseInvoiceDetail $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return PurchaseInvoiceDetail[] Returns an array of PurchaseInvoiceDetail objects
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

//    public function findOneBySomeField($value): ?PurchaseInvoiceDetail
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
