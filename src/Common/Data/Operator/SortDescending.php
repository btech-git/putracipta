<?php

namespace App\Common\Data\Operator;

class SortDescending implements SortOperatorInterface
{
    public function getLabel(): string
    {
        return 'Descending';
    }
}
