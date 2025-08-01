<?php

namespace App\Mail;

use App\Models\QuoteRequest;
use App\Services\PdfQuoteService;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Queue\SerializesModels;

/**
 * Email sent to customer with PDF quote attachment
 * 
 * This email is sent after the business owner has reviewed the quote request
 * and wants to send a professional PDF quote to the customer.
 */
class PdfQuoteMail extends Mailable
{
    use Queueable, SerializesModels;

    public QuoteRequest $quoteRequest;
    protected PdfQuoteService $pdfService;

    /**
     * Create a new message instance.
     */
    public function __construct(QuoteRequest $quoteRequest)
    {
        $this->quoteRequest = $quoteRequest;
        $this->pdfService = app(PdfQuoteService::class);
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Ihr Angebot von YLA Umzug - {$this->quoteRequest->quote_number}",
            from: [
                'address' => config('mail.from.address'),
                'name' => 'YLA Umzug Team',
            ],
            replyTo: [
                [
                    'address' => config('mail.from.address'),
                    'name' => 'YLA Umzug Team',
                ]
            ]
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            html: 'emails.pdf-quote',
            text: 'emails.pdf-quote-text',
            with: [
                'quote' => $this->quoteRequest,
                'customerName' => $this->quoteRequest->name,
                'quoteNumber' => $this->quoteRequest->quote_number,
                'services' => $this->quoteRequest->services_string,
                'totalAmount' => $this->quoteRequest->final_quote_amount ?? $this->quoteRequest->estimated_total,
                'validUntil' => now()->addDays(30)->format('d.m.Y'),
            ]
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        try {
            $pdfContent = $this->pdfService->getPdfContent($this->quoteRequest);
            $filename = "Angebot-{$this->quoteRequest->quote_number}.pdf";

            return [
                Attachment::fromData(fn () => $pdfContent, $filename)
                    ->withMime('application/pdf')
            ];

        } catch (\Exception $e) {
            \Log::error('Failed to attach PDF to email', [
                'quote_id' => $this->quoteRequest->id,
                'error' => $e->getMessage()
            ]);
            
            // Return empty array if PDF generation fails
            // The email will still be sent without attachment
            return [];
        }
    }
}