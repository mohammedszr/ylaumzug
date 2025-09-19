<?php

namespace Tests\Integration;

use Tests\TestCase;
use App\Services\OpenRouteServiceCalculator;
use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class OpenRouteServiceTest extends TestCase
{
    use RefreshDatabase;

    protected OpenRouteServiceCalculator $calculator;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create test API key setting
        Setting::create([
            'group_name' => 'api',
            'key_name' => 'openroute_api_key',
            'value' => 'test_api_key',
            'type' => 'string'
        ]);

        $this->calculator = new OpenRouteServiceCalculator();
    }

    public function test_calculates_distance_successfully(): void
    {
        // Mock successful API response
        Http::fake([
            'api.openrouteservice.org/geocode/search*' => Http::response([
                'features' => [
                    [
                        'geometry' => [
                            'coordinates' => [6.9969, 49.2401] // Saarbrücken coordinates
                        ]
                    ]
                ]
            ], 200),
            'api.openrouteservice.org/v2/matrix/driving-car' => Http::response([
                'distances' => [[0, 25000]], // 25km in meters
                'durations' => [[0, 1800]]   // 30 minutes in seconds
            ], 200)
        ]);

        $result = $this->calculator->calculateDistance('66111', '66112');

        $this->assertTrue($result['success']);
        $this->assertEquals(25.0, $result['distance_km']);
        $this->assertArrayHasKey('duration_minutes', $result);
        $this->assertArrayHasKey('calculated_at', $result);
    }

    public function test_handles_geocoding_failure(): void
    {
        // Mock failed geocoding response
        Http::fake([
            'api.openrouteservice.org/geocode/search*' => Http::response([
                'features' => [] // No results
            ], 200)
        ]);

        $result = $this->calculator->calculateDistance('00000', '11111');

        $this->assertFalse($result['success']);
        $this->assertNull($result['distance_km']);
        $this->assertArrayHasKey('error', $result);
    }

    public function test_handles_api_error_gracefully(): void
    {
        // Mock API error response
        Http::fake([
            'api.openrouteservice.org/*' => Http::response([
                'error' => 'API key invalid'
            ], 401)
        ]);

        $result = $this->calculator->calculateDistance('66111', '66112');

        $this->assertFalse($result['success']);
        $this->assertNull($result['distance_km']);
        $this->assertArrayHasKey('error', $result);
    }

    public function test_caches_distance_calculations(): void
    {
        // Mock successful API response
        Http::fake([
            'api.openrouteservice.org/geocode/search*' => Http::response([
                'features' => [
                    [
                        'geometry' => [
                            'coordinates' => [6.9969, 49.2401]
                        ]
                    ]
                ]
            ], 200),
            'api.openrouteservice.org/v2/matrix/driving-car' => Http::response([
                'distances' => [[0, 30000]], // 30km
                'durations' => [[0, 2100]]   // 35 minutes
            ], 200)
        ]);

        // First call should hit the API
        $result1 = $this->calculator->calculateDistance('66111', '66113');
        $this->assertTrue($result1['success']);

        // Second call should use cache
        $result2 = $this->calculator->calculateDistance('66111', '66113');
        $this->assertTrue($result2['success']);
        $this->assertEquals($result1['distance_km'], $result2['distance_km']);

        // Verify only one API call was made (due to caching)
        Http::assertSentCount(2); // 2 calls: geocoding for both postal codes
    }

    public function test_geocodes_german_postal_codes(): void
    {
        // Mock geocoding response for German postal code
        Http::fake([
            'api.openrouteservice.org/geocode/search*' => Http::response([
                'features' => [
                    [
                        'geometry' => [
                            'coordinates' => [6.9969, 49.2401]
                        ],
                        'properties' => [
                            'country' => 'Germany',
                            'locality' => 'Saarbrücken'
                        ]
                    ]
                ]
            ], 200)
        ]);

        $result = $this->calculator->geocodePostalCode('66111');

        $this->assertTrue($result['success']);
        $this->assertIsArray($result['coordinates']);
        $this->assertCount(2, $result['coordinates']);
        $this->assertEquals(6.9969, $result['coordinates'][0]);
        $this->assertEquals(49.2401, $result['coordinates'][1]);
    }

    public function test_handles_network_timeout(): void
    {
        // Mock timeout
        Http::fake([
            'api.openrouteservice.org/*' => function () {
                throw new \Exception('Connection timeout');
            }
        ]);

        $result = $this->calculator->calculateDistance('66111', '66112');

        $this->assertFalse($result['success']);
        $this->assertNull($result['distance_km']);
        $this->assertStringContainsString('timeout', strtolower($result['error']));
    }

    public function test_caches_failed_results_temporarily(): void
    {
        // Mock failed API response
        Http::fake([
            'api.openrouteservice.org/*' => Http::response([
                'error' => 'Service unavailable'
            ], 503)
        ]);

        // First call should hit the API and cache the failure
        $result1 = $this->calculator->calculateDistance('00000', '11111');
        $this->assertFalse($result1['success']);

        // Second call should use cached failure (no additional API call)
        $result2 = $this->calculator->calculateDistance('00000', '11111');
        $this->assertFalse($result2['success']);

        // Verify the failure was cached
        $cacheKey = 'distance:calc.00000.11111';
        $this->assertTrue(Cache::has($cacheKey));
    }

    public function test_validates_postal_code_format(): void
    {
        // Test with invalid postal codes
        $invalidCodes = ['', '1234', '123456', 'ABCDE', null];

        foreach ($invalidCodes as $code) {
            $result = $this->calculator->calculateDistance($code, '66111');
            
            // Should handle gracefully, either with validation error or API error
            $this->assertFalse($result['success']);
            $this->assertArrayHasKey('error', $result);
        }
    }
}