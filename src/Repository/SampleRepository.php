<?php

namespace App\Repository;

use App\Common\Data\Criteria\DataCriteria;
use App\Common\Data\Criteria\DataCriteriaPagination;
use App\Common\Data\Operator\FilterEqual;
use App\Common\Data\Operator\FilterNotEqual;
use App\Entity\Sample;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
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

    public function countBy(DataCriteria $criteria): int
    {
        $alias = 'e';
        $qb = $this->createQueryBuilderBy($alias, $criteria->getFilter(), null, null);
        $qb->select("COUNT({$alias})");

        try {
            $count = $qb->getQuery()->getSingleScalarResult();
            return $count === null ? 0 : $count;
        } catch (\Exception $e) {
            return 0;
        }
    }

    public function matchBy(DataCriteria $criteria): array
    {
        $qb = $this->createQueryBuilderBy('e', $criteria->getFilter(), $criteria->getSort(), $criteria->getPagination());

        return $qb->getQuery()->getResult();
    }

    private function createQueryBuilderBy(string $alias, ?array $filter, ?array $sort, ?DataCriteriaPagination $pagination): QueryBuilder
    {
        $qb = $this->createQueryBuilder($alias);
        if (!empty($filter)) {
            foreach ($filter as $field => $values) {
                if (empty($values[0])) {
                    continue;
                }
                switch ($values[0]) {
                    case FilterEqual::class:
                        $qb->andWhere("{$alias}.{$field} = :{$field}");
                        $qb->setParameter("{$field}", $values[1]);
                        break;
                    case FilterNotEqual::class:
                        $qb->andWhere("{$alias}.{$field} <> :{$field}");
                        $qb->setParameter("{$field}", $values[1]);
                        break;
                }
            }
        }
        if (!empty($sort)) {
            foreach ($sort as $field => $order) {
                if (!empty($order)) {
                    $qb->orderBy("{$alias}.{$field}", $order);
                }
            }
        }
        if (!empty($pagination)) {
            $pageSize = $pagination->getSize();
            $qb->setMaxResults($pageSize);
            $qb->setFirstResult(($pagination->getNumber() - 1) * $pageSize);
        }

        return $qb;
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
