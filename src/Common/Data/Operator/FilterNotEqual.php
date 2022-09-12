<?php

namespace App\Common\Data\Operator;

class FilterNotEqual implements FilterOperatorInterface
{
    public function getValueCount(): int
    {
        return 1;
    }

    public function getLabel(): string
    {
        return 'Not Equal';
    }
}
