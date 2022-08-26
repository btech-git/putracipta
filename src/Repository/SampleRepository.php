<?php

namespace App\Repository;

use App\Entity\Sample;
use App\Form\Type\Operator\FilterEqual;
use App\Form\Type\Operator\FilterNotEqual;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Sample>
 *
 * @method Sample|null find($id, $lockMode = null, $lockVersion = null)
 * @method Sample|null findOneBy(array $criteria, array $orderBy = null)
 * @method Sample[]    findAll()
 * @method Sample[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SampleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sample::class);
    }

    public function match(?array $filter, ?array $sort, ?int $pageSize = null, ?int $pageNumber = null): array
    {
        $builder = $this->createQueryBuilder('p');
        if (!empty($filter)) {
            foreach ($filter as $field => $values) {
                if (empty($values[0])) {
                    continue;
                }
                switch ($values[0]) {
                    case FilterEqual::class:
                        $builder->andWhere("p.{$field} = :{$field}");
                        $builder->setParameter("{$field}", $values[1]);
                        break;
                    case FilterNotEqual::class:
                        $builder->andWhere("p.{$field} <> :{$field}");
                        $builder->setParameter("{$field}", $values[1]);
                        break;
                }
            }
        }
        if (!empty($sort)) {
            foreach ($sort as $field => $order) {
                if (!empty($order)) {
                    $builder->orderBy("p.{$field}", $order);
                }
            }
        }
        $builder->setMaxResults($pageSize ?? 10);
        $builder->setFirstResult((($pageNumber ?? 1) - 1) * $pageSize);
        return $builder->getQuery()->getResult();
    }

    public function add(Sample $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Sample $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return Sample[] Returns an array of Sample objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('s.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Sample
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
