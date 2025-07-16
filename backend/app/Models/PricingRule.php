<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PricingRule extends Model
{
    use HasFactory;

    protected $fillable = [
        'service_id',
        'rule_type',
        'rule_key',
        'condition_operator',
        'condition_values',
        'price_value',
        'price_type',
        'unit',
        'description',
        'is_active',
        'priority'
    ];

    protected $casts = [
        'condition_values' => 'array',
        'price_value' => 'decimal:2',
        'is_active' => 'boolean',
        'priority' => 'integer'
    ];

    /**
     * Get the service that owns this pricing rule
     */
    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    /**
     * Check if this rule applies to given data
     */
    public function appliesTo(array $data): bool
    {
        if (!$this->is_active) {
            return false;
        }

        $value = data_get($data, $this->rule_key);
        
        if ($value === null) {
            return false;
        }

        return $this->evaluateCondition($value);
    }

    /**
     * Calculate the price for this rule given the data
     */
    public function calculatePrice(array $data): float
    {
        if (!$this->appliesTo($data)) {
            return 0;
        }

        $baseValue = data_get($data, $this->rule_key, 0);

        switch ($this->price_type) {
            case 'fixed':
                return (float) $this->price_value;
                
            case 'multiplier':
                return $baseValue * (float) $this->price_value;
                
            case 'per_unit':
                return $baseValue * (float) $this->price_value;
                
            default:
                return (float) $this->price_value;
        }
    }

    /**
     * Get description with calculated values
     */
    public function getCalculatedDescription(array $data): string
    {
        $baseDescription = $this->description ?? $this->rule_key;
        $value = data_get($data, $this->rule_key);
        $price = $this->calculatePrice($data);

        switch ($this->price_type) {
            case 'per_unit':
                return "{$baseDescription} ({$value} {$this->unit}): {$price}€";
            case 'multiplier':
                return "{$baseDescription} (x{$this->price_value}): {$price}€";
            default:
                return "{$baseDescription}: {$price}€";
        }
    }

    /**
     * Evaluate condition against value
     */
    private function evaluateCondition($value): bool
    {
        if (!$this->condition_operator) {
            return true; // No condition means always applies
        }

        $conditionValues = $this->condition_values ?? [];

        switch ($this->condition_operator) {
            case '>':
                return $value > ($conditionValues[0] ?? 0);
                
            case '<':
                return $value < ($conditionValues[0] ?? 0);
                
            case '>=':
                return $value >= ($conditionValues[0] ?? 0);
                
            case '<=':
                return $value <= ($conditionValues[0] ?? 0);
                
            case '=':
            case '==':
                return $value == ($conditionValues[0] ?? null);
                
            case 'between':
                return $value >= ($conditionValues[0] ?? 0) && 
                       $value <= ($conditionValues[1] ?? 0);
                       
            case 'in':
                return in_array($value, $conditionValues);
                
            case 'not_in':
                return !in_array($value, $conditionValues);
                
            default:
                return true;
        }
    }

    /**
     * Scope to get active rules
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to order by priority
     */
    public function scopeByPriority($query)
    {
        return $query->orderBy('priority', 'desc');
    }

    /**
     * Scope to filter by rule type
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('rule_type', $type);
    }
}