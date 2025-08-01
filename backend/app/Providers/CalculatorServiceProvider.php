<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\Calculators\DistanceCalculator;
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
        // Register Distance Calculator as singleton
        $this->app->singleton(DistanceCalculator::class);
        
        // Register Discount Calculator as singleton
        $this->app->singleton(DiscountCalculator::class);
        
        // Register Price Calculators
        $this->app->bind(MovingPriceCalculator::class, function ($app) {
            return new MovingPriceCalculator(
                $app->make(DistanceCalculator::class)
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