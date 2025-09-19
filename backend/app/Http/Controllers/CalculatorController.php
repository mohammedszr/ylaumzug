<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class CalculatorController extends Controller
{

    /**
     * Calculate pricing for selected services
     */
    public function calculate(Request $request): JsonResponse
    {
        try {
            $data = $request->all();
            
            // Basic calculation logic with fallback
            $selectedServices = $data['selectedServices'] ?? $data['services'] ?? [];
            $total = 0;
            $breakdown = [];
            
            foreach ($selectedServices as $service) {
                $cost = $this->calculateServiceCost($service, $data);
                $breakdown[] = [
                    'service' => ucfirst($service),
                    'cost' => $cost,
                    'details' => $this->getServiceDetails($service, $data)
                ];
                $total += $cost;
            }
            
            return response()->json([
                'success' => true,
                'data' => [
                    'services' => $breakdown,
                    'total_cost' => $total,
                    'currency' => 'EUR',
                    'calculation_id' => 'calc_' . uniqid(),
                    'breakdown' => [
                        'base_costs' => array_combine($selectedServices, array_column($breakdown, 'cost')),
                        'additional_costs' => ['rooms' => 0, 'distance' => 0, 'floors' => 0],
                        'discounts' => [],
                        'total' => $total
                    ]
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Calculator error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'data' => $request->all()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Fehler bei der Preisberechnung. Bitte versuchen Sie es erneut.',
                'error_code' => 'CALCULATION_ERROR'
            ], 500);
        }
    }

    /**
     * Get available services and their base pricing
     */
    public function getServices(): JsonResponse
    {
        try {
            // Static services for reliable operation
            $services = [
                [
                    'id' => 'umzug',
                    'name' => 'Umzug',
                    'description' => 'Professioneller Umzugsservice',
                    'base_price' => 150.00,
                    'is_active' => true
                ],
                [
                    'id' => 'putzservice',
                    'name' => 'Putzservice',
                    'description' => 'Gründliche Reinigung',
                    'base_price' => 80.00,
                    'is_active' => true
                ],
                [
                    'id' => 'entruempelung',
                    'name' => 'Entrümpelung',
                    'description' => 'Entrümpelung und Entsorgung',
                    'base_price' => 120.00,
                    'is_active' => true
                ]
            ];
            
            return response()->json([
                'success' => true,
                'services' => $services
            ]);

        } catch (\Exception $e) {
            Log::error('Services error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Fehler beim Laden der Services',
                'error_code' => 'SERVICES_ERROR'
            ], 500);
        }
    }

    /**
     * Check if calculator is enabled
     */
    public function isEnabled(): JsonResponse
    {
        try {
            return response()->json([
                'enabled' => true,
                'available' => true,
                'message' => 'Calculator available'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Availability check error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'enabled' => true,
                'available' => true,
                'message' => 'Calculator available'
            ]);
        }
    }

    /**
     * Calculate cost for a specific service
     */
    private function calculateServiceCost(string $service, array $data): float
    {
        $basePrices = [
            'umzug' => 150.00,
            'putzservice' => 80.00,
            'entruempelung' => 120.00
        ];
        
        $basePrice = $basePrices[$service] ?? 100.00;
        
        // Add room-based pricing
        if (isset($data['movingDetails']['rooms'])) {
            $rooms = (int) $data['movingDetails']['rooms'];
            if ($rooms > 1) {
                $basePrice += ($rooms - 1) * 50; // €50 per additional room
            }
        }
        
        return $basePrice;
    }

    /**
     * Get service details for breakdown
     */
    private function getServiceDetails(string $service, array $data): array
    {
        $details = [];
        
        $basePrices = [
            'umzug' => 150.00,
            'putzservice' => 80.00,
            'entruempelung' => 120.00
        ];
        
        $details[] = "Grundpreis: " . number_format($basePrices[$service] ?? 100, 2) . " €";
        
        if (isset($data['movingDetails']['rooms'])) {
            $rooms = (int) $data['movingDetails']['rooms'];
            if ($rooms > 1) {
                $additionalCost = ($rooms - 1) * 50;
                $details[] = "Zimmer ({$rooms}): " . number_format($additionalCost, 2) . " €";
            }
        }
        
        $details[] = "Entfernung (ca. 0 km): 0.00 €";
        $details[] = "Etagen-Zuschlag: 0.00 €";
        
        return $details;
    }
}