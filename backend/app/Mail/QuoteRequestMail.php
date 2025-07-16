<?php

namespace App\Mail;

use App\Models\QuoteRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Email sent to business owner when a new quote request is submitted
 * 
 * This email contains all customer details, service requirements, and pricing information
 * to help the business owner prepare a comprehensive quote response.
 * 
 * @see backend/ADMIN_CONFIGURATION.md for email customization
 */
class QuoteRequestMail extends Mailable
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
            subject: "Neue Anfrage #{$this->quoteRequest->quote_number} - {$this->quoteRequest->services_string}",
            replyTo: [
                [
                    'address' => $this->quoteRequest->email,
                    'name' => $this->quoteRequest->name,
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
            html: 'emails.quote-request',
            text: 'emails.quote-request-text',
            with: [
                'quote' => $this->quoteRequest,
                'customerName' => $this->quoteRequest->name,
                'quoteNumber' => $this->quoteRequest->quote_number,
                'services' => $this->quoteRequest->services_string,
                'estimatedTotal' => $this->quoteRequest->estimated_total,
                'serviceDetails' => $this->formatServiceDetails(),
                'adminUrl' => config('app.url') . '/admin/quotes/' . $this->quoteRequest->id
            ]
        );
    }

    /**
     * Format service details for email display
     */
    private function formatServiceDetails(): array
    {
        $details = [];
        $serviceDetails = $this->quoteRequest->service_details;

        // Format moving details
        if (in_array('umzug', $this->quoteRequest->selected_services)) {
            $movingDetails = $serviceDetails['movingDetails'] ?? [];
            $details['Umzug'] = [
                'Auszugsadresse' => $this->formatAddress($movingDetails['fromAddress'] ?? []),
                'Einzugsadresse' => $this->formatAddress($movingDetails['toAddress'] ?? []),
                'Wohnungsgröße' => ($movingDetails['apartmentSize'] ?? '') . ' m²',
                'Zimmeranzahl' => $movingDetails['rooms'] ?? 'Nicht angegeben',
                'Kartons (geschätzt)' => $movingDetails['boxes'] ?? '0',
                'Zusatzleistungen' => $this->formatAdditionalServices($movingDetails['additionalServices'] ?? []),
                'Besondere Gegenstände' => $movingDetails['specialItems'] ?? 'Keine Angabe'
            ];
        }

        // Format decluttering details
        if (in_array('entruempelung', $this->quoteRequest->selected_services)) {
            $declutterDetails = $serviceDetails['declutterDetails'] ?? [];
            $details['Entrümpelung'] = [
                'Adresse' => $this->formatAddress($declutterDetails['address'] ?? []),
                'Objektart' => $this->translateObjectType($declutterDetails['objectType'] ?? ''),
                'Größe' => ($declutterDetails['size'] ?? '') . ' m²',
                'Volumen' => $this->translateVolume($declutterDetails['volume'] ?? ''),
                'Müllarten' => $this->formatWasteTypes($declutterDetails['wasteTypes'] ?? []),
                'Besenreine Übergabe' => ($declutterDetails['cleanHandover'] ?? '') === 'yes' ? 'Ja' : 'Nein',
                'Zusätzliche Infos' => $declutterDetails['additionalInfo'] ?? 'Keine Angabe'
            ];
        }

        // Format cleaning details
        if (in_array('putzservice', $this->quoteRequest->selected_services)) {
            $cleaningDetails = $serviceDetails['cleaningDetails'] ?? [];
            $details['Putzservice'] = [
                'Objektart' => $this->translateObjectType($cleaningDetails['objectType'] ?? ''),
                'Größe' => ($cleaningDetails['size'] ?? '') . ' m²',
                'Reinigungsintensität' => $this->translateCleaningIntensity($cleaningDetails['cleaningIntensity'] ?? ''),
                'Bereiche' => $this->formatRooms($cleaningDetails['rooms'] ?? []),
                'Häufigkeit' => $this->translateFrequency($cleaningDetails['frequency'] ?? ''),
                'Schlüsselübergabe' => $this->translateKeyHandover($cleaningDetails['keyHandover'] ?? '')
            ];
        }

        return $details;
    }

    /**
     * Format address for display
     */
    private function formatAddress(array $address): string
    {
        if (empty($address)) {
            return 'Nicht angegeben';
        }

        $parts = array_filter([
            $address['street'] ?? '',
            $address['postalCode'] ?? '',
            $address['city'] ?? ''
        ]);

        return implode(', ', $parts) ?: 'Nicht angegeben';
    }

    /**
     * Format additional services
     */
    private function formatAdditionalServices(array $services): string
    {
        if (empty($services)) {
            return 'Keine';
        }

        $serviceNames = [
            'assembly' => 'Möbelabbau & Aufbau',
            'packing' => 'Verpackungsservice',
            'parking' => 'Halteverbotszone',
            'storage' => 'Einlagerung',
            'disposal' => 'Entsorgung'
        ];

        $formatted = array_map(function($service) use ($serviceNames) {
            return $serviceNames[$service] ?? $service;
        }, $services);

        return implode(', ', $formatted);
    }

    /**
     * Format waste types
     */
    private function formatWasteTypes(array $wasteTypes): string
    {
        if (empty($wasteTypes)) {
            return 'Nicht angegeben';
        }

        $typeNames = [
            'furniture' => 'Sperrmüll',
            'electronics' => 'Elektrogeräte',
            'hazardous' => 'Sondermüll',
            'household' => 'Hausrat',
            'construction' => 'Bauschutt'
        ];

        $formatted = array_map(function($type) use ($typeNames) {
            return $typeNames[$type] ?? $type;
        }, $wasteTypes);

        return implode(', ', $formatted);
    }

    /**
     * Format rooms for cleaning
     */
    private function formatRooms(array $rooms): string
    {
        if (empty($rooms)) {
            return 'Nicht angegeben';
        }

        $roomNames = [
            'kitchen' => 'Küche',
            'bathroom' => 'Badezimmer/WC',
            'livingRooms' => 'Wohnräume',
            'windows' => 'Fensterreinigung'
        ];

        $formatted = array_map(function($room) use ($roomNames) {
            return $roomNames[$room] ?? $room;
        }, $rooms);

        return implode(', ', $formatted);
    }

    /**
     * Translation helper methods
     */
    private function translateObjectType(string $type): string
    {
        $types = [
            'apartment' => 'Wohnung',
            'house' => 'Haus',
            'basement' => 'Keller',
            'garage' => 'Garage',
            'office' => 'Büro',
            'attic' => 'Dachboden'
        ];

        return $types[$type] ?? $type;
    }

    private function translateVolume(string $volume): string
    {
        $volumes = [
            'low' => 'Wenig (1-2 Container)',
            'medium' => 'Mittel (3-5 Container)',
            'high' => 'Viel (6+ Container)',
            'extreme' => 'Sehr viel (Messi-Haushalt)'
        ];

        return $volumes[$volume] ?? $volume;
    }

    private function translateCleaningIntensity(string $intensity): string
    {
        $intensities = [
            'normal' => 'Normalreinigung',
            'deep' => 'Grundreinigung',
            'construction' => 'Bauschlussreinigung'
        ];

        return $intensities[$intensity] ?? $intensity;
    }

    private function translateFrequency(string $frequency): string
    {
        $frequencies = [
            'once' => 'Einmalig',
            'weekly' => 'Wöchentlich',
            'biweekly' => '14-tägig',
            'monthly' => 'Monatlich'
        ];

        return $frequencies[$frequency] ?? $frequency;
    }

    private function translateKeyHandover(string $keyHandover): string
    {
        $options = [
            'present' => 'Ich bin vor Ort',
            'key' => 'Schlüsselübergabe nötig'
        ];

        return $options[$keyHandover] ?? 'Nicht angegeben';
    }
}