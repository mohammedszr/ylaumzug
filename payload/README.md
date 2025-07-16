# Payload CMS Setup Guide

## Overview
Payload CMS serves as the content management system and admin panel for the moving services website. It handles quote management, service configuration, content management, and site settings.

## Quick Start

### 1. Install Dependencies
```bash
cd payload
npm install
```

### 2. Set up Environment
```bash
cp .env.example .env
```

Edit `.env` with your MongoDB connection:
```
DATABASE_URI=mongodb://localhost:27017/umzug-cms
PAYLOAD_SECRET=your-very-secure-secret-key-here
NODE_ENV=development
```

### 3. Start MongoDB
Make sure MongoDB is running on your system:
```bash
# Windows (if using MongoDB service)
net start MongoDB

# macOS (if using Homebrew)
brew services start mongodb-community

# Linux
sudo systemctl start mongod
```

### 4. Run Payload CMS
```bash
npm run dev
```

The admin panel will be available at: http://localhost:3001/admin

### 5. Create Admin User
On first visit, you'll be prompted to create an admin user account.

## Features

### Quote Management
- **Dashboard**: View all quote requests with status tracking
- **Status Workflow**: New → Processing → Quoted → Completed → Rejected
- **Customer Details**: Full customer information and service requirements
- **Price Breakdown**: Detailed pricing calculations from calculator
- **Admin Notes**: Internal notes not visible to customers

### Service Management
- **Service Configuration**: Add/edit Umzug, Entrümpelung, Putzservice
- **Pricing Control**: Set base prices, per-km rates, hourly rates
- **Service Options**: Manage additional service options and pricing
- **Active/Inactive**: Enable/disable services without code changes

### Content Management
- **Website Pages**: Manage all website content and SEO settings
- **Email Templates**: Store and edit email templates with variables
- **Legal Content**: Manage AGB, Datenschutz, Impressum
- **Site Settings**: Company info, contact details, calculator toggle

### Site Settings
- **Calculator Toggle**: Enable/disable calculator site-wide
- **Maintenance Mode**: Put site in maintenance mode
- **Contact Information**: Company details and contact info
- **Cookie Banner**: GDPR-compliant cookie management settings

## API Integration

The React frontend automatically integrates with Payload CMS:

### Services API
```javascript
// Get active services
const services = await payloadAPI.getServices();

// Get specific service
const service = await payloadAPI.getService('umzug');
```

### Settings API
```javascript
// Get site settings
const settings = await payloadAPI.getSiteSettings();

// Check if calculator is enabled
const isEnabled = settings.calculatorEnabled;
```

### Quote Management
```javascript
// Create quote request
const quote = await payloadAPI.createQuoteRequest({
  email: 'customer@example.com',
  services: [...],
  totalPrice: 500,
  status: 'new'
});
```

## Collections Overview

### Users
- Admin user management
- Role-based access (Admin, Editor)

### Quote Requests
- Customer quote submissions
- Status tracking and workflow
- Price calculations and breakdowns
- Admin notes and customer communication

### Services
- Service definitions (Umzug, Entrümpelung, Putzservice)
- Pricing configuration
- Service options and add-ons
- Active/inactive status

### Pages
- Website content management
- SEO optimization
- Published/draft status

### Email Templates
- Template management with variables
- HTML and text versions
- Template variables documentation

## Global Settings

### Site Settings
- Calculator enable/disable
- Maintenance mode
- Company information
- Cookie banner configuration

### Legal Content
- AGB (Terms & Conditions)
- Datenschutzerklärung (Privacy Policy)
- Impressum (Legal Notice)

## Development

### Adding New Collections
1. Edit `src/payload.config.ts`
2. Add new collection configuration
3. Restart the development server
4. New collection appears in admin panel

### Custom Fields
Payload supports various field types:
- Text, Email, Number
- Rich Text (WYSIWYG)
- JSON (for complex data)
- Arrays and Groups
- Relationships between collections

### API Endpoints
All collections are automatically available via REST API:
- GET `/api/services` - List services
- POST `/api/quote-requests` - Create quote
- GET `/api/globals/site-settings` - Get settings

## Production Deployment

### Environment Variables
```
DATABASE_URI=mongodb://your-production-db/umzug-cms
PAYLOAD_SECRET=your-production-secret
NODE_ENV=production
```

### Build and Deploy
```bash
npm run build
npm run serve
```

## Troubleshooting

### MongoDB Connection Issues
- Ensure MongoDB is running
- Check connection string in `.env`
- Verify database permissions

### Admin Panel Not Loading
- Check console for JavaScript errors
- Verify all dependencies are installed
- Restart development server

### API Integration Issues
- Check CORS settings if frontend can't connect
- Verify API endpoints are responding
- Check network tab in browser dev tools

## Support

For Payload CMS documentation: https://payloadcms.com/docs
For MongoDB setup: https://docs.mongodb.com/manual/installation/