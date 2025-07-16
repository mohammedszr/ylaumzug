# Laravel Pricing Calculation Engine Documentation

## Overview

The Laravel pricing calculation engine is a sophisticated system that handles complex pricing calculations for all YLA Umzug services. It supports multiple service types, combination discounts, and database-driven pricing rules.

## Features

### 1. Multi-Service Support
- **Umzug (Moving)**: Distance-based pricing with floor surcharges
- **Entrümpelung (Decluttering)**: Volume-based pricing with waste type surcharges  
- **Putzservice (Cleaning)**: Area-based pricing with room-specific costs

### 2. Complex Pricing Logic

#### Moving Services (Umzug)
- **Distance Calculation**: Enhanced German postal code-based distance estimation
- **Base Pricing**: Apartment size-based calculations
- **Floor Surcharges**: Additional costs for stairs without elevator access
- **Additional Services**: Packing, assembly, storage, disposal options

#### Decluttering Services (Entrümpelung)
- **Volume-Based Pricing**: Low, medium, high, extreme volume categories
- **Object Type Pricing**: Specific costs for furniture, appliances, books, etc.
- **Waste Type Surcharges**: Hazardous waste, electronics, furniture disposal
- **Access Difficulty**: Surcharges for difficult access locations
- **Floor Surcharges**: Stair access penalties

#### Cleaning Services (Putzservice)
- **Area-Based Pricing**: Cost per m² based on cleaning intensity
- **Intensity Levels**: Normal, deep, construction, move-out cleaning
- **Room-Specific Costs**: Windows, kitchen, bathroom, balcony, basement
- **Frequency Discounts**: Regular cleaning service discounts

### 3. Combination Pricing
- **2-Service Discount**: 10% discount for combining two services
- **3+ Service Discount**: 15% discount for three or more services
- **Special Bonuses**: Additional discounts for specific service combinations
  - Moving + Cleaning bonus
  - Decluttering + Cleaning bonus

### 4. Additional Features
- **Express Service Surcharge**: 20% surcharge for urgent requests
- **Minimum Order Value**: Configurable minimum pricing
- **Database-Driven Rules**: All pricing rules stored in database for easy management

## API Endpoints

### Calculate Pricing
```
POST /api/calculator/calculate
```

**Request Body:**
```json
{
  "selectedServices": ["umzug", "putzservice"],
  "movingDetails": {
    "apartmentSize": 80,
    "fromAddress": {"postalCode": "10115"},
    "toAddress": {"postalCode": "10117"},
    "fromFloor": 3,
    "toFloor": 2,
    "fromElevator": "no",
    "toElevator": "yes",
    "boxes": 20,
    "additionalServices": ["packing"]
  },
  "cleaningDetails": {
    "size": 80,
    "cleaningIntensity": "deep",
    "rooms": ["windows", "kitchen"],
    "frequency": "once"
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
    "total": 1250.50,
    "breakdown": [
      {
        "service": "Umzug",
        "cost": 800.00,
        "details": [
          "Grundpreis (80m²): 640€",
          "Entfernung (15km): 30€",
          "Kartons (20 Stück): 60€",
          "Treppenaufschlag Auszug: 50€",
          "Möbelabbau & Aufbau: 200€"
        ]
      },
      {
        "service": "Putzservice", 
        "cost": 580.00,
        "details": [
          "Tiefenreinigung (80m²): 400€",
          "Fensterreinigung: 160€",
          "Küchen-Tiefenreinigung: 80€"
        ]
      },
      {
        "service": "Kombinationsrabatt",
        "cost": -138.00,
        "details": ["Kombinationsrabatt (2 Services) + Umzug-Reinigung Bonus"]
      }
    ],
    "currency": "EUR",
    "calculation_date": "2024-01-15T10:30:00.000000Z"
  },
  "currency": "EUR",
  "disclaimer": "Dies ist eine unverbindliche Schätzung. Das finale Angebot erhalten Sie nach unserer Besichtigung vor Ort."
}
```

### Get Available Services
```
GET /api/calculator/services
```

**Response:**
```json
{
  "success": true,
  "services": [
    {
      "id": "umzug",
      "name": "Umzug",
      "description": "Professioneller Umzugsservice mit Verpackung und Transport",
      "base_price": 300,
      "additional_services": [
        {
          "id": "packing",
          "name": "Verpackungsservice",
          "description": "Professionelle Verpackung Ihrer Gegenstände",
          "price": 150.00,
          "price_type": "fixed",
          "unit": "service"
        }
      ]
    }
  ]
}
```

## Database Structure

### Services Table
- `key`: Service identifier (umzug, entruempelung, putzservice)
- `name`: Display name
- `description`: Service description
- `base_price`: Base pricing
- `is_active`: Enable/disable service
- `configuration`: JSON configuration options

### Pricing Rules Table
- `service_id`: Related service
- `rule_type`: Type of pricing rule
- `rule_key`: Data field to evaluate
- `condition_operator`: Comparison operator (>, <, =, between, in)
- `condition_values`: Values for comparison
- `price_value`: Price amount
- `price_type`: fixed, multiplier, per_unit
- `priority`: Rule evaluation order

### Settings Table
- `key`: Setting identifier
- `value`: Setting value
- `type`: Data type (string, integer, decimal, boolean, json)
- `group`: Setting group for organization
- `is_public`: Whether setting is available to frontend

## Configuration

All pricing parameters are configurable through the Settings model:

```php
// Distance pricing
Setting::setValue('distance_rate_per_km', 2.0);

// Floor surcharges
Setting::setValue('floor_surcharge_rate', 50.0);
Setting::setValue('declutter_floor_rate', 30.0);

// Combination discounts
Setting::setValue('combination_discount_2_services', 0.10);
Setting::setValue('combination_discount_3_services', 0.15);

// Volume pricing for decluttering
Setting::setValue('declutter_volume_high', 1200);

// Cleaning rates
Setting::setValue('cleaning_rate_deep', 5.0);
Setting::setValue('window_cleaning_rate', 2.0);
```

## Testing

The pricing engine includes comprehensive tests covering:
- Individual service calculations
- Combination pricing
- Express surcharges
- Input validation
- API endpoint functionality

Run tests with:
```bash
php artisan test --filter=CalculatorTest
```

## Error Handling

The system includes robust error handling:
- Input validation with German error messages
- Graceful fallbacks for missing data
- Detailed logging for debugging
- User-friendly error responses

## Performance Considerations

- Settings are cached for 1 hour to reduce database queries
- Pricing rules are loaded efficiently with proper indexing
- Complex calculations are optimized for performance
- Database queries are minimized through eager loading

## Future Enhancements

- Google Maps API integration for accurate distance calculation
- Machine learning-based pricing optimization
- Real-time pricing adjustments based on demand
- Advanced reporting and analytics
- Multi-currency support