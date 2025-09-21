<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use App\Services\Calculators\MovingPriceCalculator;
use App\Services\Calculators\CleaningPriceCalculator;
use App\Services\Calculators\DeclutterPriceCalculator;

class CalculatorController extends Controller
{
    private MovingPriceCalculator $movingCalculator;
    private CleaningPriceCalculator $cleaningCalculator;
    private DeclutterPriceCalculator $declutterCalculator;

    public function __construct(
        MovingPriceCalculator $movingCalculator,
        CleaningPriceCalculator $cleaningCalculator,
        DeclutterPriceCalculator $declutterCalculator
    ) {
        $this->movingCalculator = $movingCalculator;
        $this->cleaningCalculator = $cleaningCalculator;
        $this->declutterCalculator = $declutterCalculator;
    }

    /**
     * Calculate pricing for selected services
     */
    public function calculate(Request $request): JsonResponse
    {
        try {
            $data = $request->all();
            $selectedServices = $data['selectedServices'] ?? $data['services'] ?? [];
            
            $results = [];
            $totalCost = 0;
            $calculationDetails = [];
            
            foreach ($selectedServices as $service) {
                $result = $this->calculateServiceWithDetails($service, $data);
                if ($result) {
                    $results[] = $result;
                    $totalCost += $result['cost'];
                    $calculationDetails[$service] = $result['calculation_details'] ?? [];
                }
            }
            
            return response()->json([
                'success' => true,
                'data' => [
                    'services' => $results,
                    'total_cost' => round($totalCost, 2),
                    'currency' => 'EUR',
                    'calculation_id' => 'calc_' . uniqid(),
                    'calculation_details' => $calculationDetails,
                    'input_data' => $data,
                    'breakdown' => [
                        'base_costs' => array_combine($selectedServices, array_column($results, 'cost')),
                        'total' => round($totalCost, 2)
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
     * Calculate service with detailed breakdown using proper calculators
     */
    private function calculateServiceWithDetails(string $service, array $data): ?array
    {
        try {
            switch ($service) {
                case 'umzug':
                    return $this->calculateMovingService($data);
                case 'putzservice':
                    return $this->calculateCleaningService($data);
                case 'entruempelung':
                    return $this->calculateDeclutterService($data);
                default:
                    return null;
            }
        } catch (\Exception $e) {
            Log::error("Error calculating {$service}", [
                'error' => $e->getMessage(),
                'data' => $data
            ]);
            return null;
        }
    }

    /**
     * Calculate moving service using MovingPriceCalculator
     */
    private function calculateMovingService(array $data): array
    {
        // Extract moving details and flatten the structure
        $movingData = $data['movingDetails'] ?? [];
        
        // Map the data structure to what the calculator expects
        $calculatorData = array_merge($movingData, [
            'rooms' => $movingData['flat_rooms'] ?? $movingData['rooms'] ?? 1,
            'flat_rooms' => $movingData['flat_rooms'] ?? $movingData['rooms'] ?? 1
        ]);
        
        // Debug log to see what data we're passing
        Log::info('Moving calculator data', ['data' => $calculatorData]);
        
        $result = $this->movingCalculator->calculate($calculatorData);
        
        // Debug log to see the result
        Log::info('Moving calculator result', ['result' => $result]);
        
        return [
            'service' => 'Umzug',
            'cost' => $result['total'],
            'details' => $result['breakdown'],
            'calculation_details' => $result['details'] ?? []
        ];
    }

    /**
     * Calculate cleaning service using CleaningPriceCalculator
     */
    private function calculateCleaningService(array $data): array
    {
        $cleaningData = $data['cleaningDetails'] ?? [];
        
        try {
            $result = $this->cleaningCalculator->calculate($cleaningData);
            return [
                'service' => 'Putzservice',
                'cost' => $result['total'],
                'details' => $result['breakdown'] ?? [],
                'calculation_details' => $result
            ];
        } catch (\Exception $e) {
            Log::warning('Cleaning calculator failed, using fallback', ['error' => $e->getMessage()]);
            return [
                'service' => 'Putzservice',
                'cost' => 80.0,
                'details' => ['Grundpreis: €80.00'],
                'calculation_details' => ['fallback' => true]
            ];
        }
    }

    /**
     * Calculate declutter service using DeclutterPriceCalculator
     */
    private function calculateDeclutterService(array $data): array
    {
        $declutterData = $data['declutterDetails'] ?? [];
        
        try {
            $result = $this->declutterCalculator->calculate($declutterData);
            return [
                'service' => 'Entrümpelung',
                'cost' => $result['total'],
                'details' => $result['breakdown'] ?? [],
                'calculation_details' => $result
            ];
        } catch (\Exception $e) {
            Log::warning('Declutter calculator failed, using fallback', ['error' => $e->getMessage()]);
            return [
                'service' => 'Entrümpelung',
                'cost' => 120.0,
                'details' => ['Grundpreis: €120.00'],
                'calculation_details' => ['fallback' => true]
            ];
        }
    }
}