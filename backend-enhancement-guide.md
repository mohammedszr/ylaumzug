# YLA Umzug Backend Enhancement Guide
**Comprehensive Laravel + Filament Implementation for Production-Ready Moving Services Platform**

## 🎯 Project Overview

Transform the existing React-based YLA Umzug application into a full-stack Laravel application with:
- **Advanced Filament Admin Dashboard**
- **Professional Backend Architecture** 
- **Production-Ready Features**
- **Distance Calculation API Integration**
- **Comprehensive Quote Management System**

**CRITICAL**: Preserve all existing frontend UI and functionality while adding robust backend capabilities.

---

## 📋 Implementation Checklist

### Phase 1: Laravel Foundation & Database ✅ COMPLETED
- [x] Install Laravel 10 with proper project structure
- [x] Set up SQLite database for development/staging
- [x] Create comprehensive migration system
- [x] Implement advanced model relationships
- [x] Add proper validation and business logic

### Phase 2: Filament Admin Dashboard ✅ COMPLETED
- [x] Install and configure Filament v3
- [x] Create advanced admin panel with German localization
- [x] Implement quote management system
- [x] Add settings management interface
- [x] Create user management and permissions

### Phase 3: API & Services Architecture ✅ COMPLETED
- [x] Build RESTful API for React frontend
- [x] Implement distance calculation services
- [x] Add email notification system
- [x] Create PDF generation capabilities
- [x] Integrate payment processing (future-ready)

### Phase 4: Production Features ✅ COMPLETED
- [x] Add comprehensive error handling
- [x] Implement logging and monitoring
- [x] Create backup and maintenance systems
- [x] Add security features and rate limiting
- [x] Configure deployment pipeline

---

## 🗄️ Database Schema Implementation

### Core Tables Structure

```sql
-- Quote Requests (Main Entity)
CREATE TABLE quote_requests (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    angebotsnummer VARCHAR(50) UNIQUE NOT NULL, -- QR-2025-001
    
    -- Customer Information
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    telefon VARCHAR(50),
    bevorzugter_kontakt ENUM('email', 'phone', 'whatsapp') DEFAULT 'email',
    
    -- Moving Details
    from_address TEXT NOT NULL,
    to_address TEXT NOT NULL,
    from_postal_code VARCHAR(10),
    to_postal_code VARCHAR(10),
    distance_km DECIMAL(8,2) NULL,
    moving_date DATE NOT NULL,
    moving_type ENUM('local', 'long_distance', 'international') DEFAULT 'local',
    
    -- Services
    ausgewaehlte_services JSON, -- ['umzug', 'putzservice', 'entruempelung']
    service_details JSON, -- Detailed service configuration
    
    -- Pricing
    estimated_total DECIMAL(10,2),
    endgueltiger_angebotsbetrag DECIMAL(10,2) NULL,
    
    -- Status & Workflow
    status ENUM('pending', 'reviewed', 'quoted', 'accepted', 'rejected', 'completed') DEFAULT 'pending',
    
    -- Additional Information
    special_requirements TEXT,
    admin_notizen TEXT,
    
    -- Metadata
    submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_angebotsnummer (angebotsnummer),
    INDEX idx_email (email),
    INDEX idx_status (status),
    INDEX idx_moving_date (moving_date)
);

-- Services Configuration
CREATE TABLE services (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) UNIQUE NOT NULL,
    description TEXT,
    base_price DECIMAL(10,2) DEFAULT 0,
    price_per_unit DECIMAL(10,2) DEFAULT 0,
    unit_type ENUM('hour', 'room', 'sqm', 'item', 'fixed') DEFAULT 'hour',
    is_active BOOLEAN DEFAULT TRUE,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Settings Management
CREATE TABLE settings (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    group_name VARCHAR(100) NOT NULL,
    key_name VARCHAR(100) NOT NULL,
    value TEXT,
    type ENUM('string', 'integer', 'decimal', 'boolean', 'json') DEFAULT 'string',
    description TEXT,
    is_public BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    UNIQUE KEY unique_setting (group_name, key_name),
    INDEX idx_group (group_name),
    INDEX idx_public (is_public)
);

-- Users & Authentication
CREATE TABLE users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    email_verified_at TIMESTAMP NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'manager', 'employee') DEFAULT 'employee',
    is_active BOOLEAN DEFAULT TRUE,
    last_login_at TIMESTAMP NULL,
    remember_token VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

---

## 🏗️ Laravel Architecture Implementation

### 1. Models with Advanced Relationships

```php
<?php
// app/Models/QuoteRequest.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;

class QuoteRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'angebotsnummer', 'name', 'email', 'telefon', 'bevorzugter_kontakt',
        'from_address', 'to_address', 'from_postal_code', 'to_postal_code',
        'distance_km', 'moving_date', 'moving_type', 'ausgewaehlte_services',
        'service_details', 'estimated_total', 'endgueltiger_angebotsbetrag',
        'status', 'special_requirements', 'admin_notizen', 'submitted_at'
    ];

    protected $casts = [
        'ausgewaehlte_services' => 'array',
        'service_details' => 'array',
        'moving_date' => 'date',
        'submitted_at' => 'datetime',
        'estimated_total' => 'decimal:2',
        'endgueltiger_angebotsbetrag' => 'decimal:2',
        'distance_km' => 'decimal:2'
    ];

    // Auto-generate quote number
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

    // Business Logic Methods
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

    public function calculateEstimatedPrice(): float
    {
        $calculator = app(PriceCalculatorInterface::class);
        return $calculator->calculate($this->toArray());
    }

    // Accessors & Mutators
    protected function formattedTotal(): Attribute
    {
        return Attribute::make(
            get: fn () => number_format($this->estimated_total ?? 0, 2) . ' €'
        );
    }

    protected function statusBadge(): Attribute
    {
        return Attribute::make(
            get: fn () => match($this->status) {
                'pending' => ['label' => 'Ausstehend', 'color' => 'warning'],
                'reviewed' => ['label' => 'Überprüft', 'color' => 'info'],
                'quoted' => ['label' => 'Angebot erstellt', 'color' => 'primary'],
                'accepted' => ['label' => 'Angenommen', 'color' => 'success'],
                'rejected' => ['label' => 'Abgelehnt', 'color' => 'danger'],
                'completed' => ['label' => 'Abgeschlossen', 'color' => 'success'],
                default => ['label' => 'Unbekannt', 'color' => 'gray']
            }
        );
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeRecent($query, int $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }
}
```

### 2. Service Classes & Interfaces

```php
<?php
// app/Contracts/PriceCalculatorInterface.php
namespace App\Contracts;

interface PriceCalculatorInterface
{
    public function calculate(array $quoteData): float;
    public function getBreakdown(array $quoteData): array;
}

// app/Contracts/DistanceCalculatorInterface.php
namespace App\Contracts;

interface DistanceCalculatorInterface
{
    public function calculateDistance(string $fromPostalCode, string $toPostalCode): array;
}

// app/Services/OpenRouteServiceCalculator.php
namespace App\Services;

use App\Contracts\DistanceCalculatorInterface;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class OpenRouteServiceCalculator implements DistanceCalculatorInterface
{
    private Client $client;
    private string $apiKey;
    private string $baseUrl = 'https://api.openrouteservice.org/v2';

    public function __construct()
    {
        $this->client = new Client(['timeout' => 30]);
        $this->apiKey = config('services.openroute.api_key');
        
        if (empty($this->apiKey)) {
            throw new \Exception('OpenRouteService API key not configured');
        }
    }

    public function calculateDistance(string $fromPostalCode, string $toPostalCode): array
    {
        $cacheKey = "distance_{$fromPostalCode}_{$toPostalCode}";
        
        return Cache::remember($cacheKey, 3600, function () use ($fromPostalCode, $toPostalCode) {
            try {
                $fromCoords = $this->geocodePostalCode($fromPostalCode);
                $toCoords = $this->geocodePostalCode($toPostalCode);
                
                $response = $this->client->post("{$this->baseUrl}/matrix/driving-car", [
                    'headers' => [
                        'Authorization' => $this->apiKey,
                        'Content-Type' => 'application/json'
                    ],
                    'json' => [
                        'locations' => [$fromCoords, $toCoords],
                        'metrics' => ['distance', 'duration']
                    ]
                ]);

                $data = json_decode($response->getBody(), true);
                
                return [
                    'distance_km' => round($data['distances'][0][1] / 1000, 2),
                    'duration_minutes' => round($data['durations'][0][1] / 60),
                    'from_coords' => $fromCoords,
                    'to_coords' => $toCoords,
                    'success' => true
                ];
                
            } catch (\Exception $e) {
                Log::error('Distance calculation failed', [
                    'from' => $fromPostalCode,
                    'to' => $toPostalCode,
                    'error' => $e->getMessage()
                ]);
                
                return [
                    'distance_km' => null,
                    'duration_minutes' => null,
                    'success' => false,
                    'error' => $e->getMessage()
                ];
            }
        });
    }

    private function geocodePostalCode(string $postalCode): array
    {
        $response = $this->client->get("{$this->baseUrl}/geocode/search", [
            'headers' => ['Authorization' => $this->apiKey],
            'query' => [
                'text' => $postalCode,
                'boundary.country' => 'DE',
                'size' => 1
            ]
        ]);

        $data = json_decode($response->getBody(), true);
        
        if (empty($data['features'])) {
            throw new \Exception("Postal code {$postalCode} not found");
        }

        return $data['features'][0]['geometry']['coordinates'];
    }
}

// app/Services/PriceCalculator.php
namespace App\Services;

use App\Contracts\PriceCalculatorInterface;
use App\Contracts\DistanceCalculatorInterface;

class PriceCalculator implements PriceCalculatorInterface
{
    public function __construct(
        private DistanceCalculatorInterface $distanceCalculator
    ) {}

    public function calculate(array $quoteData): float
    {
        $total = 0;
        $services = $quoteData['ausgewaehlte_services'] ?? [];
        $details = $quoteData['service_details'] ?? [];

        foreach ($services as $service) {
            $total += match($service) {
                'umzug' => $this->calculateMovingPrice($details['moving'] ?? []),
                'putzservice' => $this->calculateCleaningPrice($details['cleaning'] ?? []),
                'entruempelung' => $this->calculateDeclutterPrice($details['declutter'] ?? []),
                default => 0
            };
        }

        // Add distance-based costs for moving
        if (in_array('umzug', $services)) {
            $total += $this->calculateDistanceCost($quoteData);
        }

        return round($total, 2);
    }

    public function getBreakdown(array $quoteData): array
    {
        // Return detailed price breakdown for transparency
        return [
            'base_services' => [],
            'distance_cost' => 0,
            'additional_fees' => [],
            'discounts' => [],
            'total' => $this->calculate($quoteData)
        ];
    }

    private function calculateMovingPrice(array $details): float
    {
        $basePrice = 150; // Base moving service
        $roomMultiplier = ($details['rooms'] ?? 1) * 50;
        $floorMultiplier = ($details['floors'] ?? 0) * 25;
        
        return $basePrice + $roomMultiplier + $floorMultiplier;
    }

    private function calculateCleaningPrice(array $details): float
    {
        $basePrice = 80;
        $roomMultiplier = ($details['rooms'] ?? 1) * 30;
        
        return $basePrice + $roomMultiplier;
    }

    private function calculateDeclutterPrice(array $details): float
    {
        $basePrice = 120;
        $volumeMultiplier = ($details['volume'] ?? 1) * 40;
        
        return $basePrice + $volumeMultiplier;
    }

    private function calculateDistanceCost(array $quoteData): float
    {
        if (empty($quoteData['from_postal_code']) || empty($quoteData['to_postal_code'])) {
            return 0;
        }

        $result = $this->distanceCalculator->calculateDistance(
            $quoteData['from_postal_code'],
            $quoteData['to_postal_code']
        );

        if (!$result['success']) {
            return 0;
        }

        $distance = $result['distance_km'];
        
        // Free up to 30km, then €1.50 per km
        return $distance > 30 ? ($distance - 30) * 1.5 : 0;
    }
}
```

---

## 🎛️ Filament Admin Dashboard Implementation

### 1. Advanced Quote Request Resource

```php
<?php
// app/Filament/Resources/QuoteRequestResource.php
namespace App\Filament\Resources;

use App\Filament\Resources\QuoteRequestResource\Pages;
use App\Models\QuoteRequest;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Section;
use Filament\Notifications\Notification;

class QuoteRequestResource extends Resource
{
    protected static ?string $model = QuoteRequest::class;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationLabel = 'Angebotsanfragen';
    protected static ?string $modelLabel = 'Angebotsanfrage';
    protected static ?string $pluralModelLabel = 'Angebotsanfragen';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Angebotsinformationen')
                ->schema([
                    Forms\Components\TextInput::make('angebotsnummer')
                        ->label('Angebotsnummer')
                        ->disabled()
                        ->dehydrated(false),
                    
                    Forms\Components\Select::make('status')
                        ->label('Status')
                        ->options([
                            'pending' => 'Ausstehend',
                            'reviewed' => 'Überprüft',
                            'quoted' => 'Angebot erstellt',
                            'accepted' => 'Angenommen',
                            'rejected' => 'Abgelehnt',
                            'completed' => 'Abgeschlossen',
                        ])
                        ->required()
                        ->default('pending')
                        ->native(false),
                ])
                ->columns(2),
                
            Forms\Components\Section::make('Kundeninformationen')
                ->schema([
                    Forms\Components\TextInput::make('name')
                        ->label('Vollständiger Name')
                        ->required()
                        ->maxLength(255),
                        
                    Forms\Components\TextInput::make('email')
                        ->label('E-Mail')
                        ->email()
                        ->required()
                        ->maxLength(255),
                        
                    Forms\Components\TextInput::make('telefon')
                        ->label('Telefon')
                        ->tel()
                        ->maxLength(255),
                        
                    Forms\Components\Select::make('bevorzugter_kontakt')
                        ->label('Bevorzugter Kontakt')
                        ->options([
                            'email' => 'E-Mail',
                            'phone' => 'Telefon',
                            'whatsapp' => 'WhatsApp',
                        ])
                        ->required()
                        ->native(false),
                ])
                ->columns(2),
                
            Forms\Components\Section::make('Umzug Details')
                ->schema([
                    Forms\Components\Textarea::make('from_address')
                        ->label('Von Adresse')
                        ->required()
                        ->rows(2),
                        
                    Forms\Components\Textarea::make('to_address')
                        ->label('Nach Adresse')
                        ->required()
                        ->rows(2),
                        
                    Forms\Components\TextInput::make('from_postal_code')
                        ->label('Von PLZ')
                        ->maxLength(10),
                        
                    Forms\Components\TextInput::make('to_postal_code')
                        ->label('Nach PLZ')
                        ->maxLength(10),
                        
                    Forms\Components\DatePicker::make('moving_date')
                        ->label('Umzugsdatum')
                        ->required()
                        ->native(false),
                        
                    Forms\Components\Select::make('moving_type')
                        ->label('Umzugsart')
                        ->options([
                            'local' => 'Lokal',
                            'long_distance' => 'Fernumzug',
                            'international' => 'International',
                        ])
                        ->required()
                        ->native(false),
                        
                    Forms\Components\TextInput::make('distance_km')
                        ->label('Entfernung (km)')
                        ->numeric()
                        ->step(0.01)
                        ->disabled(),
                ])
                ->columns(2),
                
            Forms\Components\Section::make('Services & Preise')
                ->schema([
                    Forms\Components\TagsInput::make('ausgewaehlte_services')
                        ->label('Ausgewählte Services')
                        ->disabled()
                        ->dehydrated(false),
                        
                    Forms\Components\KeyValue::make('service_details')
                        ->label('Service Details')
                        ->disabled()
                        ->dehydrated(false),
                        
                    Forms\Components\TextInput::make('estimated_total')
                        ->label('Geschätzte Gesamtsumme')
                        ->disabled()
                        ->dehydrated(false)
                        ->prefix('€'),
                        
                    Forms\Components\TextInput::make('endgueltiger_angebotsbetrag')
                        ->label('Endgültiger Angebotsbetrag')
                        ->numeric()
                        ->prefix('€')
                        ->step(0.01),
                ])
                ->columns(2),
                
            Forms\Components\Section::make('Zusätzliche Informationen')
                ->schema([
                    Forms\Components\Textarea::make('special_requirements')
                        ->label('Besondere Anforderungen')
                        ->disabled()
                        ->dehydrated(false)
                        ->rows(3),
                        
                    Forms\Components\Textarea::make('admin_notizen')
                        ->label('Admin Notizen')
                        ->rows(3),
                ])
                ->columns(1),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('angebotsnummer')
                    ->label('Angebot #')
                    ->searchable()
                    ->sortable()
                    ->copyable(),
                    
                Tables\Columns\TextColumn::make('name')
                    ->label('Kunde')
                    ->searchable()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('email')
                    ->label('E-Mail')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                    
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'warning' => 'pending',
                        'info' => 'reviewed',
                        'primary' => 'quoted',
                        'success' => ['accepted', 'completed'],
                        'danger' => 'rejected',
                    ])
                    ->formatStateUsing(fn (string $state): string => match($state) {
                        'pending' => 'Ausstehend',
                        'reviewed' => 'Überprüft',
                        'quoted' => 'Angebot erstellt',
                        'accepted' => 'Angenommen',
                        'rejected' => 'Abgelehnt',
                        'completed' => 'Abgeschlossen',
                        default => ucfirst($state)
                    }),
                    
                Tables\Columns\TextColumn::make('moving_date')
                    ->label('Umzugsdatum')
                    ->date('d.m.Y')
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('estimated_total')
                    ->label('Geschätzt')
                    ->money('EUR')
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('endgueltiger_angebotsbetrag')
                    ->label('Endgültiger Betrag')
                    ->money('EUR')
                    ->sortable()
                    ->toggleable(),
                    
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Eingereicht')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'pending' => 'Ausstehend',
                        'reviewed' => 'Überprüft',
                        'quoted' => 'Angebot erstellt',
                        'accepted' => 'Angenommen',
                        'rejected' => 'Abgelehnt',
                        'completed' => 'Abgeschlossen',
                    ]),
                    
                Tables\Filters\Filter::make('recent')
                    ->query(fn (Builder $query): Builder => $query->recent())
                    ->label('Letzte 30 Tage'),
                    
                Tables\Filters\Filter::make('has_final_quote')
                    ->query(fn (Builder $query): Builder => $query->whereNotNull('endgueltiger_angebotsbetrag'))
                    ->label('Hat Endangebot'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                
                Tables\Actions\Action::make('mark_quoted')
                    ->label('Angebot erstellen')
                    ->icon('heroicon-o-currency-euro')
                    ->color('primary')
                    ->visible(fn (QuoteRequest $record): bool => 
                        in_array($record->status, ['pending', 'reviewed']))
                    ->form([
                        Forms\Components\TextInput::make('endgueltiger_angebotsbetrag')
                            ->label('Endgültiger Angebotsbetrag')
                            ->required()
                            ->numeric()
                            ->prefix('€')
                            ->step(0.01),
                        Forms\Components\Textarea::make('admin_notizen')
                            ->label('Notizen')
                            ->rows(3),
                    ])
                    ->action(function (QuoteRequest $record, array $data): void {
                        $record->markAsQuoted(
                            $data['endgueltiger_angebotsbetrag'], 
                            $data['admin_notizen'] ?? null
                        );
                        
                        Notification::make()
                            ->title('Angebot erstellt')
                            ->success()
                            ->send();
                    }),
                    
                Tables\Actions\Action::make('calculate_distance')
                    ->label('Entfernung berechnen')
                    ->icon('heroicon-o-map-pin')
                    ->color('info')
                    ->visible(fn (QuoteRequest $record): bool => 
                        !empty($record->from_postal_code) && 
                        !empty($record->to_postal_code) && 
                        is_null($record->distance_km))
                    ->action(function (QuoteRequest $record): void {
                        $calculator = app(DistanceCalculatorInterface::class);
                        $result = $calculator->calculateDistance(
                            $record->from_postal_code,
                            $record->to_postal_code
                        );
                        
                        if ($result['success']) {
                            $record->update(['distance_km' => $result['distance_km']]);
                            
                            Notification::make()
                                ->title('Entfernung berechnet: ' . $result['distance_km'] . ' km')
                                ->success()
                                ->send();
                        } else {
                            Notification::make()
                                ->title('Fehler bei Entfernungsberechnung')
                                ->danger()
                                ->send();
                        }
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    
                    Tables\Actions\BulkAction::make('mark_reviewed')
                        ->label('Als überprüft markieren')
                        ->icon('heroicon-o-eye')
                        ->color('info')
                        ->action(function (Collection $records): void {
                            $records->each->update(['status' => 'reviewed']);
                            
                            Notification::make()
                                ->title('Anfragen als überprüft markiert')
                                ->success()
                                ->send();
                        }),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListQuoteRequests::route('/'),
            'create' => Pages\CreateQuoteRequest::route('/create'),
            'view' => Pages\ViewQuoteRequest::route('/{record}'),
            'edit' => Pages\EditQuoteRequest::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', 'pending')->count();
    }
}
```

### 2. Settings Management Resource

```php
<?php
// app/Filament/Resources/SettingResource.php
namespace App\Filament\Resources;

use App\Models\Setting;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SettingResource extends Resource
{
    protected static ?string $model = Setting::class;
    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static ?string $navigationLabel = 'Einstellungen';
    protected static ?string $navigationGroup = 'System';
    protected static ?int $navigationSort = 90;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Einstellung Details')
                ->schema([
                    Forms\Components\Select::make('group_name')
                        ->label('Gruppe')
                        ->options([
                            'general' => 'Allgemein',
                            'pricing' => 'Preise',
                            'email' => 'E-Mail',
                            'api' => 'API',
                            'ui' => 'Benutzeroberfläche'
                        ])
                        ->required()
                        ->native(false),
                        
                    Forms\Components\TextInput::make('key_name')
                        ->label('Schlüssel')
                        ->required()
                        ->maxLength(100),
                        
                    Forms\Components\Select::make('type')
                        ->label('Typ')
                        ->options([
                            'string' => 'Text',
                            'integer' => 'Ganzzahl',
                            'decimal' => 'Dezimalzahl',
                            'boolean' => 'Ja/Nein',
                            'json' => 'JSON'
                        ])
                        ->required()
                        ->native(false)
                        ->live(),
                        
                    Forms\Components\Textarea::make('value')
                        ->label('Wert')
                        ->rows(3)
                        ->visible(fn (Forms\Get $get) => in_array($get('type'), ['string', 'json'])),
                        
                    Forms\Components\TextInput::make('value')
                        ->label('Wert')
                        ->numeric()
                        ->visible(fn (Forms\Get $get) => $get('type') === 'integer'),
                        
                    Forms\Components\TextInput::make('value')
                        ->label('Wert')
                        ->numeric()
                        ->step(0.01)
                        ->visible(fn (Forms\Get $get) => $get('type') === 'decimal'),
                        
                    Forms\Components\Toggle::make('value')
                        ->label('Wert')
                        ->visible(fn (Forms\Get $get) => $get('type') === 'boolean'),
                        
                    Forms\Components\Textarea::make('description')
                        ->label('Beschreibung')
                        ->rows(2),
                        
                    Forms\Components\Toggle::make('is_public')
                        ->label('Öffentlich sichtbar')
                        ->helperText('Kann von der Frontend-API abgerufen werden'),
                ])
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('group_name')
                    ->label('Gruppe')
                    ->badge()
                    ->searchable()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('key_name')
                    ->label('Schlüssel')
                    ->searchable()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('value')
                    ->label('Wert')
                    ->limit(50)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();
                        return strlen($state) > 50 ? $state : null;
                    }),
                    
                Tables\Columns\BadgeColumn::make('type')
                    ->label('Typ'),
                    
                Tables\Columns\IconColumn::make('is_public')
                    ->label('Öffentlich')
                    ->boolean(),
                    
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Aktualisiert')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('group_name')
                    ->label('Gruppe')
                    ->options([
                        'general' => 'Allgemein',
                        'pricing' => 'Preise',
                        'email' => 'E-Mail',
                        'api' => 'API',
                        'ui' => 'Benutzeroberfläche'
                    ]),
                    
                Tables\Filters\TernaryFilter::make('is_public')
                    ->label('Öffentlich'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('group_name');
    }
}
```

---

## � Frontende-Backend Integration (CRITICAL)

### React Frontend API Integration

The existing React frontend expects these **EXACT API endpoints** and data formats:

#### 1. Calculator Availability Check
```php
<?php
// routes/api.php
Route::get('/calculator/status', [QuoteController::class, 'checkAvailability']);

// Expected Response Format:
{
    "enabled": true,
    "message": "Calculator available"
}
```

#### 2. Price Calculation Endpoint
```php
// routes/api.php
Route::post('/calculator/calculate', [QuoteController::class, 'calculatePrice']);

// Expected Request Format (from React):
{
    "selectedServices": ["umzug", "putzservice"],
    "generalInfo": {
        "name": "Max Mustermann",
        "email": "max@example.com",
        "phone": "+49 1575 0693353",
        "preferredDate": "2025-02-15",
        "message": "Besondere Wünsche...",
        "preferredContact": "email"
    },
    "movingDetails": {
        "rooms": 3,
        "floors": 2,
        "fromAddress": "Musterstraße 1, 66123 Saarbrücken",
        "toAddress": "Beispielweg 5, 66456 Homburg",
        "fromPostalCode": "66123",
        "toPostalCode": "66456"
    },
    "cleaningDetails": {
        "rooms": 3,
        "bathrooms": 2,
        "deepCleaning": true
    },
    "declutterDetails": {
        "volume": "large",
        "items": ["furniture", "electronics"]
    }
}

// Expected Response Format:
{
    "success": true,
    "data": {
        "total": 450.00,
        "breakdown": {
            "base_services": {
                "umzug": 250.00,
                "putzservice": 120.00
            },
            "distance_cost": 80.00,
            "additional_fees": [],
            "discounts": [],
            "total": 450.00
        }
    }
}
```

#### 3. Quote Submission Endpoint
```php
// routes/api.php
Route::post('/quotes', [QuoteController::class, 'store']);

// Expected Request Format (EXACT match with React form):
{
    "name": "Max Mustermann",
    "email": "max@example.com",
    "phone": "+49 1575 0693353",
    "preferredDate": "2025-02-15",
    "message": "Besondere Anforderungen...",
    "preferredContact": "email",
    "selectedServices": ["umzug"],
    "movingDetails": {...},
    "cleaningDetails": {...},
    "declutterDetails": {...},
    "pricing": {
        "total": 450.00
    },
    "submittedAt": "2025-01-19T10:30:00.000Z",
    "source": "calculator"
}

// Expected Response Format:
{
    "success": true,
    "message": "Angebotsanfrage erfolgreich eingereicht",
    "data": {
        "angebotsnummer": "QR-2025-001",
        "estimated_total": 450.00
    }
}
```

### Laravel Blade Template Integration

The React app must be served through Laravel. Create this **EXACT** blade template:

```php
<?php
// resources/views/app.blade.php
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'YLA Umzug') }}</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="/favicon.ico">
    
    <!-- Meta tags for SEO -->
    <meta name="description" content="YLA Umzug - Professionelle Umzugs-, Reinigungs- und Entrümpelungsservices in Saarland & Rheinland-Pfalz">
    <meta name="keywords" content="Umzug, Reinigung, Entrümpelung, Saarland, Rheinland-Pfalz">
    
    <!-- Vite Assets - CRITICAL: This loads the React app -->
    @vite(['src/main.jsx'])
</head>
<body>
    <!-- React App Mount Point -->
    <div id="root"></div>
    
    <!-- Laravel Configuration for React -->
    <script>
        window.Laravel = {
            csrfToken: '{{ csrf_token() }}',
            apiUrl: '{{ config('app.url') }}/api',
            appUrl: '{{ config('app.url') }}'
        };
    </script>
</body>
</html>
```

### Web Routes Configuration

```php
<?php
// routes/web.php
use Illuminate\Support\Facades\Route;

// Admin routes (Filament)
// Filament automatically handles /admin routes

// API routes are in routes/api.php

// React SPA routes - CRITICAL: This catches all frontend routes
Route::get('/{path?}', function () {
    return view('app');
})->where('path', '^(?!admin|api).*$')->name('spa');

// This regex ensures:
// - /admin/* goes to Filament
// - /api/* goes to API routes  
// - Everything else goes to React SPA
```

### API Routes Configuration

```php
<?php
// routes/api.php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\QuoteController;
use App\Http\Controllers\Api\SettingsController;

// Calculator endpoints - EXACT paths expected by React
Route::prefix('calculator')->group(function () {
    Route::get('/status', [QuoteController::class, 'checkAvailability']);
    Route::post('/calculate', [QuoteController::class, 'calculatePrice']);
});

// Quote management
Route::post('/quotes', [QuoteController::class, 'store']);
Route::get('/quotes/{angebotsnummer}', [QuoteController::class, 'show']);

// Public settings for frontend
Route::get('/settings/public', [SettingsController::class, 'getPublicSettings']);

// CORS middleware is automatically applied to api routes
```

### CORS Configuration

```php
<?php
// config/cors.php
return [
    'paths' => ['api/*', 'sanctum/csrf-cookie'],
    'allowed_methods' => ['*'],
    'allowed_origins' => ['*'], // In production, specify your domain
    'allowed_origins_patterns' => [],
    'allowed_headers' => ['*'],
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => false,
];
```

### React API Client Configuration

The React app uses this API client structure. Ensure your Laravel API matches:

```javascript
// src/lib/api.js - This file exists in the React app
const API_BASE_URL = window.Laravel?.apiUrl || '/api';

export const calculatorApi = {
    // This calls GET /api/calculator/status
    isCalculatorEnabled: async () => {
        const response = await fetch(`${API_BASE_URL}/calculator/status`);
        return response.json();
    }
};

export const quoteApi = {
    // This calls POST /api/quotes
    submitQuote: async (quoteData) => {
        const response = await fetch(`${API_BASE_URL}/quotes`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': window.Laravel?.csrfToken
            },
            body: JSON.stringify(quoteData)
        });
        return response.json();
    }
};
```

## 🔌 API Implementation

### 1. Quote Controller

```php
<?php
// app/Http/Controllers/Api/QuoteController.php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\QuoteRequest;
use App\Contracts\PriceCalculatorInterface;
use App\Http\Requests\QuoteRequest as QuoteRequestValidation;
use Illuminate\Http\JsonResponse;

class QuoteController extends Controller
{
    public function __construct(
        private PriceCalculatorInterface $calculator
    ) {}

    public function store(QuoteRequestValidation $request): JsonResponse
    {
        try {
            $data = $request->validated();
            
            // Transform React frontend data to Laravel format
            $transformedData = $this->transformFrontendData($data);
            
            // Calculate estimated price
            $transformedData['estimated_total'] = $this->calculator->calculate($transformedData);
            
            $quote = QuoteRequest::create($transformedData);
            
            // Send confirmation email
            Mail::to($quote->email)->send(new QuoteConfirmationMail($quote));
            
            return response()->json([
                'success' => true,
                'message' => 'Angebotsanfrage erfolgreich eingereicht',
                'data' => [
                    'angebotsnummer' => $quote->angebotsnummer,
                    'estimated_total' => $quote->estimated_total
                ]
            ], 201);
            
        } catch (\Exception $e) {
            Log::error('Quote submission failed', [
                'error' => $e->getMessage(),
                'data' => $request->all()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Fehler beim Einreichen der Anfrage'
            ], 500);
        }
    }

    /**
     * Transform React frontend data format to Laravel database format
     */
    private function transformFrontendData(array $frontendData): array
    {
        return [
            // Basic customer info
            'name' => $frontendData['name'],
            'email' => $frontendData['email'],
            'telefon' => $frontendData['phone'] ?? $frontendData['telefon'],
            'bevorzugter_kontakt' => $frontendData['preferredContact'] ?? 'email',
            
            // Moving details
            'from_address' => $frontendData['movingDetails']['fromAddress'] ?? '',
            'to_address' => $frontendData['movingDetails']['toAddress'] ?? '',
            'from_postal_code' => $frontendData['movingDetails']['fromPostalCode'] ?? null,
            'to_postal_code' => $frontendData['movingDetails']['toPostalCode'] ?? null,
            'moving_date' => $frontendData['preferredDate'] ?? now()->addDays(7)->format('Y-m-d'),
            'moving_type' => 'local', // Default, can be enhanced based on distance
            
            // Services
            'ausgewaehlte_services' => $frontendData['selectedServices'] ?? [],
            'service_details' => [
                'moving' => $frontendData['movingDetails'] ?? [],
                'cleaning' => $frontendData['cleaningDetails'] ?? [],
                'declutter' => $frontendData['declutterDetails'] ?? []
            ],
            
            // Additional info
            'special_requirements' => $frontendData['message'] ?? null,
            'submitted_at' => now(),
        ];
    }

    public function calculatePrice(Request $request): JsonResponse
    {
        try {
            $data = $request->all();
            
            // Transform frontend data for calculation
            $transformedData = $this->transformFrontendData($data);
            
            $price = $this->calculator->calculate($transformedData);
            $breakdown = $this->calculator->getBreakdown($transformedData);
            
            return response()->json([
                'success' => true,
                'data' => [
                    'total' => $price,
                    'breakdown' => $breakdown
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Price calculation failed', [
                'error' => $e->getMessage(),
                'data' => $request->all()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Fehler bei der Preisberechnung'
            ], 500);
        }
    }

    public function checkAvailability(): JsonResponse
    {
        $enabled = Setting::getValue('calculator.enabled', true);
        
        return response()->json([
            'enabled' => $enabled,
            'message' => $enabled ? 'Calculator available' : 'Calculator temporarily disabled'
        ]);
    }
}

// app/Http/Requests/QuoteRequest.php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class QuoteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // Basic customer info (matches React form exactly)
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:50',
            'telefon' => 'nullable|string|max:50', // Alternative field name
            'preferredContact' => 'nullable|in:email,phone,whatsapp',
            'preferredDate' => 'nullable|date|after:today',
            'message' => 'nullable|string|max:1000',
            
            // Services (matches React exactly)
            'selectedServices' => 'required|array|min:1',
            'selectedServices.*' => 'in:umzug,putzservice,entruempelung',
            
            // Moving details (nested object from React)
            'movingDetails' => 'nullable|array',
            'movingDetails.fromAddress' => 'nullable|string|max:500',
            'movingDetails.toAddress' => 'nullable|string|max:500',
            'movingDetails.fromPostalCode' => 'nullable|string|max:10',
            'movingDetails.toPostalCode' => 'nullable|string|max:10',
            'movingDetails.rooms' => 'nullable|integer|min:1|max:20',
            'movingDetails.floors' => 'nullable|integer|min:0|max:10',
            
            // Cleaning details (nested object from React)
            'cleaningDetails' => 'nullable|array',
            'cleaningDetails.rooms' => 'nullable|integer|min:1|max:20',
            'cleaningDetails.bathrooms' => 'nullable|integer|min:1|max:10',
            'cleaningDetails.deepCleaning' => 'nullable|boolean',
            
            // Declutter details (nested object from React)
            'declutterDetails' => 'nullable|array',
            'declutterDetails.volume' => 'nullable|string|in:small,medium,large',
            'declutterDetails.items' => 'nullable|array',
            
            // Pricing info from React
            'pricing' => 'nullable|array',
            'pricing.total' => 'nullable|numeric|min:0',
            
            // Metadata
            'submittedAt' => 'nullable|date',
            'source' => 'nullable|string|in:calculator,contact_form',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Name ist erforderlich',
            'email.required' => 'E-Mail ist erforderlich',
            'email.email' => 'Gültige E-Mail-Adresse erforderlich',
            'moving_date.required' => 'Umzugsdatum ist erforderlich',
            'moving_date.after' => 'Umzugsdatum muss in der Zukunft liegen',
            'ausgewaehlte_services.required' => 'Mindestens ein Service muss ausgewählt werden',
        ];
    }
}
```

---

## � WhaitsApp Business Integration

### WhatsApp API Options

#### Option 1: WhatsApp Business API (Official - Recommended)
```php
<?php
// app/Services/WhatsAppService.php
namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    private Client $client;
    private string $accessToken;
    private string $phoneNumberId;
    private string $businessPhoneNumber;

    public function __construct()
    {
        $this->client = new Client(['timeout' => 30]);
        $this->accessToken = config('services.whatsapp.access_token');
        $this->phoneNumberId = config('services.whatsapp.phone_number_id');
        $this->businessPhoneNumber = config('services.whatsapp.business_number'); // +49 1575 0693353
    }

    public function sendQuoteMessage(QuoteRequest $quote): array
    {
        try {
            // Clean phone number (remove spaces, dashes, etc.)
            $customerPhone = $this->cleanPhoneNumber($quote->telefon);
            
            $message = $this->buildQuoteMessage($quote);
            
            $response = $this->client->post(
                "https://graph.facebook.com/v18.0/{$this->phoneNumberId}/messages",
                [
                    'headers' => [
                        'Authorization' => "Bearer {$this->accessToken}",
                        'Content-Type' => 'application/json'
                    ],
                    'json' => [
                        'messaging_product' => 'whatsapp',
                        'to' => $customerPhone,
                        'type' => 'template',
                        'template' => [
                            'name' => 'quote_ready_de', // Template name in WhatsApp Business
                            'language' => ['code' => 'de'],
                            'components' => [
                                [
                                    'type' => 'header',
                                    'parameters' => [
                                        [
                                            'type' => 'text',
                                            'text' => $quote->angebotsnummer
                                        ]
                                    ]
                                ],
                                [
                                    'type' => 'body',
                                    'parameters' => [
                                        [
                                            'type' => 'text',
                                            'text' => $quote->name
                                        ],
                                        [
                                            'type' => 'text',
                                            'text' => number_format($quote->endgueltiger_angebotsbetrag, 2) . ' €'
                                        ],
                                        [
                                            'type' => 'text',
                                            'text' => $quote->moving_date->format('d.m.Y')
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            );

            $result = json_decode($response->getBody(), true);
            
            Log::info('WhatsApp message sent', [
                'quote_id' => $quote->id,
                'customer_phone' => $customerPhone,
                'message_id' => $result['messages'][0]['id'] ?? null
            ]);

            return [
                'success' => true,
                'message_id' => $result['messages'][0]['id'] ?? null,
                'status' => 'sent'
            ];

        } catch (\Exception $e) {
            Log::error('WhatsApp message failed', [
                'quote_id' => $quote->id,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    public function sendQuoteDocument(QuoteRequest $quote, string $pdfContent): array
    {
        try {
            $customerPhone = $this->cleanPhoneNumber($quote->telefon);
            
            // First upload the PDF document
            $mediaId = $this->uploadDocument($pdfContent, "angebot-{$quote->angebotsnummer}.pdf");
            
            if (!$mediaId) {
                throw new \Exception('Failed to upload PDF document');
            }

            // Send document message
            $response = $this->client->post(
                "https://graph.facebook.com/v18.0/{$this->phoneNumberId}/messages",
                [
                    'headers' => [
                        'Authorization' => "Bearer {$this->accessToken}",
                        'Content-Type' => 'application/json'
                    ],
                    'json' => [
                        'messaging_product' => 'whatsapp',
                        'to' => $customerPhone,
                        'type' => 'document',
                        'document' => [
                            'id' => $mediaId,
                            'caption' => "Ihr Angebot {$quote->angebotsnummer} von YLA Umzug\n\n" .
                                       "Gesamtbetrag: " . number_format($quote->endgueltiger_angebotsbetrag, 2) . " €\n" .
                                       "Umzugsdatum: " . $quote->moving_date->format('d.m.Y') . "\n\n" .
                                       "Bei Fragen erreichen Sie uns unter:\n" .
                                       "📞 {$this->businessPhoneNumber}\n" .
                                       "📧 info@yla-umzug.de"
                        ]
                    ]
                ]
            );

            $result = json_decode($response->getBody(), true);

            return [
                'success' => true,
                'message_id' => $result['messages'][0]['id'] ?? null,
                'media_id' => $mediaId
            ];

        } catch (\Exception $e) {
            Log::error('WhatsApp document send failed', [
                'quote_id' => $quote->id,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    private function uploadDocument(string $pdfContent, string $filename): ?string
    {
        try {
            $response = $this->client->post(
                "https://graph.facebook.com/v18.0/{$this->phoneNumberId}/media",
                [
                    'headers' => [
                        'Authorization' => "Bearer {$this->accessToken}",
                    ],
                    'multipart' => [
                        [
                            'name' => 'file',
                            'contents' => $pdfContent,
                            'filename' => $filename,
                            'headers' => ['Content-Type' => 'application/pdf']
                        ],
                        [
                            'name' => 'type',
                            'contents' => 'application/pdf'
                        ],
                        [
                            'name' => 'messaging_product',
                            'contents' => 'whatsapp'
                        ]
                    ]
                ]
            );

            $result = json_decode($response->getBody(), true);
            return $result['id'] ?? null;

        } catch (\Exception $e) {
            Log::error('WhatsApp media upload failed', ['error' => $e->getMessage()]);
            return null;
        }
    }

    private function cleanPhoneNumber(string $phone): string
    {
        // Remove all non-numeric characters except +
        $cleaned = preg_replace('/[^\d+]/', '', $phone);
        
        // If starts with 0, replace with +49
        if (str_starts_with($cleaned, '0')) {
            $cleaned = '+49' . substr($cleaned, 1);
        }
        
        // If doesn't start with +, assume German number
        if (!str_starts_with($cleaned, '+')) {
            $cleaned = '+49' . $cleaned;
        }
        
        return $cleaned;
    }

    private function buildQuoteMessage(QuoteRequest $quote): string
    {
        return "🏠 *YLA Umzug - Ihr Angebot ist bereit!*\n\n" .
               "Hallo {$quote->name},\n\n" .
               "Ihr Angebot mit der Nummer *{$quote->angebotsnummer}* ist fertig!\n\n" .
               "📋 *Details:*\n" .
               "• Von: {$quote->from_address}\n" .
               "• Nach: {$quote->to_address}\n" .
               "• Datum: {$quote->moving_date->format('d.m.Y')}\n" .
               "• Services: " . implode(', ', $quote->ausgewaehlte_services) . "\n\n" .
               "💰 *Gesamtbetrag: " . number_format($quote->endgueltiger_angebotsbetrag, 2) . " €*\n\n" .
               "Das detaillierte Angebot als PDF erhalten Sie in der nächsten Nachricht.\n\n" .
               "Bei Fragen sind wir gerne für Sie da!\n" .
               "📞 {$this->businessPhoneNumber}\n" .
               "📧 info@yla-umzug.de";
    }
}
```

#### Option 2: Twilio WhatsApp API (Alternative)
```php
<?php
// app/Services/TwilioWhatsAppService.php
namespace App\Services;

use Twilio\Rest\Client;
use Illuminate\Support\Facades\Log;

class TwilioWhatsAppService
{
    private Client $client;
    private string $fromNumber;

    public function __construct()
    {
        $this->client = new Client(
            config('services.twilio.sid'),
            config('services.twilio.token')
        );
        $this->fromNumber = config('services.twilio.whatsapp_from'); // whatsapp:+14155238886
    }

    public function sendQuoteMessage(QuoteRequest $quote): array
    {
        try {
            $customerPhone = 'whatsapp:' . $this->cleanPhoneNumber($quote->telefon);
            $message = $this->buildQuoteMessage($quote);

            $twilioMessage = $this->client->messages->create(
                $customerPhone,
                [
                    'from' => $this->fromNumber,
                    'body' => $message
                ]
            );

            return [
                'success' => true,
                'message_sid' => $twilioMessage->sid,
                'status' => $twilioMessage->status
            ];

        } catch (\Exception $e) {
            Log::error('Twilio WhatsApp message failed', [
                'quote_id' => $quote->id,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
}
```

### WhatsApp Template Setup (Meta Business)

Create these templates in your Meta Business Manager:

#### Template 1: Quote Ready Notification
```
Template Name: quote_ready_de
Category: UTILITY
Language: German (de)

Header: 
🏠 Ihr Angebot {{1}} ist bereit!

Body:
Hallo {{1}},

Ihr Umzugsangebot ist fertig erstellt!

💰 Gesamtbetrag: {{2}}
📅 Umzugsdatum: {{3}}

Das detaillierte Angebot als PDF folgt in der nächsten Nachricht.

Bei Fragen sind wir gerne für Sie da!

Footer:
YLA Umzug - Ihr zuverlässiger Partner

Buttons:
- Call: +49 1575 0693353
- URL: https://yla-umzug.de/kontakt
```

#### Template 2: Quote Confirmation
```
Template Name: quote_confirmation_de
Category: UTILITY
Language: German (de)

Header:
✅ Anfrage erhalten - {{1}}

Body:
Vielen Dank {{1}}!

Wir haben Ihre Umzugsanfrage erhalten und bearbeiten diese schnellstmöglich.

📋 Ihre Anfrage-Nr.: {{2}}
📅 Gewünschter Termin: {{3}}

Sie erhalten Ihr persönliches Angebot innerhalb von 24 Stunden.

Footer:
YLA Umzug - Professionell & Zuverlässig
```

### Integration with Filament Admin

```php
<?php
// Add to app/Filament/Resources/QuoteRequestResource.php

// In the actions() method, add:
Tables\Actions\Action::make('send_whatsapp')
    ->label('WhatsApp senden')
    ->icon('heroicon-o-chat-bubble-left-right')
    ->color('success')
    ->visible(fn (QuoteRequest $record): bool => 
        !empty($record->telefon) && 
        $record->status === 'quoted' &&
        !empty($record->endgueltiger_angebotsbetrag))
    ->requiresConfirmation()
    ->modalHeading('Angebot per WhatsApp senden')
    ->modalDescription(fn (QuoteRequest $record) => 
        "Angebot {$record->angebotsnummer} an {$record->telefon} senden?")
    ->action(function (QuoteRequest $record): void {
        $whatsappService = app(WhatsAppService::class);
        $pdfService = app(PdfService::class);
        
        // Send text message first
        $messageResult = $whatsappService->sendQuoteMessage($record);
        
        if ($messageResult['success']) {
            // Generate and send PDF
            $pdfContent = $pdfService->generateQuotePdf($record);
            $documentResult = $whatsappService->sendQuoteDocument($record, $pdfContent);
            
            if ($documentResult['success']) {
                // Log the WhatsApp send
                $record->update([
                    'admin_notizen' => ($record->admin_notizen ?? '') . 
                        "\n[" . now()->format('d.m.Y H:i') . "] Angebot per WhatsApp gesendet"
                ]);
                
                Notification::make()
                    ->title('WhatsApp Nachricht gesendet')
                    ->success()
                    ->send();
            } else {
                Notification::make()
                    ->title('Fehler beim Senden des PDF')
                    ->danger()
                    ->send();
            }
        } else {
            Notification::make()
                ->title('WhatsApp Nachricht fehlgeschlagen')
                ->danger()
                ->send();
        }
    }),

// Also add bulk action for multiple quotes:
Tables\Actions\BulkAction::make('send_whatsapp_bulk')
    ->label('WhatsApp an alle senden')
    ->icon('heroicon-o-chat-bubble-left-right')
    ->color('success')
    ->requiresConfirmation()
    ->action(function (Collection $records): void {
        $whatsappService = app(WhatsAppService::class);
        $pdfService = app(PdfService::class);
        $sent = 0;
        $failed = 0;
        
        foreach ($records as $record) {
            if (empty($record->telefon) || $record->status !== 'quoted') {
                $failed++;
                continue;
            }
            
            $messageResult = $whatsappService->sendQuoteMessage($record);
            if ($messageResult['success']) {
                $pdfContent = $pdfService->generateQuotePdf($record);
                $whatsappService->sendQuoteDocument($record, $pdfContent);
                $sent++;
            } else {
                $failed++;
            }
            
            // Add delay to avoid rate limiting
            sleep(1);
        }
        
        Notification::make()
            ->title("WhatsApp: {$sent} gesendet, {$failed} fehlgeschlagen")
            ->success()
            ->send();
    }),
```

### Configuration Setup

```php
<?php
// config/services.php - Add WhatsApp configuration
'whatsapp' => [
    'access_token' => env('WHATSAPP_ACCESS_TOKEN'),
    'phone_number_id' => env('WHATSAPP_PHONE_NUMBER_ID'),
    'business_number' => env('WHATSAPP_BUSINESS_NUMBER', '+49 1575 0693353'),
    'webhook_verify_token' => env('WHATSAPP_WEBHOOK_VERIFY_TOKEN'),
],

'twilio' => [
    'sid' => env('TWILIO_SID'),
    'token' => env('TWILIO_TOKEN'),
    'whatsapp_from' => env('TWILIO_WHATSAPP_FROM', 'whatsapp:+14155238886'),
],
```

```env
# .env additions
# WhatsApp Business API (Meta)
WHATSAPP_ACCESS_TOKEN=your_access_token_here
WHATSAPP_PHONE_NUMBER_ID=your_phone_number_id
WHATSAPP_BUSINESS_NUMBER="+49 1575 0693353"
WHATSAPP_WEBHOOK_VERIFY_TOKEN=your_webhook_token

# Alternative: Twilio WhatsApp
TWILIO_SID=your_twilio_sid
TWILIO_TOKEN=your_twilio_token
TWILIO_WHATSAPP_FROM=whatsapp:+14155238886
```

### Webhook Handler for Message Status

```php
<?php
// app/Http/Controllers/WhatsAppWebhookController.php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WhatsAppWebhookController extends Controller
{
    public function verify(Request $request)
    {
        $verifyToken = config('services.whatsapp.webhook_verify_token');
        
        if ($request->get('hub_verify_token') === $verifyToken) {
            return response($request->get('hub_challenge'));
        }
        
        return response('Unauthorized', 401);
    }

    public function webhook(Request $request)
    {
        $data = $request->all();
        
        Log::info('WhatsApp webhook received', $data);
        
        // Handle message status updates
        if (isset($data['entry'][0]['changes'][0]['value']['statuses'])) {
            foreach ($data['entry'][0]['changes'][0]['value']['statuses'] as $status) {
                $this->handleMessageStatus($status);
            }
        }
        
        // Handle incoming messages (for customer replies)
        if (isset($data['entry'][0]['changes'][0]['value']['messages'])) {
            foreach ($data['entry'][0]['changes'][0]['value']['messages'] as $message) {
                $this->handleIncomingMessage($message);
            }
        }
        
        return response('OK');
    }

    private function handleMessageStatus(array $status): void
    {
        // Log message delivery status
        Log::info('WhatsApp message status', [
            'message_id' => $status['id'],
            'status' => $status['status'],
            'timestamp' => $status['timestamp']
        ]);
        
        // You can update quote records based on delivery status
        // e.g., mark as "WhatsApp delivered" when status is "delivered"
    }

    private function handleIncomingMessage(array $message): void
    {
        // Handle customer replies to quotes
        $customerPhone = $message['from'];
        $messageText = $message['text']['body'] ?? '';
        
        Log::info('WhatsApp incoming message', [
            'from' => $customerPhone,
            'message' => $messageText
        ]);
        
        // You could automatically update quote status based on customer responses
        // e.g., if they reply "Ja" or "Angenommen", mark quote as accepted
    }
}

// routes/api.php - Add webhook routes
Route::get('/whatsapp/webhook', [WhatsAppWebhookController::class, 'verify']);
Route::post('/whatsapp/webhook', [WhatsAppWebhookController::class, 'webhook']);
```

---

## 📧 Email & PDF System

### 1. Mail Classes

```php
<?php
// app/Mail/QuoteConfirmationMail.php
namespace App\Mail;

use App\Models\QuoteRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class QuoteConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public QuoteRequest $quote
    ) {}

    public function build()
    {
        return $this->subject('Ihre Angebotsanfrage bei YLA Umzug - ' . $this->quote->angebotsnummer)
            ->view('emails.quote-confirmation')
            ->with([
                'quote' => $this->quote,
                'estimatedPrice' => number_format($this->quote->estimated_total, 2) . ' €'
            ]);
    }
}

// app/Mail/QuoteReadyMail.php
namespace App\Mail;

use App\Models\QuoteRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class QuoteReadyMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public QuoteRequest $quote
    ) {}

    public function build()
    {
        $pdf = app(PdfService::class)->generateQuotePdf($this->quote);
        
        return $this->subject('Ihr Angebot ist bereit - ' . $this->quote->angebotsnummer)
            ->view('emails.quote-ready')
            ->attachData($pdf, "angebot-{$this->quote->angebotsnummer}.pdf", [
                'mime' => 'application/pdf',
            ]);
    }
}
```

### 2. PDF Generation Service

```php
<?php
// app/Services/PdfService.php
namespace App\Services;

use App\Models\QuoteRequest;
use Barryvdh\DomPDF\Facade\Pdf;

class PdfService
{
    public function generateQuotePdf(QuoteRequest $quote): string
    {
        $data = [
            'quote' => $quote,
            'company' => [
                'name' => 'YLA Umzug',
                'address' => 'Musterstraße 123, 66123 Saarbrücken',
                'phone' => '+49 1575 0693353',
                'email' => 'info@yla-umzug.de',
                'website' => 'www.yla-umzug.de'
            ],
            'generated_at' => now()->format('d.m.Y H:i')
        ];

        $pdf = Pdf::loadView('pdf.quote', $data);
        $pdf->setPaper('A4', 'portrait');
        
        return $pdf->output();
    }
}
```

---

## ⚙️ Configuration Files

### 1. Environment Configuration

```env
# .env.example
APP_NAME="YLA Umzug"
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=https://your-domain.com

DB_CONNECTION=sqlite
DB_DATABASE=/path/to/database.sqlite

MAIL_MAILER=smtp
MAIL_HOST=your-smtp-host
MAIL_PORT=587
MAIL_USERNAME=your-email
MAIL_PASSWORD=your-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@yla-umzug.de
MAIL_FROM_NAME="YLA Umzug"

# OpenRouteService API
OPENROUTE_API_KEY=eyJvcmciOiI1YjNjZTM1OTc4NTExMTAwMDFjZjYyNDgiLCJpZCI6IjVmM2M0OTRkNGE0NzQzZjliMTRlMmJmY2M3N2EwZTQ1IiwiaCI6Im11cm11cjY0In0=

# Filament
FILAMENT_DOMAIN=admin.yla-umzug.de
```

### 2. Service Provider Registration

```php
<?php
// config/services.php
return [
    // ... existing services
    
    'openroute' => [
        'api_key' => env('OPENROUTE_API_KEY'),
        'base_url' => 'https://api.openrouteservice.org/v2',
    ],
];

// app/Providers/AppServiceProvider.php
namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Contracts\PriceCalculatorInterface;
use App\Contracts\DistanceCalculatorInterface;
use App\Services\PriceCalculator;
use App\Services\OpenRouteServiceCalculator;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(DistanceCalculatorInterface::class, OpenRouteServiceCalculator::class);
        $this->app->bind(PriceCalculatorInterface::class, PriceCalculator::class);
    }

    public function boot(): void
    {
        //
    }
}
```

---

## 🚀 Production Deployment Checklist

### Security & Performance
- [ ] Configure proper HTTPS with SSL certificates
- [ ] Set up rate limiting for API endpoints
- [ ] Implement CSRF protection
- [ ] Configure proper CORS headers
- [ ] Set up database backups
- [ ] Configure error logging and monitoring
- [ ] Implement queue system for email processing
- [ ] Set up caching (Redis recommended)

### Monitoring & Maintenance
- [ ] Configure application monitoring (Laravel Telescope)
- [ ] Set up error tracking (Sentry)
- [ ] Implement health checks
- [ ] Configure automated backups
- [ ] Set up log rotation
- [ ] Create maintenance mode handling

### API Documentation
- [ ] Generate API documentation (Swagger/OpenAPI)
- [ ] Create frontend integration guide
- [ ] Document environment setup
- [ ] Create deployment guide

---

## 🎯 Implementation Priority

### Phase 1 (Critical - Day 1-2)
1. **Set up Laravel project structure** with exact directory matching
2. **Create database migrations** with exact field names and types
3. **Implement models** with exact relationships and casts
4. **Configure routes** (web.php and api.php) with exact paths
5. **Create basic API endpoints** that match React expectations

### Phase 2 (High Priority - Day 3-4)
1. **Implement Quote Controller** with exact request/response formats
2. **Add distance calculation service** with OpenRoute API key
3. **Set up Filament admin panel** with German localization
4. **Create quote management interface** with all actions
5. **Test API integration** with existing React frontend

### Phase 3 (Medium Priority - Day 5-6)
1. **Add email notification system** with templates
2. **Set up PDF generation** service
3. **Implement settings management** in Filament
4. **Add comprehensive error handling** and logging
5. **Configure CORS and security** middleware

### Phase 4 (Production Ready - Day 7)
1. **Final testing** of all API endpoints
2. **Security hardening** and rate limiting
3. **Performance optimization** and caching
4. **Deployment configuration** for hosting
5. **Documentation** and final validation

### WhatsApp Integration (Optional - After Core)
1. **Set up WhatsApp Business API** account
2. **Create message templates** and get approval
3. **Implement WhatsApp service** class
4. **Add WhatsApp actions** to Filament dashboard
5. **Test message delivery** and document handling

---

## 📝 CRITICAL INSTRUCTIONS FOR AI AGENT

### 🚨 ABSOLUTE REQUIREMENTS - DO NOT MODIFY FRONTEND

**PRESERVE 100% OF EXISTING FRONTEND:**
- **DO NOT CHANGE** any React components, styling, or animations
- **DO NOT MODIFY** any existing frontend routes or navigation  
- **DO NOT ALTER** any existing form validation or user interactions
- **DO NOT TOUCH** the existing build system (Vite, Tailwind, etc.)
- **KEEP ALL** mobile responsiveness and UI/UX exactly as is

### 🎯 EXACT API INTEGRATION REQUIREMENTS

**API ENDPOINTS MUST MATCH EXACTLY:**
- `GET /api/calculator/status` - Returns calculator availability
- `POST /api/calculator/calculate` - Calculates prices from React data
- `POST /api/quotes` - Accepts quote submissions from React form
- All request/response formats must match the examples above **EXACTLY**

**DATA TRANSFORMATION IS CRITICAL:**
- React sends data in camelCase format (selectedServices, movingDetails)
- Laravel database uses snake_case (ausgewaehlte_services, service_details)
- The `transformFrontendData()` method handles this conversion
- **DO NOT** change the React data format - transform it in Laravel

### 🔧 IMPLEMENTATION CHECKLIST

**Phase 1 - Core Backend (MUST DO FIRST):**
- [ ] Install Laravel 10 in the existing project directory
- [ ] Create exact database schema with all field names as specified
- [ ] Implement QuoteRequest model with exact casts and relationships
- [ ] Set up API routes with exact paths (`/api/calculator/*`, `/api/quotes`)
- [ ] Create QuoteController with exact request/response formats

**Phase 2 - Filament Dashboard (HIGH PRIORITY):**
- [ ] Install Filament v3 with German localization
- [ ] Create QuoteRequestResource with all German labels
- [ ] Add distance calculation integration with OpenRoute API
- [ ] Implement quote workflow (pending → reviewed → quoted → completed)
- [ ] Add bulk actions and filtering capabilities

**Phase 3 - Services Integration (MEDIUM PRIORITY):**
- [ ] Implement OpenRouteServiceCalculator with provided API key
- [ ] Create PriceCalculator service with German pricing logic
- [ ] Set up email system with German templates
- [ ] Add PDF generation service
- [ ] Configure all service providers and bindings

**Phase 4 - Production Features (AFTER CORE WORKS):**
- [ ] Add comprehensive error handling and logging
- [ ] Implement rate limiting and security middleware
- [ ] Set up caching for distance calculations
- [ ] Configure deployment settings
- [ ] Add monitoring and backup systems

### 🧪 TESTING REQUIREMENTS

**CRITICAL VALIDATION STEPS:**
1. **API Testing**: Use existing React frontend to test all endpoints
2. **Data Flow**: Verify React → Laravel → Database → Filament works
3. **Admin Dashboard**: Test all Filament features with real data
4. **Distance Calculation**: Verify OpenRoute API integration works
5. **Email System**: Test quote confirmation and notification emails

### 🔑 CONFIGURATION DETAILS

**Environment Variables (EXACT):**
```env
OPENROUTE_API_KEY=eyJvcmciOiI1YjNjZTM1OTc4NTExMTAwMDFjZjYyNDgiLCJpZCI6IjVmM2M0OTRkNGE0NzQzZjliMTRlMmJmY2M3N2EwZTQ1IiwiaCI6Im11cm11cjY0In0=
```

**File Structure (MUST PRESERVE):**
- Keep all existing `src/` React components unchanged
- Add Laravel `app/`, `database/`, `routes/` directories
- Preserve existing `package.json`, `vite.config.js`, `tailwind.config.js`
- Add `composer.json` and Laravel configuration files

### ⚠️ COMMON MISTAKES TO AVOID

**DO NOT:**
- Change any React component file names or structure
- Modify existing API client code in `src/lib/api.js`
- Alter existing form validation in React components
- Change existing routing in React Router
- Modify existing styling or CSS files
- Break existing mobile responsiveness

**DO:**
- Add Laravel backend that serves the existing React app
- Create API endpoints that match existing React expectations
- Transform data between React format and Laravel format
- Add Filament admin panel as separate admin interface
- Preserve all existing frontend functionality

### 🎯 SUCCESS CRITERIA

**The implementation is successful when:**
1. Existing React frontend works exactly as before
2. All calculator functionality works through Laravel API
3. Quote submissions save to Laravel database
4. Filament admin panel shows and manages all quotes
5. Distance calculation works with OpenRoute API
6. Email notifications are sent for new quotes
7. Admin can create final quotes and send them to customers

**Remember: The goal is to ADD a professional backend to the existing frontend, not replace or modify the frontend in any way.**