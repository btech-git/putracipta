<?php

namespace App\Common\Data\Operator;

interface FilterOperatorInterface
{
    public function getValueCount(): int;

    public function getLabel(): string;
}
