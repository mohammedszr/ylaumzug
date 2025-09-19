<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Symfony\Component\HttpFoundation\Response;

class ApiRateLimiter
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, string $key = 'api'): Response
    {
        $limiterKey = $this->resolveRequestSignature($request, $key);
        
        // Different limits for different endpoints
        $maxAttempts = match($key) {
            'calculator' => 30, // 30 requests per minute for calculator
            'quotes' => 10,     // 10 requests per minute for quote submission
            'api' => 60,        // 60 requests per minute for general API
            default => 60
        };
        
        $decayMinutes = 1; // Reset every minute

        if (RateLimiter::tooManyAttempts($limiterKey, $maxAttempts)) {
            return $this->buildRateLimitResponse($limiterKey, $maxAttempts);
        }

        RateLimiter::hit($limiterKey, $decayMinutes * 60);

        $response = $next($request);

        return $this->addRateLimitHeaders($response, $limiterKey, $maxAttempts);
    }

    /**
     * Resolve the rate limiter key for the request
     */
    protected function resolveRequestSignature(Request $request, string $key): string
    {
        $userId = $request->user()?->id;
        $ip = $request->ip();
        
        // Use user ID if authenticated, otherwise use IP
        $identifier = $userId ? "user:{$userId}" : "ip:{$ip}";
        
        return "{$key}:{$identifier}";
    }

    /**
     * Build rate limit exceeded response
     */
    protected function buildRateLimitResponse(string $key, int $maxAttempts): Response
    {
        $retryAfter = RateLimiter::availableIn($key);
        
        return response()->json([
            'success' => false,
            'message' => 'Zu viele Anfragen. Bitte versuchen Sie es in ' . $retryAfter . ' Sekunden erneut.',
            'error_code' => 'RATE_LIMIT_EXCEEDED',
            'retry_after' => $retryAfter,
            'max_attempts' => $maxAttempts,
        ], 429)->header('Retry-After', $retryAfter);
    }

    /**
     * Add rate limit headers to response
     */
    protected function addRateLimitHeaders(Response $response, string $key, int $maxAttempts): Response
    {
        $remaining = RateLimiter::remaining($key, $maxAttempts);
        $retryAfter = RateLimiter::availableIn($key);

        return $response->withHeaders([
            'X-RateLimit-Limit' => $maxAttempts,
            'X-RateLimit-Remaining' => $remaining,
            'X-RateLimit-Reset' => now()->addSeconds($retryAfter)->timestamp,
        ]);
    }
}