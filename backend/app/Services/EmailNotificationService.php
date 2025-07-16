<?php

namespace App\Services;

use App\Models\QuoteRequest;
use App\Mail\QuoteRequestMail;
use App\Mail\QuoteConfirmationMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

/**
 * Service for handling all email notifications in the system
 * 
 * This service centralizes email logic and provides methods for sending
 * different types of notifications with proper error handling and logging.
 */
class EmailNotificationService
{
    /**
     * Send quote request notification to business owner
     */
    public function sendQuoteRequestNotification(QuoteRequest $quoteRequest): bool
    {
        try {
            $businessEmail = env('BUSINESS_EMAIL', 'info@yla-umzug.de');
            
            Mail::to($businessEmail)
                ->send(new QuoteRequestMail($quoteRequest));

            Log::info("Quote request email sent to business", [
                'quote_id' => $quoteRequest->id,
                'quote_number' => $quoteRequest->quote_number,
                'business_email' => $businessEmail
            ]);

            return true;

        } catch (\Exception $e) {
            Log::error("Failed to send quote request email to business", [
                'quote_id' => $quoteRequest->id,
                'error' => $e->getMessage()
            ]);

            return false;
        }
    }

    /**
     * Send confirmation email to customer
     */
    public function sendCustomerConfirmation(QuoteRequest $quoteRequest): bool
    {
        try {
            Mail::to($quoteRequest->email)
                ->send(new QuoteConfirmationMail($quoteRequest));

            Log::info("Confirmation email sent to customer", [
                'quote_id' => $quoteRequest->id,
                'quote_number' => $quoteRequest->quote_number,
                'customer_email' => $quoteRequest->email
            ]);

            return true;

        } catch (\Exception $e) {
            Log::error("Failed to send confirmation email to customer", [
                'quote_id' => $quoteRequest->id,
                'customer_email' => $quoteRequest->email,
                'error' => $e->getMessage()
            ]);

            return false;
        }
    }

    /**
     * Send both business notification and customer confirmation
     */
    public function sendQuoteNotifications(QuoteRequest $quoteRequest): array
    {
        $results = [
            'business_notification' => $this->sendQuoteRequestNotification($quoteRequest),
            'customer_confirmation' => $this->sendCustomerConfirmation($quoteRequest)
        ];

        // Log overall result
        if ($results['business_notification'] && $results['customer_confirmation']) {
            Log::info("All quote emails sent successfully", [
                'quote_id' => $quoteRequest->id,
                'quote_number' => $quoteRequest->quote_number
            ]);
        } else {
            Log::warning("Some quote emails failed to send", [
                'quote_id' => $quoteRequest->id,
                'results' => $results
            ]);
        }

        return $results;
    }

    /**
     * Test email configuration by sending a test email
     */
    public function sendTestEmail(string $toEmail = null): bool
    {
        try {
            $testEmail = $toEmail ?? env('BUSINESS_EMAIL', 'info@yla-umzug.de');
            
            Mail::raw(
                "Dies ist eine Test-E-Mail vom YLA Umzug System.\n\n" .
                "Wenn Sie diese E-Mail erhalten, ist die E-Mail-Konfiguration korrekt eingerichtet.\n\n" .
                "Gesendet am: " . now()->format('d.m.Y H:i:s') . "\n" .
                "Server: " . config('app.url'),
                function ($message) use ($testEmail) {
                    $message->to($testEmail)
                           ->subject('YLA Umzug - Test E-Mail')
                           ->from(config('mail.from.address'), config('mail.from.name'));
                }
            );

            Log::info("Test email sent successfully", ['to' => $testEmail]);
            return true;

        } catch (\Exception $e) {
            Log::error("Test email failed", [
                'to' => $testEmail ?? 'unknown',
                'error' => $e->getMessage()
            ]);

            return false;
        }
    }

    /**
     * Get email configuration status
     */
    public function getEmailStatus(): array
    {
        return [
            'mailer' => config('mail.default'),
            'host' => config('mail.mailers.smtp.host'),
            'port' => config('mail.mailers.smtp.port'),
            'encryption' => config('mail.mailers.smtp.encryption'),
            'from_address' => config('mail.from.address'),
            'from_name' => config('mail.from.name'),
            'business_email' => env('BUSINESS_EMAIL'),
            'configured' => !empty(config('mail.mailers.smtp.host')) && !empty(config('mail.from.address'))
        ];
    }
}