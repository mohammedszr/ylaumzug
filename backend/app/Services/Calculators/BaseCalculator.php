<?php

namespace App\Services\Calculators;

use App\Contracts\PriceCalculatorInterface;
use App\Models\Setting;
use Illuminate\Support\Facades\Log;

abstract class BaseCalculator implements PriceCalculatorInterface
{
    /**
     * Get setting value with fallback
     */
    protected function getSetting(string $key, mixed $default = null): mixed
    {
        return Setting::getValue($key, $default);
    }

    /**
     * Log calculation step for debugging
     */
    protected function logCalculation(string $step, array $data = []): void
    {
        Log::debug("Calculator [{$this->getServiceKey()}]: {$step}", $data);
    }

    /**
     * Format price breakdown item
     */
    protected function formatBreakdownItem(string $description, float $amount): string
    {
        return "{$description}: " . number_format($amount, 2) . "€";
    }

    /**
     * Safely get numeric value from data
     */
    protected function getNumericValue(array $data, string $key, float $default = 0.0): float
    {
        $value = $data[$key] ?? $default;
        return is_numeric($value) ? (float) $value : $default;
    }

    /**
     * Safely get string value from data
     */
    protected function getStringValue(array $data, string $key, string $default = ''): string
    {
        return (string) ($data[$key] ?? $default);
    }

    /**
     * Safely get array value from data
     */
    protected function getArrayValue(array $data, string $key, array $default = []): array
    {
        $value = $data[$key] ?? $default;
        return is_array($value) ? $value : $default;
    }

    /**
     * Apply minimum cost if needed
     */
    protected function applyMinimumCost(float $calculatedCost, float $minimumCost): float
    {
        return max($calculatedCost, $minimumCost);
    }
}