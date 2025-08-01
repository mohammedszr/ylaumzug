<?php

namespace App\Contracts;

use App\DTOs\PriceResult;

interface PriceCalculatorInterface
{
    /**
     * Calculate price for the given data
     *
     * @param array $data Input data for calculation
     * @return PriceResult
     */
    public function calculate(array $data): PriceResult;

    /**
     * Get the service key this calculator handles
     *
     * @return string
     */
    public function getServiceKey(): string;

    /**
     * Validate input data for this calculator
     *
     * @param array $data
     * @return array Validation errors (empty if valid)
     */
    public function validateData(array $data): array;
}