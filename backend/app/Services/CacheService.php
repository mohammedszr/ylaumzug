<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * Centralized caching service for the application
 * 
 * This service provides a consistent interface for caching operations
 * with proper error handling and cache key management.
 */
class CacheService
{
    /**
     * Cache durations in seconds
     */
    const CACHE_DURATIONS = [
        'settings' => 3600,        // 1 hour
        'distance' => 3600,        // 1 hour
        'pricing' => 1800,         // 30 minutes
        'services' => 7200,        // 2 hours
        'api_response' => 300,     // 5 minutes
        'user_session' => 86400,   // 24 hours
    ];

    /**
     * Cache key prefixes
     */
    const CACHE_PREFIXES = [
        'settings' => 'settings',
        'distance' => 'distance',
        'pricing' => 'pricing',
        'services' => 'services',
        'api' => 'api',
        'user' => 'user',
    ];

    /**
     * Get cached value with fallback
     */
    public function remember(string $key, callable $callback, ?int $ttl = null, string $type = 'default'): mixed
    {
        try {
            $cacheKey = $this->buildCacheKey($key, $type);
            $duration = $ttl ?? self::CACHE_DURATIONS[$type] ?? 3600;

            return Cache::remember($cacheKey, $duration, $callback);

        } catch (\Exception $e) {
            Log::warning('Cache remember failed, executing callback directly', [
                'key' => $key,
                'type' => $type,
                'error' => $e->getMessage()
            ]);

            // If caching fails, execute callback directly
            return $callback();
        }
    }

    /**
     * Store value in cache
     */
    public function put(string $key, mixed $value, ?int $ttl = null, string $type = 'default'): bool
    {
        try {
            $cacheKey = $this->buildCacheKey($key, $type);
            $duration = $ttl ?? self::CACHE_DURATIONS[$type] ?? 3600;

            return Cache::put($cacheKey, $value, $duration);

        } catch (\Exception $e) {
            Log::warning('Cache put failed', [
                'key' => $key,
                'type' => $type,
                'error' => $e->getMessage()
            ]);

            return false;
        }
    }

    /**
     * Get value from cache
     */
    public function get(string $key, mixed $default = null, string $type = 'default'): mixed
    {
        try {
            $cacheKey = $this->buildCacheKey($key, $type);
            return Cache::get($cacheKey, $default);

        } catch (\Exception $e) {
            Log::warning('Cache get failed', [
                'key' => $key,
                'type' => $type,
                'error' => $e->getMessage()
            ]);

            return $default;
        }
    }

    /**
     * Forget cached value
     */
    public function forget(string $key, string $type = 'default'): bool
    {
        try {
            $cacheKey = $this->buildCacheKey($key, $type);
            return Cache::forget($cacheKey);

        } catch (\Exception $e) {
            Log::warning('Cache forget failed', [
                'key' => $key,
                'type' => $type,
                'error' => $e->getMessage()
            ]);

            return false;
        }
    }

    /**
     * Flush cache by type/prefix
     */
    public function flushByType(string $type): bool
    {
        try {
            $prefix = self::CACHE_PREFIXES[$type] ?? $type;
            
            // For Redis, we can use pattern-based deletion
            if (config('cache.default') === 'redis') {
                $redis = Cache::getRedis();
                $keys = $redis->keys("{$prefix}:*");
                
                if (!empty($keys)) {
                    $redis->del($keys);
                }
                
                return true;
            }

            // For other cache drivers, we need to track keys manually
            // This is a simplified approach - in production you might want
            // to use cache tags if supported by your cache driver
            Log::info('Cache flush by type requested but not fully supported for current driver', [
                'type' => $type,
                'driver' => config('cache.default')
            ]);

            return false;

        } catch (\Exception $e) {
            Log::error('Cache flush by type failed', [
                'type' => $type,
                'error' => $e->getMessage()
            ]);

            return false;
        }
    }

    /**
     * Get cache statistics
     */
    public function getStats(): array
    {
        try {
            $stats = [
                'driver' => config('cache.default'),
                'connection' => config('cache.stores.' . config('cache.default')),
                'status' => 'connected',
            ];

            // Try to perform a simple cache operation to verify connection
            $testKey = 'cache_test_' . time();
            Cache::put($testKey, 'test', 10);
            $testValue = Cache::get($testKey);
            Cache::forget($testKey);

            if ($testValue !== 'test') {
                $stats['status'] = 'error';
                $stats['error'] = 'Cache test failed';
            }

            return $stats;

        } catch (\Exception $e) {
            return [
                'driver' => config('cache.default'),
                'status' => 'error',
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Build cache key with prefix
     */
    private function buildCacheKey(string $key, string $type): string
    {
        $prefix = self::CACHE_PREFIXES[$type] ?? $type;
        return "{$prefix}:{$key}";
    }

    /**
     * Cache settings with automatic invalidation
     */
    public function cacheSettings(string $group, callable $callback): mixed
    {
        return $this->remember(
            "group.{$group}",
            $callback,
            self::CACHE_DURATIONS['settings'],
            'settings'
        );
    }

    /**
     * Cache distance calculation
     */
    public function cacheDistance(string $from, string $to, callable $callback): mixed
    {
        $key = "calc.{$from}.{$to}";
        
        return $this->remember(
            $key,
            $callback,
            self::CACHE_DURATIONS['distance'],
            'distance'
        );
    }

    /**
     * Cache pricing calculation
     */
    public function cachePricing(string $hash, callable $callback): mixed
    {
        return $this->remember(
            "calc.{$hash}",
            $callback,
            self::CACHE_DURATIONS['pricing'],
            'pricing'
        );
    }

    /**
     * Cache API response
     */
    public function cacheApiResponse(string $endpoint, array $params, callable $callback): mixed
    {
        $key = "response." . md5($endpoint . serialize($params));
        
        return $this->remember(
            $key,
            $callback,
            self::CACHE_DURATIONS['api_response'],
            'api'
        );
    }

    /**
     * Invalidate settings cache
     */
    public function invalidateSettings(?string $group = null): bool
    {
        if ($group) {
            return $this->forget("group.{$group}", 'settings');
        }

        return $this->flushByType('settings');
    }

    /**
     * Invalidate distance cache
     */
    public function invalidateDistance(?string $from = null, ?string $to = null): bool
    {
        if ($from && $to) {
            return $this->forget("calc.{$from}.{$to}", 'distance');
        }

        return $this->flushByType('distance');
    }
}