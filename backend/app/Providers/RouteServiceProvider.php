<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to your application's "home" route.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const HOME = '/home';

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     */
    public function boot(): void
    {
        $this->configureRateLimiting();

        $this->routes(function () {
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));

            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        });
    }

    /**
     * Configure the rate limiters for the application.
     */
    protected function configureRateLimiting(): void
    {
        // General API rate limiting
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)
                ->by($request->user()?->id ?: $request->ip())
                ->response(function (Request $request, array $headers) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Zu viele Anfragen. Bitte versuchen Sie es später erneut.',
                        'error_code' => 'RATE_LIMIT_EXCEEDED',
                    ], 429, $headers);
                });
        });

        // Calculator API rate limiting (more permissive for price calculations)
        RateLimiter::for('calculator', function (Request $request) {
            return Limit::perMinute(30)
                ->by($request->user()?->id ?: $request->ip())
                ->response(function (Request $request, array $headers) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Zu viele Preisberechnungen. Bitte warten Sie einen Moment.',
                        'error_code' => 'CALCULATOR_RATE_LIMIT_EXCEEDED',
                    ], 429, $headers);
                });
        });

        // Quote submission rate limiting (stricter to prevent spam)
        RateLimiter::for('quotes', function (Request $request) {
            return Limit::perMinute(5)
                ->by($request->user()?->id ?: $request->ip())
                ->response(function (Request $request, array $headers) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Zu viele Angebotsanfragen. Bitte warten Sie vor der nächsten Anfrage.',
                        'error_code' => 'QUOTE_RATE_LIMIT_EXCEEDED',
                    ], 429, $headers);
                });
        });

        // Settings API rate limiting
        RateLimiter::for('settings', function (Request $request) {
            return Limit::perMinute(20)
                ->by($request->user()?->id ?: $request->ip())
                ->response(function (Request $request, array $headers) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Zu viele Einstellungsanfragen. Bitte versuchen Sie es später erneut.',
                        'error_code' => 'SETTINGS_RATE_LIMIT_EXCEEDED',
                    ], 429, $headers);
                });
        });
    }
}