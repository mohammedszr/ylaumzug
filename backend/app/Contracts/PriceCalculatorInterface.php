<?php

namespace App\Contracts;

interface PriceCalculatorInterface
{
    public function calculate(array $quoteData): float;
    public function getBreakdown(array $quoteData): array;
}