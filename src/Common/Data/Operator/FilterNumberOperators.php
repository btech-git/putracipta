<?php

namespace App\Common\Data\Operator;

class FilterNumberOperators implements FilterOperators
{
    public function getOperatorList(): array
    {
        return [
            FilterEqual::class => true,
            FilterNotEqual::class => true,
        ];
    }
}
