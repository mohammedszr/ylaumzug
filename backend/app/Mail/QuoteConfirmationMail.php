<?php

namespace App\Mail;

use App\Models\QuoteRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Email sent to customer confirming their quote request has been received
 * 
 * This email provides confirmation, sets expectations, and includes next steps
 * to maintain customer engagement and trust.
 */
class QuoteConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    public QuoteRequest $quoteRequest;

    /**
     * Create a new message instance.
     */
    public function __construct(QuoteRequest $quoteRequest)
    {
        $this->quoteRequest = $quoteRequest;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Bestätigung Ihrer Anfrage #{$this->quoteRequest->quote_number} - YLA Umzug",
            from: [
                'address' => config('mail.from.address'),
                'name' => config('mail.from.name'),
            ]
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            html: 'emails.quote-confirmation',
            text: 'emails.quote-confirmation-text',
            with: [
                'quote' => $this->quoteRequest,
                'customerName' => $this->quoteRequest->name,
                'quoteNumber' => $this->quoteRequest->quote_number,
                'services' => $this->quoteRequest->services_string,
                'estimatedTotal' => $this->quoteRequest->estimated_total,
                'businessEmail' => env('BUSINESS_EMAIL', 'info@yla-umzug.de'),
                'businessPhone' => env('BUSINESS_PHONE', '+49 123 456789'),
                'responseTime' => env('EMAIL_RESPONSE_TIME', '24 Stunden')
            ]
        );
    }
}