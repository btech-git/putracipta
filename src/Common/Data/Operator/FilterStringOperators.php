<?php

namespace App\Common\Data\Operator;

class FilterStringOperators implements FilterOperators
{
    public function getOperatorList(): array
    {
        return [
            FilterEqual::class => true,
            FilterNotEqual::class => true,
            FilterBetween::class => true,
        ];
    }
}
