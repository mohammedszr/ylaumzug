# Umzugs und Hausleistungen Rechner YLA Umzug - Laravel Backend

## Overview

This Laravel backend provides a clean, modular pricing calculator system with an admin interface for the Umzugs und Hausleistungen Rechner YLA Umzug website.

### Key Features
- **Modular Calculator System**: Separate calculators for Moving, Decluttering, and Cleaning services
- **Admin Interface**: Web-based settings management at `/admin/settings`
- **Database-Driven Configuration**: All pricing stored in database settings
- **Email & PDF System**: Quote processing with email notifications and PDF generation

## Quick Setup

### Requirements
- PHP 8.1+
- Composer
- MySQL/PostgreSQL
- Laravel 10.x

### Installation

```bash
# Install dependencies
composer install

# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Configure database in .env file
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=yla_umzug
# DB_USERNAME=your_username
# DB_PASSWORD=your_password

# Run automated setup (migrations + seeders)
php artisan calculator:setup

# Start development server
php artisan serve
```

## Configuration

### Admin Interface
Visit `/admin/settings` to configure:
- Business information
- Service areas (postal codes)
- Pricing for all services
- Discounts and surcharges
- Toggle calculator on/off

### API Endpoints
- `POST /api/calculator/calculate` - Calculate pricing
- `POST /api/quotes/submit` - Submit quote request
- `GET /api/calculator/enabled` - Check calculator status
- `GET /api/settings/public` - Get public settings

## Architecture

### Calculator System
- `MovingPriceCalculator` - Distance, floors, additional services
- `DeclutterPriceCalculator` - Volume, waste types, access difficulty
- `CleaningPriceCalculator` - Area-based, room surcharges, frequency discounts
- `DiscountCalculator` - Combination discounts, express surcharges
- `DistanceCalculator` - German postal code distance estimation

### Database Tables
- `services` - Service definitions
- `settings` - All pricing configuration
- `quote_requests` - Customer quote submissions

## Documentation

For detailed information, see:
- `REFACTORED_CALCULATOR_GUIDE.md` - Complete system guide
- `EMAIL_CONFIGURATION_GUIDE.md` - Email setup
- `PDF_QUOTE_DOCUMENTATION.md` - PDF generation
- `QUOTE_PROCESSING_GUIDE.md` - Quote management

## Development

The system is designed to be maintainable by junior developers with clear separation of concerns and comprehensive documentation.