<?php

namespace App\Common\Data\Operator;

class FilterBetween implements FilterOperator
{
    public function getValueCount(): int
    {
        return 2;
    }
}
