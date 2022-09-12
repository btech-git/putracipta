<?php

namespace App\Common\Data\Operator;

class FilterNotBetween implements FilterOperatorInterface
{
    public function getValueCount(): int
    {
        return 2;
    }

    public function getLabel(): string
    {
        return 'Not Between';
    }
}
