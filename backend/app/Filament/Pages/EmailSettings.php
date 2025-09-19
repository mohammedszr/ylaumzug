<?php

namespace App\Filament\Pages;

use App\Services\EmailNotificationService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use App\Models\Setting;

class EmailSettings extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-envelope';
    protected static ?string $navigationLabel = 'E-Mail Einstellungen';
    protected static ?string $navigationGroup = 'System';
    protected static ?int $navigationSort = 91;
    protected static string $view = 'filament.pages.email-settings';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'from_address' => Setting::getValue('email.from_address', config('mail.from.address')),
            'from_name' => Setting::getValue('email.from_name', config('mail.from.name')),
            'response_time' => Setting::getValue('email.response_time', '24 Stunden'),
            'business_email' => Setting::getValue('general.business_email', 'info@yla-umzug.de'),
            'business_phone' => Setting::getValue('general.business_phone', '+49 1575 0693353'),
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('E-Mail Konfiguration')
                    ->description('Grundlegende E-Mail-Einstellungen für ausgehende Nachrichten')
                    ->schema([
                        Forms\Components\TextInput::make('from_address')
                            ->label('Absender E-Mail')
                            ->email()
                            ->required()
                            ->helperText('E-Mail-Adresse, die als Absender verwendet wird'),
                            
                        Forms\Components\TextInput::make('from_name')
                            ->label('Absender Name')
                            ->required()
                            ->helperText('Name, der als Absender angezeigt wird'),
                            
                        Forms\Components\TextInput::make('response_time')
                            ->label('Antwortzeit')
                            ->required()
                            ->helperText('Erwartete Antwortzeit für Kundenanfragen'),
                    ])
                    ->columns(2),
                    
                Forms\Components\Section::make('Geschäftsinformationen')
                    ->description('Kontaktinformationen für E-Mail-Templates')
                    ->schema([
                        Forms\Components\TextInput::make('business_email')
                            ->label('Geschäfts E-Mail')
                            ->email()
                            ->required()
                            ->helperText('Hauptgeschäfts-E-Mail-Adresse'),
                            
                        Forms\Components\TextInput::make('business_phone')
                            ->label('Geschäftstelefon')
                            ->tel()
                            ->required()
                            ->helperText('Hauptgeschäftstelefonnummer'),
                    ])
                    ->columns(2),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        Setting::setValue('email.from_address', $data['from_address'], 'string', 'email');
        Setting::setValue('email.from_name', $data['from_name'], 'string', 'email');
        Setting::setValue('email.response_time', $data['response_time'], 'string', 'email');
        Setting::setValue('general.business_email', $data['business_email'], 'string', 'general');
        Setting::setValue('general.business_phone', $data['business_phone'], 'string', 'general');

        Notification::make()
            ->title('E-Mail-Einstellungen gespeichert')
            ->success()
            ->send();
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('test_email')
                ->label('Test E-Mail senden')
                ->icon('heroicon-o-paper-airplane')
                ->color('info')
                ->form([
                    Forms\Components\TextInput::make('test_email')
                        ->label('Test E-Mail Adresse')
                        ->email()
                        ->required()
                        ->default(Setting::getValue('general.business_email', 'info@yla-umzug.de')),
                ])
                ->action(function (array $data): void {
                    $emailService = app(EmailNotificationService::class);
                    $success = $emailService->sendTestEmail($data['test_email']);
                    
                    if ($success) {
                        Notification::make()
                            ->title('Test E-Mail gesendet')
                            ->body("Test E-Mail wurde an {$data['test_email']} gesendet.")
                            ->success()
                            ->send();
                    } else {
                        Notification::make()
                            ->title('Test E-Mail fehlgeschlagen')
                            ->body('Die Test E-Mail konnte nicht gesendet werden. Überprüfen Sie die Konfiguration.')
                            ->danger()
                            ->send();
                    }
                }),
                
            Action::make('view_status')
                ->label('E-Mail Status')
                ->icon('heroicon-o-information-circle')
                ->color('gray')
                ->modalContent(function (): \Illuminate\Contracts\Support\Htmlable {
                    $emailService = app(EmailNotificationService::class);
                    $status = $emailService->getEmailStatus();
                    
                    $html = '<div class="space-y-4">';
                    $html .= '<h3 class="text-lg font-semibold">E-Mail Konfigurationsstatus</h3>';
                    $html .= '<div class="grid grid-cols-2 gap-4">';
                    
                    foreach ($status as $key => $value) {
                        $label = match($key) {
                            'mailer' => 'Mailer',
                            'host' => 'SMTP Host',
                            'port' => 'SMTP Port',
                            'encryption' => 'Verschlüsselung',
                            'from_address' => 'Absender E-Mail',
                            'from_name' => 'Absender Name',
                            'business_email' => 'Geschäfts E-Mail',
                            'configured' => 'Konfiguriert',
                            default => $key
                        };
                        
                        $displayValue = is_bool($value) ? ($value ? 'Ja' : 'Nein') : ($value ?: 'Nicht gesetzt');
                        $color = $key === 'configured' ? ($value ? 'text-green-600' : 'text-red-600') : 'text-gray-600';
                        
                        $html .= "<div><strong>{$label}:</strong> <span class=\"{$color}\">{$displayValue}</span></div>";
                    }
                    
                    $html .= '</div></div>';
                    
                    return new \Illuminate\Support\HtmlString($html);
                })
                ->modalHeading('E-Mail Konfigurationsstatus')
                ->modalSubmitAction(false)
                ->modalCancelActionLabel('Schließen'),
        ];
    }
}