<?php

namespace App\Common\Data\Operator;

class FilterEqual implements FilterOperator
{
    public function getValueCount(): int
    {
        return 1;
    }
}
