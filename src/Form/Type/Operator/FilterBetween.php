<?php

namespace App\Form\Type\Operator;

class FilterBetween implements FilterOperator
{
    public function getValueCount(): int
    {
        return 2;
    }
}
