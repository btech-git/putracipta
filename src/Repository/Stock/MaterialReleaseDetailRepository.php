<?php

namespace App\Repository\Stock;

use App\Entity\Stock\MaterialReleaseDetail;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<MaterialReleaseDetail>
 *
 * @method MaterialReleaseDetail|null find($id, $lockMode = null, $lockVersion = null)
 * @method MaterialReleaseDetail|null findOneBy(array $criteria, array $orderBy = null)
 * @method MaterialReleaseDetail[]    findAll()
 * @method MaterialReleaseDetail[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MaterialReleaseDetailRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MaterialReleaseDetail::class);
    }

    public function save(MaterialReleaseDetail $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(MaterialReleaseDetail $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return MaterialReleaseDetail[] Returns an array of MaterialReleaseDetail objects
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

//    public function findOneBySomeField($value): ?MaterialReleaseDetail
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
