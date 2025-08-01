<?php

namespace App\Services;

use App\Services\Calculators\MovingPriceCalculator;
use App\Services\Calculators\DeclutterPriceCalculator;
use App\Services\Calculators\CleaningPriceCalculator;
use App\Services\Calculators\DiscountCalculator;
use App\Models\Setting;
use Illuminate\Support\Facades\Log;

/**
 * Refactored Pricing Calculation Engine
 * 
 * This service orchestrates multiple specialized calculators to provide
 * accurate pricing for all YLA Umzug services.
 * 
 * Features:
 * - Modular calculator architecture
 * - Multi-service combination discounts
 * - Express service surcharges
 * - Minimum order value enforcement
 * 
 * @see backend/ADMIN_CONFIGURATION.md for pricing customization
 */
class PricingService
{
    public function __construct(
        private MovingPriceCalculator $movingCalculator,
        private DeclutterPriceCalculator $declutterCalculator,
        private CleaningPriceCalculator $cleaningCalculator,
        private DiscountCalculator $discountCalculator
    ) {}

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
                
                if ($result->total > 0) {
                    $breakdown[] = $result->toArray();
                    $totalCost += $result->total;
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
        $minimumOrder = Setting::getValue('minimum_order_value', 150);
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

        Log::info('Pricing calculation completed', [
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
}