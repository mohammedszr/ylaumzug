<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;
use App\Models\Service;
use App\Models\Setting;
use App\Models\QuoteRequest;
use App\Mail\QuoteRequestMail;
use App\Mail\QuoteConfirmationMail;

class UserFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->seedBasicServices();
        $this->seedBasicSettings();
        Mail::fake();
    }

    /** @test */
    public function complete_user_flow_from_calculator_to_quote_submission()
    {
        // Step 1: Get available services
        $servicesResponse = $this->getJson('/api/calculator/services');
        
        $servicesResponse->assertStatus(200)
                        ->assertJson(['success' => true])
                        ->assertJsonStructure([
                            'services' => [
                                '*' => ['key', 'name', 'description', 'base_price']
                            ]
                        ]);

        // Step 2: Calculate price for moving service
        $calculationData = [
            'selectedServices' => ['umzug'],
            'movingDetails' => [
                'apartmentSize' => 80,
                'fromAddress' => [
                    'street' => 'Musterstraße 1',
                    'postalCode' => '10115',
                    'city' => 'Berlin'
                ],
                'toAddress' => [
                    'street' => 'Beispielweg 5',
                    'postalCode' => '80331',
                    'city' => 'München'
                ],
                'fromFloor' => 3,
                'toFloor' => 2,
                'fromElevator' => 'no',
                'toElevator' => 'yes',
                'boxes' => 20,
                'additionalServices' => ['packing']
            ]
        ];

        $calculationResponse = $this->postJson('/api/calculator/calculate', $calculationData);
        
        $calculationResponse->assertStatus(200)
                           ->assertJson(['success' => true])
                           ->assertJsonStructure([
                               'pricing' => [
                                   'total',
                                   'breakdown',
                                   'currency'
                               ]
                           ]);

        $pricing = $calculationResponse->json('pricing');
        $this->assertGreaterThan(0, $pricing['total']);
        $this->assertEquals('EUR', $pricing['currency']);

        // Step 3: Submit quote request with calculated data
        $quoteData = [
            'name' => 'Max Mustermann',
            'email' => 'max.mustermann@example.com',
            'phone' => '+49 123 456789',
            'message' => 'Ich benötige einen Umzug von Berlin nach München.',
            'selectedServices' => $calculationData['selectedServices'],
            'serviceDetails' => [
                'movingDetails' => $calculationData['movingDetails']
            ],
            'estimatedTotal' => $pricing['total'],
            'pricingBreakdown' => $pricing['breakdown']
        ];

        $quoteResponse = $this->postJson('/api/quotes', $quoteData);
        
        $quoteResponse->assertStatus(201)
                     ->assertJson(['success' => true])
                     ->assertJsonStructure([
                         'message',
                         'quote_number'
                     ]);

        // Step 4: Verify quote was stored in database
        $this->assertDatabaseHas('quote_requests', [
            'name' => 'Max Mustermann',
            'email' => 'max.mustermann@example.com',
            'phone' => '+49 123 456789',
            'estimated_total' => $pricing['total'],
            'status' => 'new'
        ]);

        $quoteRequest = QuoteRequest::where('email', 'max.mustermann@example.com')->first();
        $this->assertNotNull($quoteRequest);
        $this->assertEquals(['umzug'], $quoteRequest->selected_services);
        $this->assertArrayHasKey('movingDetails', $quoteRequest->service_details);

        // Step 5: Verify emails were sent
        Mail::assertSent(QuoteRequestMail::class, function ($mail) use ($quoteRequest) {
            return $mail->quoteRequest->id === $quoteRequest->id;
        });

        Mail::assertSent(QuoteConfirmationMail::class, function ($mail) use ($quoteRequest) {
            return $mail->quoteRequest->id === $quoteRequest->id;
        });
    }

    /** @test */
    public function complete_flow_with_multiple_services_and_combination_discount()
    {
        // Calculate price for multiple services
        $calculationData = [
            'selectedServices' => ['umzug', 'putzservice', 'entruempelung'],
            'movingDetails' => [
                'apartmentSize' => 100,
                'fromAddress' => ['postalCode' => '10115'],
                'toAddress' => ['postalCode' => '80331']
            ],
            'cleaningDetails' => [
                'size' => 100,
                'cleaningIntensity' => 'deep',
                'rooms' => ['windows', 'kitchen', 'bathroom']
            ],
            'declutterDetails' => [
                'volume' => 'high',
                'wasteTypes' => ['furniture', 'electronics'],
                'floor' => 2,
                'elevator' => 'yes'
            ]
        ];

        $calculationResponse = $this->postJson('/api/calculator/calculate', $calculationData);
        
        $calculationResponse->assertStatus(200);
        $pricing = $calculationResponse->json('pricing');

        // Verify combination discount is applied
        $breakdown = collect($pricing['breakdown']);
        $hasDiscount = $breakdown->contains(function ($item) {
            return str_contains($item['service'], 'Kombinationsrabatt');
        });
        $this->assertTrue($hasDiscount, 'Combination discount should be applied for 3 services');

        // Submit quote with all service details
        $quoteData = [
            'name' => 'Anna Schmidt',
            'email' => 'anna.schmidt@example.com',
            'phone' => '+49 987 654321',
            'selectedServices' => $calculationData['selectedServices'],
            'serviceDetails' => [
                'movingDetails' => $calculationData['movingDetails'],
                'cleaningDetails' => $calculationData['cleaningDetails'],
                'declutterDetails' => $calculationData['declutterDetails']
            ],
            'estimatedTotal' => $pricing['total'],
            'pricingBreakdown' => $pricing['breakdown']
        ];

        $quoteResponse = $this->postJson('/api/quotes', $quoteData);
        
        $quoteResponse->assertStatus(201);

        // Verify all service details are stored
        $quoteRequest = QuoteRequest::where('email', 'anna.schmidt@example.com')->first();
        $this->assertEquals(['umzug', 'putzservice', 'entruempelung'], $quoteRequest->selected_services);
        $this->assertArrayHasKey('movingDetails', $quoteRequest->service_details);
        $this->assertArrayHasKey('cleaningDetails', $quoteRequest->service_details);
        $this->assertArrayHasKey('declutterDetails', $quoteRequest->service_details);
    }

    /** @test */
    public function flow_with_express_service_surcharge()
    {
        $calculationData = [
            'selectedServices' => ['umzug'],
            'movingDetails' => [
                'apartmentSize' => 60,
                'fromAddress' => ['postalCode' => '10115'],
                'toAddress' => ['postalCode' => '10117']
            ],
            'generalInfo' => [
                'urgency' => 'express',
                'preferredDate' => '2024-02-15'
            ]
        ];

        $calculationResponse = $this->postJson('/api/calculator/calculate', $calculationData);
        
        $calculationResponse->assertStatus(200);
        $pricing = $calculationResponse->json('pricing');

        // Verify express surcharge is applied
        $breakdown = collect($pricing['breakdown']);
        $hasSurcharge = $breakdown->contains(function ($item) {
            return str_contains($item['service'], 'Express-Zuschlag');
        });
        $this->assertTrue($hasSurcharge, 'Express surcharge should be applied');

        // Submit quote with express service
        $quoteData = [
            'name' => 'Peter Weber',
            'email' => 'peter.weber@example.com',
            'phone' => '+49 555 123456',
            'selectedServices' => $calculationData['selectedServices'],
            'serviceDetails' => [
                'movingDetails' => $calculationData['movingDetails'],
                'generalInfo' => $calculationData['generalInfo']
            ],
            'estimatedTotal' => $pricing['total'],
            'pricingBreakdown' => $pricing['breakdown']
        ];

        $quoteResponse = $this->postJson('/api/quotes', $quoteData);
        
        $quoteResponse->assertStatus(201);

        // Verify express service details are stored
        $quoteRequest = QuoteRequest::where('email', 'peter.weber@example.com')->first();
        $this->assertArrayHasKey('generalInfo', $quoteRequest->service_details);
        $this->assertEquals('express', $quoteRequest->service_details['generalInfo']['urgency']);
    }

    /** @test */
    public function flow_handles_validation_errors_gracefully()
    {
        // Try to calculate without required services
        $invalidCalculationData = [
            'selectedServices' => [],
            'movingDetails' => [
                'apartmentSize' => 80
            ]
        ];

        $calculationResponse = $this->postJson('/api/calculator/calculate', $invalidCalculationData);
        
        $calculationResponse->assertStatus(422)
                           ->assertJsonValidationErrors(['selectedServices']);

        // Try to submit quote without required contact info
        $invalidQuoteData = [
            'selectedServices' => ['umzug'],
            'serviceDetails' => [
                'movingDetails' => [
                    'apartmentSize' => 80,
                    'fromAddress' => ['postalCode' => '10115'],
                    'toAddress' => ['postalCode' => '10117']
                ]
            ]
            // Missing name, email, phone
        ];

        $quoteResponse = $this->postJson('/api/quotes', $invalidQuoteData);
        
        $quoteResponse->assertStatus(422)
                     ->assertJsonValidationErrors(['name', 'email', 'phone']);

        // Verify no quote was created
        $this->assertDatabaseCount('quote_requests', 0);
        
        // Verify no emails were sent
        Mail::assertNothingSent();
    }

    /** @test */
    public function flow_with_minimum_order_value_adjustment()
    {
        // Calculate a small cleaning service that should trigger minimum order value
        $calculationData = [
            'selectedServices' => ['putzservice'],
            'cleaningDetails' => [
                'size' => 20,
                'cleaningIntensity' => 'normal'
            ]
        ];

        $calculationResponse = $this->postJson('/api/calculator/calculate', $calculationData);
        
        $calculationResponse->assertStatus(200);
        $pricing = $calculationResponse->json('pricing');

        // Should meet minimum order value
        $minimumValue = (float) Setting::where('key', 'minimum_order_value')->value('value');
        $this->assertGreaterThanOrEqual($minimumValue, $pricing['total']);

        // Submit quote
        $quoteData = [
            'name' => 'Maria Gonzalez',
            'email' => 'maria@example.com',
            'phone' => '+49 444 555666',
            'selectedServices' => $calculationData['selectedServices'],
            'serviceDetails' => [
                'cleaningDetails' => $calculationData['cleaningDetails']
            ],
            'estimatedTotal' => $pricing['total'],
            'pricingBreakdown' => $pricing['breakdown']
        ];

        $quoteResponse = $this->postJson('/api/quotes', $quoteData);
        
        $quoteResponse->assertStatus(201);

        // Verify minimum order value is reflected in stored quote
        $quoteRequest = QuoteRequest::where('email', 'maria@example.com')->first();
        $this->assertGreaterThanOrEqual($minimumValue, $quoteRequest->estimated_total);
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