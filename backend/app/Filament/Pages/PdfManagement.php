<?php

namespace App\Filament\Pages;

use App\Services\PdfQuoteService;
use Filament\Pages\Page;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Storage;

class PdfManagement extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationLabel = 'PDF Verwaltung';
    protected static ?string $navigationGroup = 'System';
    protected static ?int $navigationSort = 92;
    protected static string $view = 'filament.pages.pdf-management';

    public array $stats = [];

    public function mount(): void
    {
        $pdfService = app(PdfQuoteService::class);
        $this->stats = $pdfService->getStorageStats();
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('cleanup_old_pdfs')
                ->label('Alte PDFs löschen')
                ->icon('heroicon-o-trash')
                ->color('danger')
                ->form([
                    \Filament\Forms\Components\TextInput::make('days')
                        ->label('Tage alt')
                        ->numeric()
                        ->default(90)
                        ->required()
                        ->helperText('PDFs älter als diese Anzahl Tage werden gelöscht'),
                ])
                ->requiresConfirmation()
                ->modalHeading('Alte PDF-Dateien löschen')
                ->modalDescription('Möchten Sie wirklich alle PDF-Dateien löschen, die älter als die angegebene Anzahl von Tagen sind?')
                ->action(function (array $data): void {
                    $pdfService = app(PdfQuoteService::class);
                    $deletedCount = $pdfService->cleanupOldPdfs($data['days']);
                    
                    // Refresh stats
                    $this->stats = $pdfService->getStorageStats();
                    
                    Notification::make()
                        ->title('PDF-Bereinigung abgeschlossen')
                        ->body("{$deletedCount} alte PDF-Dateien wurden gelöscht.")
                        ->success()
                        ->send();
                }),
                
            Action::make('refresh_stats')
                ->label('Statistiken aktualisieren')
                ->icon('heroicon-o-arrow-path')
                ->color('info')
                ->action(function (): void {
                    $pdfService = app(PdfQuoteService::class);
                    $this->stats = $pdfService->getStorageStats();
                    
                    Notification::make()
                        ->title('Statistiken aktualisiert')
                        ->success()
                        ->send();
                }),
                
            Action::make('test_pdf_generation')
                ->label('PDF-Test')
                ->icon('heroicon-o-document-plus')
                ->color('warning')
                ->requiresConfirmation()
                ->modalHeading('PDF-Generierung testen')
                ->modalDescription('Möchten Sie die PDF-Generierung mit einem Test-Angebot testen?')
                ->action(function (): void {
                    try {
                        // Create a test quote for PDF generation
                        $testQuote = new \App\Models\QuoteRequest([
                            'angebotsnummer' => 'TEST-' . now()->format('Y-m-d-His'),
                            'name' => 'Test Kunde',
                            'email' => 'test@example.com',
                            'telefon' => '+49 123 456789',
                            'moving_date' => now()->addDays(7),
                            'bevorzugter_kontakt' => 'email',
                            'ausgewaehlte_services' => ['umzug'],
                            'service_details' => [
                                'movingDetails' => [
                                    'fromAddress' => ['street' => 'Teststraße 1', 'postalCode' => '12345', 'city' => 'Teststadt'],
                                    'toAddress' => ['street' => 'Zielstraße 2', 'postalCode' => '54321', 'city' => 'Zielstadt'],
                                    'rooms' => 3,
                                    'apartmentSize' => '80'
                                ]
                            ],
                            'estimated_total' => 650.00,
                            'status' => 'pending'
                        ]);
                        
                        $pdfService = app(PdfQuoteService::class);
                        $pdfContent = $pdfService->getPdfContent($testQuote);
                        
                        // Save test PDF
                        $filename = 'test-pdf-' . now()->format('Y-m-d-His') . '.pdf';
                        Storage::disk('local')->put("quotes/{$filename}", $pdfContent);
                        
                        // Refresh stats
                        $this->stats = $pdfService->getStorageStats();
                        
                        Notification::make()
                            ->title('PDF-Test erfolgreich')
                            ->body("Test-PDF wurde erstellt: {$filename}")
                            ->success()
                            ->send();
                            
                    } catch (\Exception $e) {
                        Notification::make()
                            ->title('PDF-Test fehlgeschlagen')
                            ->body('Fehler: ' . $e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),
        ];
    }
}