<?php

namespace App\Services;

use App\Contracts\PriceCalculatorInterface;
use App\Contracts\DistanceCalculatorInterface;
use App\Services\Calculators\MovingPriceCalculator;
use App\Services\Calculators\CleaningPriceCalculator;
use App\Services\Calculators\DeclutterPriceCalculator;
use App\Services\Calculators\DiscountCalculator;
use App\Models\Setting;
use Illuminate\Support\Facades\Log;

class PricingService implements PriceCalculatorInterface
{
    public function __construct(
        private DistanceCalculatorInterface $distanceCalculator,
        private MovingPriceCalculator $movingCalculator,
        private CleaningPriceCalculator $cleaningCalculator,
        private DeclutterPriceCalculator $declutterCalculator,
        private DiscountCalculator $discountCalculator
    ) {}

    public function calculate(array $quoteData): float
    {
        $total = 0;
        $services = $quoteData['ausgewaehlte_services'] ?? [];
        $details = $quoteData['service_details'] ?? [];

        foreach ($services as $service) {
            $total += match($service) {
                'umzug' => $this->calculateMovingPrice($details['moving'] ?? []),
                'putzservice' => $this->calculateCleaningPrice($details['cleaning'] ?? []),
                'entruempelung' => $this->calculateDeclutterPrice($details['declutter'] ?? []),
                default => 0
            };
        }

        // Add distance-based costs for moving
        if (in_array('umzug', $services)) {
            $total += $this->calculateDistanceCost($quoteData);
        }

        return round($total, 2);
    }

    public function getBreakdown(array $quoteData): array
    {
        // Return detailed price breakdown for transparency
        return [
            'base_services' => [],
            'distance_cost' => 0,
            'additional_fees' => [],
            'discounts' => [],
            'total' => $this->calculate($quoteData)
        ];
    }

    /**
     * Calculate total pricing for all selected services (enhanced method)
     */
    public function calculateTotal(array $services, array $serviceDetails): array
    {
        Log::info('Starting enhanced pricing calculation', [
            'services' => $services,
            'details_keys' => array_keys($serviceDetails)
        ]);

        $breakdown = [];
        $totalCost = 0.0;

        // Calculate each service using specialized calculators
        foreach ($services as $serviceKey) {
            try {
                $calculator = $this->getCalculatorForService($serviceKey);
                
                if (!$calculator) {
                    Log::warning("No calculator found for service: {$serviceKey}");
                    continue;
                }

                // Get service-specific data
                $serviceData = $this->getServiceData($serviceKey, $serviceDetails);
                
                // Validate data
                $validationErrors = $calculator->validateData($serviceData);
                if (!empty($validationErrors)) {
                    Log::warning("Validation errors for {$serviceKey}", $validationErrors);
                    continue;
                }

                // Calculate price
                $result = $calculator->calculate($serviceData);
                
                if ($result['cost'] > 0) {
                    $breakdown[] = $result;
                    $totalCost += $result['cost'];
                }

            } catch (\Exception $e) {
                Log::error("Error calculating {$serviceKey} cost", [
                    'error' => $e->getMessage(),
                    'service' => $serviceKey
                ]);
                continue;
            }
        }

        // Apply combination discount if multiple services
        if (count($services) > 1) {
            $discount = $this->discountCalculator->calculateCombinationDiscount($services, $totalCost);
            if ($discount['discount'] > 0) {
                $breakdown[] = [
                    'service' => 'Kombinationsrabatt',
                    'cost' => -$discount['discount'],
                    'details' => [$discount['description']]
                ];
                $totalCost -= $discount['discount'];
            }
        }

        // Apply express surcharge if needed
        $generalInfo = $serviceDetails['generalInfo'] ?? [];
        if (($generalInfo['urgency'] ?? '') === 'express') {
            $surcharge = $this->discountCalculator->calculateExpressSurcharge($totalCost);
            if ($surcharge['surcharge'] > 0) {
                $breakdown[] = [
                    'service' => 'Express-Zuschlag',
                    'cost' => $surcharge['surcharge'],
                    'details' => [$surcharge['description']]
                ];
                $totalCost += $surcharge['surcharge'];
            }
        }

        // Apply minimum order value
        $minimumOrder = Setting::getValue('pricing.minimum_order_value', 150);
        if ($totalCost < $minimumOrder) {
            $adjustment = $minimumOrder - $totalCost;
            $breakdown[] = [
                'service' => 'Mindestbestellwert',
                'cost' => $adjustment,
                'details' => ["Mindestbestellwert: {$minimumOrder}€"]
            ];
            $totalCost = $minimumOrder;
        }

        $result = [
            'total' => round($totalCost, 2),
            'breakdown' => $breakdown,
            'currency' => 'EUR',
            'calculation_date' => now()->toISOString()
        ];

        Log::info('Enhanced pricing calculation completed', [
            'total' => $result['total'],
            'services_count' => count($services)
        ]);

        return $result;
    }

    /**
     * Get the appropriate calculator for a service
     */
    private function getCalculatorForService(string $serviceKey): ?object
    {
        return match($serviceKey) {
            'umzug' => $this->movingCalculator,
            'entruempelung' => $this->declutterCalculator,
            'putzservice' => $this->cleaningCalculator,
            default => null
        };
    }

    /**
     * Extract service-specific data from the request
     */
    private function getServiceData(string $serviceKey, array $serviceDetails): array
    {
        $serviceData = $serviceDetails["{$serviceKey}Details"] ?? [];
        $generalInfo = $serviceDetails['generalInfo'] ?? [];
        
        // Merge service-specific data with general info
        return array_merge($serviceData, $generalInfo, [
            'service_key' => $serviceKey
        ]);
    }

    private function calculateMovingPrice(array $details): float
    {
        $basePrice = 150; // Base moving service
        $roomMultiplier = ($details['rooms'] ?? 1) * 50;
        $floorMultiplier = ($details['floors'] ?? 0) * 25;
        
        return $basePrice + $roomMultiplier + $floorMultiplier;
    }

    private function calculateCleaningPrice(array $details): float
    {
        $basePrice = 80;
        $roomMultiplier = ($details['rooms'] ?? 1) * 30;
        
        return $basePrice + $roomMultiplier;
    }

    private function calculateDeclutterPrice(array $details): float
    {
        $basePrice = 120;
        $volumeMultiplier = ($details['volume'] ?? 1) * 40;
        
        return $basePrice + $volumeMultiplier;
    }

    private function calculateDistanceCost(array $quoteData): float
    {
        if (empty($quoteData['from_postal_code']) || empty($quoteData['to_postal_code'])) {
            return 0;
        }

        $result = $this->distanceCalculator->calculateDistance(
            $quoteData['from_postal_code'],
            $quoteData['to_postal_code']
        );

        if (!$result['success']) {
            return 0;
        }

        $distance = $result['distance_km'];
        
        // Free up to 30km, then €1.50 per km
        return $distance > 30 ? ($distance - 30) * 1.5 : 0;
    }
}