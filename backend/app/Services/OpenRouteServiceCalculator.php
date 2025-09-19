<?php

namespace App\Services;

use App\Contracts\DistanceCalculatorInterface;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class OpenRouteServiceCalculator implements DistanceCalculatorInterface
{
    private Client $client;
    private string $apiKey;
    private string $baseUrl = 'https://api.openrouteservice.org/v2';

    public function __construct()
    {
        $this->client = new Client(['timeout' => 30]);
        $this->apiKey = config('services.openroute.api_key');
        
        if (empty($this->apiKey)) {
            throw new \Exception('OpenRouteService API key not configured');
        }
    }

    public function calculateDistance(string $fromPostalCode, string $toPostalCode): array
    {
        $cacheKey = "distance_{$fromPostalCode}_{$toPostalCode}";
        
        return Cache::remember($cacheKey, 3600, function () use ($fromPostalCode, $toPostalCode) {
            try {
                $fromCoords = $this->geocodePostalCode($fromPostalCode);
                $toCoords = $this->geocodePostalCode($toPostalCode);
                
                $response = $this->client->post("{$this->baseUrl}/matrix/driving-car", [
                    'headers' => [
                        'Authorization' => $this->apiKey,
                        'Content-Type' => 'application/json'
                    ],
                    'json' => [
                        'locations' => [$fromCoords, $toCoords],
                        'metrics' => ['distance', 'duration']
                    ]
                ]);

                $data = json_decode($response->getBody(), true);
                
                return [
                    'distance_km' => round($data['distances'][0][1] / 1000, 2),
                    'duration_minutes' => round($data['durations'][0][1] / 60),
                    'from_coords' => $fromCoords,
                    'to_coords' => $toCoords,
                    'success' => true
                ];
                
            } catch (\Exception $e) {
                Log::error('Distance calculation failed', [
                    'from' => $fromPostalCode,
                    'to' => $toPostalCode,
                    'error' => $e->getMessage()
                ]);
                
                return [
                    'distance_km' => null,
                    'duration_minutes' => null,
                    'success' => false,
                    'error' => $e->getMessage()
                ];
            }
        });
    }

    public function geocodePostalCode(string $postalCode): array
    {
        $response = $this->client->get("{$this->baseUrl}/geocode/search", [
            'headers' => ['Authorization' => $this->apiKey],
            'query' => [
                'text' => $postalCode,
                'boundary.country' => 'DE',
                'size' => 1
            ]
        ]);

        $data = json_decode($response->getBody(), true);
        
        if (empty($data['features'])) {
            throw new \Exception("Postal code {$postalCode} not found");
        }

        return $data['features'][0]['geometry']['coordinates'];
    }
}