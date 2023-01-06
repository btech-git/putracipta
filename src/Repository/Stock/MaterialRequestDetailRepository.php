<?php

namespace App\Repository\Stock;

use App\Entity\Stock\MaterialRequestDetail;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<MaterialRequestDetail>
 *
 * @method MaterialRequestDetail|null find($id, $lockMode = null, $lockVersion = null)
 * @method MaterialRequestDetail|null findOneBy(array $criteria, array $orderBy = null)
 * @method MaterialRequestDetail[]    findAll()
 * @method MaterialRequestDetail[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MaterialRequestDetailRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MaterialRequestDetail::class);
    }

    public function save(MaterialRequestDetail $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(MaterialRequestDetail $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return MaterialRequestDetail[] Returns an array of MaterialRequestDetail objects
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

//    public function findOneBySomeField($value): ?MaterialRequestDetail
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
