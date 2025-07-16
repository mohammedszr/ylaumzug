<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Service;
use App\Models\Setting;

class CalculatorTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Seed basic services for testing
        $this->seedBasicServices();
        $this->seedBasicSettings();
    }

    /** @test */
    public function it_can_calculate_moving_service_pricing()
    {
        $requestData = [
            'selectedServices' => ['umzug'],
            'movingDetails' => [
                'apartmentSize' => 80,
                'fromAddress' => ['postalCode' => '10115'],
                'toAddress' => ['postalCode' => '10117'],
                'fromFloor' => 3,
                'toFloor' => 2,
                'fromElevator' => 'no',
                'toElevator' => 'yes',
                'boxes' => 20,
                'additionalServices' => ['packing']
            ]
        ];

        $response = $this->postJson('/api/calculator/calculate', $requestData);

        $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'currency' => 'EUR'
                ])
                ->assertJsonStructure([
                    'pricing' => [
                        'total',
                        'breakdown',
                        'currency'
                    ]
                ]);

        $pricing = $response->json('pricing');
        $this->assertGreaterThan(0, $pricing['total']);
        $this->assertIsArray($pricing['breakdown']);
    }

    /** @test */
    public function it_can_calculate_decluttering_service_pricing()
    {
        $requestData = [
            'selectedServices' => ['entruempelung'],
            'declutterDetails' => [
                'volume' => 'high',
                'wasteTypes' => ['furniture', 'electronics'],
                'floor' => 4,
                'elevator' => 'no',
                'objectTypes' => ['furniture', 'appliances'],
                'accessDifficulty' => 'difficult'
            ]
        ];

        $response = $this->postJson('/api/calculator/calculate', $requestData);

        $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'currency' => 'EUR'
                ]);

        $pricing = $response->json('pricing');
        $this->assertGreaterThan(0, $pricing['total']);
    }

    /** @test */
    public function it_can_calculate_cleaning_service_pricing()
    {
        $requestData = [
            'selectedServices' => ['putzservice'],
            'cleaningDetails' => [
                'size' => 100,
                'cleaningIntensity' => 'deep',
                'rooms' => ['windows', 'kitchen', 'bathroom'],
                'frequency' => 'monthly'
            ]
        ];

        $response = $this->postJson('/api/calculator/calculate', $requestData);

        $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'currency' => 'EUR'
                ]);

        $pricing = $response->json('pricing');
        $this->assertGreaterThan(0, $pricing['total']);
    }

    /** @test */
    public function it_applies_combination_discount_for_multiple_services()
    {
        $singleServiceData = [
            'selectedServices' => ['umzug'],
            'movingDetails' => [
                'apartmentSize' => 80,
                'fromAddress' => ['postalCode' => '10115'],
                'toAddress' => ['postalCode' => '10117']
            ]
        ];

        $multiServiceData = [
            'selectedServices' => ['umzug', 'putzservice'],
            'movingDetails' => [
                'apartmentSize' => 80,
                'fromAddress' => ['postalCode' => '10115'],
                'toAddress' => ['postalCode' => '10117']
            ],
            'cleaningDetails' => [
                'size' => 80,
                'cleaningIntensity' => 'normal'
            ]
        ];

        $singleResponse = $this->postJson('/api/calculator/calculate', $singleServiceData);
        $multiResponse = $this->postJson('/api/calculator/calculate', $multiServiceData);

        $singleTotal = $singleResponse->json('pricing.total');
        $multiTotal = $multiResponse->json('pricing.total');

        // Multi-service should have combination discount applied
        $this->assertArrayHasKey('breakdown', $multiResponse->json('pricing'));
        
        // Check if combination discount is in breakdown
        $breakdown = $multiResponse->json('pricing.breakdown');
        $hasDiscount = collect($breakdown)->contains(function ($item) {
            return str_contains($item['service'], 'Kombinationsrabatt');
        });
        
        $this->assertTrue($hasDiscount, 'Combination discount should be applied for multiple services');
    }

    /** @test */
    public function it_applies_express_surcharge()
    {
        $normalData = [
            'selectedServices' => ['umzug'],
            'movingDetails' => [
                'apartmentSize' => 80,
                'fromAddress' => ['postalCode' => '10115'],
                'toAddress' => ['postalCode' => '10117']
            ],
            'generalInfo' => [
                'urgency' => 'normal'
            ]
        ];

        $expressData = [
            'selectedServices' => ['umzug'],
            'movingDetails' => [
                'apartmentSize' => 80,
                'fromAddress' => ['postalCode' => '10115'],
                'toAddress' => ['postalCode' => '10117']
            ],
            'generalInfo' => [
                'urgency' => 'express'
            ]
        ];

        $normalResponse = $this->postJson('/api/calculator/calculate', $normalData);
        $expressResponse = $this->postJson('/api/calculator/calculate', $expressData);

        $normalTotal = $normalResponse->json('pricing.total');
        $expressTotal = $expressResponse->json('pricing.total');

        $this->assertGreaterThan($normalTotal, $expressTotal, 'Express service should cost more');
        
        // Check if express surcharge is in breakdown
        $breakdown = $expressResponse->json('pricing.breakdown');
        $hasSurcharge = collect($breakdown)->contains(function ($item) {
            return str_contains($item['service'], 'Express-Zuschlag');
        });
        
        $this->assertTrue($hasSurcharge, 'Express surcharge should be applied');
    }

    /** @test */
    public function it_can_get_available_services()
    {
        $response = $this->getJson('/api/calculator/services');

        $response->assertStatus(200)
                ->assertJson([
                    'success' => true
                ])
                ->assertJsonStructure([
                    'services' => [
                        '*' => [
                            'id',
                            'name',
                            'description',
                            'base_price'
                        ]
                    ]
                ]);
    }

    /** @test */
    public function it_validates_required_fields()
    {
        $response = $this->postJson('/api/calculator/calculate', []);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['selectedServices']);
    }

    /** @test */
    public function it_validates_service_types()
    {
        $requestData = [
            'selectedServices' => ['invalid_service']
        ];

        $response = $this->postJson('/api/calculator/calculate', $requestData);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['selectedServices.0']);
    }

    private function seedBasicServices()
    {
        Service::create([
            'key' => 'umzug',
            'name' => 'Umzug',
            'description' => 'Professioneller Umzugsservice',
            'base_price' => 300.00,
            'is_active' => true,
            'sort_order' => 1
        ]);

        Service::create([
            'key' => 'entruempelung',
            'name' => 'Entrümpelung',
            'description' => 'Haushaltsauflösung und Entsorgung',
            'base_price' => 300.00,
            'is_active' => true,
            'sort_order' => 2
        ]);

        Service::create([
            'key' => 'putzservice',
            'name' => 'Putzservice',
            'description' => 'Grundreinigung und besenreine Übergabe',
            'base_price' => 150.00,
            'is_active' => true,
            'sort_order' => 3
        ]);
    }

    private function seedBasicSettings()
    {
        $settings = [
            'minimum_order_value' => 150,
            'distance_rate_per_km' => 2.0,
            'floor_surcharge_rate' => 50.0,
            'declutter_floor_rate' => 30.0,
            'combination_discount_2_services' => 0.10,
            'combination_discount_3_services' => 0.15,
            'express_surcharge' => 0.20,
            'hazardous_waste_surcharge' => 150.0,
            'electronics_disposal_cost' => 100.0,
            'furniture_disposal_cost' => 80.0,
            'access_difficulty_surcharge' => 100.0,
            'cleaning_rate_normal' => 3.0,
            'cleaning_rate_deep' => 5.0,
            'cleaning_rate_construction' => 7.0,
            'window_cleaning_rate' => 2.0,
            'regular_cleaning_discount' => 0.15,
            'declutter_volume_low' => 300,
            'declutter_volume_medium' => 600,
            'declutter_volume_high' => 1200,
            'declutter_volume_extreme' => 2000
        ];

        foreach ($settings as $key => $value) {
            Setting::create([
                'key' => $key,
                'value' => (string) $value,
                'type' => is_float($value) ? 'decimal' : 'integer'
            ]);
        }
    }
}