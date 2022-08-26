<?php

namespace App\Form\Type\Operator;

class FilterNotEqual implements FilterOperator
{
    public function getValueCount(): int
    {
        return 1;
    }
}
