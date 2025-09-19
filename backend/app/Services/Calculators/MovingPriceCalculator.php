<?php

namespace App\Services\Calculators;

use App\Models\Setting;
use App\Contracts\DistanceCalculatorInterface;

class MovingPriceCalculator
{
    public function __construct(
        private DistanceCalculatorInterface $distanceCalculator
    ) {}

    public function calculate(array $details): array
    {
        $breakdown = [];
        $total = 0;

        // Base price from settings
        $basePrice = Setting::getValue('pricing.umzug.base_price', 150);
        $breakdown[] = "Grundpreis: {$basePrice}€";
        $total += $basePrice;

        // Room-based pricing
        $rooms = $details['rooms'] ?? 1;
        $roomPrice = Setting::getValue('pricing.umzug.price_per_room', 50);
        $roomCost = $rooms * $roomPrice;
        $breakdown[] = "Zimmer ({$rooms}x): {$roomCost}€";
        $total += $roomCost;

        // Floor surcharge
        $floors = $details['floors'] ?? 0;
        if ($floors > 2) {
            $floorPrice = Setting::getValue('pricing.umzug.floor_surcharge', 25);
            $floorCost = ($floors - 2) * $floorPrice;
            $breakdown[] = "Etagen-Zuschlag: {$floorCost}€";
            $total += $floorCost;
        }

        // Distance calculation
        if (!empty($details['fromAddress']['postalCode']) && !empty($details['toAddress']['postalCode'])) {
            $distanceCost = $this->calculateDistanceCost(
                $details['fromAddress']['postalCode'],
                $details['toAddress']['postalCode']
            );
            if ($distanceCost > 0) {
                $breakdown[] = "Entfernungskosten: {$distanceCost}€";
                $total += $distanceCost;
            }
        }

        return [
            'service' => 'Umzug',
            'cost' => round($total, 2),
            'details' => $breakdown
        ];
    }

    private function calculateDistanceCost(string $fromPostalCode, string $toPostalCode): float
    {
        try {
            $result = $this->distanceCalculator->calculateDistance($fromPostalCode, $toPostalCode);
            
            if (!$result['success']) {
                return 0;
            }

            $distance = $result['distance_km'];
            $freeDistance = Setting::getValue('pricing.umzug.free_distance_km', 30);
            $pricePerKm = Setting::getValue('pricing.umzug.price_per_km', 1.5);
            
            return $distance > $freeDistance ? ($distance - $freeDistance) * $pricePerKm : 0;
        } catch (\Exception $e) {
            return 0;
        }
    }

    public function validateData(array $data): array
    {
        $errors = [];
        
        if (empty($data['rooms']) || $data['rooms'] < 1) {
            $errors[] = 'Anzahl Zimmer ist erforderlich';
        }
        
        return $errors;
    }
}