<?php

namespace App\Repository\AccessPoint;

use App\Entity\AccessPoint\ItemBoxKey;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ItemBoxKey>
 *
 * @method ItemBoxKey|null find($id, $lockMode = null, $lockVersion = null)
 * @method ItemBoxKey|null findOneBy(array $criteria, array $orderBy = null)
 * @method ItemBoxKey[]    findAll()
 * @method ItemBoxKey[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ItemBoxKeyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ItemBoxKey::class);
    }

    public function add(ItemBoxKey $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(ItemBoxKey $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return ItemBoxKey[] Returns an array of ItemBoxKey objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('i')
//            ->andWhere('i.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('i.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?ItemBoxKey
//    {
//        return $this->createQueryBuilder('i')
//            ->andWhere('i.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
