<?php

namespace App\Common\Data\Operator;

class FilterNotEqual implements FilterOperator
{
    public function getValueCount(): int
    {
        return 1;
    }
}
