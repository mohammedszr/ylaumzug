<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'name',
        'description',
        'base_price',
        'is_active',
        'configuration',
        'sort_order'
    ];

    protected $casts = [
        'base_price' => 'decimal:2',
        'is_active' => 'boolean',
        'configuration' => 'array',
        'sort_order' => 'integer'
    ];

    /**
     * Get the pricing rules for this service
     */
    public function pricingRules(): HasMany
    {
        return $this->hasMany(PricingRule::class);
    }

    /**
     * Get the additional services for this service
     */
    public function additionalServices(): HasMany
    {
        return $this->hasMany(AdditionalService::class);
    }

    /**
     * Get active pricing rules ordered by priority
     */
    public function getActivePricingRules()
    {
        return $this->pricingRules()
            ->where('is_active', true)
            ->orderBy('priority', 'desc')
            ->get();
    }

    /**
     * Get active additional services ordered by sort order
     */
    public function getActiveAdditionalServices()
    {
        return $this->additionalServices()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();
    }

    /**
     * Scope to get only active services
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to order by sort order
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }

    /**
     * Get service by key
     */
    public static function findByKey(string $key): ?self
    {
        return static::where('key', $key)->first();
    }

    /**
     * Get all active services for frontend
     */
    public static function getForFrontend(): array
    {
        return static::active()
            ->ordered()
            ->get()
            ->map(function ($service) {
                return [
                    'id' => $service->key,
                    'name' => $service->name,
                    'description' => $service->description,
                    'base_price' => $service->base_price,
                    'additional_services' => $service->getActiveAdditionalServices()
                        ->map(function ($addon) {
                            return [
                                'id' => $addon->key,
                                'name' => $addon->name,
                                'description' => $addon->description,
                                'price' => $addon->price,
                                'price_type' => $addon->price_type,
                                'unit' => $addon->unit
                            ];
                        })->toArray()
                ];
            })->toArray();
    }
}