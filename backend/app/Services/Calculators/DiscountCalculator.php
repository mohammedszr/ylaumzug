<?php

namespace App\Services\Calculators;

use App\Models\Setting;

class DiscountCalculator
{
    public function calculateCombinationDiscount(array $services, float $totalCost): array
    {
        $serviceCount = count($services);
        
        if ($serviceCount < 2) {
            return ['discount' => 0, 'description' => ''];
        }

        $discountPercentage = match($serviceCount) {
            2 => Setting::getValue('pricing.discounts.two_services', 10),
            default => Setting::getValue('pricing.discounts.three_plus_services', 15)
        };

        $discount = ($totalCost * $discountPercentage) / 100;

        return [
            'discount' => round($discount, 2),
            'description' => "Kombinationsrabatt ({$discountPercentage}% für {$serviceCount} Services)"
        ];
    }

    public function calculateExpressSurcharge(float $totalCost): array
    {
        $surchargePercentage = Setting::getValue('pricing.surcharges.express', 20);
        $surcharge = ($totalCost * $surchargePercentage) / 100;

        return [
            'surcharge' => round($surcharge, 2),
            'description' => "Express-Zuschlag ({$surchargePercentage}%)"
        ];
    }
}