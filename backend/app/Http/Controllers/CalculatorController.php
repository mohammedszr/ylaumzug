<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Services\PricingService;
use App\Http\Requests\CalculateRequest;

class CalculatorController extends Controller
{
    protected $pricingService;

    public function __construct(PricingService $pricingService)
    {
        $this->pricingService = $pricingService;
    }

    /**
     * Calculate pricing for selected services
     */
    public function calculate(CalculateRequest $request): JsonResponse
    {
        try {
            // Check if calculator is enabled
            if (!config('app.calculator_enabled', true)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Calculator is currently disabled'
                ], 503);
            }

            $services = $request->input('selectedServices', []);
            $serviceDetails = $request->only([
                'movingDetails',
                'cleaningDetails',
                'declutterDetails',
                'generalInfo'
            ]);

            // Calculate pricing using the pricing service
            $pricing = $this->pricingService->calculateTotal($services, $serviceDetails);

            return response()->json([
                'success' => true,
                'pricing' => $pricing,
                'currency' => 'EUR',
                'disclaimer' => 'Dies ist eine unverbindliche Schätzung. Das finale Angebot erhalten Sie nach unserer Besichtigung vor Ort.'
            ]);

        } catch (\Exception $e) {
            \Log::error('Calculator error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Fehler bei der Berechnung. Bitte versuchen Sie es erneut oder kontaktieren Sie uns direkt.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Get available services and their base pricing
     */
    public function getServices(): JsonResponse
    {
        try {
            $services = $this->pricingService->getAvailableServices();
            
            return response()->json([
                'success' => true,
                'services' => $services
            ]);

        } catch (\Exception $e) {
            \Log::error('Get services error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Fehler beim Laden der Services'
            ], 500);
        }
    }
}