<?php

namespace App\Mail;

use App\Models\QuoteRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class QuoteReadyMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public QuoteRequest $quote
    ) {}

    public function build()
    {
        return $this->subject('Ihr Angebot ist bereit - ' . $this->quote->angebotsnummer)
            ->view('emails.quote-ready')
            ->with([
                'quote' => $this->quote,
                'finalAmount' => number_format($this->quote->endgueltiger_angebotsbetrag, 2) . ' €'
            ]);
    }
}