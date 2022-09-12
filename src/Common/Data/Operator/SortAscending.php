<?php

namespace App\Common\Data\Operator;

class SortAscending implements SortOperatorInterface
{
    public function getLabel(): string
    {
        return 'Ascending';
    }
}
