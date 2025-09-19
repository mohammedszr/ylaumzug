<?php

namespace App\Jobs;

use App\Models\QuoteRequest;
use App\Services\EmailNotificationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * Job for sending quote emails asynchronously
 * 
 * This job handles sending both business notification and customer confirmation
 * emails in the background to improve response times and reliability.
 */
class SendQuoteEmailsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public QuoteRequest $quoteRequest;
    public int $tries = 5;
    public array $backoff = [60, 300, 900]; // 1 min, 5 min, 15 min
    public int $maxExceptions = 3;

    /**
     * Create a new job instance.
     */
    public function __construct(QuoteRequest $quoteRequest)
    {
        $this->quoteRequest = $quoteRequest;
    }

    /**
     * Execute the job.
     */
    public function handle(EmailNotificationService $emailService): void
    {
        Log::info('Starting quote email job', [
            'quote_id' => $this->quoteRequest->id,
            'quote_number' => $this->quoteRequest->quote_number
        ]);

        try {
            $results = $emailService->sendQuoteNotifications($this->quoteRequest);

            // Update quote request with email status
            $this->quoteRequest->update([
                'email_sent_at' => now(),
                'email_status' => [
                    'business_notification' => $results['business_notification'],
                    'customer_confirmation' => $results['customer_confirmation'],
                    'sent_at' => now()->toISOString()
                ]
            ]);

            Log::info('Quote email job completed successfully', [
                'quote_id' => $this->quoteRequest->id,
                'results' => $results
            ]);

        } catch (\Exception $e) {
            Log::error('Quote email job failed', [
                'quote_id' => $this->quoteRequest->id,
                'error' => $e->getMessage(),
                'attempt' => $this->attempts()
            ]);

            // If this is the final attempt, mark as failed
            if ($this->attempts() >= $this->tries) {
                $this->quoteRequest->update([
                    'email_status' => [
                        'business_notification' => false,
                        'customer_confirmation' => false,
                        'error' => $e->getMessage(),
                        'failed_at' => now()->toISOString()
                    ]
                ]);
            }

            throw $e; // Re-throw to trigger retry
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('Quote email job failed permanently', [
            'quote_id' => $this->quoteRequest->id,
            'error' => $exception->getMessage()
        ]);

        // Optionally notify admin of email failure
        // Could send a notification to admin about failed email delivery
    }
}