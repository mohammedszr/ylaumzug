<?php

namespace App\Services;

use App\Models\Service;
use App\Models\PricingRule;
use App\Models\AdditionalService;
use App\Models\Setting;
use Illuminate\Support\Facades\Log;

/**
 * Advanced Pricing Calculation Engine
 * 
 * This service handles complex pricing calculations for all YLA Umzug services
 * using database-driven pricing rules and business logic.
 * 
 * Features:
 * - Database-driven pricing rules
 * - Multi-service combination discounts
 * - Distance-based calculations
 * - Volume and size-based pricing
 * - Additional service pricing
 * - Express service surcharges
 * 
 * @see backend/ADMIN_CONFIGURATION.md for pricing customization
 */
class PricingService
{
    /**
     * Calculate total pricing for all selected services
     * 
     * @param array $services Array of service keys (umzug, entruempelung, putzservice)
     * @param array $serviceDetails Detailed form data for each service
     * @return array Complete pricing breakdown with total
     */
    public function calculateTotal(array $services, array $serviceDetails): array
    {
        Log::info('Starting pricing calculation', [
            'services' => $services,
            'details_keys' => array_keys($serviceDetails)
        ]);

        $breakdown = [];
        $totalCost = 0;

        // Calculate each service individually
        foreach ($services as $serviceKey) {
            try {
                $serviceCost = $this->calculateServiceCost($serviceKey, $serviceDetails);
                
                if ($serviceCost['total'] > 0) {
                    $breakdown[] = [
                        'service' => $serviceCost['name'],
                        'cost' => $serviceCost['total'],
                        'details' => $serviceCost['breakdown']
                    ];
                    $totalCost += $serviceCost['total'];
                }
            } catch (\Exception $e) {
                Log::error("Error calculating {$serviceKey} cost", [
                    'error' => $e->getMessage(),
                    'service' => $serviceKey
                ]);
                
                // Continue with other services, don't fail completely
                continue;
            }
        }

        // Apply combination discount if multiple services
        if (count($services) > 1) {
            $discount = $this->calculateCombinationDiscount($totalCost, count($services));
            if ($discount > 0) {
                $breakdown[] = [
                    'service' => 'Kombinationsrabatt',
                    'cost' => -$discount,
                    'details' => [$this->getCombinationDiscountDescription(count($services))]
                ];
                $totalCost -= $discount;
            }
        }

        // Apply urgency surcharge if needed
        $generalInfo = $serviceDetails['generalInfo'] ?? [];
        if (($generalInfo['urgency'] ?? '') === 'express') {
            $surcharge = $this->calculateExpressSurcharge($totalCost);
            if ($surcharge > 0) {
                $breakdown[] = [
                    'service' => 'Express-Zuschlag',
                    'cost' => $surcharge,
                    'details' => [$this->getExpressSurchargeDescription()]
                ];
                $totalCost += $surcharge;
            }
        }

        // Apply minimum order value
        $minimumOrder = Setting::getValue('minimum_order_value', 150);
        if ($totalCost < $minimumOrder) {
            $totalCost = $minimumOrder;
            $breakdown[] = [
                'service' => 'Mindestbestellwert',
                'cost' => $minimumOrder - array_sum(array_column($breakdown, 'cost')),
                'details' => ["Mindestbestellwert: {$minimumOrder}€"]
            ];
        }

        $result = [
            'total' => round($totalCost, 2),
            'breakdown' => $breakdown,
            'currency' => 'EUR',
            'calculation_date' => now()->toISOString()
        ];

        Log::info('Pricing calculation completed', [
            'total' => $result['total'],
            'services_count' => count($services)
        ]);

        return $result;
    }

    /**
     * Calculate cost for a specific service using database-driven pricing rules
     * 
     * @param string $serviceKey Service identifier (umzug, entruempelung, putzservice)
     * @param array $serviceDetails All form data for calculation
     * @return array Service cost breakdown
     */
    private function calculateServiceCost(string $serviceKey, array $serviceDetails): array
    {
        $service = Service::findByKey($serviceKey);
        
        if (!$service || !$service->is_active) {
            Log::warning("Service not found or inactive: {$serviceKey}");
            return ['name' => $serviceKey, 'total' => 0, 'breakdown' => []];
        }

        $serviceData = $serviceDetails["{$serviceKey}Details"] ?? [];
        $generalInfo = $serviceDetails['generalInfo'] ?? [];
        
        // Combine all data for rule evaluation
        $calculationData = array_merge($serviceData, $generalInfo, [
            'service_key' => $serviceKey
        ]);

        $breakdown = [];
        $total = 0;

        // Apply all active pricing rules for this service
        $pricingRules = $service->getActivePricingRules();
        
        foreach ($pricingRules as $rule) {
            if ($rule->appliesTo($calculationData)) {
                $ruleCost = $rule->calculatePrice($calculationData);
                
                if ($ruleCost > 0) {
                    $breakdown[] = $rule->getCalculatedDescription($calculationData);
                    $total += $ruleCost;
                }
            }
        }

        // Add additional services if selected
        if (isset($serviceData['additionalServices']) && is_array($serviceData['additionalServices'])) {
            $additionalCosts = $this->calculateAdditionalServices(
                $service, 
                $serviceData['additionalServices'], 
                $calculationData
            );
            
            $breakdown = array_merge($breakdown, $additionalCosts['breakdown']);
            $total += $additionalCosts['total'];
        }

        // Apply service-specific calculations
        switch ($serviceKey) {
            case 'umzug':
                $specificCosts = $this->calculateMovingSpecifics($calculationData);
                break;
            case 'entruempelung':
                $specificCosts = $this->calculateDeclutterSpecifics($calculationData);
                break;
            case 'putzservice':
                $specificCosts = $this->calculateCleaningSpecifics($calculationData);
                break;
            default:
                $specificCosts = ['total' => 0, 'breakdown' => []];
        }

        $breakdown = array_merge($breakdown, $specificCosts['breakdown']);
        $total += $specificCosts['total'];

        return [
            'name' => $service->name,
            'total' => round($total, 2),
            'breakdown' => $breakdown
        ];
    }

    /**
     * Calculate additional services costs
     * 
     * @param Service $service Main service
     * @param array $selectedAddons Array of selected additional service keys
     * @param array $calculationData Data for calculations
     * @return array Additional services cost breakdown
     */
    private function calculateAdditionalServices(Service $service, array $selectedAddons, array $calculationData): array
    {
        $breakdown = [];
        $total = 0;

        $additionalServices = $service->getActiveAdditionalServices();

        foreach ($selectedAddons as $addonKey) {
            $addon = $additionalServices->firstWhere('key', $addonKey);
            
            if ($addon) {
                $quantity = $this->getAdditionalServiceQuantity($addonKey, $calculationData);
                $cost = $addon->calculatePrice($quantity);
                
                if ($cost > 0) {
                    $breakdown[] = $addon->getCalculatedDescription($quantity);
                    $total += $cost;
                }
            }
        }

        return [
            'total' => $total,
            'breakdown' => $breakdown
        ];
    }

    /**
     * Get quantity for additional service calculation
     */
    private function getAdditionalServiceQuantity(string $addonKey, array $data): float
    {
        switch ($addonKey) {
            case 'storage':
                return (float) ($data['apartmentSize'] ?? 1);
            case 'packing':
                return (float) ($data['boxes'] ?? 1);
            default:
                return 1; // Fixed price services
        }
    }

    /**
     * Calculate moving-specific costs (distance, floors, etc.)
     */
    private function calculateMovingSpecifics(array $data): array
    {
        $breakdown = [];
        $total = 0;

        // Distance calculation
        if (isset($data['fromAddress']['postalCode']) && isset($data['toAddress']['postalCode'])) {
            $distance = $this->calculateDistance(
                $data['fromAddress']['postalCode'],
                $data['toAddress']['postalCode']
            );
            
            $distanceRate = Setting::getValue('distance_rate_per_km', 2.0);
            $distanceCost = $distance * $distanceRate;
            
            $breakdown[] = "Entfernung ({$distance}km): {$distanceCost}€";
            $total += $distanceCost;
        }

        // Floor surcharges
        $floorCosts = $this->calculateFloorSurcharges($data);
        $breakdown = array_merge($breakdown, $floorCosts['breakdown']);
        $total += $floorCosts['total'];

        return [
            'total' => $total,
            'breakdown' => $breakdown
        ];
    }

    /**
     * Calculate floor surcharges for moving
     */
    private function calculateFloorSurcharges(array $data): array
    {
        $breakdown = [];
        $total = 0;
        
        $floorRate = Setting::getValue('floor_surcharge_rate', 50.0);

        // From address floor surcharge
        $fromFloor = (int) ($data['fromFloor'] ?? 0);
        if ($fromFloor > 2 && ($data['fromElevator'] ?? '') !== 'yes') {
            $cost = ($fromFloor - 2) * $floorRate;
            $breakdown[] = "Treppenaufschlag Auszug ({$fromFloor}. Stock): {$cost}€";
            $total += $cost;
        }

        // To address floor surcharge
        $toFloor = (int) ($data['toFloor'] ?? 0);
        if ($toFloor > 2 && ($data['toElevator'] ?? '') !== 'yes') {
            $cost = ($toFloor - 2) * $floorRate;
            $breakdown[] = "Treppenaufschlag Einzug ({$toFloor}. Stock): {$cost}€";
            $total += $cost;
        }

        return [
            'total' => $total,
            'breakdown' => $breakdown
        ];
    }

    /**
     * Calculate decluttering-specific costs
     */
    private function calculateDeclutterSpecifics(array $data): array
    {
        $breakdown = [];
        $total = 0;

        // Waste type specific surcharges
        $wasteTypes = $data['wasteTypes'] ?? [];
        
        if (in_array('hazardous', $wasteTypes)) {
            $cost = Setting::getValue('hazardous_waste_surcharge', 150.0);
            $breakdown[] = "Sondermüll-Zuschlag: {$cost}€";
            $total += $cost;
        }

        if (in_array('electronics', $wasteTypes)) {
            $cost = Setting::getValue('electronics_disposal_cost', 100.0);
            $breakdown[] = "Elektrogeräte-Entsorgung: {$cost}€";
            $total += $cost;
        }

        // Floor surcharge for decluttering
        $floor = (int) ($data['floor'] ?? 0);
        if ($floor > 2 && ($data['elevator'] ?? '') !== 'yes') {
            $floorRate = Setting::getValue('declutter_floor_rate', 30.0);
            $cost = ($floor - 2) * $floorRate;
            $breakdown[] = "Treppenaufschlag ({$floor}. Stock): {$cost}€";
            $total += $cost;
        }

        return [
            'total' => $total,
            'breakdown' => $breakdown
        ];
    }

    /**
     * Calculate cleaning-specific costs
     */
    private function calculateCleaningSpecifics(array $data): array
    {
        $breakdown = [];
        $total = 0;

        // Window cleaning addon
        $rooms = $data['rooms'] ?? [];
        if (in_array('windows', $rooms)) {
            $size = (int) ($data['size'] ?? 50);
            $windowRate = Setting::getValue('window_cleaning_rate', 2.0);
            $cost = $size * $windowRate;
            $breakdown[] = "Fensterreinigung ({$size}m²): {$cost}€";
            $total += $cost;
        }

        // Frequency discount
        $frequency = $data['frequency'] ?? 'once';
        if ($frequency !== 'once') {
            $discountRate = Setting::getValue('regular_cleaning_discount', 0.15);
            $discount = round($total * $discountRate);
            if ($discount > 0) {
                $breakdown[] = "Regelmäßigkeitsrabatt: -{$discount}€";
                $total -= $discount;
            }
        }

        return [
            'total' => $total,
            'breakdown' => $breakdown
        ];
    }

    /**
     * Calculate combination discount based on settings
     */
    private function calculateCombinationDiscount(float $totalCost, int $serviceCount): float
    {
        if ($serviceCount >= 3) {
            $discountRate = Setting::getValue('combination_discount_3_services', 0.15);
            return round($totalCost * $discountRate);
        } elseif ($serviceCount >= 2) {
            $discountRate = Setting::getValue('combination_discount_2_services', 0.10);
            return round($totalCost * $discountRate);
        }
        
        return 0;
    }

    /**
     * Calculate express service surcharge
     */
    private function calculateExpressSurcharge(float $totalCost): float
    {
        $surchargeRate = Setting::getValue('express_surcharge', 0.20);
        return round($totalCost * $surchargeRate);
    }

    /**
     * Get combination discount description
     */
    private function getCombinationDiscountDescription(int $serviceCount): string
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
    private function getExpressSurchargeDescription(): string
    {
        $rate = Setting::getValue('express_surcharge', 0.20) * 100;
        return "Express-Service Aufschlag ({$rate}%)";
    }

    /**
     * Calculate moving service costs (legacy method - kept for compatibility)
     */
    private function calculateMovingCost(array $details): array
    {
        $breakdown = [];
        $total = 0;

        // Base cost by apartment size
        $apartmentSize = (int) ($details['apartmentSize'] ?? 50);
        $baseCost = max(300, $apartmentSize * 8);
        $breakdown[] = "Grundpreis ({$apartmentSize}m²): {$baseCost}€";
        $total += $baseCost;

        // Distance calculation (simplified - would use Google Maps API in production)
        $fromAddress = $details['fromAddress'] ?? [];
        $toAddress = $details['toAddress'] ?? [];
        
        if (!empty($fromAddress['postalCode']) && !empty($toAddress['postalCode'])) {
            $distance = $this->calculateDistance($fromAddress['postalCode'], $toAddress['postalCode']);
            $distanceCost = $distance * 2; // 2€ per km
            $breakdown[] = "Entfernung ({$distance}km): {$distanceCost}€";
            $total += $distanceCost;
        }

        // Furniture and boxes
        $boxes = (int) ($details['boxes'] ?? 0);
        if ($boxes > 0) {
            $boxCost = $boxes * 3;
            $breakdown[] = "Kartons ({$boxes} Stück): {$boxCost}€";
            $total += $boxCost;
        }

        // Additional services
        $additionalServices = $details['additionalServices'] ?? [];
        foreach ($additionalServices as $service) {
            $cost = $this->getAdditionalServiceCost($service);
            if ($cost > 0) {
                $breakdown[] = $this->getAdditionalServiceName($service) . ": {$cost}€";
                $total += $cost;
            }
        }

        // Floor and elevator adjustments
        $fromFloor = (int) ($details['fromFloor'] ?? 0);
        $toFloor = (int) ($details['toFloor'] ?? 0);
        
        if ($fromFloor > 2 && ($details['fromElevator'] ?? '') !== 'yes') {
            $floorCost = ($fromFloor - 2) * 50;
            $breakdown[] = "Treppenaufschlag Auszug: {$floorCost}€";
            $total += $floorCost;
        }
        
        if ($toFloor > 2 && ($details['toElevator'] ?? '') !== 'yes') {
            $floorCost = ($toFloor - 2) * 50;
            $breakdown[] = "Treppenaufschlag Einzug: {$floorCost}€";
            $total += $floorCost;
        }

        return [
            'total' => $total,
            'breakdown' => $breakdown
        ];
    }

    /**
     * Calculate decluttering service costs
     */
    private function calculateDeclutterCost(array $details): array
    {
        $breakdown = [];
        $total = 0;

        // Base cost by volume
        $volume = $details['volume'] ?? 'medium';
        $volumePrices = [
            'low' => 300,
            'medium' => 600,
            'high' => 1200,
            'extreme' => 2000
        ];
        
        $baseCost = $volumePrices[$volume] ?? 600;
        $breakdown[] = "Volumen ({$volume}): {$baseCost}€";
        $total += $baseCost;

        // Waste type surcharges
        $wasteTypes = $details['wasteTypes'] ?? [];
        if (in_array('hazardous', $wasteTypes)) {
            $hazardousCost = 150;
            $breakdown[] = "Sondermüll-Zuschlag: {$hazardousCost}€";
            $total += $hazardousCost;
        }
        
        if (in_array('electronics', $wasteTypes)) {
            $electronicsCost = 100;
            $breakdown[] = "Elektrogeräte-Entsorgung: {$electronicsCost}€";
            $total += $electronicsCost;
        }

        // Clean handover
        if (($details['cleanHandover'] ?? '') === 'yes') {
            $cleaningCost = 150;
            $breakdown[] = "Besenreine Übergabe: {$cleaningCost}€";
            $total += $cleaningCost;
        }

        // Floor adjustment
        $floor = (int) ($details['floor'] ?? 0);
        if ($floor > 2 && ($details['elevator'] ?? '') !== 'yes') {
            $floorCost = ($floor - 2) * 30;
            $breakdown[] = "Treppenaufschlag: {$floorCost}€";
            $total += $floorCost;
        }

        return [
            'total' => $total,
            'breakdown' => $breakdown
        ];
    }

    /**
     * Calculate cleaning service costs
     */
    private function calculateCleaningCost(array $details): array
    {
        $breakdown = [];
        $total = 0;

        // Base cost by size and intensity
        $size = (int) ($details['size'] ?? 50);
        $intensity = $details['cleaningIntensity'] ?? 'normal';
        
        $intensityMultipliers = [
            'normal' => 3,
            'deep' => 5,
            'construction' => 7
        ];
        
        $multiplier = $intensityMultipliers[$intensity] ?? 3;
        $baseCost = $size * $multiplier;
        $breakdown[] = ucfirst($intensity) . "reinigung ({$size}m²): {$baseCost}€";
        $total += $baseCost;

        // Room-specific additions
        $rooms = $details['rooms'] ?? [];
        if (in_array('windows', $rooms)) {
            $windowCost = $size * 2;
            $breakdown[] = "Fensterreinigung: {$windowCost}€";
            $total += $windowCost;
        }

        // Frequency discount
        $frequency = $details['frequency'] ?? 'once';
        if ($frequency !== 'once') {
            $discount = round($total * 0.15);
            $breakdown[] = "Regelmäßigkeitsrabatt: -{$discount}€";
            $total -= $discount;
        }

        return [
            'total' => $total,
            'breakdown' => $breakdown
        ];
    }

    /**
     * Calculate combination discount
     */
    private function calculateCombinationDiscount(float $totalCost, int $serviceCount): float
    {
        if ($serviceCount >= 3) {
            return round($totalCost * 0.15); // 15% discount for 3+ services
        } elseif ($serviceCount >= 2) {
            return round($totalCost * 0.10); // 10% discount for 2+ services
        }
        
        return 0;
    }

    /**
     * Calculate distance between postal codes (simplified)
     */
    private function calculateDistance(string $from, string $to): int
    {
        // Simplified distance calculation
        // In production, this would use Google Maps Distance Matrix API
        $fromCode = (int) $from;
        $toCode = (int) $to;
        
        $difference = abs($fromCode - $toCode);
        
        // Rough estimation based on postal code difference
        if ($difference < 100) return 15;
        if ($difference < 500) return 35;
        if ($difference < 1000) return 65;
        if ($difference < 5000) return 120;
        
        return 200;
    }

    /**
     * Get additional service cost
     */
    private function getAdditionalServiceCost(string $service): int
    {
        $costs = [
            'assembly' => 200,
            'packing' => 150,
            'parking' => 80,
            'storage' => 100,
            'disposal' => 120
        ];
        
        return $costs[$service] ?? 0;
    }

    /**
     * Get additional service name
     */
    private function getAdditionalServiceName(string $service): string
    {
        $names = [
            'assembly' => 'Möbelabbau & Aufbau',
            'packing' => 'Verpackungsservice',
            'parking' => 'Halteverbotszone',
            'storage' => 'Einlagerung',
            'disposal' => 'Entsorgung'
        ];
        
        return $names[$service] ?? $service;
    }

    /**
     * Get available services
     */
    public function getAvailableServices(): array
    {
        return [
            [
                'id' => 'umzug',
                'name' => 'Umzug',
                'description' => 'Professioneller Umzugsservice mit Verpackung und Transport',
                'base_price' => 300
            ],
            [
                'id' => 'entruempelung',
                'name' => 'Entrümpelung',
                'description' => 'Haushaltsauflösung und fachgerechte Entsorgung',
                'base_price' => 300
            ],
            [
                'id' => 'putzservice',
                'name' => 'Putzservice',
                'description' => 'Grundreinigung und besenreine Übergabe',
                'base_price' => 150
            ]
        ];
    }
}