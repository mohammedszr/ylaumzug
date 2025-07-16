<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\QuoteRequest;
use App\Http\Requests\SubmitQuoteRequest;
use App\Services\EmailNotificationService;
use App\Jobs\SendQuoteEmailsJob;

class QuoteController extends Controller
{
    protected EmailNotificationService $emailService;

    public function __construct(EmailNotificationService $emailService)
    {
        $this->emailService = $emailService;
    }

    /**
     * Submit a new quote request
     */
    public function submit(SubmitQuoteRequest $request): JsonResponse
    {
        try {
            // Create quote request record
            $quoteRequest = QuoteRequest::create([
                'name' => $request->input('name'),
                'email' => $request->input('email'),
                'phone' => $request->input('phone'),
                'preferred_date' => $request->input('preferredDate'),
                'preferred_contact' => $request->input('preferredContact', 'email'),
                'message' => $request->input('message'),
                'selected_services' => $request->input('selectedServices', []),
                'service_details' => $request->only([
                    'movingDetails',
                    'cleaningDetails', 
                    'declutterDetails'
                ]),
                'pricing_data' => $request->input('pricingData'),
                'status' => 'pending'
            ]);

            // Send email notifications (with queue support)
            if (config('queue.default') !== 'sync') {
                // Use queue for better reliability in production
                SendQuoteEmailsJob::dispatch($quoteRequest);
                \Log::info('Quote emails queued for sending', ['quote_id' => $quoteRequest->id]);
            } else {
                // Send immediately for development/testing
                $emailResults = $this->emailService->sendQuoteNotifications($quoteRequest);
                
                // Log if any emails failed (but don't fail the request)
                if (!$emailResults['business_notification'] || !$emailResults['customer_confirmation']) {
                    \Log::warning('Some emails failed to send', [
                        'quote_id' => $quoteRequest->id,
                        'results' => $emailResults
                    ]);
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Ihre Anfrage wurde erfolgreich gesendet. Wir melden uns innerhalb von 24 Stunden bei Ihnen zurück.',
                'quote_id' => $quoteRequest->id
            ]);

        } catch (\Exception $e) {
            \Log::error('Quote submission error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Fehler beim Senden der Anfrage. Bitte versuchen Sie es erneut oder kontaktieren Sie uns direkt.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Get all quote requests (admin only)
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $quotes = QuoteRequest::query()
                ->when($request->input('status'), function ($query, $status) {
                    return $query->where('status', $status);
                })
                ->orderBy('created_at', 'desc')
                ->paginate(20);

            return response()->json([
                'success' => true,
                'quotes' => $quotes
            ]);

        } catch (\Exception $e) {
            \Log::error('Get quotes error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Fehler beim Laden der Anfragen'
            ], 500);
        }
    }

    /**
     * Get specific quote request (admin only)
     */
    public function show(QuoteRequest $quote): JsonResponse
    {
        try {
            return response()->json([
                'success' => true,
                'quote' => $quote
            ]);

        } catch (\Exception $e) {
            \Log::error('Get quote error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Fehler beim Laden der Anfrage'
            ], 500);
        }
    }

    /**
     * Update quote status (admin only)
     */
    public function updateStatus(Request $request, QuoteRequest $quote): JsonResponse
    {
        try {
            $request->validate([
                'status' => 'required|in:pending,reviewed,quoted,accepted,rejected'
            ]);

            $quote->update([
                'status' => $request->input('status'),
                'admin_notes' => $request->input('admin_notes')
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Status erfolgreich aktualisiert',
                'quote' => $quote
            ]);

        } catch (\Exception $e) {
            \Log::error('Update quote status error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Fehler beim Aktualisieren des Status'
            ], 500);
        }
    }
}