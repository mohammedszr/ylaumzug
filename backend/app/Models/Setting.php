<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
        'type',
        'group',
        'description',
        'is_public'
    ];

    protected $casts = [
        'is_public' => 'boolean'
    ];

    /**
     * Get a setting value by key
     */
    public static function getValue(string $key, $default = null)
    {
        $cacheKey = "setting.{$key}";
        
        return Cache::remember($cacheKey, 3600, function () use ($key, $default) {
            $setting = static::where('key', $key)->first();
            
            if (!$setting) {
                return $default;
            }

            return static::castValue($setting->value, $setting->type);
        });
    }

    /**
     * Set a setting value
     */
    public static function setValue(string $key, $value, string $type = null): void
    {
        $setting = static::firstOrNew(['key' => $key]);
        
        if ($type) {
            $setting->type = $type;
        } elseif (!$setting->exists) {
            $setting->type = static::detectType($value);
        }

        $setting->value = static::prepareValue($value, $setting->type);
        $setting->save();

        // Clear cache
        Cache::forget("setting.{$key}");
    }

    /**
     * Get multiple settings by group
     */
    public static function getGroup(string $group): array
    {
        $cacheKey = "settings.group.{$group}";
        
        return Cache::remember($cacheKey, 3600, function () use ($group) {
            return static::where('group', $group)
                ->get()
                ->mapWithKeys(function ($setting) {
                    return [
                        $setting->key => static::castValue($setting->value, $setting->type)
                    ];
                })
                ->toArray();
        });
    }

    /**
     * Get public settings (for frontend)
     */
    public static function getPublicSettings(): array
    {
        $cacheKey = 'settings.public';
        
        return Cache::remember($cacheKey, 3600, function () {
            return static::where('is_public', true)
                ->get()
                ->mapWithKeys(function ($setting) {
                    return [
                        $setting->key => static::castValue($setting->value, $setting->type)
                    ];
                })
                ->toArray();
        });
    }

    /**
     * Cast value to appropriate type
     */
    private static function castValue($value, string $type)
    {
        switch ($type) {
            case 'boolean':
                return filter_var($value, FILTER_VALIDATE_BOOLEAN);
            case 'integer':
                return (int) $value;
            case 'decimal':
                return (float) $value;
            case 'json':
                return json_decode($value, true);
            case 'array':
                return is_array($value) ? $value : json_decode($value, true);
            default:
                return $value;
        }
    }

    /**
     * Prepare value for storage
     */
    private static function prepareValue($value, string $type): string
    {
        switch ($type) {
            case 'boolean':
                return $value ? '1' : '0';
            case 'json':
            case 'array':
                return json_encode($value);
            default:
                return (string) $value;
        }
    }

    /**
     * Detect type from value
     */
    private static function detectType($value): string
    {
        if (is_bool($value)) {
            return 'boolean';
        }
        if (is_int($value)) {
            return 'integer';
        }
        if (is_float($value)) {
            return 'decimal';
        }
        if (is_array($value)) {
            return 'array';
        }
        return 'string';
    }

    /**
     * Clear all settings cache
     */
    public static function clearCache(): void
    {
        Cache::flush(); // Simple approach - in production, use more targeted cache clearing
    }

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        static::saved(function ($model) {
            Cache::forget("setting.{$model->key}");
            Cache::forget("settings.group.{$model->group}");
            if ($model->is_public) {
                Cache::forget('settings.public');
            }
        });

        static::deleted(function ($model) {
            Cache::forget("setting.{$model->key}");
            Cache::forget("settings.group.{$model->group}");
            if ($model->is_public) {
                Cache::forget('settings.public');
            }
        });
    }

    /**
     * Scope for public settings
     */
    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    /**
     * Scope for settings by group
     */
    public function scopeInGroup($query, string $group)
    {
        return $query->where('group', $group);
    }
}