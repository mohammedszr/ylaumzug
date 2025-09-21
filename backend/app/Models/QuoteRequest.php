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
        'quote_number',
        'angebotsnummer',
        'name',
        'email',
        'phone',
        'telefon',
        'preferred_contact',
        'bevorzugter_kontakt',
        'message',
        'selected_services',
        'ausgewaehlte_services',
        'service_details',
        'pricing_data',
        'from_postal_code',
        'to_postal_code',
        'distance_km',
        'preferred_date',
        'moving_type',
        'final_quote_amount',
        'status',
        'admin_notes',
        'quoted_at',
        'responded_at',
        'source',
        'user_agent',
        'ip_address',
        'email_sent_at',
        'whatsapp_sent_at',
        // Moving Addresses
        'from_street',
        'from_city',
        'from_floor',
        'from_elevator',
        'to_street',
        'to_city',
        'to_floor',
        'to_elevator',
        // Apartment Details
        'flat_size_m2',
        'flat_rooms',
        'parking_options',
        // Transport Volume
        'boxes_count',
        'beds_count',
        'wardrobes_count',
        'sofas_count',
        'tables_chairs_count',
        'washing_machine_count',
        'fridge_count',
        'other_electronics_count',
        'furniture_disassembly',
        'fragile_items',
        // Additional Services
        'service_furniture_assembly',
        'service_packing',
        'service_no_parking_zone',
        'service_storage',
        'service_disposal',
        // Calculation Details
        'base_price',
        'distance_price',
        'floor_price',
        'volume_price',
        'services_price',
        'price_breakdown',
        // Enhanced Address Information
        'from_full_address',
        'to_full_address',
        'from_latitude',
        'from_longitude',
        'to_latitude',
        'to_longitude'
    ];

    protected $casts = [
        'selected_services' => 'array',
        'ausgewaehlte_services' => 'array',
        'service_details' => 'array',
        'pricing_data' => 'array',
        'price_breakdown' => 'array',
        'preferred_date' => 'date',
        'quoted_at' => 'datetime',
        'responded_at' => 'datetime',
        'final_quote_amount' => 'decimal:2',
        'distance_km' => 'decimal:2',
        'flat_size_m2' => 'decimal:2',
        'base_price' => 'decimal:2',
        'distance_price' => 'decimal:2',
        'floor_price' => 'decimal:2',
        'volume_price' => 'decimal:2',
        'services_price' => 'decimal:2',
        'from_latitude' => 'decimal:8',
        'from_longitude' => 'decimal:8',
        'to_latitude' => 'decimal:8',
        'to_longitude' => 'decimal:8',
        'from_elevator' => 'boolean',
        'to_elevator' => 'boolean',
        'furniture_disassembly' => 'boolean',
        'service_furniture_assembly' => 'boolean',
        'service_packing' => 'boolean',
        'service_no_parking_zone' => 'boolean',
        'service_storage' => 'boolean',
        'service_disposal' => 'boolean',
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
            $quoteNumber = self::generateQuoteNumber();
            if (empty($model->quote_number)) {
                $model->quote_number = $quoteNumber;
            }
            if (empty($model->angebotsnummer)) {
                $model->angebotsnummer = $quoteNumber;
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