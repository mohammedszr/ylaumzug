<?php

namespace App\Services;

use App\Models\QuoteRequest;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

/**
 * Service for generating PDF quotes from quote requests
 * 
 * This service handles the creation of professional PDF quotes that can be
 * emailed to customers or stored for record keeping.
 */
class PdfQuoteService
{
    /**
     * Generate PDF quote for a quote request
     */
    public function generateQuotePdf(QuoteRequest $quote): string
    {
        try {
            // Load the PDF view with quote data
            $pdf = Pdf::loadView('pdf.quote', [
                'quote' => $quote,
                'formatAddress' => [$this, 'formatAddress'],
                'formatAdditionalServices' => [$this, 'formatAdditionalServices'],
                'formatWasteTypes' => [$this, 'formatWasteTypes'],
                'formatRooms' => [$this, 'formatRooms'],
                'translateObjectType' => [$this, 'translateObjectType'],
                'translateVolume' => [$this, 'translateVolume'],
                'translateCleaningIntensity' => [$this, 'translateCleaningIntensity'],
                'translateFrequency' => [$this, 'translateFrequency'],
                'translateKeyHandover' => [$this, 'translateKeyHandover'],
            ]);

            // Configure PDF settings
            $pdf->setPaper('A4', 'portrait');
            $pdf->setOptions([
                'dpi' => 150,
                'defaultFont' => 'DejaVu Sans',
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => false,
                'debugKeepTemp' => false,
            ]);

            // Generate filename
            $filename = $this->generatePdfFilename($quote);
            
            // Save PDF to storage
            $pdfContent = $pdf->output();
            Storage::disk('local')->put("quotes/{$filename}", $pdfContent);

            Log::info('PDF quote generated successfully', [
                'quote_id' => $quote->id,
                'filename' => $filename,
                'file_size' => strlen($pdfContent)
            ]);

            return $filename;

        } catch (\Exception $e) {
            Log::error('PDF generation failed', [
                'quote_id' => $quote->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            throw new \Exception('PDF-Generierung fehlgeschlagen: ' . $e->getMessage());
        }
    }

    /**
     * Get PDF content as string for email attachment
     */
    public function getPdfContent(QuoteRequest $quote): string
    {
        try {
            $pdf = Pdf::loadView('pdf.quote', [
                'quote' => $quote,
                'formatAddress' => [$this, 'formatAddress'],
                'formatAdditionalServices' => [$this, 'formatAdditionalServices'],
                'formatWasteTypes' => [$this, 'formatWasteTypes'],
                'formatRooms' => [$this, 'formatRooms'],
                'translateObjectType' => [$this, 'translateObjectType'],
                'translateVolume' => [$this, 'translateVolume'],
                'translateCleaningIntensity' => [$this, 'translateCleaningIntensity'],
                'translateFrequency' => [$this, 'translateFrequency'],
                'translateKeyHandover' => [$this, 'translateKeyHandover'],
            ]);

            $pdf->setPaper('A4', 'portrait');
            $pdf->setOptions([
                'dpi' => 150,
                'defaultFont' => 'DejaVu Sans',
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => false,
            ]);

            return $pdf->output();

        } catch (\Exception $e) {
            Log::error('PDF content generation failed', [
                'quote_id' => $quote->id,
                'error' => $e->getMessage()
            ]);
            
            throw new \Exception('PDF-Inhalt konnte nicht generiert werden: ' . $e->getMessage());
        }
    }

    /**
     * Generate standardized PDF filename
     */
    private function generatePdfFilename(QuoteRequest $quote): string
    {
        $date = now()->format('Y-m-d');
        $quoteNumber = str_replace(['/', '\\', ' '], '-', $quote->quote_number);
        
        return "angebot-{$quoteNumber}-{$date}.pdf";
    }

    /**
     * Format address for display
     */
    public function formatAddress(array $address): string
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
    public function formatAdditionalServices(array $services): string
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
    public function formatWasteTypes(array $wasteTypes): string
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
    public function formatRooms(array $rooms): string
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
    public function translateObjectType(string $type): string
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

    public function translateVolume(string $volume): string
    {
        $volumes = [
            'low' => 'Wenig (1-2 Container)',
            'medium' => 'Mittel (3-5 Container)',
            'high' => 'Viel (6+ Container)',
            'extreme' => 'Sehr viel (Messi-Haushalt)'
        ];

        return $volumes[$volume] ?? $volume;
    }

    public function translateCleaningIntensity(string $intensity): string
    {
        $intensities = [
            'normal' => 'Normalreinigung',
            'deep' => 'Grundreinigung',
            'construction' => 'Bauschlussreinigung'
        ];

        return $intensities[$intensity] ?? $intensity;
    }

    public function translateFrequency(string $frequency): string
    {
        $frequencies = [
            'once' => 'Einmalig',
            'weekly' => 'Wöchentlich',
            'biweekly' => '14-tägig',
            'monthly' => 'Monatlich'
        ];

        return $frequencies[$frequency] ?? $frequency;
    }

    public function translateKeyHandover(string $keyHandover): string
    {
        $options = [
            'present' => 'Ich bin vor Ort',
            'key' => 'Schlüsselübergabe nötig'
        ];

        return $options[$keyHandover] ?? 'Nicht angegeben';
    }

    /**
     * Clean up old PDF files (optional maintenance method)
     */
    public function cleanupOldPdfs(int $daysOld = 90): int
    {
        try {
            $files = Storage::disk('local')->files('quotes');
            $deletedCount = 0;
            $cutoffDate = now()->subDays($daysOld);

            foreach ($files as $file) {
                $lastModified = Storage::disk('local')->lastModified($file);
                
                if ($lastModified < $cutoffDate->timestamp) {
                    Storage::disk('local')->delete($file);
                    $deletedCount++;
                }
            }

            Log::info('PDF cleanup completed', [
                'deleted_files' => $deletedCount,
                'cutoff_days' => $daysOld
            ]);

            return $deletedCount;

        } catch (\Exception $e) {
            Log::error('PDF cleanup failed', [
                'error' => $e->getMessage()
            ]);
            
            return 0;
        }
    }
}