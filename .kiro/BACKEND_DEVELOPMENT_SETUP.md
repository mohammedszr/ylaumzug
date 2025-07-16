# 🚀 Backend Development Setup Guide

## 📋 **Quick Start: Run Backend Development Environment**

### **Step 1: Navigate to Backend Directory**
```bash
cd backend
```

### **Step 2: Install Dependencies**
```bash
composer install
```

### **Step 3: Environment Setup**
```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

### **Step 4: Database Configuration**
Edit `.env` file with your database settings:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=yla_umzug
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

### **Step 5: Run Database Migrations**
```bash
# Create database tables
php artisan migrate

# Seed with sample data
php artisan db:seed
```

### **Step 6: Start Development Server**
```bash
# Start Laravel development server
php artisan serve

# Backend will be available at: http://localhost:8000
```

---

## 🔧 **Backend Features Ready**

### **✅ API Endpoints Available**
```
Calculator API:
├── POST /api/calculate - Calculate pricing for services
├── GET /api/services - Get available services
└── GET /api/pricing-rules - Get pricing configuration

Quote Management:
├── POST /api/quotes - Submit quote request
├── GET /api/quotes - List all quotes (admin)
└── PUT /api/quotes/{id} - Update quote status

Admin Functions:
├── GET /admin/test-email - Test email configuration
├── POST /admin/send-test - Send test email
└── GET /admin/quotes - Quote management interface
```

### **✅ Database Structure**
```sql
Tables Created:
├── services (Umzug, Entrümpelung, Hausreinigung)
├── pricing_rules (Dynamic pricing configuration)
├── quote_requests (Customer inquiries)
└── settings (Application configuration)
```

### **✅ Email System**
```
Email Templates:
├── Quote Request (to business owner)
├── Quote Confirmation (to customer)
└── Admin Notifications
```

---

## 🧪 **Testing the Backend**

### **Test Calculator API**
```bash
# Test pricing calculation
curl -X POST http://localhost:8000/api/calculate \
  -H "Content-Type: application/json" \
  -d '{
    "services": ["umzug"],
    "from_address": "Saarbrücken",
    "to_address": "Kaiserslautern",
    "apartment_size": "3_zimmer",
    "furniture_amount": "normal"
  }'
```

### **Test Quote Submission**
```bash
# Test quote request
curl -X POST http://localhost:8000/api/quotes \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Max Mustermann",
    "email": "test@example.com",
    "phone": "+49 123 456789",
    "services": ["entrümpelung"],
    "message": "Benötige Kostenvoranschlag für 3-Zimmer Wohnung"
  }'
```

### **Test Email System**
Visit: `http://localhost:8000/admin/test-email`

---

## 📊 **Backend Monitoring**

### **Check System Status**
```bash
# Check Laravel version
php artisan --version

# Check database connection
php artisan migrate:status

# Check email configuration
php artisan config:show mail

# View application logs
tail -f storage/logs/laravel.log
```

### **Performance Monitoring**
```bash
# Check queue status (if using queues)
php artisan queue:work

# Monitor database queries
php artisan telescope:install (optional)
```

---

## 🔐 **Security Configuration**

### **Production Security Checklist**
```env
# .env Production Settings
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yla-umzug.de

# Email Configuration (Production)
MAIL_MAILER=smtp
MAIL_HOST=your-smtp-server
MAIL_PORT=587
MAIL_USERNAME=info@yla-umzug.de
MAIL_PASSWORD=your-email-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=info@yla-umzug.de
MAIL_FROM_NAME="YLA Umzug"
```

### **CORS Configuration**
File: `config/cors.php`
```php
'allowed_origins' => [
    'https://yla-umzug.de',
    'http://localhost:5173', // Vite dev server
],
```

---

## 📱 **Frontend-Backend Integration**

### **API Configuration**
File: `src/lib/api.js` is already configured for:
```javascript
const API_BASE_URL = import.meta.env.VITE_API_URL || 'http://localhost:8000/api';

Available Functions:
├── calculatePrice() - Calculator integration
├── submitQuote() - Quote form submission
├── getServices() - Service data
└── getPricingRules() - Dynamic pricing
```

### **Environment Variables**
Create `.env` in frontend root:
```env
VITE_API_URL=http://localhost:8000/api
VITE_WHATSAPP_NUMBER=4915750693353
```

---

## 🚀 **Deployment Ready**

### **Production Deployment Steps**
1. **Server Requirements**: PHP 8.1+, MySQL 8.0+, Composer
2. **Environment Setup**: Configure production `.env`
3. **Database Migration**: Run `php artisan migrate --force`
4. **Optimization**: Run `php artisan optimize`
5. **Queue Workers**: Set up `php artisan queue:work` (if using queues)
6. **Cron Jobs**: Add Laravel scheduler to crontab

### **Recommended Server Stack**
- **Web Server**: Nginx or Apache
- **PHP**: 8.1+ with required extensions
- **Database**: MySQL 8.0+ or PostgreSQL
- **Process Manager**: Supervisor (for queue workers)
- **SSL**: Let's Encrypt certificate

---

## 📞 **Support & Maintenance**

### **Common Issues & Solutions**
```bash
# Clear application cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear

# Fix file permissions
chmod -R 755 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

# Database connection issues
php artisan config:cache
php artisan migrate:status
```

### **Backup Strategy**
```bash
# Database backup
mysqldump -u username -p yla_umzug > backup_$(date +%Y%m%d).sql

# Application backup
tar -czf app_backup_$(date +%Y%m%d).tar.gz /path/to/application
```

---

## 🎯 **Development Workflow**

### **Local Development**
1. Start backend: `php artisan serve`
2. Start frontend: `npm run dev`
3. Test integration: Visit `http://localhost:5173`
4. Monitor logs: `tail -f storage/logs/laravel.log`

### **Code Quality**
```bash
# Run tests
php artisan test

# Code formatting
./vendor/bin/pint

# Static analysis
./vendor/bin/phpstan analyse
```

**🚀 Backend is ready for development and production deployment!**