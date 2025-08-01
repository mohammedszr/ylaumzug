<?php

namespace App\Services\Calculators;

use App\Models\Setting;

class DiscountCalculator
{
    /**
     * Calculate combination discount for multiple services
     */
    public function calculateCombinationDiscount(array $services, float $totalCost): array
    {
        $serviceCount = count($services);
        
        if ($serviceCount < 2) {
            return ['discount' => 0.0, 'description' => ''];
        }

        $discountRate = $this->getCombinationDiscountRate($serviceCount);
        $discount = $totalCost * $discountRate;

        // Add special combination bonuses
        $bonus = $this->calculateCombinationBonus($services);
        $discount += $bonus['amount'];

        $description = $this->getCombinationDiscountDescription($serviceCount, $bonus);

        return [
            'discount' => round($discount, 2),
            'description' => $description
        ];
    }

    /**
     * Calculate express service surcharge
     */
    public function calculateExpressSurcharge(float $totalCost): array
    {
        $surchargeRate = Setting::getValue('express_surcharge', 0.20);
        $surcharge = $totalCost * $surchargeRate;

        return [
            'surcharge' => round($surcharge, 2),
            'description' => $this->getExpressSurchargeDescription($surchargeRate)
        ];
    }

    /**
     * Get combination discount rate based on service count
     */
    private function getCombinationDiscountRate(int $serviceCount): float
    {
        if ($serviceCount >= 3) {
            return Setting::getValue('combination_discount_3_services', 0.15);
        } elseif ($serviceCount >= 2) {
            return Setting::getValue('combination_discount_2_services', 0.10);
        }
        
        return 0.0;
    }

    /**
     * Calculate special combination bonuses
     */
    private function calculateCombinationBonus(array $services): array
    {
        $bonus = 0.0;
        $descriptions = [];

        // Moving + Cleaning bonus
        if (in_array('umzug', $services) && in_array('putzservice', $services)) {
            $movingCleaningBonus = Setting::getValue('moving_cleaning_bonus', 50.0);
            $bonus += $movingCleaningBonus;
            $descriptions[] = 'Umzug-Reinigung Bonus';
        }

        // Decluttering + Cleaning bonus
        if (in_array('entruempelung', $services) && in_array('putzservice', $services)) {
            $declutterCleaningBonus = Setting::getValue('declutter_cleaning_bonus', 75.0);
            $bonus += $declutterCleaningBonus;
            $descriptions[] = 'Entrümpelung-Reinigung Bonus';
        }

        return [
            'amount' => $bonus,
            'descriptions' => $descriptions
        ];
    }

    /**
     * Get combination discount description
     */
    private function getCombinationDiscountDescription(int $serviceCount, array $bonus): string
    {
        $baseDescription = $this->getBaseCombinationDescription($serviceCount);
        
        if (!empty($bonus['descriptions'])) {
            $bonusText = implode(' + ', $bonus['descriptions']);
            return "{$baseDescription} + {$bonusText}";
        }

        return $baseDescription;
    }

    /**
     * Get base combination discount description
     */
    private function getBaseCombinationDescription(int $serviceCount): string
    {
        if ($serviceCount >= 3) {
            $rate = Setting::getValue('combination_discount_3_services', 0.15) * 100;
            return "Kombinationsrabatt für 3+ Services ({$rate}%)";
        } else {
            $rate = Setting::getValue('combination_discount_2_services', 0.10) * 100;
            return "Kombinationsrabatt für 2 Services ({$rate}%)";
        }
    }

    /**
     * Get express surcharge description
     */
    private function getExpressSurchargeDescription(float $rate): string
    {
        $percentage = $rate * 100;
        return "Express-Service Aufschlag ({$percentage}%)";
    }
}