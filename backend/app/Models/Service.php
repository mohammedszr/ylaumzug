<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'base_price',
        'price_per_unit',
        'unit_type',
        'is_active',
        'sort_order',
        'pricing_config'
    ];

    protected $casts = [
        'base_price' => 'decimal:2',
        'price_per_unit' => 'decimal:2',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
        'pricing_config' => 'array'
    ];

    /**
     * Get quote requests that use this service
     */
    public function quoteRequests()
    {
        return $this->hasMany(QuoteRequest::class);
    }

    /**
     * Calculate price for this service based on details
     */
    public function calculatePrice(array $details): float
    {
        $price = $this->base_price;
        
        if ($this->price_per_unit > 0 && isset($details['quantity'])) {
            $price += $this->price_per_unit * $details['quantity'];
        }
        
        // Apply pricing configuration rules
        if ($this->pricing_config) {
            $price = $this->applyPricingConfig($price, $details);
        }
        
        return $price;
    }

    /**
     * Apply pricing configuration rules
     */
    private function applyPricingConfig(float $basePrice, array $details): float
    {
        $config = $this->pricing_config;
        $price = $basePrice;
        
        // Room-based pricing
        if (isset($config['room_multiplier']) && isset($details['rooms'])) {
            $price *= (1 + ($config['room_multiplier'] * ($details['rooms'] - 1)));
        }
        
        // Floor-based pricing
        if (isset($config['floor_cost']) && isset($details['floors'])) {
            $price += $config['floor_cost'] * $details['floors'];
        }
        
        // Size-based pricing
        if (isset($config['size_multipliers']) && isset($details['size'])) {
            $multiplier = $config['size_multipliers'][$details['size']] ?? 1;
            $price *= $multiplier;
        }
        
        return $price;
    }

    /**
     * Check if service is available
     */
    public function isAvailable(): bool
    {
        return $this->is_active;
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
     * Get service by slug
     */
    public static function findBySlug(string $slug): ?self
    {
        return static::where('slug', $slug)->first();
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
                    'id' => $service->slug,
                    'name' => $service->name,
                    'description' => $service->description,
                    'base_price' => $service->base_price,
                    'price_per_unit' => $service->price_per_unit,
                    'unit_type' => $service->unit_type,
                    'pricing_config' => $service->pricing_config
                ];
            })->toArray();
    }
}