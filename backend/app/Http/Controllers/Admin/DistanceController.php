<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\QuoteRequest;
use App\Contracts\DistanceCalculatorInterface;
use Illuminate\Http\JsonResponse;

class DistanceController extends Controller
{
    public function __construct(
        private DistanceCalculatorInterface $distanceCalculator
    ) {}

    /**
     * Calculate distance for a quote request
     */
    public function calculateForQuote(QuoteRequest $quote): JsonResponse
    {
        if (empty($quote->from_postal_code) || empty($quote->to_postal_code)) {
            return response()->json([
                'success' => false,
                'message' => 'Postleitzahlen sind erforderlich'
            ], 400);
        }

        try {
            $result = $this->distanceCalculator->calculateDistance(
                $quote->from_postal_code,
                $quote->to_postal_code
            );

            if ($result['success']) {
                $quote->update(['distance_km' => $result['distance_km']]);

                return response()->json([
                    'success' => true,
                    'message' => 'Entfernung berechnet: ' . $result['distance_km'] . ' km',
                    'distance_km' => $result['distance_km'],
                    'duration_minutes' => $result['duration_minutes']
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Fehler bei Entfernungsberechnung: ' . $result['error']
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Fehler bei Entfernungsberechnung'
            ], 500);
        }
    }
}