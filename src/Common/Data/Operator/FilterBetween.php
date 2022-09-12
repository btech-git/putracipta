<?php

namespace App\Common\Data\Operator;

class FilterBetween implements FilterOperatorInterface
{
    public function getValueCount(): int
    {
        return 2;
    }

    public function getLabel(): string
    {
        return 'Between';
    }
}
