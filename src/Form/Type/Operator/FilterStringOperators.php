<?php

namespace App\Form\Type\Operator;

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
