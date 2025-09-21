<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Contracts\DistanceCalculatorInterface;
use App\Contracts\PriceCalculatorInterface;
use App\Services\OpenRouteServiceCalculator;
use App\Services\PricingService;
use App\Services\Calculators\MovingPriceCalculator;
use App\Services\Calculators\CleaningPriceCalculator;
use App\Services\Calculators\DeclutterPriceCalculator;
use App\Services\Calculators\DiscountCalculator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(DistanceCalculatorInterface::class, OpenRouteServiceCalculator::class);
        $this->app->bind(PriceCalculatorInterface::class, PricingService::class);
        
        // Register OpenRouteServiceCalculator as singleton
        $this->app->singleton(OpenRouteServiceCalculator::class);
        
        // Register calculator services with proper dependencies
        $this->app->singleton(MovingPriceCalculator::class, function ($app) {
            return new MovingPriceCalculator($app->make(OpenRouteServiceCalculator::class));
        });
        
        $this->app->singleton(CleaningPriceCalculator::class);
        $this->app->singleton(DeclutterPriceCalculator::class);
        $this->app->singleton(DiscountCalculator::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}