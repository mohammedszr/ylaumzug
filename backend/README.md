# Laravel Backend for YLA Umzug Calculator

## Setup Instructions

This Laravel backend handles the calculator API, quote processing, and admin panel for the YLA Umzug website.

### Requirements
- PHP 8.1+
- Composer
- MySQL/PostgreSQL
- Laravel 10.x

### Installation

1. Install PHP and Composer on your system
2. Run the following commands:

```bash
# Install Laravel dependencies
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

# Run database migrations
php artisan migrate

# Start development server
php artisan serve
```

### API Endpoints

- `POST /api/calculator/calculate` - Calculate pricing for services
- `POST /api/quotes/submit` - Submit quote request
- `GET /api/settings/calculator-enabled` - Check if calculator is enabled

### Admin Panel

Access the admin panel at `/admin` after setting up authentication.

## Development Notes

This backend is designed to be simple and maintainable by junior developers, following Laravel conventions and best practices.