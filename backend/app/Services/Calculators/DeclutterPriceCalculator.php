<?php

namespace App\Services\Calculators;

use App\DTOs\PriceResult;

class DeclutterPriceCalculator extends BaseCalculator
{
    public function getServiceKey(): string
    {
        return 'entruempelung';
    }

    public function calculate(array $data): PriceResult
    {
        $this->logCalculation('Starting declutter calculation', ['data_keys' => array_keys($data)]);

        $breakdown = [];
        $total = 0.0;

        // Volume-based base cost
        $baseCost = $this->calculateVolumeCost($data);
        if ($baseCost > 0) {
            $volume = $this->getStringValue($data, 'volume', 'medium');
            $breakdown[] = $this->formatBreakdownItem("Volumen ({$volume})", $baseCost);
            $total += $baseCost;
        }

        // Waste type surcharges
        $wasteCosts = $this->calculateWasteSurcharges($data);
        $breakdown = array_merge($breakdown, $wasteCosts['breakdown']);
        $total += $wasteCosts['total'];

        // Floor surcharge
        $floorCost = $this->calculateFloorSurcharge($data);
        if ($floorCost > 0) {
            $floor = $this->getNumericValue($data, 'floor');
            $breakdown[] = $this->formatBreakdownItem("Treppenaufschlag ({$floor}. Stock)", $floorCost);
            $total += $floorCost;
        }

        // Access difficulty surcharge
        $accessCost = $this->calculateAccessDifficulty($data);
        if ($accessCost > 0) {
            $breakdown[] = $this->formatBreakdownItem("Erschwerter Zugang", $accessCost);
            $total += $accessCost;
        }

        // Clean handover
        $cleanCost = $this->calculateCleanHandover($data);
        if ($cleanCost > 0) {
            $breakdown[] = $this->formatBreakdownItem("Besenreine Übergabe", $cleanCost);
            $total += $cleanCost;
        }

        // Apply minimum cost
        $minimumCost = $this->getSetting('minimum_declutter_cost', 300);
        $total = $this->applyMinimumCost($total, $minimumCost);

        $this->logCalculation('Declutter calculation completed', ['total' => $total]);

        return new PriceResult('Entrümpelung', $total, $breakdown);
    }

    public function validateData(array $data): array
    {
        $errors = [];

        // Validate volume
        $volume = $this->getStringValue($data, 'volume');
        $validVolumes = ['low', 'medium', 'high', 'extreme'];
        if (!in_array($volume, $validVolumes)) {
            $errors[] = 'Invalid volume level';
        }

        // Validate object type
        $objectType = $this->getStringValue($data, 'objectType');
        $validTypes = ['apartment', 'house', 'basement', 'garage', 'office', 'attic'];
        if (!empty($objectType) && !in_array($objectType, $validTypes)) {
            $errors[] = 'Invalid object type';
        }

        return $errors;
    }

    /**
     * Calculate volume-based cost
     */
    private function calculateVolumeCost(array $data): float
    {
        $volume = $this->getStringValue($data, 'volume', 'medium');
        
        $volumePrices = [
            'low' => $this->getSetting('declutter_volume_low', 300),
            'medium' => $this->getSetting('declutter_volume_medium', 600),
            'high' => $this->getSetting('declutter_volume_high', 1200),
            'extreme' => $this->getSetting('declutter_volume_extreme', 2000)
        ];

        $baseCost = $volumePrices[$volume] ?? $volumePrices['medium'];

        // Apply object type multiplier
        $objectType = $this->getStringValue($data, 'objectType');
        $multiplier = $this->getObjectTypeMultiplier($objectType);
        
        return $baseCost * $multiplier;
    }

    /**
     * Get object type multiplier
     */
    private function getObjectTypeMultiplier(string $objectType): float
    {
        $multipliers = [
            'apartment' => 1.0,
            'house' => 1.2,
            'basement' => 0.8,
            'garage' => 0.9,
            'office' => 1.1,
            'attic' => 1.3
        ];

        return $multipliers[$objectType] ?? 1.0;
    }

    /**
     * Calculate waste type surcharges
     */
    private function calculateWasteSurcharges(array $data): array
    {
        $breakdown = [];
        $total = 0.0;
        $wasteTypes = $this->getArrayValue($data, 'wasteTypes');

        $wasteSurcharges = [
            'hazardous' => [
                'cost' => $this->getSetting('hazardous_waste_surcharge', 150.0),
                'name' => 'Sondermüll-Zuschlag'
            ],
            'electronics' => [
                'cost' => $this->getSetting('electronics_disposal_cost', 100.0),
                'name' => 'Elektrogeräte-Entsorgung'
            ],
            'construction' => [
                'cost' => $this->getSetting('construction_waste_cost', 200.0),
                'name' => 'Bauschutt-Entsorgung'
            ],
            'furniture' => [
                'cost' => $this->getSetting('furniture_disposal_cost', 80.0),
                'name' => 'Möbel-Entsorgung'
            ]
        ];

        foreach ($wasteTypes as $wasteType) {
            if (isset($wasteSurcharges[$wasteType])) {
                $surcharge = $wasteSurcharges[$wasteType];
                $breakdown[] = $this->formatBreakdownItem($surcharge['name'], $surcharge['cost']);
                $total += $surcharge['cost'];
            }
        }

        return ['breakdown' => $breakdown, 'total' => $total];
    }

    /**
     * Calculate floor surcharge
     */
    private function calculateFloorSurcharge(array $data): float
    {
        $floor = $this->getNumericValue($data, 'floor');
        $elevator = $this->getStringValue($data, 'elevator');
        
        if ($floor > 2 && $elevator !== 'yes') {
            $floorRate = $this->getSetting('declutter_floor_rate', 30.0);
            return ($floor - 2) * $floorRate;
        }

        return 0.0;
    }

    /**
     * Calculate access difficulty surcharge
     */
    private function calculateAccessDifficulty(array $data): float
    {
        $parking = $this->getStringValue($data, 'parking');
        
        if ($parking === 'difficult') {
            return $this->getSetting('access_difficulty_surcharge', 100.0);
        }

        return 0.0;
    }

    /**
     * Calculate clean handover cost
     */
    private function calculateCleanHandover(array $data): float
    {
        $cleanHandover = $this->getStringValue($data, 'cleanHandover');
        
        if ($cleanHandover === 'yes') {
            return $this->getSetting('clean_handover_cost', 150.0);
        }

        return 0.0;
    }
}