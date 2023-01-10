<?php

namespace App\Common\Doctrine\Repository;

use App\Common\Data\Criteria\DataCriteria;
use App\Common\Data\Criteria\DataCriteriaPagination;
use Doctrine\ORM\QueryBuilder;

trait EntityDataFetch
{
    public function fetchData(DataCriteria $criteria, $callback = null, string $alias = 'e'): array
    {
        $count = $this->fetchCount($criteria, $callback);
        $pagination = $criteria->getPagination();
        if ($pagination->getSize() < 1) {
            $pagination->setSize(1);
        }
        $lastPageNumber = $count === 0 ? 1 : ceil($count / $pagination->getSize());
        if ($pagination->getNumber() < 1) {
            $pagination->setNumber(1);
        } else if ($pagination->getNumber() > $lastPageNumber) {
            $pagination->setNumber($lastPageNumber);
        }
        $objects = $this->fetchObjects($criteria, $callback, $alias);

        return [$count, $objects];
    }

    public function fetchCount(DataCriteria $criteria, $callback = null, string $alias = 'e'): int
    {
        $qb = $this->createQueryBuilder($alias);

        if ($callback !== null) {
            $callback($qb, $alias);
        }

        $this->processDataCriteria($qb, $alias, $criteria->getFilter(), null, null);
        $qb->select("COUNT({$alias})");

        try {
            $count = $qb->getQuery()->getSingleScalarResult();
            return $count === null ? 0 : $count;
        } catch (\Exception $e) {
            return 0;
        }
    }

    public function fetchObjects(DataCriteria $criteria, $callback = null, string $alias = 'e'): array
    {
        $qb = $this->createQueryBuilder($alias);

        if ($callback !== null) {
            $callback($qb, $alias);
        }

        $this->processDataCriteria($qb, $alias, $criteria->getFilter(), $criteria->getSort(), $criteria->getPagination());
        return $qb->getQuery()->getResult();
    }

    private function processDataCriteria(QueryBuilder $qb, string $alias, ?array $filter, ?array $sort, ?DataCriteriaPagination $pagination): void
    {
        if (!empty($filter)) {
            foreach ($filter as $field => $values) {
                $filterOperator = $values[0];
                if (!empty($filterOperator)) {
                    $filterValues = array_slice($values, 1);
                    (new $filterOperator)->addFilterToQueryBuilder($qb, $alias, $field, $filterValues);
                }
            }
        }
        if (!empty($sort)) {
            foreach ($sort as $field => $sortOperator) {
                if (!empty($sortOperator)) {
                    (new $sortOperator)->addSortToQueryBuilder($qb, $alias, $field);
                }
            }
        }
        if (!empty($pagination)) {
            $pageSize = $pagination->getSize();
            $pageNumber = $pagination->getNumber();
            $qb->setMaxResults($pageSize);
            $qb->setFirstResult(($pageNumber - 1) * $pageSize);
        }
    }
}
