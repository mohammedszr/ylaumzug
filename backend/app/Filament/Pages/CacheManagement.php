<?php

namespace App\Filament\Pages;

use App\Services\CacheService;
use Filament\Pages\Page;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Artisan;

class CacheManagement extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-bolt';
    protected static ?string $navigationLabel = 'Cache Verwaltung';
    protected static ?string $navigationGroup = 'System';
    protected static ?int $navigationSort = 93;
    protected static string $view = 'filament.pages.cache-management';

    public array $stats = [];

    public function mount(): void
    {
        $cacheService = app(CacheService::class);
        $this->stats = $cacheService->getStats();
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('clear_all_cache')
                ->label('Alle Caches leeren')
                ->icon('heroicon-o-trash')
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading('Alle Caches leeren')
                ->modalDescription('Möchten Sie wirklich alle Caches leeren? Dies kann die Performance vorübergehend beeinträchtigen.')
                ->action(function (): void {
                    try {
                        Cache::flush();
                        Artisan::call('config:clear');
                        Artisan::call('route:clear');
                        Artisan::call('view:clear');
                        
                        // Refresh stats
                        $cacheService = app(CacheService::class);
                        $this->stats = $cacheService->getStats();
                        
                        Notification::make()
                            ->title('Alle Caches geleert')
                            ->body('Alle Cache-Daten wurden erfolgreich gelöscht.')
                            ->success()
                            ->send();
                    } catch (\Exception $e) {
                        Notification::make()
                            ->title('Cache-Löschung fehlgeschlagen')
                            ->body('Fehler: ' . $e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),
                
            Action::make('clear_settings_cache')
                ->label('Einstellungen Cache leeren')
                ->icon('heroicon-o-cog-6-tooth')
                ->color('warning')
                ->action(function (): void {
                    try {
                        $cacheService = app(CacheService::class);
                        $cacheService->flushByType('settings');
                        
                        Notification::make()
                            ->title('Einstellungen Cache geleert')
                            ->success()
                            ->send();
                    } catch (\Exception $e) {
                        Notification::make()
                            ->title('Fehler beim Leeren des Einstellungen-Cache')
                            ->body('Fehler: ' . $e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),
                
            Action::make('clear_distance_cache')
                ->label('Entfernungs Cache leeren')
                ->icon('heroicon-o-map-pin')
                ->color('warning')
                ->action(function (): void {
                    try {
                        $cacheService = app(CacheService::class);
                        $cacheService->flushByType('distance');
                        
                        Notification::make()
                            ->title('Entfernungs Cache geleert')
                            ->success()
                            ->send();
                    } catch (\Exception $e) {
                        Notification::make()
                            ->title('Fehler beim Leeren des Entfernungs-Cache')
                            ->body('Fehler: ' . $e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),
                
            Action::make('optimize_application')
                ->label('Anwendung optimieren')
                ->icon('heroicon-o-rocket-launch')
                ->color('success')
                ->action(function (): void {
                    try {
                        // Clear old caches
                        Artisan::call('config:clear');
                        Artisan::call('route:clear');
                        Artisan::call('view:clear');
                        
                        // Rebuild optimized caches
                        Artisan::call('config:cache');
                        Artisan::call('route:cache');
                        Artisan::call('view:cache');
                        
                        // Refresh stats
                        $cacheService = app(CacheService::class);
                        $this->stats = $cacheService->getStats();
                        
                        Notification::make()
                            ->title('Anwendung optimiert')
                            ->body('Konfiguration, Routen und Views wurden für bessere Performance optimiert.')
                            ->success()
                            ->send();
                    } catch (\Exception $e) {
                        Notification::make()
                            ->title('Optimierung fehlgeschlagen')
                            ->body('Fehler: ' . $e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),
                
            Action::make('refresh_stats')
                ->label('Statistiken aktualisieren')
                ->icon('heroicon-o-arrow-path')
                ->color('info')
                ->action(function (): void {
                    $cacheService = app(CacheService::class);
                    $this->stats = $cacheService->getStats();
                    
                    Notification::make()
                        ->title('Statistiken aktualisiert')
                        ->success()
                        ->send();
                }),
        ];
    }
}