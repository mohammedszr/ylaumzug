# YLA Umzug Laravel Backend Documentation

## 📋 Overview

This Laravel backend provides a robust API system for the YLA Umzug multi-service calculator, quote management, and admin panel. The system is designed to be simple, maintainable, and scalable for junior developers.

## 🏗️ Architecture & Code Design

### Design Principles
- **Separation of Concerns**: Controllers handle HTTP, Services handle business logic
- **Single Responsibility**: Each class has one clear purpose
- **German-First**: All user-facing content in German
- **Mobile-Optimized**: API designed for mobile-first React frontend
- **Junior Developer Friendly**: Clear naming, extensive comments, standard Laravel patterns

### Key Components

```
backend/
├── app/
│   ├── Http/Controllers/          # API endpoints
│   │   ├── CalculatorController   # Price calculations
│   │   ├── QuoteController        # Quote management
│   │   └── SettingsController     # Configuration
│   ├── Http/Requests/             # Input validation
│   ├── Http/Middleware/           # CORS handling
│   ├── Services/                  # Business logic
│   │   └── PricingService         # Complex pricing calculations
│   └── Models/                    # Database models (to be created)
├── routes/
│   ├── api.php                    # API routes
│   └── web.php                    # Admin panel routes
└── config/                        # Laravel configuration
```

## 🧮 Pricing Engine Logic

### PricingService Explained

The `PricingService` is the heart of the calculator system. Here's how it works:

#### 1. **Multi-Service Calculation**
```php
public function calculateTotal(array $services, array $serviceDetails)
```
- Loops through selected services (umzug, entruempelung, putzservice)
- Calculates each service independently
- Applies combination discounts for multiple services
- Adds express surcharges if needed

#### 2. **Moving Cost Calculation**
```php
private function calculateMovingCost(array $details)
```
**Factors considered:**
- **Base Cost**: `apartmentSize * 8€` (minimum 300€)
- **Distance**: Calculated from postal codes (2€/km)
- **Boxes**: 3€ per box
- **Floor Surcharge**: 50€ per floor above 2nd (if no elevator)
- **Additional Services**: Assembly (200€), Packing (150€), etc.

**Example Calculation:**
```
80m² apartment = 640€ base
50km distance = 100€
20 boxes = 60€
3rd floor, no elevator = 50€
Total: 850€
```

#### 3. **Decluttering Cost Calculation**
```php
private function calculateDeclutterCost(array $details)
```
**Volume-Based Pricing:**
- Low: 300€
- Medium: 600€
- High: 1,200€
- Extreme (Messi): 2,000€

**Additional Costs:**
- Hazardous waste: +150€
- Electronics disposal: +100€
- Clean handover: +150€
- Floor surcharge: 30€ per floor above 2nd

#### 4. **Cleaning Cost Calculation**
```php
private function calculateCleaningCost(array $details)
```
**Intensity Multipliers:**
- Normal: 3€/m²
- Deep cleaning: 5€/m²
- Construction cleaning: 7€/m²

**Additional Services:**
- Window cleaning: +2€/m²
- Regular service discount: -15%

#### 5. **Smart Discounts**
- **2 services**: 10% discount
- **3+ services**: 15% discount
- **Regular cleaning**: 15% discount

## 🔧 Setup & Customization Guide

### 1. **Environment Setup**

**Required Software:**
```bash
# Install PHP 8.1+
# Install Composer
# Install MySQL/PostgreSQL
```

**Installation Steps:**
```bash
cd backend
composer install
cp .env.example .env
php artisan key:generate
```

### 2. **Database Configuration**

**Edit `.env` file:**
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=yla_umzug
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

### 3. **Business Configuration**

**Edit `.env` file:**
```env
# Business Settings
BUSINESS_EMAIL=info@yla-umzug.de
BUSINESS_PHONE="+49 123 456789"
CALCULATOR_ENABLED=true

# Email Settings
MAIL_FROM_ADDRESS="noreply@yla-umzug.de"
MAIL_FROM_NAME="YLA Umzug"
```

### 4. **CORS Configuration**

**Update allowed origins in `HandleCors.php`:**
```php
$allowedOrigins = [
    'http://localhost:5173',     // Vite dev server
    'https://your-domain.de',    // Your production domain
    'https://www.your-domain.de' // Your production domain with www
];
```

## 🎯 API Endpoints Documentation

### Calculator API

#### `POST /api/calculator/calculate`
**Purpose**: Calculate pricing for selected services

**Request Body:**
```json
{
  "selectedServices": ["umzug", "putzservice"],
  "movingDetails": {
    "apartmentSize": 80,
    "fromAddress": {"postalCode": "66111", "city": "Saarbrücken"},
    "toAddress": {"postalCode": "67655", "city": "Kaiserslautern"},
    "boxes": 20,
    "additionalServices": ["packing"]
  },
  "cleaningDetails": {
    "size": 80,
    "cleaningIntensity": "deep",
    "rooms": ["kitchen", "bathroom", "windows"]
  },
  "generalInfo": {
    "urgency": "normal"
  }
}
```

**Response:**
```json
{
  "success": true,
  "pricing": {
    "total": 1250,
    "breakdown": [
      {
        "service": "Umzug",
        "cost": 950,
        "details": ["Grundpreis (80m²): 640€", "Entfernung (50km): 100€", ...]
      },
      {
        "service": "Putzservice", 
        "cost": 400,
        "details": ["Grundreinigung (80m²): 400€"]
      },
      {
        "service": "Kombinationsrabatt",
        "cost": -100,
        "details": ["Rabatt für mehrere Services"]
      }
    ],
    "currency": "EUR"
  },
  "disclaimer": "Dies ist eine unverbindliche Schätzung..."
}
```

### Quote Management API

#### `POST /api/quotes/submit`
**Purpose**: Submit quote request with customer details

**Request Body:**
```json
{
  "name": "Max Mustermann",
  "email": "max@example.de",
  "phone": "+49 123 456789",
  "preferredDate": "2024-02-15",
  "preferredContact": "email",
  "message": "Zusätzliche Informationen...",
  "selectedServices": ["umzug"],
  "pricingData": { "total": 850, "breakdown": [...] },
  "movingDetails": { ... }
}
```

## 🔒 Security Considerations

### Input Validation
- **CalculateRequest**: Validates all calculator inputs
- **SubmitQuoteRequest**: Validates customer data
- **German error messages**: User-friendly validation feedback

### CORS Protection
- **Whitelist approach**: Only allowed origins can access API
- **Credentials support**: For authenticated requests
- **Preflight handling**: Proper OPTIONS request handling

### Error Handling
```php
try {
    // Business logic
} catch (\Exception $e) {
    \Log::error('Calculator error: ' . $e->getMessage());
    
    return response()->json([
        'success' => false,
        'message' => 'User-friendly German error message',
        'error' => config('app.debug') ? $e->getMessage() : null
    ], 500);
}
```

## 📧 Email System (Ready for Implementation)

### Email Templates Structure
```php
// Quote request to business owner
Mail::to(config('mail.business_email'))
    ->send(new QuoteRequestMail($quoteRequest));

// Confirmation to customer  
Mail::to($quoteRequest->email)
    ->send(new QuoteConfirmationMail($quoteRequest));
```

## 🎛️ Admin Panel Structure

### Routes
- `/admin/` - Dashboard
- `/admin/quotes` - Quote management
- `/admin/settings` - Calculator toggle & pricing

### Features (To Be Implemented)
- Quote review and approval
- Pricing management
- Calculator enable/disable
- Basic analytics

## 🚀 Deployment Considerations

### Production Setup
1. **Environment**: Set `APP_ENV=production`
2. **Debug**: Set `APP_DEBUG=false`
3. **Database**: Configure production database
4. **Email**: Configure SMTP settings
5. **HTTPS**: Ensure SSL certificate
6. **Caching**: Enable Laravel caching

### Performance Optimization
- **Database indexing**: On quote searches
- **API caching**: For pricing rules
- **Log rotation**: Prevent log file bloat

## 🔧 Customization Points

### 1. **Pricing Logic** (`PricingService.php`)
**Easy to modify:**
- Base prices per service
- Distance calculation rates
- Additional service costs
- Discount percentages

**Example customization:**
```php
// Change base moving cost from 8€/m² to 10€/m²
$baseCost = max(300, $apartmentSize * 10);

// Adjust combination discount
if ($serviceCount >= 2) {
    return round($totalCost * 0.12); // Changed from 0.10 to 0.12
}
```

### 2. **Service Configuration**
**Add new services in `getAvailableServices()`:**
```php
[
    'id' => 'new_service',
    'name' => 'New Service Name',
    'description' => 'Service description',
    'base_price' => 200
]
```

### 3. **Validation Rules**
**Modify in Request classes:**
```php
// Add new validation rules
'newField' => 'required|string|max:100',
'newNumericField' => 'nullable|numeric|min:0|max:1000'
```

## 🐛 Troubleshooting

### Common Issues

1. **CORS Errors**
   - Check allowed origins in `HandleCors.php`
   - Verify React dev server URL

2. **Validation Errors**
   - Check request format matches validation rules
   - Verify German error messages display correctly

3. **Calculation Errors**
   - Check `PricingService` logic
   - Verify input data format
   - Check Laravel logs for detailed errors

### Debug Mode
```php
// Enable detailed error messages
APP_DEBUG=true

// Check Laravel logs
tail -f storage/logs/laravel.log
```

## 📝 Next Steps

### Immediate Tasks
1. **Database Setup**: Create migrations and models
2. **Email Templates**: Implement German email templates  
3. **Frontend Integration**: Connect React calculator to API
4. **Admin Panel**: Build quote management interface

### Future Enhancements
- PDF quote generation
- Advanced analytics
- Multi-language support
- Payment integration

## 👥 Developer Notes

### For Junior Developers
- **Follow Laravel conventions**: Use Artisan commands, follow naming patterns
- **Test thoroughly**: Use Postman/Insomnia to test API endpoints
- **Read Laravel docs**: Official documentation is excellent
- **Use Laravel debugging tools**: `dd()`, `dump()`, Laravel Telescope

### Code Standards
- **PSR-4 autoloading**: Follow namespace conventions
- **Descriptive naming**: Variables and methods should be self-documenting
- **German comments**: For business logic explanations
- **Error logging**: Always log errors with context

This backend provides a solid foundation that can grow with your business needs while remaining maintainable by junior developers.