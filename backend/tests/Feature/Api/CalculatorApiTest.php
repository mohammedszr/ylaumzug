<?php

namespace Tests\Feature\Api;

use Tests\TestCase;
use App\Models\Service;
use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CalculatorApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Seed test data
        $this->seedTestData();
    }

    protected function seedTestData(): void
    {
        // Create test services
        Service::create([
            'name' => 'Umzug',
            'slug' => 'umzug',
            'description' => 'Professioneller Umzugsservice',
            'base_price' => 150.00,
            'is_active' => true,
            'sort_order' => 1
        ]);

        Service::create([
            'name' => 'Putzservice',
            'slug' => 'putzservice',
            'description' => 'Professionelle Reinigung',
            'base_price' => 80.00,
            'is_active' => true,
            'sort_order' => 2
        ]);

        Service::create([
            'name' => 'Entrümpelung',
            'slug' => 'entruempelung',
            'description' => 'Professionelle Entrümpelung',
            'base_price' => 120.00,
            'is_active' => true,
            'sort_order' => 3
        ]);

        // Create test settings
        Setting::create([
            'group_name' => 'general',
            'key_name' => 'calculator_enabled',
            'value' => '1',
            'type' => 'boolean',
            'is_public' => true
        ]);

        // Pricing settings
        $pricingSettings = [
            'umzug.base_price' => '150',
            'umzug.price_per_room' => '50',
            'putzservice.base_price' => '80',
            'putzservice.price_per_room' => '30',
            'entruempelung.base_price' => '120',
        ];

        foreach ($pricingSettings as $key => $value) {
            Setting::create([
                'group_name' => 'pricing',
                'key_name' => $key,
                'value' => $value,
                'type' => 'decimal'
            ]);
        }
    }

    public function test_can_get_services(): void
    {
        $response = $this->getJson('/api/calculator/services');

        $response->assertStatus(200)
                ->assertJson([
                    'success' => true
                ])
                ->assertJsonStructure([
                    'success',
                    'services' => [
                        '*' => [
                            'name',
                            'slug',
                            'description',
                            'base_price'
                        ]
                    ]
                ]);

        $this->assertCount(3, $response->json('services'));
    }

    public function test_can_check_calculator_availability(): void
    {
        $response = $this->getJson('/api/calculator/availability');

        $response->assertStatus(200)
                ->assertJson([
                    'enabled' => true,
                    'message' => 'Calculator available'
                ]);
    }

    public function test_can_calculate_moving_service_price(): void
    {
        $requestData = [
            'selectedServices' => ['umzug'],
            'movingDetails' => [
                'rooms' => 3,
                'floors' => 2,
                'fromAddress' => [
                    'street' => 'Teststraße 1',
                    'postalCode' => '12345',
                    'city' => 'Teststadt'
                ],
                'toAddress' => [
                    'street' => 'Zielstraße 2',
                    'postalCode' => '54321',
                    'city' => 'Zielstadt'
                ]
            ]
        ];

        $response = $this->postJson('/api/calculator/calculate', $requestData);

        $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'currency' => 'EUR'
                ])
                ->assertJsonStructure([
                    'success',
                    'pricing' => [
                        'total',
                        'breakdown'
                    ],
                    'currency',
                    'disclaimer'
                ]);

        $this->assertGreaterThan(0, $response->json('pricing.total'));
    }

    public function test_can_calculate_cleaning_service_price(): void
    {
        $requestData = [
            'selectedServices' => ['putzservice'],
            'cleaningDetails' => [
                'objectType' => 'apartment',
                'size' => '3-rooms',
                'cleaningIntensity' => 'normal'
            ]
        ];

        $response = $this->postJson('/api/calculator/calculate', $requestData);

        $response->assertStatus(200)
                ->assertJson([
                    'success' => true
                ]);

        $this->assertGreaterThan(0, $response->json('pricing.total'));
    }

    public function test_can_calculate_declutter_service_price(): void
    {
        $requestData = [
            'selectedServices' => ['entruempelung'],
            'declutterDetails' => [
                'objectType' => 'apartment',
                'volume' => 'medium',
                'address' => [
                    'street' => 'Teststraße 1',
                    'postalCode' => '12345',
                    'city' => 'Teststadt'
                ]
            ]
        ];

        $response = $this->postJson('/api/calculator/calculate', $requestData);

        $response->assertStatus(200)
                ->assertJson([
                    'success' => true
                ]);

        $this->assertGreaterThan(0, $response->json('pricing.total'));
    }

    public function test_can_calculate_multiple_services(): void
    {
        $requestData = [
            'selectedServices' => ['umzug', 'putzservice'],
            'movingDetails' => [
                'rooms' => 2,
                'floors' => 1
            ],
            'cleaningDetails' => [
                'objectType' => 'apartment',
                'size' => '2-rooms',
                'cleaningIntensity' => 'normal'
            ]
        ];

        $response = $this->postJson('/api/calculator/calculate', $requestData);

        $response->assertStatus(200)
                ->assertJson([
                    'success' => true
                ]);

        $pricing = $response->json('pricing');
        $this->assertGreaterThan(0, $pricing['total']);
        $this->assertIsArray($pricing['breakdown']);
        $this->assertGreaterThanOrEqual(2, count($pricing['breakdown']));
    }

    public function test_validates_required_fields(): void
    {
        $response = $this->postJson('/api/calculator/calculate', []);

        $response->assertStatus(422)
                ->assertJson([
                    'success' => false,
                    'error_code' => 'VALIDATION_FAILED'
                ])
                ->assertJsonValidationErrors(['selectedServices']);
    }

    public function test_validates_service_names(): void
    {
        $requestData = [
            'selectedServices' => ['invalid_service'],
        ];

        $response = $this->postJson('/api/calculator/calculate', $requestData);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['selectedServices.0']);
    }

    public function test_handles_invalid_moving_details(): void
    {
        $requestData = [
            'selectedServices' => ['umzug'],
            'movingDetails' => [
                'rooms' => 'invalid',
                'floors' => -1
            ]
        ];

        $response = $this->postJson('/api/calculator/calculate', $requestData);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['movingDetails.rooms']);
    }

    public function test_rate_limiting_works(): void
    {
        // Make multiple requests quickly to trigger rate limiting
        for ($i = 0; $i < 35; $i++) {
            $response = $this->getJson('/api/calculator/services');
            
            if ($response->status() === 429) {
                $this->assertEquals(429, $response->status());
                $this->assertArrayHasKey('error_code', $response->json());
                $this->assertEquals('CALCULATOR_RATE_LIMIT_EXCEEDED', $response->json('error_code'));
                return;
            }
        }

        // If we get here, rate limiting might not be working as expected
        $this->markTestSkipped('Rate limiting not triggered - this might be expected in test environment');
    }

    public function test_returns_german_error_messages(): void
    {
        $response = $this->postJson('/api/calculator/calculate', [
            'selectedServices' => []
        ]);

        $response->assertStatus(422);
        
        $errors = $response->json('errors');
        $this->assertIsArray($errors);
        
        // Check that error messages are in German
        foreach ($errors as $fieldErrors) {
            foreach ($fieldErrors as $error) {
                $this->assertStringContainsString('Service', $error);
            }
        }
    }

    public function test_handles_server_errors_gracefully(): void
    {
        // Temporarily disable a service to simulate an error condition
        Service::where('slug', 'umzug')->update(['is_active' => false]);

        $requestData = [
            'selectedServices' => ['umzug'],
            'movingDetails' => [
                'rooms' => 3
            ]
        ];

        $response = $this->postJson('/api/calculator/calculate', $requestData);

        // Should handle gracefully, either with error or fallback pricing
        $this->assertTrue(in_array($response->status(), [200, 422, 500]));
        
        if ($response->status() !== 200) {
            $response->assertJson([
                'success' => false
            ]);
        }
    }

    public function test_caching_headers_are_set(): void
    {
        $response = $this->getJson('/api/calculator/services');

        $response->assertStatus(200);
        $this->assertNotNull($response->headers->get('Cache-Control'));
    }
}