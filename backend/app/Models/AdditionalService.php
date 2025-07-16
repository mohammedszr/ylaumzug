<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdditionalService extends Model
{
    use HasFactory;

    protected $fillable = [
        'service_id',
        'key',
        'name',
        'description',
        'price',
        'price_type',
        'unit',
        'is_active',
        'sort_order'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_active' => 'boolean',
        'sort_order' => 'integer'
    ];

    /**
     * Get the service that owns this additional service
     */
    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    /**
     * Calculate price for given quantity/value
     */
    public function calculatePrice(float $quantity = 1): float
    {
        switch ($this->price_type) {
            case 'fixed':
                return (float) $this->price;
            case 'per_hour':
            case 'per_m2':
            case 'per_km':
            case 'per_piece':
                return $quantity * (float) $this->price;
            default:
                return (float) $this->price;
        }
    }

    /**
     * Get formatted price description
     */
    public function getPriceDescriptionAttribute(): string
    {
        switch ($this->price_type) {
            case 'fixed':
                return "{$this->price}€";
            case 'per_hour':
                return "{$this->price}€/Stunde";
            case 'per_m2':
                return "{$this->price}€/m²";
            case 'per_km':
                return "{$this->price}€/km";
            case 'per_piece':
                return "{$this->price}€/Stück";
            default:
                return "{$this->price}€";
        }
    }

    /**
     * Get calculated description with quantity
     */
    public function getCalculatedDescription(float $quantity = 1): string
    {
        $price = $this->calculatePrice($quantity);
        
        switch ($this->price_type) {
            case 'fixed':
                return "{$this->name}: {$price}€";
            default:
                return "{$this->name} ({$quantity} {$this->unit}): {$price}€";
        }
    }

    /**
     * Scope to get active additional services
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
     * Find by key
     */
    public static function findByKey(string $key): ?self
    {
        return static::where('key', $key)->first();
    }
}