<?php

namespace App\Form\Type\Operator;

class FilterNotBetween implements FilterOperator
{
    public function getValueCount(): int
    {
        return 2;
    }
}
