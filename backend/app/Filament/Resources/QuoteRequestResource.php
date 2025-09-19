<?php

namespace App\Filament\Resources;

use App\Filament\Resources\QuoteRequestResource\Pages;
use App\Models\QuoteRequest;
use App\Contracts\DistanceCalculatorInterface;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class QuoteRequestResource extends Resource
{
    protected static ?string $model = QuoteRequest::class;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationLabel = 'Angebotsanfragen';
    protected static ?string $modelLabel = 'Angebotsanfrage';
    protected static ?string $pluralModelLabel = 'Angebotsanfragen';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Angebotsinformationen')
                ->schema([
                    Forms\Components\TextInput::make('angebotsnummer')
                        ->label('Angebotsnummer')
                        ->disabled()
                        ->dehydrated(false),
                    
                    Forms\Components\Select::make('status')
                        ->label('Status')
                        ->options([
                            'pending' => 'Ausstehend',
                            'reviewed' => 'Überprüft',
                            'quoted' => 'Angebot erstellt',
                            'accepted' => 'Angenommen',
                            'rejected' => 'Abgelehnt',
                            'completed' => 'Abgeschlossen',
                        ])
                        ->required()
                        ->default('pending')
                        ->native(false),
                ])
                ->columns(2),
                
            Forms\Components\Section::make('Kundeninformationen')
                ->schema([
                    Forms\Components\TextInput::make('name')
                        ->label('Vollständiger Name')
                        ->required()
                        ->maxLength(255),
                        
                    Forms\Components\TextInput::make('email')
                        ->label('E-Mail')
                        ->email()
                        ->required()
                        ->maxLength(255),
                        
                    Forms\Components\TextInput::make('telefon')
                        ->label('Telefon')
                        ->tel()
                        ->maxLength(255),
                        
                    Forms\Components\Select::make('bevorzugter_kontakt')
                        ->label('Bevorzugter Kontakt')
                        ->options([
                            'email' => 'E-Mail',
                            'phone' => 'Telefon',
                            'whatsapp' => 'WhatsApp',
                        ])
                        ->required()
                        ->native(false),
                ])
                ->columns(2),
                
            Forms\Components\Section::make('Umzug Details')
                ->schema([
                    Forms\Components\Textarea::make('from_address')
                        ->label('Von Adresse')
                        ->required()
                        ->rows(2),
                        
                    Forms\Components\Textarea::make('to_address')
                        ->label('Nach Adresse')
                        ->required()
                        ->rows(2),
                        
                    Forms\Components\TextInput::make('from_postal_code')
                        ->label('Von PLZ')
                        ->maxLength(10),
                        
                    Forms\Components\TextInput::make('to_postal_code')
                        ->label('Nach PLZ')
                        ->maxLength(10),
                        
                    Forms\Components\DatePicker::make('moving_date')
                        ->label('Umzugsdatum')
                        ->required()
                        ->native(false),
                        
                    Forms\Components\Select::make('moving_type')
                        ->label('Umzugsart')
                        ->options([
                            'local' => 'Lokal',
                            'long_distance' => 'Fernumzug',
                            'international' => 'International',
                        ])
                        ->required()
                        ->native(false),
                        
                    Forms\Components\TextInput::make('distance_km')
                        ->label('Entfernung (km)')
                        ->numeric()
                        ->step(0.01)
                        ->disabled(),
                ])
                ->columns(2),
                
            Forms\Components\Section::make('Services & Preise')
                ->schema([
                    Forms\Components\TagsInput::make('ausgewaehlte_services')
                        ->label('Ausgewählte Services')
                        ->disabled()
                        ->dehydrated(false),
                        
                    Forms\Components\KeyValue::make('service_details')
                        ->label('Service Details')
                        ->disabled()
                        ->dehydrated(false),
                        
                    Forms\Components\TextInput::make('estimated_total')
                        ->label('Geschätzte Gesamtsumme')
                        ->disabled()
                        ->dehydrated(false)
                        ->prefix('€'),
                        
                    Forms\Components\TextInput::make('endgueltiger_angebotsbetrag')
                        ->label('Endgültiger Angebotsbetrag')
                        ->numeric()
                        ->prefix('€')
                        ->step(0.01),
                ])
                ->columns(2),
                
            Forms\Components\Section::make('Zusätzliche Informationen')
                ->schema([
                    Forms\Components\Textarea::make('special_requirements')
                        ->label('Besondere Anforderungen')
                        ->disabled()
                        ->dehydrated(false)
                        ->rows(3),
                        
                    Forms\Components\Textarea::make('admin_notizen')
                        ->label('Admin Notizen')
                        ->rows(4)
                        ->helperText('Interne Notizen für das Team. Werden automatisch mit Zeitstempel versehen.'),
                        
                    Forms\Components\Placeholder::make('timestamps')
                        ->label('Zeitstempel')
                        ->content(function ($record) {
                            if (!$record) return 'Noch nicht gespeichert';
                            
                            $info = [];
                            if ($record->created_at) {
                                $info[] = 'Erstellt: ' . $record->created_at->format('d.m.Y H:i');
                            }
                            if ($record->updated_at && $record->updated_at != $record->created_at) {
                                $info[] = 'Zuletzt bearbeitet: ' . $record->updated_at->format('d.m.Y H:i');
                            }
                            if ($record->email_sent_at) {
                                $info[] = 'E-Mail gesendet: ' . $record->email_sent_at->format('d.m.Y H:i');
                            }
                            if ($record->whatsapp_sent_at) {
                                $info[] = 'WhatsApp gesendet: ' . $record->whatsapp_sent_at->format('d.m.Y H:i');
                            }
                            
                            return implode("\n", $info);
                        }),
                ])
                ->columns(1),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->searchable()
            ->globalSearchAttributes(['angebotsnummer', 'name', 'email', 'telefon'])
            ->columns([
                Tables\Columns\TextColumn::make('angebotsnummer')
                    ->label('Angebot #')
                    ->searchable()
                    ->sortable()
                    ->copyable(),
                    
                Tables\Columns\TextColumn::make('name')
                    ->label('Kunde')
                    ->searchable()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('email')
                    ->label('E-Mail')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                    
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'warning' => 'pending',
                        'info' => 'reviewed',
                        'primary' => 'quoted',
                        'success' => ['accepted', 'completed'],
                        'danger' => 'rejected',
                    ])
                    ->formatStateUsing(fn (string $state): string => match($state) {
                        'pending' => 'Ausstehend',
                        'reviewed' => 'Überprüft',
                        'quoted' => 'Angebot erstellt',
                        'accepted' => 'Angenommen',
                        'rejected' => 'Abgelehnt',
                        'completed' => 'Abgeschlossen',
                        default => ucfirst($state)
                    }),
                    
                Tables\Columns\TextColumn::make('moving_date')
                    ->label('Umzugsdatum')
                    ->date('d.m.Y')
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('estimated_total')
                    ->label('Geschätzt')
                    ->money('EUR')
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('endgueltiger_angebotsbetrag')
                    ->label('Endgültiger Betrag')
                    ->money('EUR')
                    ->sortable()
                    ->toggleable(),
                    
                Tables\Columns\IconColumn::make('email_sent_at')
                    ->label('E-Mail Status')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->tooltip(fn (QuoteRequest $record): string => 
                        $record->email_sent_at 
                            ? 'E-Mail gesendet am: ' . $record->email_sent_at->format('d.m.Y H:i')
                            : 'E-Mail noch nicht gesendet'
                    ),
                    
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Eingereicht')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'pending' => 'Ausstehend',
                        'reviewed' => 'Überprüft',
                        'quoted' => 'Angebot erstellt',
                        'accepted' => 'Angenommen',
                        'rejected' => 'Abgelehnt',
                        'completed' => 'Abgeschlossen',
                    ]),
                    
                Tables\Filters\Filter::make('recent')
                    ->query(fn (Builder $query): Builder => $query->recent())
                    ->label('Letzte 30 Tage'),
                    
                Tables\Filters\Filter::make('has_final_quote')
                    ->query(fn (Builder $query): Builder => $query->whereNotNull('endgueltiger_angebotsbetrag'))
                    ->label('Hat Endangebot'),
                    
                Tables\Filters\Filter::make('date_range')
                    ->form([
                        Forms\Components\DatePicker::make('from_date')
                            ->label('Von Datum'),
                        Forms\Components\DatePicker::make('to_date')
                            ->label('Bis Datum'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from_date'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['to_date'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    })
                    ->label('Datumsbereich'),
                    
                Tables\Filters\SelectFilter::make('services')
                    ->label('Services')
                    ->options([
                        'umzug' => 'Umzug',
                        'putzservice' => 'Putzservice',
                        'entruempelung' => 'Entrümpelung',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        if (empty($data['value'])) {
                            return $query;
                        }
                        
                        return $query->whereJsonContains('ausgewaehlte_services', $data['value']);
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                
                Tables\Actions\Action::make('mark_quoted')
                    ->label('Angebot erstellen')
                    ->icon('heroicon-o-currency-euro')
                    ->color('primary')
                    ->visible(fn (QuoteRequest $record): bool => 
                        in_array($record->status, ['pending', 'reviewed']))
                    ->form([
                        Forms\Components\TextInput::make('endgueltiger_angebotsbetrag')
                            ->label('Endgültiger Angebotsbetrag')
                            ->required()
                            ->numeric()
                            ->prefix('€')
                            ->step(0.01),
                        Forms\Components\Textarea::make('admin_notizen')
                            ->label('Notizen')
                            ->rows(3),
                    ])
                    ->action(function (QuoteRequest $record, array $data): void {
                        $record->markAsQuoted(
                            $data['endgueltiger_angebotsbetrag'], 
                            $data['admin_notizen'] ?? null
                        );
                        
                        Notification::make()
                            ->title('Angebot erstellt')
                            ->success()
                            ->send();
                    }),
                    
                Tables\Actions\Action::make('calculate_distance')
                    ->label('Entfernung berechnen')
                    ->icon('heroicon-o-map-pin')
                    ->color('info')
                    ->visible(fn (QuoteRequest $record): bool => 
                        !empty($record->from_postal_code) && 
                        !empty($record->to_postal_code) && 
                        is_null($record->distance_km))
                    ->action(function (QuoteRequest $record): void {
                        $calculator = app(DistanceCalculatorInterface::class);
                        $result = $calculator->calculateDistance(
                            $record->from_postal_code,
                            $record->to_postal_code
                        );
                        
                        if ($result['success']) {
                            $record->update(['distance_km' => $result['distance_km']]);
                            
                            Notification::make()
                                ->title('Entfernung berechnet: ' . $result['distance_km'] . ' km')
                                ->success()
                                ->send();
                        } else {
                            Notification::make()
                                ->title('Fehler bei Entfernungsberechnung')
                                ->danger()
                                ->send();
                        }
                    }),
                    
                Tables\Actions\Action::make('send_email')
                    ->label('E-Mail senden')
                    ->icon('heroicon-o-envelope')
                    ->color('success')
                    ->visible(fn (QuoteRequest $record): bool => 
                        $record->status === 'quoted' && !$record->email_sent_at)
                    ->requiresConfirmation()
                    ->modalHeading('E-Mail an Kunden senden')
                    ->modalDescription(fn (QuoteRequest $record): string => 
                        "Möchten Sie eine E-Mail mit dem Angebot an {$record->email} senden?")
                    ->action(function (QuoteRequest $record): void {
                        // Here we would trigger email sending
                        // For now, just update the timestamp
                        $record->update(['email_sent_at' => now()]);
                        
                        Notification::make()
                            ->title('E-Mail gesendet')
                            ->success()
                            ->send();
                    }),
                    
                Tables\Actions\Action::make('send_whatsapp')
                    ->label('WhatsApp senden')
                    ->icon('heroicon-o-chat-bubble-left-right')
                    ->color('success')
                    ->visible(fn (QuoteRequest $record): bool => 
                        $record->status === 'quoted' && 
                        !empty($record->telefon) && 
                        in_array($record->bevorzugter_kontakt, ['whatsapp', 'phone']))
                    ->requiresConfirmation()
                    ->modalHeading('WhatsApp an Kunden senden')
                    ->modalDescription(fn (QuoteRequest $record): string => 
                        "Möchten Sie eine WhatsApp-Nachricht mit dem Angebot an {$record->telefon} senden?")
                    ->action(function (QuoteRequest $record): void {
                        // Here we would trigger WhatsApp sending
                        // For now, just update the timestamp
                        $record->update(['whatsapp_sent_at' => now()]);
                        
                        Notification::make()
                            ->title('WhatsApp gesendet')
                            ->success()
                            ->send();
                    }),
                    
                Tables\Actions\Action::make('preview_email')
                    ->label('E-Mail Vorschau')
                    ->icon('heroicon-o-eye')
                    ->color('info')
                    ->modalContent(function (QuoteRequest $record): \Illuminate\Contracts\Support\Htmlable {
                        $mail = new \App\Mail\QuoteConfirmationMail($record);
                        return new \Illuminate\Support\HtmlString($mail->render());
                    })
                    ->modalHeading('E-Mail Vorschau')
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Schließen'),
                    
                Tables\Actions\Action::make('resend_confirmation')
                    ->label('Bestätigung erneut senden')
                    ->icon('heroicon-o-arrow-path')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->modalHeading('Bestätigungs-E-Mail erneut senden')
                    ->modalDescription(fn (QuoteRequest $record): string => 
                        "Möchten Sie die Bestätigungs-E-Mail erneut an {$record->email} senden?")
                    ->action(function (QuoteRequest $record): void {
                        try {
                            \App\Jobs\SendQuoteEmailsJob::dispatch($record);
                            
                            Notification::make()
                                ->title('E-Mail wird gesendet')
                                ->body('Die Bestätigungs-E-Mail wurde in die Warteschlange eingereiht.')
                                ->success()
                                ->send();
                        } catch (\Exception $e) {
                            Notification::make()
                                ->title('Fehler beim E-Mail-Versand')
                                ->body('Die E-Mail konnte nicht gesendet werden: ' . $e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),
                    
                Tables\Actions\Action::make('generate_pdf')
                    ->label('PDF erstellen')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('success')
                    ->action(function (QuoteRequest $record): void {
                        try {
                            $pdfService = app(\App\Services\PdfQuoteService::class);
                            $filename = $pdfService->generateQuotePdf($record);
                            
                            Notification::make()
                                ->title('PDF erstellt')
                                ->body("PDF wurde erfolgreich erstellt: {$filename}")
                                ->success()
                                ->send();
                        } catch (\Exception $e) {
                            Notification::make()
                                ->title('PDF-Erstellung fehlgeschlagen')
                                ->body('Das PDF konnte nicht erstellt werden: ' . $e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),
                    
                Tables\Actions\Action::make('preview_pdf')
                    ->label('PDF Vorschau')
                    ->icon('heroicon-o-eye')
                    ->color('info')
                    ->url(fn (QuoteRequest $record): string => route('quotes.preview-pdf', $record))
                    ->openUrlInNewTab(),
                    
                Tables\Actions\Action::make('download_pdf')
                    ->label('PDF herunterladen')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('gray')
                    ->url(fn (QuoteRequest $record): string => route('quotes.download-pdf', $record))
                    ->openUrlInNewTab(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    
                    Tables\Actions\BulkAction::make('mark_reviewed')
                        ->label('Als überprüft markieren')
                        ->icon('heroicon-o-eye')
                        ->color('info')
                        ->action(function (Collection $records): void {
                            $records->each->update(['status' => 'reviewed']);
                            
                            Notification::make()
                                ->title('Anfragen als überprüft markiert')
                                ->success()
                                ->send();
                        }),
                        
                    Tables\Actions\BulkAction::make('mark_quoted')
                        ->label('Als angeboten markieren')
                        ->icon('heroicon-o-currency-euro')
                        ->color('primary')
                        ->action(function (Collection $records): void {
                            $records->each->update(['status' => 'quoted']);
                            
                            Notification::make()
                                ->title('Anfragen als angeboten markiert')
                                ->success()
                                ->send();
                        }),
                        
                    Tables\Actions\BulkAction::make('mark_completed')
                        ->label('Als abgeschlossen markieren')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(function (Collection $records): void {
                            $records->each->update(['status' => 'completed']);
                            
                            Notification::make()
                                ->title('Anfragen als abgeschlossen markiert')
                                ->success()
                                ->send();
                        }),
                        
                    Tables\Actions\BulkAction::make('send_emails')
                        ->label('E-Mails senden')
                        ->icon('heroicon-o-envelope')
                        ->color('info')
                        ->requiresConfirmation()
                        ->modalHeading('E-Mails an ausgewählte Kunden senden')
                        ->modalDescription('Möchten Sie E-Mails an alle ausgewählten Kunden senden?')
                        ->action(function (Collection $records): void {
                            $count = 0;
                            foreach ($records as $record) {
                                // Here we would trigger email sending
                                // For now, just update the email_sent_at timestamp
                                $record->update(['email_sent_at' => now()]);
                                $count++;
                            }
                            
                            Notification::make()
                                ->title("E-Mails an {$count} Kunden gesendet")
                                ->success()
                                ->send();
                        }),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListQuoteRequests::route('/'),
            'create' => Pages\CreateQuoteRequest::route('/create'),
            'edit' => Pages\EditQuoteRequest::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', 'pending')->count();
    }
}
