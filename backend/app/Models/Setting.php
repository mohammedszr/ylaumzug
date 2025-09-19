<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = [
        'group_name',
        'key_name',
        'value',
        'type',
        'description',
        'is_public'
    ];

    protected $casts = [
        'is_public' => 'boolean'
    ];

    /**
     * Get a setting value with caching
     */
    public static function getValue(string $key, mixed $default = null): mixed
    {
        $cacheService = app(\App\Services\CacheService::class);
        
        return $cacheService->remember(
            $key,
            function () use ($key, $default) {
                // Support both group.key and key formats
                if (str_contains($key, '.')) {
                    [$group, $keyName] = explode('.', $key, 2);
                    $setting = static::where('group_name', $group)
                        ->where('key_name', $keyName)
                        ->first();
                } else {
                    $setting = static::where('key_name', $key)->first();
                }
                
                if (!$setting) {
                    return $default;
                }

                return static::castValue($setting->value, $setting->type);
            },
            null,
            'settings'
        );
    }

    /**
     * Set a setting value and clear cache
     */
    public static function setValue(string $key, mixed $value, string $type = 'string', string $group = 'general'): void
    {
        if (str_contains($key, '.')) {
            [$group, $keyName] = explode('.', $key, 2);
        } else {
            $keyName = $key;
        }

        static::updateOrCreate(
            ['group_name' => $group, 'key_name' => $keyName],
            [
                'value' => static::prepareValue($value, $type),
                'type' => $type
            ]
        );

        // Clear cache using the cache service
        $cacheService = app(\App\Services\CacheService::class);
        $cacheService->forget($key, 'settings');
        $cacheService->forget("group.{$group}", 'settings');
    }

    /**
     * Cast value to appropriate type
     */
    private static function castValue(string $value, string $type): mixed
    {
        return match($type) {
            'integer' => (int) $value,
            'float', 'decimal' => (float) $value,
            'boolean' => filter_var($value, FILTER_VALIDATE_BOOLEAN),
            'json', 'array' => json_decode($value, true),
            default => $value
        };
    }

    /**
     * Prepare value for storage
     */
    private static function prepareValue(mixed $value, string $type): string
    {
        return match($type) {
            'json', 'array' => json_encode($value),
            'boolean' => $value ? '1' : '0',
            default => (string) $value
        };
    }

    /**
     * Get all public settings for frontend
     */
    public static function getPublicSettings(): array
    {
        return static::where('is_public', true)
            ->get()
            ->mapWithKeys(function ($setting) {
                return [$setting->key => static::castValue($setting->value, $setting->type)];
            })
            ->toArray();
    }

    /**
     * Get settings by group with caching
     */
    public static function getByGroup(string $group): array
    {
        $cacheService = app(\App\Services\CacheService::class);
        
        return $cacheService->cacheSettings($group, function () use ($group) {
            return static::where('group_name', $group)
                ->get()
                ->mapWithKeys(function ($setting) {
                    return [$setting->key_name => static::castValue($setting->value, $setting->type)];
                })
                ->toArray();
        });
    }

    /**
     * Get all settings grouped by group name
     */
    public static function getAllGrouped(): array
    {
        return static::all()
            ->groupBy('group_name')
            ->map(function ($settings) {
                return $settings->mapWithKeys(function ($setting) {
                    return [$setting->key_name => [
                        'value' => static::castValue($setting->value, $setting->type),
                        'type' => $setting->type,
                        'description' => $setting->description,
                        'is_public' => $setting->is_public
                    ]];
                });
            })
            ->toArray();
    }
}