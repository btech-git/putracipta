<?php

namespace App\Common\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;

class NumberToFormattedTransformer implements DataTransformerInterface
{
    private int $decimals;
    private string $decimalSeparator;
    private string $thousandsSeparator;

    public function __construct(int $decimals, string $decimalSeparator, string $thousandsSeparator)
    {
        $this->decimals = $decimals;
        $this->decimalSeparator = $decimalSeparator;
        $this->thousandsSeparator = $thousandsSeparator;
    }

    public function transform($number)
    {
        return number_format($number, $this->decimals, $this->decimalSeparator, $this->thousandsSeparator);
    }

    public function reverseTransform($formatted)
    {
        return str_replace([$this->thousandsSeparator, $this->decimalSeparator], ['', '.'], $formatted);;
    }
}
