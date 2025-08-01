# YLA Umzug - Content Management Guide

## 📋 Table of Contents

1. [Overview](#overview)
2. [Accessing the Admin Panel](#accessing-the-admin-panel)
3. [Managing Services](#managing-services)
4. [Pricing Configuration](#pricing-configuration)
5. [Quote Management](#quote-management)
6. [Content Pages](#content-pages)
7. [Email Templates](#email-templates)
8. [Calculator Settings](#calculator-settings)
9. [SEO Management](#seo-management)
10. [Troubleshooting](#troubleshooting)

## 🎯 Overview

The YLA Umzug content management system uses **Payload CMS** for easy content management and **Laravel Admin** for business settings. This guide will help you manage all aspects of your website content without needing technical knowledge.

### What You Can Manage

- **Services**: Add, edit, or remove services (Umzug, Putzservice, Entrümpelung)
- **Pricing**: Update pricing rules and calculator settings
- **Quotes**: Review and manage customer quote requests
- **Content**: Edit website pages, blog posts, and legal content
- **Email Templates**: Customize email notifications
- **SEO Settings**: Manage meta tags, descriptions, and structured data

## 🔐 Accessing the Admin Panel

### Payload CMS Admin (Content Management)

1. **URL**: `http://localhost:3001/admin` (development) or `https://yourdomain.com/admin` (production)
2. **Login**: Use your admin credentials
3. **Dashboard**: Overview of all content and recent activity

### Laravel Admin (Business Settings)

1. **URL**: `http://localhost:8000/admin` (development) or `https://yourdomain.com/api/admin` (production)
2. **Login**: Use your admin credentials
3. **Settings**: Manage calculator settings and pricing

## 🛠️ Managing Services

### Adding a New Service

1. Go to **Payload Admin** → **Services**
2. Click **"Create New"**
3. Fill in the required fields:
   - **Name**: Service name (e.g., "Umzug")
   - **Key**: Unique identifier (e.g., "umzug")
   - **Description**: Brief description
   - **Base Price**: Starting price in EUR
   - **Is Active**: Enable/disable the service
   - **Sort Order**: Display order on website

4. Click **"Save"**

### Editing Existing Services

1. Go to **Services** in Payload Admin
2. Click on the service you want to edit
3. Make your changes
4. Click **"Save"**

### Service Configuration Examples

**Moving Service (Umzug)**
```
Name: Umzug
Key: umzug
Description: Professioneller Umzugsservice mit erfahrenem Team
Base Price: 300.00
Is Active: ✓
Sort Order: 1
```

**Cleaning Service (Putzservice)**
```
Name: Putzservice
Key: putzservice
Description: Grundreinigung und besenreine Übergabe
Base Price: 150.00
Is Active: ✓
Sort Order: 2
```

**Decluttering Service (Entrümpelung)**
```
Name: Entrümpelung
Key: entruempelung
Description: Haushaltsauflösung und fachgerechte Entsorgung
Base Price: 300.00
Is Active: ✓
Sort Order: 3
```

## 💰 Pricing Configuration

### Accessing Pricing Settings

1. Go to **Laravel Admin** → **Settings**
2. Find the **Calculator Settings** section
3. Edit pricing values as needed

### Key Pricing Settings

**Basic Pricing**
- `minimum_order_value`: Minimum order amount (default: 150 EUR)
- `distance_rate_per_km`: Cost per kilometer for moving (default: 2.00 EUR)
- `floor_surcharge_rate`: Additional cost per floor without elevator (default: 50.00 EUR)

**Discounts**
- `combination_discount_2_services`: Discount for 2 services (default: 10%)
- `combination_discount_3_services`: Discount for 3 services (default: 15%)
- `regular_cleaning_discount`: Discount for regular cleaning (default: 15%)

**Surcharges**
- `express_surcharge`: Express service surcharge (default: 20%)
- `hazardous_waste_surcharge`: Hazardous waste disposal (default: 150.00 EUR)
- `access_difficulty_surcharge`: Difficult access surcharge (default: 100.00 EUR)

**Cleaning Rates**
- `cleaning_rate_normal`: Normal cleaning per m² (default: 3.00 EUR)
- `cleaning_rate_deep`: Deep cleaning per m² (default: 5.00 EUR)
- `cleaning_rate_construction`: Construction cleaning per m² (default: 7.00 EUR)

**Decluttering Volumes**
- `declutter_volume_low`: Low volume base price (default: 300 EUR)
- `declutter_volume_medium`: Medium volume base price (default: 600 EUR)
- `declutter_volume_high`: High volume base price (default: 1200 EUR)
- `declutter_volume_extreme`: Extreme volume base price (default: 2000 EUR)

### Updating Prices

1. **Navigate** to Laravel Admin → Settings
2. **Find** the setting you want to change
3. **Edit** the value
4. **Save** changes
5. **Test** the calculator to verify changes

> **⚠️ Important**: Price changes take effect immediately on the website. Test thoroughly before making changes.

## 📋 Quote Management

### Viewing Quote Requests

1. Go to **Payload Admin** → **Quote Requests**
2. See all customer quote requests with status
3. Click on any quote to view details

### Quote Information Includes

- **Customer Details**: Name, email, phone
- **Services Requested**: Selected services
- **Service Details**: Specific requirements
- **Estimated Price**: Calculator estimate
- **Status**: New, Processing, Quoted, Completed
- **Submission Date**: When quote was requested

### Managing Quote Status

1. **Open** the quote request
2. **Change Status**:
   - **New**: Just submitted
   - **Processing**: Being reviewed
   - **Quoted**: Quote sent to customer
   - **Completed**: Service completed

3. **Add Notes**: Internal notes for tracking
4. **Save** changes

### Quote Response Workflow

1. **Review** quote details
2. **Calculate** final price (may differ from estimate)
3. **Contact** customer with official quote
4. **Update** status to "Quoted"
5. **Follow up** as needed
6. **Mark** as "Completed" when service is done

## 📄 Content Pages

### Managing Website Pages

1. Go to **Payload Admin** → **Pages**
2. Edit existing pages or create new ones
3. Available pages:
   - Homepage content
   - About page
   - Services pages
   - Blog posts
   - Legal pages (AGB, Datenschutz, Impressum)

### Editing Page Content

1. **Select** the page to edit
2. **Use** the rich text editor for content
3. **Add** images, links, and formatting
4. **Preview** changes before publishing
5. **Publish** when ready

### SEO Settings for Pages

Each page includes SEO settings:
- **Meta Title**: Page title for search engines
- **Meta Description**: Page description for search results
- **Keywords**: Relevant keywords
- **Open Graph**: Social media sharing settings

### Content Best Practices

- **Write** in clear, simple German
- **Use** relevant keywords naturally
- **Keep** paragraphs short and readable
- **Include** calls-to-action
- **Optimize** images for web
- **Test** on mobile devices

## 📧 Email Templates

### Available Email Templates

1. **Quote Request Email**: Sent to business when customer requests quote
2. **Customer Confirmation**: Sent to customer confirming quote submission
3. **Quote Response**: Template for sending official quotes

### Editing Email Templates

1. Go to **Payload Admin** → **Email Templates**
2. Select template to edit
3. Use available variables:
   - `{{customer_name}}`: Customer's name
   - `{{quote_number}}`: Quote reference number
   - `{{estimated_total}}`: Estimated price
   - `{{services}}`: Selected services
   - `{{contact_info}}`: Customer contact details

### Email Template Example

```html
Liebe/r {{customer_name}},

vielen Dank für Ihre Anfrage ({{quote_number}}).

Ihre gewählten Services:
{{services}}

Geschätzte Kosten: {{estimated_total}}€

Wir werden uns innerhalb von 24 Stunden bei Ihnen melden.

Mit freundlichen Grüßen,
YLA Umzug Team
```

## ⚙️ Calculator Settings

### Enabling/Disabling Calculator

1. Go to **Laravel Admin** → **Settings**
2. Find **"Calculator Enabled"** setting
3. Toggle **On/Off**
4. Save changes

When disabled:
- Calculator is hidden from website
- Users see contact form instead
- Existing quotes remain accessible

### Calculator Display Options

- **Show on Homepage**: Display calculator prominently
- **Dedicated Page**: Full calculator experience
- **Mobile Optimization**: Touch-friendly interface
- **Progress Indicator**: Show completion progress

### Testing Calculator Changes

1. **Make** pricing changes
2. **Go** to website calculator
3. **Test** different service combinations
4. **Verify** calculations are correct
5. **Check** email notifications work

## 🔍 SEO Management

### Global SEO Settings

1. Go to **Payload Admin** → **Site Settings**
2. Configure:
   - **Site Title**: Main website title
   - **Site Description**: Default meta description
   - **Keywords**: Primary keywords
   - **Contact Information**: Business details
   - **Social Media**: Links to social profiles

### Local SEO

Configure local business information:
- **Business Name**: YLA Umzug
- **Address**: Complete business address
- **Phone**: Contact phone number
- **Service Areas**: Cities/regions served
- **Business Hours**: Operating hours

### Structured Data

The system automatically generates structured data for:
- **Local Business**: Business information
- **Services**: Service descriptions
- **Reviews**: Customer testimonials
- **FAQ**: Frequently asked questions

### SEO Best Practices

- **Use** location-based keywords (Saarbrücken, Trier, etc.)
- **Create** helpful, informative content
- **Optimize** page loading speed
- **Ensure** mobile-friendly design
- **Update** content regularly
- **Monitor** search rankings

## 🔧 Troubleshooting

### Common Issues

**Calculator Not Working**
1. Check if calculator is enabled in settings
2. Verify pricing settings are configured
3. Test API endpoints
4. Check browser console for errors

**Email Not Sending**
1. Verify email configuration in Laravel
2. Check email templates are configured
3. Test email settings
4. Check spam folders

**Content Not Updating**
1. Clear cache in Payload Admin
2. Refresh browser cache
3. Check if changes were saved
4. Verify user permissions

**Pricing Calculations Wrong**
1. Review pricing settings
2. Test with known values
3. Check for decimal/integer settings
4. Verify discount calculations

### Getting Help

1. **Check** this documentation first
2. **Test** in staging environment
3. **Contact** technical support if needed
4. **Document** any issues found

### Backup and Recovery

**Regular Backups**
- Content is automatically backed up
- Database backups run daily
- File backups include uploaded images

**Recovery Process**
1. Contact technical support
2. Specify what needs to be restored
3. Provide approximate date/time
4. Verify restored content

## 📞 Support Contacts

**Technical Issues**
- Email: tech-support@example.com
- Phone: +49 XXX XXXXXXX

**Content Questions**
- Email: content@example.com

**Emergency Support**
- Phone: +49 XXX XXXXXXX (24/7)

---

**Last Updated:** $(date)
**Version:** 1.0.0

> **💡 Tip**: Bookmark this guide and the admin panel URLs for quick access. Always test changes in a staging environment before applying to production.