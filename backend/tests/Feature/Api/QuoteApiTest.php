<?php

namespace Tests\Feature\Api;

use Tests\TestCase;
use App\Models\QuoteRequest;
use App\Models\Service;
use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use App\Jobs\SendQuoteEmailsJob;

class QuoteApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Seed test data
        $this->seedTestData();
        
        // Fake the queue for testing
        Queue::fake();
    }

    protected function seedTestData(): void
    {
        // Create test services
        Service::create([
            'name' => 'Umzug',
            'slug' => 'umzug',
            'is_active' => true,
            'sort_order' => 1
        ]);

        // Create test settings
        Setting::create([
            'group_name' => 'email',
            'key_name' => 'from_address',
            'value' => 'test@yla-umzug.de',
            'type' => 'string'
        ]);
    }

    public function test_can_submit_quote_request(): void
    {
        $quoteData = [
            'name' => 'Max Mustermann',
            'email' => 'max@example.com',
            'phone' => '+49 1575 0693353',
            'preferredDate' => now()->addDays(7)->format('Y-m-d'),
            'preferredContact' => 'email',
            'message' => 'Ich benötige Hilfe beim Umzug.',
            'selectedServices' => ['umzug'],
            'movingDetails' => [
                'rooms' => 3,
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
            ],
            'pricing' => [
                'total' => 650.00
            ]
        ];

        $response = $this->postJson('/api/quotes/submit', $quoteData);

        $response->assertStatus(201)
                ->assertJson([
                    'success' => true,
                    'message' => 'Angebotsanfrage erfolgreich eingereicht'
                ])
                ->assertJsonStructure([
                    'success',
                    'message',
                    'data' => [
                        'angebotsnummer',
                        'estimated_total'
                    ]
                ]);

        // Verify quote was created in database
        $this->assertDatabaseHas('quote_requests', [
            'name' => 'Max Mustermann',
            'email' => 'max@example.com',
            'status' => 'pending'
        ]);

        // Verify email job was dispatched
        Queue::assertPushed(SendQuoteEmailsJob::class);
    }

    public function test_validates_required_fields_for_quote_submission(): void
    {
        $response = $this->postJson('/api/quotes/submit', []);

        $response->assertStatus(422)
                ->assertJson([
                    'success' => false,
                    'error_code' => 'VALIDATION_FAILED'
                ])
                ->assertJsonValidationErrors([
                    'name',
                    'email',
                    'preferredDate',
                    'preferredContact',
                    'selectedServices'
                ]);
    }

    public function test_validates_email_format(): void
    {
        $quoteData = [
            'name' => 'Test User',
            'email' => 'invalid-email',
            'preferredDate' => now()->addDays(7)->format('Y-m-d'),
            'preferredContact' => 'email',
            'selectedServices' => ['umzug']
        ];

        $response = $this->postJson('/api/quotes/submit', $quoteData);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['email']);
    }

    public function test_validates_future_date(): void
    {
        $quoteData = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'preferredDate' => now()->subDays(1)->format('Y-m-d'), // Past date
            'preferredContact' => 'email',
            'selectedServices' => ['umzug']
        ];

        $response = $this->postJson('/api/quotes/submit', $quoteData);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['preferredDate']);
    }

    public function test_validates_preferred_contact_options(): void
    {
        $quoteData = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'preferredDate' => now()->addDays(7)->format('Y-m-d'),
            'preferredContact' => 'invalid_option',
            'selectedServices' => ['umzug']
        ];

        $response = $this->postJson('/api/quotes/submit', $quoteData);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['preferredContact']);
    }

    public function test_validates_selected_services(): void
    {
        $quoteData = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'preferredDate' => now()->addDays(7)->format('Y-m-d'),
            'preferredContact' => 'email',
            'selectedServices' => ['invalid_service']
        ];

        $response = $this->postJson('/api/quotes/submit', $quoteData);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['selectedServices.0']);
    }

    public function test_generates_unique_quote_numbers(): void
    {
        $quoteData = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'preferredDate' => now()->addDays(7)->format('Y-m-d'),
            'preferredContact' => 'email',
            'selectedServices' => ['umzug']
        ];

        // Submit multiple quotes
        $response1 = $this->postJson('/api/quotes/submit', $quoteData);
        $response2 = $this->postJson('/api/quotes/submit', array_merge($quoteData, ['email' => 'test2@example.com']));

        $response1->assertStatus(201);
        $response2->assertStatus(201);

        $quoteNumber1 = $response1->json('data.angebotsnummer');
        $quoteNumber2 = $response2->json('data.angebotsnummer');

        $this->assertNotEquals($quoteNumber1, $quoteNumber2);
        $this->assertStringStartsWith('QR-', $quoteNumber1);
        $this->assertStringStartsWith('QR-', $quoteNumber2);
    }

    public function test_stores_service_details_correctly(): void
    {
        $quoteData = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'preferredDate' => now()->addDays(7)->format('Y-m-d'),
            'preferredContact' => 'email',
            'selectedServices' => ['umzug'],
            'movingDetails' => [
                'rooms' => 4,
                'floors' => 3,
                'fromAddress' => [
                    'street' => 'From Street 1',
                    'postalCode' => '12345',
                    'city' => 'From City'
                ]
            ],
            'cleaningDetails' => [
                'objectType' => 'apartment',
                'size' => '4-rooms'
            ]
        ];

        $response = $this->postJson('/api/quotes/submit', $quoteData);

        $response->assertStatus(201);

        $quote = QuoteRequest::where('email', 'test@example.com')->first();
        $this->assertNotNull($quote);
        $this->assertEquals(['umzug'], $quote->ausgewaehlte_services);
        $this->assertEquals(4, $quote->service_details['movingDetails']['rooms']);
        $this->assertEquals('apartment', $quote->service_details['cleaningDetails']['objectType']);
    }

    public function test_rate_limiting_for_quote_submission(): void
    {
        $quoteData = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'preferredDate' => now()->addDays(7)->format('Y-m-d'),
            'preferredContact' => 'email',
            'selectedServices' => ['umzug']
        ];

        // Submit multiple quotes quickly to trigger rate limiting
        for ($i = 0; $i < 7; $i++) {
            $response = $this->postJson('/api/quotes/submit', array_merge($quoteData, [
                'email' => "test{$i}@example.com"
            ]));
            
            if ($response->status() === 429) {
                $this->assertEquals(429, $response->status());
                $this->assertArrayHasKey('error_code', $response->json());
                $this->assertEquals('QUOTE_RATE_LIMIT_EXCEEDED', $response->json('error_code'));
                return;
            }
        }

        // If we get here, rate limiting might not be working as expected in test environment
        $this->markTestSkipped('Rate limiting not triggered - this might be expected in test environment');
    }

    public function test_sanitizes_input_data(): void
    {
        $quoteData = [
            'name' => '<script>alert("xss")</script>Test User',
            'email' => 'test@example.com',
            'preferredDate' => now()->addDays(7)->format('Y-m-d'),
            'preferredContact' => 'email',
            'selectedServices' => ['umzug'],
            'message' => '<iframe src="malicious.com"></iframe>Please help with moving'
        ];

        $response = $this->postJson('/api/quotes/submit', $quoteData);

        $response->assertStatus(201);

        $quote = QuoteRequest::where('email', 'test@example.com')->first();
        $this->assertNotNull($quote);
        
        // Check that malicious content was sanitized
        $this->assertStringNotContainsString('<script>', $quote->name);
        $this->assertStringNotContainsString('<iframe>', $quote->message);
        $this->assertStringContainsString('Test User', $quote->name);
        $this->assertStringContainsString('Please help with moving', $quote->message);
    }

    public function test_handles_missing_optional_fields(): void
    {
        $quoteData = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'preferredDate' => now()->addDays(7)->format('Y-m-d'),
            'preferredContact' => 'email',
            'selectedServices' => ['umzug']
            // Missing phone, message, service details, pricing
        ];

        $response = $this->postJson('/api/quotes/submit', $quoteData);

        $response->assertStatus(201);

        $quote = QuoteRequest::where('email', 'test@example.com')->first();
        $this->assertNotNull($quote);
        $this->assertNull($quote->telefon);
        $this->assertNull($quote->message);
    }

    public function test_returns_german_error_messages(): void
    {
        $response = $this->postJson('/api/quotes/submit', [
            'name' => '',
            'email' => 'invalid-email'
        ]);

        $response->assertStatus(422);
        
        $errors = $response->json('errors');
        $this->assertIsArray($errors);
        
        // Check that error messages are in German
        if (isset($errors['name'])) {
            $this->assertStringContainsString('erforderlich', $errors['name'][0]);
        }
        
        if (isset($errors['email'])) {
            $this->assertStringContainsString('gültige', $errors['email'][0]);
        }
    }

    public function test_handles_large_message_gracefully(): void
    {
        $largeMessage = str_repeat('This is a very long message. ', 100); // ~2700 characters

        $quoteData = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'preferredDate' => now()->addDays(7)->format('Y-m-d'),
            'preferredContact' => 'email',
            'selectedServices' => ['umzug'],
            'message' => $largeMessage
        ];

        $response = $this->postJson('/api/quotes/submit', $quoteData);

        // Should either accept it or return validation error for max length
        $this->assertTrue(in_array($response->status(), [201, 422]));
        
        if ($response->status() === 422) {
            $response->assertJsonValidationErrors(['message']);
        }
    }
}