<?php

namespace App\Common\Data\Operator;

use Doctrine\ORM\QueryBuilder;

class FilterLessEqual implements FilterOperatorInterface
{
    public function getValueCount(): int
    {
        return 1;
    }

    public function getLabel(): string
    {
        return 'Less or equal';
    }

    public function addFilterToQueryBuilder(QueryBuilder $qb, string $alias, string $field, array $values): void
    {
        $qb->andWhere("{$alias}.{$field} <= :{$alias}_{$field}");
        $qb->setParameter("{$alias}_{$field}", $values[0]);
    }
}
