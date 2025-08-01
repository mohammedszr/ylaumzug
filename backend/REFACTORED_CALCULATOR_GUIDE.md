# Refactored Calculator System Guide

## Overview

The YLA Umzug pricing calculator has been completely refactored from a single 931-line service into clean, manageable, specialized calculators. This new architecture is maintainable, testable, and easily configurable.

## New Architecture

### Calculator Classes (Total: ~450 lines vs 931 lines)

1. **MovingPriceCalculator** (~150 lines)
   - Handles moving service pricing
   - Distance calculations
   - Floor surcharges
   - Additional services

2. **DeclutterPriceCalculator** (~100 lines)
   - Volume-based pricing
   - Waste type surcharges
   - Access difficulty calculations

3. **CleaningPriceCalculator** (~100 lines)
   - Area-based pricing
   - Room-specific surcharges
   - Frequency discounts

4. **DiscountCalculator** (~50 lines)
   - Combination discounts
   - Express surcharges
   - Special bonuses

5. **DistanceCalculator** (~50 lines)
   - German postal code distance estimation
   - Service area validation

## Key Improvements

### ✅ **Fixed Issues**
- **Single Responsibility**: Each calculator handles one service type
- **Testability**: Small, focused classes are easy to test
- **Maintainability**: Changes to one service don't affect others
- **Configuration**: All pricing moved to database settings
- **Validation**: Proper input validation for each calculator
- **Error Handling**: Graceful failure handling per service

### ✅ **New Features**
- **Admin Interface**: Simple web interface to update prices
- **Settings Management**: Database-driven configuration
- **Calculator Toggle**: Enable/disable calculator instantly
- **Caching**: Settings are cached for performance
- **API Endpoints**: Clean API for settings management

## Installation & Setup

### 1. Run Setup Command
```bash
cd backend
php artisan calculator:setup
```

This command will:
- Run database migrations
- Seed default settings
- Seed services
- Clear caches

### 2. Configure Settings
Visit the admin panel at `/admin/settings` to configure:
- Business information
- Service areas (postal codes)
- Pricing for all services
- Discounts and surcharges

### 3. Test the System
```bash
# Test calculator API
curl -X POST http://localhost:8000/api/calculator/calculate \
  -H "Content-Type: application/json" \
  -d '{
    "selectedServices": ["umzug"],
    "movingDetails": {
      "apartmentSize": 80,
      "fromAddress": {"postalCode": "66111"},
      "toAddress": {"postalCode": "66112"},
      "boxes": 20
    }
  }'
```

## Admin Interface

### Access
- URL: `/admin/settings`
- Features:
  - Toggle calculator on/off
  - Update all pricing settings
  - Organize settings by category
  - Real-time calculator status

### Settings Categories
1. **Calculator Control** - Enable/disable
2. **Business Information** - Contact details, service areas
3. **Moving Service Pricing** - Base rates, surcharges
4. **Decluttering Service Pricing** - Volume pricing, waste surcharges
5. **Cleaning Service Pricing** - Area rates, room surcharges
6. **Discounts & Bonuses** - Combination discounts
7. **Surcharges** - Express service fees

## API Endpoints

### Calculator API
```
POST /api/calculator/calculate
GET  /api/calculator/services
GET  /api/calculator/enabled
```

### Settings API
```
GET  /api/settings/public
GET  /admin/api/settings
PUT  /admin/settings
POST /admin/settings/toggle-calculator
```

## Configuration Examples

### Basic Pricing Setup
```php
// Moving service base pricing
Setting::setValue('base_price_per_sqm', 8.0);
Setting::setValue('distance_rate', 2.0);
Setting::setValue('box_handling_rate', 3.0);

// Decluttering volume pricing
Setting::setValue('declutter_volume_medium', 600);
Setting::setValue('hazardous_waste_surcharge', 150.0);

// Cleaning rates
Setting::setValue('cleaning_rate_deep', 5.0);
Setting::setValue('window_cleaning_rate', 2.0);

// Discounts
Setting::setValue('combination_discount_2_services', 0.10); // 10%
```

### Service Areas
```php
// Set postal codes you serve
Setting::setValue('service_areas', [
    '66111', '66112', '66113',  // Exact codes
    '661*',                      // Prefix match
    '67655', '67656'
], 'json');
```

## Testing

### Unit Tests
```bash
# Test individual calculators
php artisan test --filter=MovingPriceCalculatorTest
php artisan test --filter=DeclutterPriceCalculatorTest
php artisan test --filter=CleaningPriceCalculatorTest
```

### Integration Tests
```bash
# Test full pricing service
php artisan test --filter=PricingServiceTest
```

## Customization

### Adding New Services
1. Create new calculator class implementing `PriceCalculatorInterface`
2. Add to `PricingService::getCalculatorForService()`
3. Register in `CalculatorServiceProvider`
4. Add settings to `SettingsSeeder`

### Adding New Pricing Rules
1. Add settings to database
2. Update relevant calculator class
3. Add to admin interface

### Custom Distance Calculation
Replace `DistanceCalculator` with Google Maps API integration:
```php
class GoogleMapsDistanceCalculator extends DistanceCalculator
{
    public function calculateDistance(string $from, string $to): int
    {
        // Implement Google Maps Distance Matrix API
    }
}
```

## Troubleshooting

### Calculator Not Working
1. Check if enabled: `Setting::getValue('calculator_enabled')`
2. Verify settings are seeded: `php artisan db:seed --class=SettingsSeeder`
3. Clear cache: `php artisan cache:clear`

### Wrong Prices
1. Check settings in admin panel
2. Verify calculator logic in relevant class
3. Check logs: `storage/logs/laravel.log`

### Admin Panel Issues
1. Ensure routes are loaded: `php artisan route:list | grep admin`
2. Check permissions on storage directory
3. Verify CSRF token in forms

## Performance

### Caching
- Settings are cached for 1 hour
- Clear cache after updates: `Cache::forget("setting.{$key}")`

### Database Optimization
- Settings table has indexes on `group` and `is_public`
- Consider Redis for high-traffic sites

## Security

### Admin Access
- Add authentication middleware in production
- Restrict admin routes to authorized users
- Use HTTPS for admin panel

### Input Validation
- All calculators validate input data
- API requests are validated via Form Requests
- Sanitize user input before processing

## Migration from Old System

### Backup First
```bash
# Backup database
mysqldump -u user -p database > backup.sql

# Backup old PricingService
cp app/Services/PricingService.php app/Services/PricingService.php.backup
```

### Migration Steps
1. Run `php artisan calculator:setup`
2. Configure settings via admin panel
3. Test calculations match old system
4. Update frontend API calls if needed
5. Deploy and monitor

## Support

### Logs
- Calculator operations: `storage/logs/laravel.log`
- Settings changes: Logged automatically
- API requests: Laravel request logs

### Debugging
```php
// Enable debug mode in .env
APP_DEBUG=true

// Check specific setting
Setting::getValue('base_price_per_sqm');

// Test calculator directly
$calculator = app(MovingPriceCalculator::class);
$result = $calculator->calculate($data);
```

---

**The refactored system is now production-ready with proper separation of concerns, comprehensive configuration options, and a user-friendly admin interface.**