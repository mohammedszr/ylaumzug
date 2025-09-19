<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\QuoteRequest;
use App\Models\Service;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Mail;
use App\Jobs\SendQuoteEmailsJob;
use App\Mail\QuoteConfirmationMail;

class QuoteWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create admin user
        $this->admin = User::factory()->create([
            'email' => 'admin@yla-umzug.de',
            'role' => 'admin'
        ]);
        
        // Seed test data
        $this->seedTestData();
        
        // Fake queues and mail
        Queue::fake();
        Mail::fake();
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
            'value' => 'noreply@yla-umzug.de',
            'type' => 'string'
        ]);

        Setting::create([
            'group_name' => 'general',
            'key_name' => 'business_email',
            'value' => 'info@yla-umzug.de',
            'type' => 'string'
        ]);
    }

    public function test_complete_quote_workflow(): void
    {
        // Step 1: Submit quote via API
        $quoteData = [
            'name' => 'Max Mustermann',
            'email' => 'max@example.com',
            'phone' => '+49 1575 0693353',
            'preferredDate' => now()->addDays(7)->format('Y-m-d'),
            'preferredContact' => 'email',
            'message' => 'Ich benötige Hilfe beim Umzug meiner 3-Zimmer-Wohnung.',
            'selectedServices' => ['umzug'],
            'movingDetails' => [
                'rooms' => 3,
                'floors' => 2,
                'fromAddress' => [
                    'street' => 'Alte Straße 1',
                    'postalCode' => '66111',
                    'city' => 'Saarbrücken'
                ],
                'toAddress' => [
                    'street' => 'Neue Straße 5',
                    'postalCode' => '66112',
                    'city' => 'Saarbrücken'
                ]
            ],
            'pricing' => [
                'total' => 650.00
            ]
        ];

        $response = $this->postJson('/api/quotes/submit', $quoteData);

        // Verify quote submission was successful
        $response->assertStatus(201)
                ->assertJson(['success' => true]);

        $quoteNumber = $response->json('data.angebotsnummer');
        $this->assertNotNull($quoteNumber);

        // Step 2: Verify quote created in database
        $quote = QuoteRequest::where('angebotsnummer', $quoteNumber)->first();
        $this->assertNotNull($quote);
        $this->assertEquals('Max Mustermann', $quote->name);
        $this->assertEquals('max@example.com', $quote->email);
        $this->assertEquals('pending', $quote->status);
        $this->assertEquals(650.00, $quote->estimated_total);

        // Step 3: Verify email job was dispatched
        Queue::assertPushed(SendQuoteEmailsJob::class, function ($job) use ($quote) {
            return $job->quoteRequest->id === $quote->id;
        });

        // Step 4: Simulate admin viewing the quote (would be done through Filament)
        $this->actingAs($this->admin);
        
        // Admin can view the quote
        $this->assertDatabaseHas('quote_requests', [
            'id' => $quote->id,
            'status' => 'pending'
        ]);

        // Step 5: Admin marks quote as reviewed
        $quote->markAsReviewed('Anfrage geprüft, Besichtigung erforderlich.');
        
        $this->assertEquals('reviewed', $quote->fresh()->status);
        $this->assertStringContainsString('Besichtigung erforderlich', $quote->fresh()->admin_notizen);

        // Step 6: Admin creates final quote
        $finalAmount = 720.00;
        $adminNotes = 'Finales Angebot nach Besichtigung erstellt.';
        
        $quote->markAsQuoted($finalAmount, $adminNotes);
        
        $quote = $quote->fresh();
        $this->assertEquals('quoted', $quote->status);
        $this->assertEquals($finalAmount, $quote->endgueltiger_angebotsbetrag);
        $this->assertStringContainsString('Finales Angebot', $quote->admin_notizen);

        // Step 7: Verify quote ready email would be sent
        // (In real implementation, this would be triggered by markAsQuoted)
        Mail::assertSent(QuoteConfirmationMail::class, function ($mail) use ($quote) {
            return $mail->quoteRequest->id === $quote->id;
        });

        // Step 8: Simulate customer acceptance (would be done via email link or phone)
        $quote->update(['status' => 'accepted']);
        
        $this->assertEquals('accepted', $quote->fresh()->status);

        // Step 9: Mark as completed after service delivery
        $quote->update(['status' => 'completed']);
        
        $this->assertEquals('completed', $quote->fresh()->status);
    }

    public function test_quote_rejection_workflow(): void
    {
        // Create a quote
        $quote = QuoteRequest::create([
            'angebotsnummer' => 'QR-2025-TEST',
            'name' => 'Test Customer',
            'email' => 'test@example.com',
            'telefon' => '+49 123 456789',
            'moving_date' => now()->addDays(14),
            'bevorzugter_kontakt' => 'email',
            'ausgewaehlte_services' => ['umzug'],
            'service_details' => ['movingDetails' => ['rooms' => 2]],
            'estimated_total' => 400.00,
            'status' => 'pending'
        ]);

        // Admin reviews and rejects
        $this->actingAs($this->admin);
        
        $quote->update([
            'status' => 'rejected',
            'admin_notizen' => 'Außerhalb unseres Servicegebiets.'
        ]);

        $quote = $quote->fresh();
        $this->assertEquals('rejected', $quote->status);
        $this->assertStringContainsString('Außerhalb', $quote->admin_notizen);
    }

    public function test_quote_statistics_calculation(): void
    {
        // Create test quotes with different statuses
        $quotes = [
            ['status' => 'pending', 'estimated_total' => 500.00],
            ['status' => 'reviewed', 'estimated_total' => 600.00],
            ['status' => 'quoted', 'estimated_total' => 700.00, 'endgueltiger_angebotsbetrag' => 720.00],
            ['status' => 'accepted', 'estimated_total' => 800.00, 'endgueltiger_angebotsbetrag' => 780.00],
            ['status' => 'completed', 'estimated_total' => 900.00, 'endgueltiger_angebotsbetrag' => 850.00],
            ['status' => 'rejected', 'estimated_total' => 300.00],
        ];

        foreach ($quotes as $index => $quoteData) {
            QuoteRequest::create(array_merge([
                'angebotsnummer' => "QR-2025-{$index}",
                'name' => "Customer {$index}",
                'email' => "customer{$index}@example.com",
                'moving_date' => now()->addDays(7),
                'bevorzugter_kontakt' => 'email',
                'ausgewaehlte_services' => ['umzug'],
                'service_details' => ['movingDetails' => ['rooms' => 2]],
            ], $quoteData));
        }

        $stats = QuoteRequest::getStatistics();

        $this->assertEquals(6, $stats['total']);
        $this->assertEquals(1, $stats['pending']);
        $this->assertEquals(6, $stats['this_month']);
        $this->assertGreaterThan(0, $stats['avg_estimate']);
        
        // Conversion rate: 2 completed/accepted out of 6 total = 33.33%
        $this->assertGreaterThan(30, $stats['conversion_rate']);
        $this->assertLessThan(40, $stats['conversion_rate']);
    }

    public function test_quote_search_and_filtering(): void
    {
        // Create test quotes
        $quote1 = QuoteRequest::create([
            'angebotsnummer' => 'QR-2025-001',
            'name' => 'Anna Schmidt',
            'email' => 'anna@example.com',
            'moving_date' => now()->addDays(7),
            'bevorzugter_kontakt' => 'email',
            'ausgewaehlte_services' => ['umzug'],
            'service_details' => ['movingDetails' => ['rooms' => 3]],
            'status' => 'pending',
            'estimated_total' => 600.00
        ]);

        $quote2 = QuoteRequest::create([
            'angebotsnummer' => 'QR-2025-002',
            'name' => 'Peter Müller',
            'email' => 'peter@example.com',
            'moving_date' => now()->addDays(14),
            'bevorzugter_kontakt' => 'phone',
            'ausgewaehlte_services' => ['umzug', 'putzservice'],
            'service_details' => ['movingDetails' => ['rooms' => 4]],
            'status' => 'quoted',
            'estimated_total' => 800.00
        ]);

        // Test filtering by status
        $pendingQuotes = QuoteRequest::where('status', 'pending')->get();
        $this->assertCount(1, $pendingQuotes);
        $this->assertEquals('Anna Schmidt', $pendingQuotes->first()->name);

        // Test filtering by service
        $umzugQuotes = QuoteRequest::withService('umzug')->get();
        $this->assertCount(2, $umzugQuotes);

        $putzQuotes = QuoteRequest::withService('putzservice')->get();
        $this->assertCount(1, $putzQuotes);
        $this->assertEquals('Peter Müller', $putzQuotes->first()->name);

        // Test recent quotes scope
        $recentQuotes = QuoteRequest::recent()->get();
        $this->assertCount(2, $recentQuotes);

        // Test optimized admin listing
        $adminQuotes = QuoteRequest::forAdminListing()->get();
        $this->assertCount(2, $adminQuotes);
        
        // Verify only necessary fields are selected for performance
        $firstQuote = $adminQuotes->first();
        $this->assertNotNull($firstQuote->angebotsnummer);
        $this->assertNotNull($firstQuote->name);
        $this->assertNotNull($firstQuote->status);
    }

    public function test_quote_number_generation(): void
    {
        $currentYear = date('Y');
        
        // Create first quote of the year
        $quote1 = QuoteRequest::create([
            'name' => 'Test Customer 1',
            'email' => 'test1@example.com',
            'moving_date' => now()->addDays(7),
            'bevorzugter_kontakt' => 'email',
            'ausgewaehlte_services' => ['umzug'],
            'service_details' => ['movingDetails' => ['rooms' => 2]],
            'status' => 'pending'
        ]);

        $this->assertEquals("QR-{$currentYear}-001", $quote1->angebotsnummer);

        // Create second quote
        $quote2 = QuoteRequest::create([
            'name' => 'Test Customer 2',
            'email' => 'test2@example.com',
            'moving_date' => now()->addDays(7),
            'bevorzugter_kontakt' => 'email',
            'ausgewaehlte_services' => ['umzug'],
            'service_details' => ['movingDetails' => ['rooms' => 2]],
            'status' => 'pending'
        ]);

        $this->assertEquals("QR-{$currentYear}-002", $quote2->angebotsnummer);

        // Verify uniqueness
        $this->assertNotEquals($quote1->angebotsnummer, $quote2->angebotsnummer);
    }

    public function test_quote_data_integrity(): void
    {
        $quote = QuoteRequest::create([
            'angebotsnummer' => 'QR-2025-TEST',
            'name' => 'Data Test Customer',
            'email' => 'datatest@example.com',
            'telefon' => '+49 123 456789',
            'moving_date' => now()->addDays(7),
            'bevorzugter_kontakt' => 'email',
            'ausgewaehlte_services' => ['umzug', 'putzservice'],
            'service_details' => [
                'movingDetails' => [
                    'rooms' => 4,
                    'floors' => 3,
                    'fromAddress' => [
                        'street' => 'Test Street 1',
                        'postalCode' => '12345',
                        'city' => 'Test City'
                    ]
                ],
                'cleaningDetails' => [
                    'objectType' => 'apartment',
                    'size' => '4-rooms',
                    'cleaningIntensity' => 'deep'
                ]
            ],
            'estimated_total' => 950.00,
            'status' => 'pending'
        ]);

        // Verify JSON data is properly stored and retrieved
        $retrievedQuote = QuoteRequest::find($quote->id);
        
        $this->assertEquals(['umzug', 'putzservice'], $retrievedQuote->ausgewaehlte_services);
        $this->assertEquals(4, $retrievedQuote->service_details['movingDetails']['rooms']);
        $this->assertEquals('apartment', $retrievedQuote->service_details['cleaningDetails']['objectType']);
        $this->assertEquals(950.00, $retrievedQuote->estimated_total);

        // Test attribute accessors
        $this->assertEquals('Umzug, Putzservice', $retrievedQuote->services_string);
        $this->assertEquals('E-Mail', $retrievedQuote->preferred_contact_formatted);
        $this->assertEquals('Ausstehend', $retrievedQuote->status_german);
    }
}