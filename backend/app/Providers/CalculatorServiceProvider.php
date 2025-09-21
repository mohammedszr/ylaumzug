<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\OpenRouteServiceCalculator;
use App\Services\Calculators\MovingPriceCalculator;
use App\Services\Calculators\DeclutterPriceCalculator;
use App\Services\Calculators\CleaningPriceCalculator;
use App\Services\Calculators\DiscountCalculator;

class CalculatorServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Register OpenRouteServiceCalculator as singleton
        $this->app->singleton(OpenRouteServiceCalculator::class);
        
        // Register Discount Calculator as singleton
        $this->app->singleton(DiscountCalculator::class);
        
        // Register Price Calculators with correct dependencies
        $this->app->bind(MovingPriceCalculator::class, function ($app) {
            return new MovingPriceCalculator(
                $app->make(OpenRouteServiceCalculator::class)
            );
        });
        
        $this->app->bind(DeclutterPriceCalculator::class);
        $this->app->bind(CleaningPriceCalculator::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}