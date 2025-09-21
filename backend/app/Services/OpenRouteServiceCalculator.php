<?php

namespace App\Services;

use App\Contracts\DistanceCalculatorInterface;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class OpenRouteServiceCalculator implements DistanceCalculatorInterface
{
    private Client $client;
    private string $apiKey;
    private string $baseUrl = 'https://api.openrouteservice.org/v2';

    public function __construct()
    {
        $this->client = new Client(['timeout' => 30]);
        $this->apiKey = config('services.openroute.api_key', env('OPENROUTESERVICE_API_KEY'));
        
        if (empty($this->apiKey)) {
            Log::warning('OpenRouteService API key not configured, using fallback calculations');
        }
    }

    public function calculateDistance(string $fromPostalCode, string $toPostalCode): array
    {
        $cacheKey = "distance_{$fromPostalCode}_{$toPostalCode}";
        
        return Cache::remember($cacheKey, 3600, function () use ($fromPostalCode, $toPostalCode) {
            try {
                if (empty($this->apiKey)) {
                    return $this->calculateFallbackDistance($fromPostalCode, $toPostalCode);
                }

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
                
                return $this->calculateFallbackDistance($fromPostalCode, $toPostalCode);
            }
        });
    }

    /**
     * Calculate distance with detailed address information
     */
    public function calculateDistanceWithDetails(array $fromAddress, array $toAddress): array
    {
        try {
            $fromAddressString = $this->buildAddressString($fromAddress);
            $toAddressString = $this->buildAddressString($toAddress);
            
            // Create cache key
            $cacheKey = 'distance_details_' . md5($fromAddressString . '_' . $toAddressString);
            
            return Cache::remember($cacheKey, 86400, function () use ($fromAddress, $toAddress, $fromAddressString, $toAddressString) {
                return $this->performDetailedDistanceCalculation($fromAddress, $toAddress, $fromAddressString, $toAddressString);
            });
            
        } catch (\Exception $e) {
            Log::error('Detailed distance calculation failed', [
                'from' => $fromAddress,
                'to' => $toAddress,
                'error' => $e->getMessage()
            ]);
            
            return $this->getFallbackDistanceDetails($fromAddress, $toAddress);
        }
    }

    /**
     * Geocode address and return coordinates with details
     */
    public function geocodeAddressDetailed(string $address): ?array
    {
        try {
            if (empty($this->apiKey)) {
                return null;
            }

            $cacheKey = 'geocode_' . md5($address);
            
            return Cache::remember($cacheKey, 86400, function () use ($address) {
                $response = Http::withHeaders([
                    'Authorization' => $this->apiKey
                ])->get($this->baseUrl . '/geocode/search', [
                    'text' => $address,
                    'size' => 1
                ]);

                if (!$response->successful()) {
                    return null;
                }

                $data = $response->json();
                
                if (empty($data['features'])) {
                    return null;
                }

                $feature = $data['features'][0];
                $coordinates = $feature['geometry']['coordinates'];
                
                return [
                    'latitude' => $coordinates[1],
                    'longitude' => $coordinates[0],
                    'formatted_address' => $feature['properties']['label'] ?? $address,
                    'confidence' => $feature['properties']['confidence'] ?? 0
                ];
            });
            
        } catch (\Exception $e) {
            Log::error('Geocoding failed', ['address' => $address, 'error' => $e->getMessage()]);
            return null;
        }
    }

    public function geocodePostalCode(string $postalCode): array
    {
        if (empty($this->apiKey)) {
            throw new \Exception('API key not available');
        }

        $response = $this->client->get("{$this->baseUrl}/geocode/search", [
            'headers' => ['Authorization' => $this->apiKey],
            'query' => [
                'text' => $postalCode,
                'size' => 1
            ]
        ]);

        $data = json_decode($response->getBody(), true);
        
        if (empty($data['features'])) {
            throw new \Exception("Postal code {$postalCode} not found");
        }

        return $data['features'][0]['geometry']['coordinates'];
    }

    private function performDetailedDistanceCalculation(array $fromAddress, array $toAddress, string $fromAddressString, string $toAddressString): array
    {
        if (empty($this->apiKey)) {
            return $this->getFallbackDistanceDetails($fromAddress, $toAddress);
        }

        // Geocode both addresses
        $fromGeocode = $this->geocodeAddressDetailed($fromAddressString);
        $toGeocode = $this->geocodeAddressDetailed($toAddressString);

        if (!$fromGeocode || !$toGeocode) {
            return $this->getFallbackDistanceDetails($fromAddress, $toAddress);
        }

        // Calculate route
        $response = Http::withHeaders([
            'Authorization' => $this->apiKey,
            'Content-Type' => 'application/json'
        ])->post($this->baseUrl . '/directions/driving-car', [
            'coordinates' => [
                [$fromGeocode['longitude'], $fromGeocode['latitude']],
                [$toGeocode['longitude'], $toGeocode['latitude']]
            ],
            'format' => 'json',
            'instructions' => false
        ]);

        if (!$response->successful()) {
            return $this->getFallbackDistanceDetails($fromAddress, $toAddress);
        }

        $data = $response->json();
        
        if (!isset($data['routes'][0]['summary'])) {
            return $this->getFallbackDistanceDetails($fromAddress, $toAddress);
        }

        $summary = $data['routes'][0]['summary'];

        return [
            'distance_km' => round($summary['distance'] / 1000, 2),
            'duration_minutes' => round($summary['duration'] / 60, 0),
            'from_coordinates' => [
                'latitude' => $fromGeocode['latitude'],
                'longitude' => $fromGeocode['longitude']
            ],
            'to_coordinates' => [
                'latitude' => $toGeocode['latitude'],
                'longitude' => $toGeocode['longitude']
            ],
            'from_formatted_address' => $fromGeocode['formatted_address'],
            'to_formatted_address' => $toGeocode['formatted_address'],
            'success' => true
        ];
    }

    private function buildAddressString(array $address): string
    {
        $parts = [];
        
        if (!empty($address['street'])) {
            $parts[] = $address['street'];
        }
        
        if (!empty($address['postcode']) && !empty($address['city'])) {
            $parts[] = $address['postcode'] . ' ' . $address['city'];
        } elseif (!empty($address['city'])) {
            $parts[] = $address['city'];
        }
        
        return implode(', ', $parts);
    }

    private function calculateFallbackDistance(string $fromPostalCode, string $toPostalCode): array
    {
        // Extract numeric postal codes
        $fromPostal = (int) preg_replace('/\D/', '', $fromPostalCode);
        $toPostal = (int) preg_replace('/\D/', '', $toPostalCode);
        
        // Very basic postal code distance estimation (Germany)
        $postalDiff = abs($fromPostal - $toPostal);
        
        $distance = 50.0; // Default
        if ($postalDiff < 100) $distance = 5.0;
        elseif ($postalDiff < 500) $distance = 15.0;
        elseif ($postalDiff < 1000) $distance = 30.0;
        elseif ($postalDiff < 5000) $distance = 75.0;
        else $distance = 150.0;
        
        return [
            'distance_km' => $distance,
            'duration_minutes' => round($distance * 1.5, 0),
            'from_coords' => null,
            'to_coords' => null,
            'success' => false,
            'fallback' => true
        ];
    }

    private function getFallbackDistanceDetails(array $fromAddress, array $toAddress): array
    {
        $fromPostal = $fromAddress['postcode'] ?? '00000';
        $toPostal = $toAddress['postcode'] ?? '00000';
        
        $fallback = $this->calculateFallbackDistance($fromPostal, $toPostal);
        
        return [
            'distance_km' => $fallback['distance_km'],
            'duration_minutes' => $fallback['duration_minutes'],
            'from_coordinates' => ['latitude' => null, 'longitude' => null],
            'to_coordinates' => ['latitude' => null, 'longitude' => null],
            'from_formatted_address' => $this->buildAddressString($fromAddress),
            'to_formatted_address' => $this->buildAddressString($toAddress),
            'success' => false,
            'fallback' => true
        ];
    }
}