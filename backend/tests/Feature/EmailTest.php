<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;
use App\Models\QuoteRequest;
use App\Mail\QuoteRequestMail;
use App\Mail\QuoteConfirmationMail;
use App\Services\EmailNotificationService;

class EmailTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Mail::fake();
    }

    /** @test */
    public function it_sends_quote_request_email_to_business_owner()
    {
        $quoteRequest = QuoteRequest::factory()->create([
            'name' => 'Test Customer',
            'email' => 'customer@example.com',
            'phone' => '+49 123 456789',
            'selected_services' => ['umzug'],
            'service_details' => [
                'movingDetails' => [
                    'fromAddress' => ['street' => 'Test Street 1', 'city' => 'Berlin'],
                    'toAddress' => ['street' => 'Test Street 2', 'city' => 'Munich'],
                    'apartmentSize' => '80'
                ]
            ]
        ]);

        $emailService = new EmailNotificationService();
        $result = $emailService->sendQuoteRequestNotification($quoteRequest);

        $this->assertTrue($result);
        
        Mail::assertSent(QuoteRequestMail::class, function ($mail) use ($quoteRequest) {
            return $mail->quoteRequest->id === $quoteRequest->id;
        });
    }

    /** @test */
    public function it_sends_confirmation_email_to_customer()
    {
        $quoteRequest = QuoteRequest::factory()->create([
            'name' => 'Test Customer',
            'email' => 'customer@example.com'
        ]);

        $emailService = new EmailNotificationService();
        $result = $emailService->sendCustomerConfirmation($quoteRequest);

        $this->assertTrue($result);
        
        Mail::assertSent(QuoteConfirmationMail::class, function ($mail) use ($quoteRequest) {
            return $mail->quoteRequest->id === $quoteRequest->id;
        });
    }

    /** @test */
    public function it_sends_both_emails_when_quote_is_submitted()
    {
        $quoteRequest = QuoteRequest::factory()->create();

        $emailService = new EmailNotificationService();
        $results = $emailService->sendQuoteNotifications($quoteRequest);

        $this->assertTrue($results['business_notification']);
        $this->assertTrue($results['customer_confirmation']);
        
        Mail::assertSent(QuoteRequestMail::class);
        Mail::assertSent(QuoteConfirmationMail::class);
    }

    /** @test */
    public function it_can_send_test_email()
    {
        $emailService = new EmailNotificationService();
        $result = $emailService->sendTestEmail('test@example.com');

        $this->assertTrue($result);
        
        Mail::assertSent(function ($mail) {
            return $mail->hasTo('test@example.com') && 
                   str_contains($mail->subject, 'Test E-Mail');
        });
    }

    /** @test */
    public function it_returns_email_configuration_status()
    {
        $emailService = new EmailNotificationService();
        $status = $emailService->getEmailStatus();

        $this->assertIsArray($status);
        $this->assertArrayHasKey('mailer', $status);
        $this->assertArrayHasKey('host', $status);
        $this->assertArrayHasKey('port', $status);
        $this->assertArrayHasKey('from_address', $status);
        $this->assertArrayHasKey('configured', $status);
    }

    /** @test */
    public function quote_request_email_contains_correct_data()
    {
        $quoteRequest = QuoteRequest::factory()->create([
            'name' => 'Max Mustermann',
            'email' => 'max@example.com',
            'phone' => '+49 123 456789',
            'selected_services' => ['umzug', 'putzservice'],
            'estimated_total' => 850.00,
            'service_details' => [
                'movingDetails' => [
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
                    'apartmentSize' => '80',
                    'rooms' => '3'
                ]
            ]
        ]);

        Mail::send(new QuoteRequestMail($quoteRequest));

        Mail::assertSent(QuoteRequestMail::class, function ($mail) use ($quoteRequest) {
            $content = $mail->render();
            
            return str_contains($content, 'Max Mustermann') &&
                   str_contains($content, 'max@example.com') &&
                   str_contains($content, '+49 123 456789') &&
                   str_contains($content, '850,00€') &&
                   str_contains($content, 'Musterstraße 1') &&
                   str_contains($content, 'Berlin') &&
                   str_contains($content, 'München');
        });
    }

    /** @test */
    public function customer_confirmation_email_contains_correct_data()
    {
        $quoteRequest = QuoteRequest::factory()->create([
            'name' => 'Anna Schmidt',
            'email' => 'anna@example.com',
            'selected_services' => ['entruempelung'],
            'estimated_total' => 450.00
        ]);

        Mail::send(new QuoteConfirmationMail($quoteRequest));

        Mail::assertSent(QuoteConfirmationMail::class, function ($mail) use ($quoteRequest) {
            $content = $mail->render();
            
            return str_contains($content, 'Anna Schmidt') &&
                   str_contains($content, $quoteRequest->quote_number) &&
                   str_contains($content, '450,00€') &&
                   str_contains($content, 'Vielen Dank für Ihre Anfrage');
        });
    }
}