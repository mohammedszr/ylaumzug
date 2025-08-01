<?php

namespace App\Services\Calculators;

use App\DTOs\PriceResult;

class MovingPriceCalculator extends BaseCalculator
{
    public function __construct(
        private DistanceCalculator $distanceCalculator
    ) {}

    public function getServiceKey(): string
    {
        return 'umzug';
    }

    public function calculate(array $data): PriceResult
    {
        $this->logCalculation('Starting moving calculation', ['data_keys' => array_keys($data)]);

        $breakdown = [];
        $total = 0.0;

        // Base cost calculation
        $baseCost = $this->calculateBaseCost($data);
        if ($baseCost > 0) {
            $breakdown[] = $this->formatBreakdownItem(
                "Grundpreis ({$this->getNumericValue($data, 'apartmentSize')}m²)",
                $baseCost
            );
            $total += $baseCost;
        }

        // Distance cost
        $distanceCost = $this->calculateDistanceCost($data);
        if ($distanceCost > 0) {
            $distance = $this->calculateDistance($data);
            $breakdown[] = $this->formatBreakdownItem("Entfernung ({$distance}km)", $distanceCost);
            $total += $distanceCost;
        }

        // Box handling cost
        $boxCost = $this->calculateBoxCost($data);
        if ($boxCost > 0) {
            $boxes = $this->getNumericValue($data, 'boxes');
            $breakdown[] = $this->formatBreakdownItem("Kartons ({$boxes} Stück)", $boxCost);
            $total += $boxCost;
        }

        // Floor surcharges
        $floorCosts = $this->calculateFloorSurcharges($data);
        $breakdown = array_merge($breakdown, $floorCosts['breakdown']);
        $total += $floorCosts['total'];

        // Additional services
        $additionalCosts = $this->calculateAdditionalServices($data);
        $breakdown = array_merge($breakdown, $additionalCosts['breakdown']);
        $total += $additionalCosts['total'];

        // Apply minimum cost
        $minimumCost = $this->getSetting('minimum_moving_cost', 300);
        $total = $this->applyMinimumCost($total, $minimumCost);

        $this->logCalculation('Moving calculation completed', ['total' => $total]);

        return new PriceResult('Umzug', $total, $breakdown);
    }

    public function validateData(array $data): array
    {
        $errors = [];

        // Validate apartment size
        $apartmentSize = $this->getNumericValue($data, 'apartmentSize');
        if ($apartmentSize <= 0) {
            $errors[] = 'Apartment size must be greater than 0';
        }

        // Validate addresses if provided
        $fromAddress = $this->getArrayValue($data, 'fromAddress');
        $toAddress = $this->getArrayValue($data, 'toAddress');

        if (!empty($fromAddress['postalCode']) && !empty($toAddress['postalCode'])) {
            if (!$this->distanceCalculator->isInServiceArea($fromAddress['postalCode'])) {
                $errors[] = 'From address is outside service area';
            }
            if (!$this->distanceCalculator->isInServiceArea($toAddress['postalCode'])) {
                $errors[] = 'To address is outside service area';
            }
        }

        return $errors;
    }

    /**
     * Calculate base cost based on apartment size
     */
    private function calculateBaseCost(array $data): float
    {
        $apartmentSize = $this->getNumericValue($data, 'apartmentSize');
        $ratePerSqm = $this->getSetting('base_price_per_sqm', 8.0);
        
        return $apartmentSize * $ratePerSqm;
    }

    /**
     * Calculate distance-based cost
     */
    private function calculateDistanceCost(array $data): float
    {
        $distance = $this->calculateDistance($data);
        $distanceRate = $this->getSetting('distance_rate', 2.0);
        
        return $distance * $distanceRate;
    }

    /**
     * Calculate distance between addresses
     */
    private function calculateDistance(array $data): int
    {
        $fromAddress = $this->getArrayValue($data, 'fromAddress');
        $toAddress = $this->getArrayValue($data, 'toAddress');

        $fromPostalCode = $fromAddress['postalCode'] ?? '';
        $toPostalCode = $toAddress['postalCode'] ?? '';

        if (empty($fromPostalCode) || empty($toPostalCode)) {
            return 0;
        }

        return $this->distanceCalculator->calculateDistance($fromPostalCode, $toPostalCode);
    }

    /**
     * Calculate box handling cost
     */
    private function calculateBoxCost(array $data): float
    {
        $boxes = $this->getNumericValue($data, 'boxes');
        $boxRate = $this->getSetting('box_handling_rate', 3.0);
        
        return $boxes * $boxRate;
    }

    /**
     * Calculate floor surcharges for both addresses
     */
    private function calculateFloorSurcharges(array $data): array
    {
        $breakdown = [];
        $total = 0.0;
        $floorRate = $this->getSetting('floor_surcharge', 50.0);

        // From address floor surcharge
        $fromFloor = $this->getNumericValue($data, 'fromFloor');
        $fromElevator = $this->getStringValue($data, 'fromElevator');
        
        if ($fromFloor > 2 && $fromElevator !== 'yes') {
            $cost = ($fromFloor - 2) * $floorRate;
            $breakdown[] = $this->formatBreakdownItem(
                "Treppenaufschlag Auszug ({$fromFloor}. Stock)",
                $cost
            );
            $total += $cost;
        }

        // To address floor surcharge
        $toFloor = $this->getNumericValue($data, 'toFloor');
        $toElevator = $this->getStringValue($data, 'toElevator');
        
        if ($toFloor > 2 && $toElevator !== 'yes') {
            $cost = ($toFloor - 2) * $floorRate;
            $breakdown[] = $this->formatBreakdownItem(
                "Treppenaufschlag Einzug ({$toFloor}. Stock)",
                $cost
            );
            $total += $cost;
        }

        return ['breakdown' => $breakdown, 'total' => $total];
    }

    /**
     * Calculate additional services cost
     */
    private function calculateAdditionalServices(array $data): array
    {
        $breakdown = [];
        $total = 0.0;
        $additionalServices = $this->getArrayValue($data, 'additionalServices');

        $servicePrices = [
            'assembly' => $this->getSetting('furniture_assembly_cost', 200.0),
            'packing' => $this->getSetting('packing_service_cost', 150.0),
            'parking' => $this->getSetting('parking_permit_cost', 80.0),
            'storage' => $this->getSetting('storage_service_cost', 100.0),
            'disposal' => $this->getSetting('disposal_service_cost', 120.0)
        ];

        $serviceNames = [
            'assembly' => 'Möbelabbau & Aufbau',
            'packing' => 'Verpackungsservice',
            'parking' => 'Halteverbotszone',
            'storage' => 'Einlagerung',
            'disposal' => 'Entsorgung'
        ];

        foreach ($additionalServices as $service) {
            if (isset($servicePrices[$service])) {
                $cost = $servicePrices[$service];
                $name = $serviceNames[$service] ?? $service;
                
                $breakdown[] = $this->formatBreakdownItem($name, $cost);
                $total += $cost;
            }
        }

        return ['breakdown' => $breakdown, 'total' => $total];
    }
}