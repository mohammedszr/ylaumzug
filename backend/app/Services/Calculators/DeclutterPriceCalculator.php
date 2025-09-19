<?php

namespace App\Services\Calculators;

use App\Models\Setting;

class DeclutterPriceCalculator
{
    public function calculate(array $details): array
    {
        $breakdown = [];
        $total = 0;

        // Base price from settings
        $basePrice = Setting::getValue('pricing.entruempelung.base_price', 120);
        $breakdown[] = "Grundpreis: {$basePrice}€";
        $total += $basePrice;

        // Size-based pricing
        $sizeMultipliers = [
            'small' => 1,
            'medium' => 2,
            'large' => 3,
            'very-large' => 4
        ];

        $size = $details['size'] ?? 'medium';
        $multiplier = $sizeMultipliers[$size] ?? 2;
        $volumePrice = Setting::getValue('pricing.entruempelung.price_per_volume', 40);
        $volumeCost = $multiplier * $volumePrice;
        $breakdown[] = "Volumen ({$size}): {$volumeCost}€";
        $total += $volumeCost;

        // Object type surcharge
        $objectType = $details['objectType'] ?? 'apartment';
        if ($objectType === 'house') {
            $surcharge = Setting::getValue('pricing.entruempelung.house_surcharge', 100);
            $breakdown[] = "Haus-Zuschlag: {$surcharge}€";
            $total += $surcharge;
        } elseif ($objectType === 'basement') {
            $surcharge = Setting::getValue('pricing.entruempelung.basement_surcharge', 50);
            $breakdown[] = "Keller-Zuschlag: {$surcharge}€";
            $total += $surcharge;
        }

        return [
            'service' => 'Entrümpelung',
            'cost' => round($total, 2),
            'details' => $breakdown
        ];
    }

    public function validateData(array $data): array
    {
        $errors = [];
        
        if (empty($data['objectType'])) {
            $errors[] = 'Objekttyp ist erforderlich';
        }
        
        if (empty($data['size'])) {
            $errors[] = 'Größe ist erforderlich';
        }
        
        return $errors;
    }
}