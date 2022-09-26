<?php

namespace App\Repository\Accounting;

use App\Entity\Accounting\JournalVoucherHeader;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<JournalVoucherHeader>
 *
 * @method JournalVoucherHeader|null find($id, $lockMode = null, $lockVersion = null)
 * @method JournalVoucherHeader|null findOneBy(array $criteria, array $orderBy = null)
 * @method JournalVoucherHeader[]    findAll()
 * @method JournalVoucherHeader[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class JournalVoucherHeaderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, JournalVoucherHeader::class);
    }

    public function add(JournalVoucherHeader $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(JournalVoucherHeader $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return JournalVoucherHeader[] Returns an array of JournalVoucherHeader objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('j')
//            ->andWhere('j.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('j.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?JournalVoucherHeader
//    {
//        return $this->createQueryBuilder('j')
//            ->andWhere('j.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
