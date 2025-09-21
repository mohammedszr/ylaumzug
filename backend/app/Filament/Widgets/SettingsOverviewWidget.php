<?php

namespace App\Filament\Widgets;

use App\Models\Setting;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class SettingsOverviewWidget extends BaseWidget
{
    protected static ?int $sort = 2;
    protected static ?string $pollingInterval = null;

    protected function getStats(): array
    {
        $totalSettings = Setting::count();
        $publicSettings = Setting::where('is_public', true)->count();
        $calculatorEnabled = Setting::getValue('general.calculator_enabled', false);
        $basePrice = Setting::getValue('pricing.umzug.base_price', 0);

        return [
            Stat::make('Gesamt Einstellungen', $totalSettings)
                ->description('Alle konfigurierten Einstellungen')
                ->descriptionIcon('heroicon-m-cog-6-tooth')
                ->color('primary'),

            Stat::make('Öffentliche Einstellungen', $publicSettings)
                ->description('Über API verfügbar')
                ->descriptionIcon('heroicon-m-eye')
                ->color('success'),

            Stat::make('Preisrechner Status', $calculatorEnabled ? 'Aktiviert' : 'Deaktiviert')
                ->description('Frontend Preisrechner')
                ->descriptionIcon($calculatorEnabled ? 'heroicon-m-check-circle' : 'heroicon-m-x-circle')
                ->color($calculatorEnabled ? 'success' : 'danger'),

            Stat::make('Umzug Grundpreis', number_format($basePrice, 2, ',', '.') . ' €')
                ->description('Aktueller Basispreis')
                ->descriptionIcon('heroicon-m-currency-euro')
                ->color('info'),
        ];
    }
}