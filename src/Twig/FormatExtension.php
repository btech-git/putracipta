<?php

namespace App\Twig;

use App\Util\NumberWord;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class FormatExtension extends AbstractExtension
{
    public function getName()
    {
        return 'format_extension';
    }

    public function getFilters()
    {
        return [
            new TwigFilter('say', [$this, 'filterSay']),
        ];
    }

    public function filterSay($number)
    {
        return NumberWord::name($number);
    }
}
