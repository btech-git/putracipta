<?php

namespace App\Common\Data\Operator;

use Doctrine\ORM\QueryBuilder;

class FilterBegin implements FilterOperatorInterface
{
    public function getValueCount(): int
    {
        return 1;
    }

    public function getLabel(): string
    {
        return 'Begin with';
    }

    public function addFilterToQueryBuilder(QueryBuilder $qb, string $alias, string $field, array $values): void
    {
        $qb->andWhere("{$alias}.{$field} LIKE :{$alias}_{$field}");
        $qb->setParameter("{$alias}_{$field}", "{$values[0]}%");
    }
}
