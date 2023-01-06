<?php

namespace App\Repository\Stock;

use App\Entity\Stock\MaterialReleaseHeader;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<MaterialReleaseHeader>
 *
 * @method MaterialReleaseHeader|null find($id, $lockMode = null, $lockVersion = null)
 * @method MaterialReleaseHeader|null findOneBy(array $criteria, array $orderBy = null)
 * @method MaterialReleaseHeader[]    findAll()
 * @method MaterialReleaseHeader[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MaterialReleaseHeaderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MaterialReleaseHeader::class);
    }

    public function save(MaterialReleaseHeader $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(MaterialReleaseHeader $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return MaterialReleaseHeader[] Returns an array of MaterialReleaseHeader objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('m.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?MaterialReleaseHeader
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
