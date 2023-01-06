<?php

namespace App\Repository\Stock;

use App\Entity\Stock\MaterialRequestHeader;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<MaterialRequestHeader>
 *
 * @method MaterialRequestHeader|null find($id, $lockMode = null, $lockVersion = null)
 * @method MaterialRequestHeader|null findOneBy(array $criteria, array $orderBy = null)
 * @method MaterialRequestHeader[]    findAll()
 * @method MaterialRequestHeader[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MaterialRequestHeaderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MaterialRequestHeader::class);
    }

    public function save(MaterialRequestHeader $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(MaterialRequestHeader $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return MaterialRequestHeader[] Returns an array of MaterialRequestHeader objects
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

//    public function findOneBySomeField($value): ?MaterialRequestHeader
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
