<?php

namespace App\Filament\Widgets;

use App\Models\QuoteRequest;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class QuoteStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $stats = QuoteRequest::getStatistics();
        
        return [
            Stat::make('Ausstehende Anfragen', $stats['pending'])
                ->description('Neue Anfragen')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),
                
            Stat::make('Diesen Monat', $stats['this_month'])
                ->description('Anfragen diesen Monat')
                ->descriptionIcon('heroicon-m-calendar')
                ->color('primary'),
                
            Stat::make('Conversion Rate', number_format($stats['conversion_rate'], 1) . '%')
                ->description('Angenommene Angebote')
                ->descriptionIcon('heroicon-m-chart-bar')
                ->color('success'),
                
            Stat::make('Durchschnittswert', '€' . number_format($stats['avg_estimate'], 0))
                ->description('Durchschnittlicher Angebotswert')
                ->descriptionIcon('heroicon-m-currency-euro')
                ->color('info'),
        ];
    }
}