<?php

namespace App\Form\Type\Operator;

class FilterEqual implements FilterOperator
{
    public function getValueCount(): int
    {
        return 1;
    }
}
