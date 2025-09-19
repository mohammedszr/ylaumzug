<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\Mail;
use App\Contracts\PriceCalculatorInterface;
use App\Mail\QuoteReadyMail;

class QuoteRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'angebotsnummer',
        'name',
        'email',
        'telefon',
        'bevorzugter_kontakt',
        'message',
        'from_address',
        'to_address',
        'from_postal_code',
        'to_postal_code',
        'distance_km',
        'moving_date',
        'moving_type',
        'ausgewaehlte_services',
        'service_details',
        'estimated_total',
        'endgueltiger_angebotsbetrag',
        'status',
        'special_requirements',
        'admin_notizen',
        'submitted_at',
        'email_sent_at',
        'whatsapp_sent_at'
    ];

    protected $casts = [
        'ausgewaehlte_services' => 'array',
        'service_details' => 'array',
        'moving_date' => 'date',
        'submitted_at' => 'datetime',
        'estimated_total' => 'decimal:2',
        'endgueltiger_angebotsbetrag' => 'decimal:2',
        'distance_km' => 'decimal:2',
        'email_sent_at' => 'datetime',
        'whatsapp_sent_at' => 'datetime'
    ];

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->angebotsnummer)) {
                $model->angebotsnummer = self::generateQuoteNumber();
            }
        });
    }

    public static function generateQuoteNumber(): string
    {
        $year = date('Y');
        $lastQuote = self::whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->first();
        
        $number = $lastQuote ? (int)substr($lastQuote->angebotsnummer, -3) + 1 : 1;
        
        return sprintf('QR-%s-%03d', $year, $number);
    }

    public function calculateEstimatedPrice(): float
    {
        $calculator = app(PriceCalculatorInterface::class);
        return $calculator->calculate($this->toArray());
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

        $services = collect($this->ausgewaehlte_services)
            ->map(fn($service) => $serviceNames[$service] ?? $service)
            ->join(', ');

        return $services;
    }

    /**
     * Get formatted preferred contact method
     */
    public function getPreferredContactFormattedAttribute(): string
    {
        return match($this->bevorzugter_kontakt) {
            'email' => 'E-Mail',
            'phone' => 'Telefon',
            'whatsapp' => 'WhatsApp',
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
    public function markAsReviewed(?string $adminNotes = null): void
    {
        $this->update([
            'status' => 'reviewed',
            'admin_notizen' => $adminNotes
        ]);
    }

    /**
     * Mark as quoted
     */
    public function markAsQuoted(float $amount, ?string $notes = null): void
    {
        $this->update([
            'status' => 'quoted',
            'endgueltiger_angebotsbetrag' => $amount,
            'admin_notizen' => $notes
        ]);
        
        // Send quote email
        Mail::to($this->email)->send(new QuoteReadyMail($this));
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
        return $query->whereJsonContains('ausgewaehlte_services', $service);
    }

    /**
     * Scope for optimized admin listing with eager loading
     */
    public function scopeForAdminListing(Builder $query): Builder
    {
        return $query->select([
            'id',
            'angebotsnummer',
            'name',
            'email',
            'telefon',
            'status',
            'moving_date',
            'estimated_total',
            'endgueltiger_angebotsbetrag',
            'email_sent_at',
            'whatsapp_sent_at',
            'created_at',
            'updated_at'
        ]);
    }

    /**
     * Scope for dashboard statistics (optimized)
     */
    public function scopeForStats(Builder $query): Builder
    {
        return $query->select([
            'id',
            'status',
            'estimated_total',
            'endgueltiger_angebotsbetrag',
            'created_at'
        ]);
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
            'avg_estimate' => static::whereNotNull('estimated_total')
                ->avg('estimated_total'),
            'conversion_rate' => static::count() > 0 
                ? (static::whereIn('status', ['accepted', 'completed'])->count() / static::count()) * 100 
                : 0
        ];
    }
}