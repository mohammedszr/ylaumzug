<?php

namespace App\Services\Calculators;

use App\Models\Setting;

class CleaningPriceCalculator
{
    public function calculate(array $details): array
    {
        $breakdown = [];
        $total = 0;

        // Base price from settings
        $basePrice = Setting::getValue('pricing.putzservice.base_price', 80);
        $breakdown[] = "Grundpreis: {$basePrice}€";
        $total += $basePrice;

        // Size-based pricing
        $sizeMultipliers = [
            '1-room' => 1,
            '2-rooms' => 1.5,
            '3-rooms' => 2,
            '4-rooms' => 2.5,
            '5-rooms' => 3,
            '6-rooms' => 3.5
        ];

        $size = $details['size'] ?? '3-rooms';
        $multiplier = $sizeMultipliers[$size] ?? 2;
        $roomPrice = Setting::getValue('pricing.putzservice.price_per_room', 30);
        $sizeCost = $multiplier * $roomPrice;
        $breakdown[] = "Größe ({$size}): {$sizeCost}€";
        $total += $sizeCost;

        // Intensity surcharge
        $intensity = $details['cleaningIntensity'] ?? 'normal';
        if ($intensity === 'deep') {
            $surcharge = Setting::getValue('pricing.putzservice.deep_cleaning_surcharge', 50);
            $breakdown[] = "Tiefenreinigung: {$surcharge}€";
            $total += $surcharge;
        } elseif ($intensity === 'construction') {
            $surcharge = Setting::getValue('pricing.putzservice.construction_cleaning_surcharge', 100);
            $breakdown[] = "Baureinigung: {$surcharge}€";
            $total += $surcharge;
        }

        return [
            'service' => 'Putzservice',
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