<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class QuoteRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'quote_number',
        'name',
        'email',
        'phone',
        'preferred_date',
        'preferred_contact',
        'message',
        'selected_services',
        'service_details',
        'pricing_data',
        'status',
        'admin_notes',
        'final_quote_amount',
        'quoted_at',
        'responded_at',
        'source',
        'user_agent',
        'ip_address',
        'email_sent_at',
        'email_status'
    ];

    protected $casts = [
        'preferred_date' => 'date',
        'selected_services' => 'array',
        'service_details' => 'array',
        'pricing_data' => 'array',
        'final_quote_amount' => 'decimal:2',
        'quoted_at' => 'datetime',
        'responded_at' => 'datetime',
        'email_sent_at' => 'datetime',
        'email_status' => 'array'
    ];

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (!$model->quote_number) {
                $model->quote_number = static::generateQuoteNumber();
            }
        });
    }

    /**
     * Generate unique quote number
     */
    public static function generateQuoteNumber(): string
    {
        do {
            $number = 'YLA-' . date('Y') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
        } while (static::where('quote_number', $number)->exists());

        return $number;
    }

    /**
     * Get the estimated total from pricing data
     */
    public function getEstimatedTotalAttribute(): ?float
    {
        return $this->pricing_data['total'] ?? null;
    }

    /**
     * Get the services as a formatted string
     */
    public function getServicesStringAttribute(): string
    {
        $serviceNames = [
            'umzug' => 'Umzug',
            'entruempelung' => 'Entrümpelung',
            'putzservice' => 'Putzservice'
        ];

        $services = collect($this->selected_services)
            ->map(fn($service) => $serviceNames[$service] ?? $service)
            ->join(', ');

        return $services;
    }

    /**
     * Get formatted preferred contact method
     */
    public function getPreferredContactFormattedAttribute(): string
    {
        return match($this->preferred_contact) {
            'email' => 'E-Mail',
            'phone' => 'Telefon',
            default => 'E-Mail'
        };
    }

    /**
     * Get status in German
     */
    public function getStatusGermanAttribute(): string
    {
        return match($this->status) {
            'pending' => 'Ausstehend',
            'reviewed' => 'Geprüft',
            'quoted' => 'Angebot erstellt',
            'accepted' => 'Angenommen',
            'rejected' => 'Abgelehnt',
            'completed' => 'Abgeschlossen',
            default => 'Unbekannt'
        };
    }

    /**
     * Check if quote is still pending
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if quote has been responded to
     */
    public function hasResponse(): bool
    {
        return in_array($this->status, ['quoted', 'accepted', 'rejected', 'completed']);
    }

    /**
     * Mark as reviewed
     */
    public function markAsReviewed(string $adminNotes = null): void
    {
        $this->update([
            'status' => 'reviewed',
            'admin_notes' => $adminNotes,
            'responded_at' => now()
        ]);
    }

    /**
     * Mark as quoted
     */
    public function markAsQuoted(float $finalAmount, string $adminNotes = null): void
    {
        $this->update([
            'status' => 'quoted',
            'final_quote_amount' => $finalAmount,
            'admin_notes' => $adminNotes,
            'quoted_at' => now(),
            'responded_at' => now()
        ]);
    }

    /**
     * Scope for pending quotes
     */
    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for recent quotes
     */
    public function scopeRecent(Builder $query, int $days = 30): Builder
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Scope for quotes by status
     */
    public function scopeByStatus(Builder $query, string $status): Builder
    {
        return $query->where('status', $status);
    }

    /**
     * Scope for quotes with specific service
     */
    public function scopeWithService(Builder $query, string $service): Builder
    {
        return $query->whereJsonContains('selected_services', $service);
    }

    /**
     * Get summary statistics
     */
    public static function getStatistics(): array
    {
        return [
            'total' => static::count(),
            'pending' => static::pending()->count(),
            'this_month' => static::whereMonth('created_at', now()->month)->count(),
            'avg_estimate' => static::whereNotNull('pricing_data->total')
                ->get()
                ->avg(fn($quote) => $quote->pricing_data['total'] ?? 0),
            'conversion_rate' => static::count() > 0 
                ? (static::whereIn('status', ['accepted', 'completed'])->count() / static::count()) * 100 
                : 0
        ];
    }
}