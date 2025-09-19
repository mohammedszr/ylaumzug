<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\QuoteRequest;
use App\Http\Requests\SubmitQuoteRequest;
use App\Services\EmailNotificationService;
use App\Services\PdfQuoteService;
use App\Jobs\SendQuoteEmailsJob;
use App\Mail\PdfQuoteMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class QuoteController extends Controller
{
    protected EmailNotificationService $emailService;
    protected PdfQuoteService $pdfService;

    public function __construct(EmailNotificationService $emailService, PdfQuoteService $pdfService)
    {
        $this->emailService = $emailService;
        $this->pdfService = $pdfService;
    }

    /**
     * Submit a new quote request
     */
    public function submit(SubmitQuoteRequest $request): JsonResponse
    {
        try {
            // Create quote request record with German field names
            $quoteRequest = QuoteRequest::create([
                'name' => $request->input('name'),
                'email' => $request->input('email'),
                'telefon' => $request->input('phone'),
                'moving_date' => $request->input('preferredDate'),
                'bevorzugter_kontakt' => $request->input('preferredContact', 'email'),
                'message' => $request->input('message'),
                'ausgewaehlte_services' => $request->input('selectedServices', []),
                'service_details' => $request->only([
                    'movingDetails',
                    'cleaningDetails', 
                    'declutterDetails'
                ]),
                'estimated_total' => $request->input('pricing.total'),
                'status' => 'pending',
                'submitted_at' => now()
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
                'message' => 'Angebotsanfrage erfolgreich eingereicht',
                'data' => [
                    'angebotsnummer' => $quoteRequest->angebotsnummer,
                    'estimated_total' => $quoteRequest->estimated_total
                ]
            ], 201);

        } catch (\Exception $e) {
            Log::error('Quote submission failed', [
                'error' => $e->getMessage(),
                'data' => $request->all()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Fehler beim Einreichen der Anfrage'
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

    /**
     * Generate PDF quote (admin only)
     */
    public function generatePdf(QuoteRequest $quote): JsonResponse
    {
        try {
            $filename = $this->pdfService->generateQuotePdf($quote);

            return response()->json([
                'success' => true,
                'message' => 'PDF erfolgreich generiert',
                'filename' => $filename,
                'download_url' => route('quotes.download-pdf', $quote)
            ]);

        } catch (\Exception $e) {
            \Log::error('PDF generation error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Fehler bei der PDF-Generierung: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Download PDF quote (admin only)
     */
    public function downloadPdf(QuoteRequest $quote)
    {
        try {
            $pdfContent = $this->pdfService->getPdfContent($quote);
            $filename = "Angebot-{$quote->quote_number}.pdf";

            return response($pdfContent)
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', "attachment; filename=\"{$filename}\"");

        } catch (\Exception $e) {
            \Log::error('PDF download error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Fehler beim PDF-Download: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Send PDF quote via email (admin only)
     */
    public function sendPdfQuote(Request $request, QuoteRequest $quote): JsonResponse
    {
        try {
            $request->validate([
                'final_quote_amount' => 'nullable|numeric|min:0',
                'admin_notes' => 'nullable|string|max:1000'
            ]);

            // Update quote with final amount if provided
            if ($request->has('final_quote_amount')) {
                $quote->update([
                    'final_quote_amount' => $request->input('final_quote_amount'),
                    'admin_notes' => $request->input('admin_notes'),
                    'status' => 'quoted',
                    'quoted_at' => now()
                ]);
            }

            // Send PDF quote email
            Mail::to($quote->email)->send(new PdfQuoteMail($quote));

            \Log::info('PDF quote email sent', [
                'quote_id' => $quote->id,
                'customer_email' => $quote->email,
                'final_amount' => $quote->final_quote_amount
            ]);

            return response()->json([
                'success' => true,
                'message' => 'PDF-Angebot erfolgreich per E-Mail versendet',
                'quote' => $quote->fresh()
            ]);

        } catch (\Exception $e) {
            \Log::error('Send PDF quote error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Fehler beim Versenden des PDF-Angebots: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Preview PDF quote (admin only)
     */
    public function previewPdf(QuoteRequest $quote)
    {
        try {
            $pdfContent = $this->pdfService->getPdfContent($quote);

            return response($pdfContent)
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'inline');

        } catch (\Exception $e) {
            \Log::error('PDF preview error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Fehler bei der PDF-Vorschau: ' . $e->getMessage()
            ], 500);
        }
    }
}