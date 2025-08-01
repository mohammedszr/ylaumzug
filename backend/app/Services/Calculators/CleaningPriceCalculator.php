<?php

namespace App\Services\Calculators;

use App\DTOs\PriceResult;

class CleaningPriceCalculator extends BaseCalculator
{
    public function getServiceKey(): string
    {
        return 'putzservice';
    }

    public function calculate(array $data): PriceResult
    {
        $this->logCalculation('Starting cleaning calculation', ['data_keys' => array_keys($data)]);

        $breakdown = [];
        $total = 0.0;

        // Base area cost
        $baseCost = $this->calculateBaseCost($data);
        if ($baseCost > 0) {
            $size = $this->getNumericValue($data, 'size');
            $intensity = $this->getStringValue($data, 'cleaningIntensity', 'normal');
            $intensityName = $this->getIntensityName($intensity);
            
            $breakdown[] = $this->formatBreakdownItem(
                "{$intensityName} ({$size}m²)",
                $baseCost
            );
            $total += $baseCost;
        }

        // Room-specific surcharges
        $roomCosts = $this->calculateRoomSurcharges($data);
        $breakdown = array_merge($breakdown, $roomCosts['breakdown']);
        $total += $roomCosts['total'];

        // Apply frequency discount
        $discount = $this->calculateFrequencyDiscount($data, $total);
        if ($discount > 0) {
            $frequency = $this->getStringValue($data, 'frequency', 'once');
            $breakdown[] = $this->formatBreakdownItem(
                "Regelmäßigkeitsrabatt ({$frequency})",
                -$discount
            );
            $total -= $discount;
        }

        // Apply minimum cost
        $minimumCost = $this->getSetting('minimum_cleaning_cost', 150);
        $total = $this->applyMinimumCost($total, $minimumCost);

        $this->logCalculation('Cleaning calculation completed', ['total' => $total]);

        return new PriceResult('Putzservice', $total, $breakdown);
    }

    public function validateData(array $data): array
    {
        $errors = [];

        // Validate size
        $size = $this->getNumericValue($data, 'size');
        if ($size <= 0) {
            $errors[] = 'Size must be greater than 0';
        }

        // Validate cleaning intensity
        $intensity = $this->getStringValue($data, 'cleaningIntensity');
        $validIntensities = ['normal', 'deep', 'construction', 'moveout'];
        if (!in_array($intensity, $validIntensities)) {
            $errors[] = 'Invalid cleaning intensity';
        }

        // Validate frequency
        $frequency = $this->getStringValue($data, 'frequency');
        $validFrequencies = ['once', 'weekly', 'biweekly', 'monthly'];
        if (!in_array($frequency, $validFrequencies)) {
            $errors[] = 'Invalid frequency';
        }

        return $errors;
    }

    /**
     * Calculate base cost based on area and intensity
     */
    private function calculateBaseCost(array $data): float
    {
        $size = $this->getNumericValue($data, 'size');
        $intensity = $this->getStringValue($data, 'cleaningIntensity', 'normal');
        
        $intensityRates = [
            'normal' => $this->getSetting('cleaning_rate_normal', 3.0),
            'deep' => $this->getSetting('cleaning_rate_deep', 5.0),
            'construction' => $this->getSetting('cleaning_rate_construction', 7.0),
            'moveout' => $this->getSetting('cleaning_rate_moveout', 6.0)
        ];

        $rate = $intensityRates[$intensity] ?? $intensityRates['normal'];
        
        return $size * $rate;
    }

    /**
     * Calculate room-specific surcharges
     */
    private function calculateRoomSurcharges(array $data): array
    {
        $breakdown = [];
        $total = 0.0;
        $rooms = $this->getArrayValue($data, 'rooms');
        $size = $this->getNumericValue($data, 'size');

        $roomSurcharges = [
            'windows' => [
                'cost' => $size * $this->getSetting('window_cleaning_rate', 2.0),
                'name' => 'Fensterreinigung'
            ],
            'kitchen' => [
                'cost' => $this->getSetting('kitchen_deep_clean_cost', 80.0),
                'name' => 'Küchen-Tiefenreinigung'
            ],
            'bathroom' => [
                'cost' => $this->getSetting('bathroom_deep_clean_cost', 60.0),
                'name' => 'Bad-Tiefenreinigung'
            ],
            'balcony' => [
                'cost' => $this->getSetting('balcony_clean_cost', 40.0),
                'name' => 'Balkonreinigung'
            ],
            'basement' => [
                'cost' => $this->getSetting('basement_clean_cost', 50.0),
                'name' => 'Kellerreinigung'
            ]
        ];

        foreach ($rooms as $room) {
            if (isset($roomSurcharges[$room])) {
                $surcharge = $roomSurcharges[$room];
                $breakdown[] = $this->formatBreakdownItem($surcharge['name'], $surcharge['cost']);
                $total += $surcharge['cost'];
            }
        }

        return ['breakdown' => $breakdown, 'total' => $total];
    }

    /**
     * Calculate frequency discount
     */
    private function calculateFrequencyDiscount(array $data, float $totalBeforeDiscount): float
    {
        $frequency = $this->getStringValue($data, 'frequency', 'once');
        
        $discountRates = [
            'weekly' => $this->getSetting('frequency_discount_weekly', 0.20),
            'biweekly' => $this->getSetting('frequency_discount_biweekly', 0.15),
            'monthly' => $this->getSetting('frequency_discount_monthly', 0.10),
            'once' => 0.0
        ];

        $discountRate = $discountRates[$frequency] ?? 0.0;
        
        return $totalBeforeDiscount * $discountRate;
    }

    /**
     * Get intensity display name
     */
    private function getIntensityName(string $intensity): string
    {
        $names = [
            'normal' => 'Grundreinigung',
            'deep' => 'Tiefenreinigung',
            'construction' => 'Baureinigung',
            'moveout' => 'Auszugsreinigung'
        ];

        return $names[$intensity] ?? 'Reinigung';
    }
}