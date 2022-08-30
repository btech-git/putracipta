<?php

namespace App\Common\Data\Operator;

class FilterNotBetween implements FilterOperator
{
    public function getValueCount(): int
    {
        return 2;
    }
}
