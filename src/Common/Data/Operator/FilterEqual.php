<?php

namespace App\Common\Data\Operator;

class FilterEqual implements FilterOperatorInterface
{
    public function getValueCount(): int
    {
        return 1;
    }

    public function getLabel(): string
    {
        return 'Equal';
    }
}
