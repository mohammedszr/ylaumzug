# Umzugs und Hausleistungen Rechner YLA Umzug - Developer Documentation

## 📋 Table of Contents

1. [Project Overview](#project-overview)
2. [Architecture](#architecture)
3. [Development Setup](#development-setup)
4. [Testing](#testing)
5. [Deployment](#deployment)
6. [Code Structure](#code-structure)
7. [API Documentation](#api-documentation)
8. [Troubleshooting](#troubleshooting)
9. [Best Practices](#best-practices)

## 🎯 Project Overview

Umzugs und Hausleistungen Rechner YLA Umzug is a German moving services website with an integrated cost calculator. The system helps customers estimate costs for moving, cleaning, and decluttering services, then submit quote requests directly to the business.

### Key Features

- **Multi-Service Calculator**: Calculate costs for Umzug (moving), Putzservice (cleaning), and Entrümpelung (decluttering)
- **Quote Management**: Submit and manage customer quote requests
- **Admin Panel**: Manage pricing, services, and content through Filament Admin Panel
- **Mobile-First Design**: Responsive design optimized for mobile devices
- **German Localization**: All content and UI in German

### Technology Stack

**Frontend:**
- React 18 with Vite
- Tailwind CSS for styling
- React Router for navigation
- Framer Motion for animations

**Backend:**
- Laravel 10 (PHP 8.2)
- MySQL database
- Redis for caching
- Laravel Sanctum for API authentication

**Content Management:**
- Filament Admin Panel with SQLite/MySQL
- Professional admin interface for quote and settings management

**Infrastructure:**
- Docker & Docker Compose
- Nginx reverse proxy
- SSL/TLS encryption

## 🏗️ Architecture

### System Architecture

```
┌─────────────────┐    ┌─────────────────┐
│   React Frontend │    │  Laravel Backend │
│   (Port 3000)   │◄──►│   (Port 8000)   │
└─────────────────┘    └─────────────────┘
                       │   Filament Admin │
                       │   (Port 8000)    │
         │                       │                       │
         │                       │                       │
         ▼                       ▼                       ▼
┌─────────────────┐    ┌─────────────────┐    ┌─────────────────┐
│      Nginx      │    │      MySQL      │    │     MongoDB     │
│   (Port 80/443) │    │   (Port 3306)   │    │  (Port 27017)   │
└─────────────────┘    └─────────────────┘    └─────────────────┘
```

### Data Flow

1. **User Interaction**: User interacts with React frontend
2. **API Calls**: Frontend makes API calls to Laravel backend
3. **Business Logic**: Laravel processes requests and calculates pricing
4. **Database Operations**: Data stored in SQLite/MySQL (quotes, settings, users)
5. **Email Notifications**: Laravel sends emails for quote requests with queue support
6. **Admin Management**: Quote and settings management through Filament Admin Panel

## 🚀 Development Setup

### Prerequisites

- Node.js 18+ and npm
- PHP 8.2+ and Composer
- Docker and Docker Compose
- Git

### Quick Start

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd yla-umzug
   ```

2. **Start development environment**
   ```bash
   # Start all services
   docker-compose up -d
   
   # Or start individual services
   npm run dev          # Frontend (port 3000)
   cd backend && php artisan serve  # Backend (port 8000)
   cd payload && npm run dev        # Payload CMS (port 3001)
   ```

3. **Set up backend**
   ```bash
   cd backend
   composer install
   cp .env.example .env
   php artisan key:generate
   php artisan migrate --seed
   ```

4. **Set up frontend**
   ```bash
   npm install
   npm run dev
   ```

5. **Set up Payload CMS**
   ```bash
   cd payload
   npm install
   npm run dev
   ```

### Environment Configuration

**Backend (.env)**
```env
APP_NAME="YLA Umzug"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=yla_umzug
DB_USERNAME=root
DB_PASSWORD=

MAIL_MAILER=smtp
MAIL_HOST=mailpit
MAIL_PORT=1025
MAIL_FROM_ADDRESS="info@yla-umzug.de"

CALCULATOR_ENABLED=true
```

**Frontend (vite.config.js)**
```javascript
export default defineConfig({
  plugins: [react()],
  server: {
    port: 3000,
    proxy: {
      '/api': 'http://localhost:8000'
    }
  }
})
```

## 🧪 Testing

### Running Tests

**All Tests**
```bash
./scripts/test-runner.sh all
```

**Frontend Tests Only**
```bash
npm test
# or
./scripts/test-runner.sh frontend
```

**Backend Tests Only**
```bash
cd backend
php artisan test
# or
./scripts/test-runner.sh backend
```

**Integration Tests**
```bash
./scripts/test-runner.sh integration
```

### Test Structure

**Frontend Tests** (`src/test/`)
- `Calculator.test.jsx` - Calculator component tests
- `api.test.js` - API function tests
- `UserFlow.test.jsx` - End-to-end user flow tests

**Backend Tests** (`backend/tests/`)
- `Feature/CalculatorTest.php` - Calculator API tests
- `Feature/EmailTest.php` - Email system tests
- `Feature/UserFlowTest.php` - Complete user flow tests
- `Unit/PricingServiceTest.php` - Pricing logic tests

### Writing Tests

**Frontend Test Example**
```javascript
import { describe, it, expect, vi } from 'vitest'
import { render, screen } from '@testing-library/react'
import Calculator from '../components/calculator/Calculator'

describe('Calculator Component', () => {
  it('renders service selection', () => {
    render(<Calculator />)
    expect(screen.getByText('Service wählen')).toBeInTheDocument()
  })
})
```

**Backend Test Example**
```php
<?php
class CalculatorTest extends TestCase
{
    /** @test */
    public function it_calculates_moving_service_pricing()
    {
        $response = $this->postJson('/api/calculator/calculate', [
            'selectedServices' => ['umzug'],
            'movingDetails' => ['apartmentSize' => 80]
        ]);

        $response->assertStatus(200)
                ->assertJson(['success' => true]);
    }
}
```

## 🚢 Deployment

### Staging Deployment

```bash
./scripts/deploy-staging.sh
```

This will:
- Build Docker images
- Start staging environment
- Run database migrations
- Execute tests
- Perform health checks

**Staging URLs:**
- Frontend: http://localhost:3000
- Backend: http://localhost:8001
- Payload CMS: http://localhost:3002

### Production Deployment

```bash
./scripts/deploy-production.sh
```

**⚠️ Production deployment requires:**
- SSL certificates in `nginx/ssl/`
- Production environment variables
- Database backups
- Confirmation prompts

### Deployment Commands

```bash
# Deploy to staging
./scripts/deploy-staging.sh deploy

# Check deployment status
./scripts/deploy-staging.sh status

# View logs
./scripts/deploy-staging.sh logs

# Rollback if needed
./scripts/deploy-staging.sh rollback

# Stop environment
./scripts/deploy-staging.sh stop
```

## 📁 Code Structure

### Frontend Structure

```
src/
├── components/
│   ├── calculator/          # Calculator components
│   │   ├── Calculator.jsx   # Main calculator
│   │   ├── ServiceSelection.jsx
│   │   ├── MovingDetails.jsx
│   │   ├── CleaningDetails.jsx
│   │   ├── DeclutterDetails.jsx
│   │   ├── PriceSummary.jsx
│   │   └── GeneralInfo.jsx
│   └── ui/                  # Reusable UI components
│       ├── card.jsx
│       ├── toaster.jsx
│       └── cookie-banner.jsx
├── pages/                   # Page components
│   ├── HomePage.jsx
│   ├── CalculatorPage.jsx
│   ├── ContactPage.jsx
│   └── ...
├── lib/
│   └── api.js              # API communication
└── test/                   # Test files
    ├── Calculator.test.jsx
    ├── api.test.js
    └── UserFlow.test.jsx
```

### Backend Structure

```
backend/
├── app/
│   ├── Http/Controllers/
│   │   ├── CalculatorController.php
│   │   ├── QuoteController.php
│   │   └── Admin/SettingsController.php
│   ├── Models/
│   │   ├── Service.php
│   │   ├── QuoteRequest.php
│   │   └── Setting.php
│   ├── Services/
│   │   ├── PricingService.php
│   │   ├── EmailNotificationService.php
│   │   └── Calculators/
│   │       ├── MovingPriceCalculator.php
│   │       ├── CleaningPriceCalculator.php
│   │       └── DeclutterPriceCalculator.php
│   └── Mail/
│       ├── QuoteRequestMail.php
│       └── QuoteConfirmationMail.php
├── database/
│   ├── migrations/
│   └── seeders/
├── tests/
│   ├── Feature/
│   └── Unit/
└── resources/
    └── views/
        └── emails/
```

## 📚 API Documentation

### Calculator Endpoints

**Get Available Services**
```http
GET /api/calculator/services
```

Response:
```json
{
  "success": true,
  "services": [
    {
      "key": "umzug",
      "name": "Umzug",
      "description": "Professioneller Umzugsservice",
      "base_price": 300.00
    }
  ]
}
```

**Calculate Price**
```http
POST /api/calculator/calculate
Content-Type: application/json

{
  "selectedServices": ["umzug"],
  "movingDetails": {
    "apartmentSize": 80,
    "fromAddress": {"postalCode": "10115"},
    "toAddress": {"postalCode": "10117"}
  }
}
```

Response:
```json
{
  "success": true,
  "pricing": {
    "total": 450.00,
    "breakdown": [
      {"service": "Umzug Grundpreis", "price": 300.00},
      {"service": "Entfernungszuschlag", "price": 150.00}
    ],
    "currency": "EUR"
  }
}
```

### Quote Endpoints

**Submit Quote Request**
```http
POST /api/quotes
Content-Type: application/json

{
  "name": "Max Mustermann",
  "email": "max@example.com",
  "phone": "+49 123 456789",
  "selectedServices": ["umzug"],
  "serviceDetails": {...},
  "estimatedTotal": 450.00
}
```

## 🔧 Troubleshooting

### Common Issues

**Frontend not loading**
```bash
# Check if Vite dev server is running
npm run dev

# Clear node_modules and reinstall
rm -rf node_modules package-lock.json
npm install
```

**Backend API errors**
```bash
# Check Laravel logs
cd backend
tail -f storage/logs/laravel.log

# Clear Laravel cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear
```

**Database connection issues**
```bash
# Check database connection
cd backend
php artisan tinker
>>> DB::connection()->getPdo();

# Run migrations
php artisan migrate:fresh --seed
```

**Docker issues**
```bash
# Restart all containers
docker-compose down
docker-compose up -d --build

# Check container logs
docker-compose logs -f [service-name]

# Clean up Docker
docker system prune -a
```

### Debug Mode

**Enable Laravel debugging**
```env
APP_DEBUG=true
LOG_LEVEL=debug
```

**Enable frontend debugging**
```javascript
// In vite.config.js
export default defineConfig({
  define: {
    __DEV__: true
  }
})
```

## ✅ Best Practices

### Code Style

**Frontend (JavaScript/React)**
- Use functional components with hooks
- Follow React naming conventions
- Use TypeScript for type safety (when applicable)
- Keep components small and focused
- Use proper error boundaries

**Backend (PHP/Laravel)**
- Follow PSR-12 coding standards
- Use Laravel conventions (Eloquent, Artisan, etc.)
- Implement proper validation
- Use service classes for business logic
- Write comprehensive tests

### Security

- Validate all user inputs
- Use CSRF protection
- Implement proper authentication
- Sanitize database queries
- Use HTTPS in production
- Keep dependencies updated

### Performance

- Optimize images and assets
- Use Laravel caching
- Implement database indexing
- Minimize API calls
- Use lazy loading where appropriate
- Monitor application performance

### Git Workflow

```bash
# Create feature branch
git checkout -b feature/new-feature

# Make changes and commit
git add .
git commit -m "feat: add new feature"

# Push and create pull request
git push origin feature/new-feature
```

### Commit Messages

Use conventional commits:
- `feat:` - New features
- `fix:` - Bug fixes
- `docs:` - Documentation changes
- `style:` - Code style changes
- `refactor:` - Code refactoring
- `test:` - Test additions/changes
- `chore:` - Maintenance tasks

## 📞 Support

For questions or issues:

1. Check this documentation
2. Review existing tests for examples
3. Check Laravel and React documentation
4. Create an issue in the project repository

## 📝 Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Write/update tests
5. Update documentation
6. Submit a pull request

---

**Last Updated:** $(date)
**Version:** 1.0.0