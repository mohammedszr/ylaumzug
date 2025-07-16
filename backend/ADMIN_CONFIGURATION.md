# Admin Configuration Guide - YLA Umzug Backend

## 📋 Overview

This document explains how to customize and configure the YLA Umzug calculator system without touching the code. All business-specific settings, pricing rules, and content can be modified through the database or admin panel.

## 🎯 Quick Configuration Checklist

### ✅ Essential Settings to Configure

**Before going live, you MUST configure these settings:**

- [ ] **Business Information** (email, phone, address)
- [ ] **Service Areas** (postal codes you serve)
- [ ] **Pricing Rules** (base prices, distance rates)
- [ ] **Email Configuration** (SMTP settings)
- [ ] **Calculator Toggle** (enable/disable)

## 🏢 Business Information Configuration

### Database Table: `settings`

**Required Settings to Update:**

```sql
-- Update these in the settings table
UPDATE settings SET value = 'YOUR_ACTUAL_EMAIL@yla-umzug.de' WHERE key = 'business_email';
UPDATE settings SET value = 'YOUR_ACTUAL_PHONE' WHERE key = 'business_phone';
UPDATE settings SET value = 'YOUR_ACTUAL_ADDRESS' WHERE key = 'business_address';
UPDATE settings SET value = 'quotes@YOUR_DOMAIN.de' WHERE key = 'quote_notification_email';
```

**Questions for You:**
1. **What is your main business email address?** (for receiving quotes)
2. **What is your business phone number?** (displayed on website)
3. **What is your complete business address?** (for contact page)
4. **Do you want a separate email for quote notifications?** (recommended)

## 💰 Pricing Configuration

### Service Base Prices

**Database Table: `services`**

Current base prices:
- Umzug: 300€
- Entrümpelung: 300€
- Putzservice: 150€

**To modify:**
```sql
UPDATE services SET base_price = 350.00 WHERE key = 'umzug';
UPDATE services SET base_price = 400.00 WHERE key = 'entruempelung';
UPDATE services SET base_price = 200.00 WHERE key = 'putzservice';
```

### Pricing Rules Configuration

**Database Table: `pricing_rules`**

**Key Pricing Factors to Review:**

#### Umzug Pricing:
- **Per m² rate**: Currently 8€/m² (`rule_key = 'apartmentSize'`)
- **Distance rate**: Currently 2€/km (`rule_key = 'distance_km'`)
- **Box handling**: Currently 3€/box (`rule_key = 'boxes'`)
- **Floor surcharge**: Currently 50€/floor above 2nd (`rule_key = 'floor_surcharge'`)

#### Entrümpelung Volume Pricing:
- **Low volume**: 300€ (1-2 containers)
- **Medium volume**: 600€ (3-5 containers)
- **High volume**: 1,200€ (6+ containers)
- **Extreme volume**: 2,000€ (Messi households)

#### Additional Charges:
- **Hazardous waste**: 150€
- **Electronics disposal**: 100€
- **Clean handover**: 150€

**Questions for You:**
1. **What should be your base price per square meter for moving?** (currently 8€/m²)
2. **What should be your distance rate?** (currently 2€/km)
3. **Are the volume-based prices for decluttering realistic for your market?**
4. **Do you want to adjust any surcharges or additional service prices?**

### Discount Configuration

**Database Table: `settings`**

```sql
-- Combination discounts
UPDATE settings SET value = '0.12' WHERE key = 'combination_discount_2_services'; -- 12% for 2 services
UPDATE settings SET value = '0.18' WHERE key = 'combination_discount_3_services'; -- 18% for 3+ services

-- Express surcharge
UPDATE settings SET value = '0.25' WHERE key = 'express_surcharge'; -- 25% surcharge
```

**Questions for You:**
1. **What discount should customers get for booking multiple services?**
   - 2 services: Currently 10%
   - 3+ services: Currently 15%
2. **What surcharge for express/urgent service?** (currently 20%)

## 🗺️ Service Area Configuration

**Database Table: `settings` (key: `service_areas`)**

**Current Service Areas:**
```json
["66111", "66112", "66113", "67655", "67656", "54290", "54292"]
```

**To update your service areas:**
```sql
UPDATE settings 
SET value = '["YOUR_POSTAL_CODES", "HERE"]' 
WHERE key = 'service_areas';
```

**Questions for You:**
1. **What postal codes do you serve?** (list all areas)
2. **What is your maximum service distance?** (currently 100km)
3. **Do you charge extra for certain areas?** (can be configured)

## 📧 Email Configuration

### SMTP Settings (.env file)

**Update these in your `.env` file:**
```env
MAIL_MAILER=smtp
MAIL_HOST=your-smtp-server.com
MAIL_PORT=587
MAIL_USERNAME=your-email@yla-umzug.de
MAIL_PASSWORD=your-email-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@yla-umzug.de"
MAIL_FROM_NAME="YLA Umzug"
```

**Questions for You:**
1. **What email provider do you use?** (Gmail, Outlook, custom SMTP)
2. **What should be the sender name for emails?** (currently "YLA Umzug")
3. **Do you want automatic confirmation emails to customers?** (recommended: yes)

## 🎛️ Calculator Control

### Enable/Disable Calculator

**Database Table: `settings`**

```sql
-- Disable calculator
UPDATE settings SET value = '0' WHERE key = 'calculator_enabled';

-- Enable calculator
UPDATE settings SET value = '1' WHERE key = 'calculator_enabled';

-- Update maintenance message
UPDATE settings SET value = 'Custom maintenance message' WHERE key = 'calculator_maintenance_message';
```

**When to disable:**
- During price updates
- System maintenance
- Seasonal closures
- High demand periods

## 🔧 Additional Services Configuration

**Database Table: `additional_services`**

**Current Additional Services for Moving:**
- Möbelabbau & Aufbau: 200€
- Verpackungsservice: 150€
- Halteverbotszone: 80€
- Einlagerung: 100€/m²
- Entsorgung: 120€

**To modify prices:**
```sql
UPDATE additional_services SET price = 250.00 WHERE key = 'assembly';
UPDATE additional_services SET price = 180.00 WHERE key = 'packing';
```

**Questions for You:**
1. **Do you offer furniture assembly/disassembly?** What should it cost?
2. **Do you provide packing services?** What's your rate?
3. **Do you handle parking permits?** What do you charge?
4. **Do you offer storage services?** What's your rate per m²?

## 📊 Admin Panel Access

### Creating Admin User

**Run this command to create an admin user:**
```bash
php artisan make:user
# Follow prompts to create admin account
```

**Admin Panel URLs:**
- Dashboard: `/admin`
- Quote Management: `/admin/quotes`
- Settings: `/admin/settings`

## 🚀 Going Live Checklist

### Before Launch:
- [ ] Update all business information
- [ ] Configure service areas
- [ ] Set realistic pricing
- [ ] Test email delivery
- [ ] Create admin user account
- [ ] Test calculator with real data
- [ ] Enable calculator

### After Launch:
- [ ] Monitor quote submissions
- [ ] Adjust pricing based on demand
- [ ] Review and respond to quotes promptly
- [ ] Update service areas as needed

## 🔍 Monitoring & Analytics

### Key Metrics to Track:
- Quote submission rate
- Service popularity
- Average quote value
- Conversion rate (quotes to customers)
- Geographic distribution

### Database Queries for Analytics:

```sql
-- Quote statistics
SELECT 
    COUNT(*) as total_quotes,
    AVG(JSON_EXTRACT(pricing_data, '$.total')) as avg_quote_value,
    COUNT(CASE WHEN status = 'accepted' THEN 1 END) as accepted_quotes
FROM quote_requests 
WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY);

-- Popular services
SELECT 
    service,
    COUNT(*) as count
FROM (
    SELECT JSON_UNQUOTE(JSON_EXTRACT(selected_services, CONCAT('$[', numbers.n, ']'))) as service
    FROM quote_requests
    CROSS JOIN (SELECT 0 n UNION SELECT 1 UNION SELECT 2) numbers
    WHERE JSON_EXTRACT(selected_services, CONCAT('$[', numbers.n, ']')) IS NOT NULL
) services
GROUP BY service
ORDER BY count DESC;
```

## 🛠️ Troubleshooting

### Common Issues:

1. **Calculator not working:**
   - Check `calculator_enabled` setting
   - Verify pricing rules are active
   - Check Laravel logs

2. **Emails not sending:**
   - Verify SMTP settings in `.env`
   - Check email addresses in settings
   - Test with `php artisan tinker`

3. **Wrong prices calculated:**
   - Review pricing rules in database
   - Check rule priorities
   - Verify condition operators

### Debug Commands:
```bash
# Check settings
php artisan tinker
>>> App\Models\Setting::all();

# Test email
>>> Mail::raw('Test email', function($msg) { $msg->to('test@example.com'); });

# Check pricing rules
>>> App\Models\PricingRule::where('is_active', true)->get();
```

## 📞 Support

If you need help configuring any of these settings:

1. **Check the Laravel logs** first: `storage/logs/laravel.log`
2. **Use the admin panel** for basic settings
3. **Contact your developer** for complex pricing rule changes
4. **Test thoroughly** before going live

---

**Remember:** Always backup your database before making changes to pricing or settings!