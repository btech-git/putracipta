<?php

namespace App\Repository\Transaction;

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
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PurchaseRequestDetail::class);
    }

    public function add(PurchaseRequestDetail $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(PurchaseRequestDetail $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return PurchaseRequestDetail[] Returns an array of PurchaseRequestDetail objects
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

//    public function findOneBySomeField($value): ?PurchaseRequestDetail
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
