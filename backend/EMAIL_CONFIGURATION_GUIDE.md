# Email System Configuration Guide

## Overview

The YLA Umzug email system provides comprehensive email notifications for quote requests. When a customer submits a quote through the calculator, the system automatically sends:

1. **Quote Request Notification** - Detailed email to business owner (info@yla-umzug.de)
2. **Customer Confirmation** - Professional confirmation email to the customer

## Email System Features

### ✅ Implemented Features

- **Dual Email Delivery**: Automatic emails to both business owner and customer
- **Professional Templates**: Branded HTML and text email templates
- **German Language**: All content in German for local market
- **Mobile Responsive**: Optimized for mobile email clients
- **Comprehensive Data**: Complete quote details, pricing, and customer information
- **Error Handling**: Graceful fallback if emails fail to send
- **Email Testing**: Built-in tools for testing email configuration
- **Logging**: Comprehensive email delivery logging

### 📧 Email Templates

#### Business Owner Notification (`quote-request.blade.php`)
- **Complete Customer Data**: Name, email, phone, preferred contact method
- **Service Breakdown**: Detailed information for each selected service
- **Pricing Information**: Estimated costs and pricing breakdown
- **Admin Integration**: Direct links to admin panel for quote management
- **Professional Design**: Branded layout with clear call-to-action buttons
- **Mobile Optimized**: Responsive design for mobile email clients

#### Customer Confirmation (`quote-confirmation.blade.php`)
- **Professional Branding**: YLA Umzug branded design with logo
- **Clear Next Steps**: Step-by-step process explanation
- **Service Summary**: Overview of requested services and estimated pricing
- **Contact Information**: Business phone and email for customer inquiries
- **Trust Building**: Company benefits and service guarantees
- **Response Timeline**: Clear expectations for follow-up timing

## Production Email Configuration

### Step 1: Update Environment Variables

Copy the production email settings from `.env.example` and configure them in your `.env` file:

```bash
# Production Email Settings
MAIL_MAILER=smtp
MAIL_HOST=smtp.strato.de
MAIL_PORT=587
MAIL_USERNAME=info@yla-umzug.de
MAIL_PASSWORD=your_email_password
MAIL_ENCRYPTION=tls

# Email Addresses
MAIL_FROM_ADDRESS="noreply@yla-umzug.de"
MAIL_FROM_NAME="YLA Umzug"
BUSINESS_EMAIL=info@yla-umzug.de
ADMIN_EMAIL=info@yla-umzug.de

# Email Settings
EMAIL_RESPONSE_TIME="24 Stunden"
```

### Step 2: Common Email Provider Settings

#### Strato (Recommended for German businesses)
```bash
MAIL_HOST=smtp.strato.de
MAIL_PORT=587
MAIL_ENCRYPTION=tls
```

#### Gmail/Google Workspace
```bash
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_ENCRYPTION=tls
```

#### 1&1 IONOS
```bash
MAIL_HOST=smtp.ionos.de
MAIL_PORT=587
MAIL_ENCRYPTION=tls
```

#### Web.de
```bash
MAIL_HOST=smtp.web.de
MAIL_PORT=587
MAIL_ENCRYPTION=tls
```

### Step 3: Test Email Configuration

Use the built-in email testing endpoints:

```bash
# Test email configuration
curl -X POST http://your-domain.com/api/admin/email/test \
  -H "Content-Type: application/json" \
  -d '{"email": "test@example.com"}'

# Check email status
curl -X GET http://your-domain.com/api/admin/email/status
```

## Email Templates

The system includes professionally designed email templates:

### Business Owner Notification (`quote-request.blade.php`)
- Complete customer information
- Service details breakdown
- Estimated pricing
- Direct links to admin panel
- Mobile-responsive design

### Customer Confirmation (`quote-confirmation.blade.php`)
- Professional branded design
- Clear next steps
- Contact information
- Service summary
- Response time expectations

## Email Features

### Automatic Email Sending
- **Quote Submission**: Automatically sends emails to both business owner and customer
- **Error Handling**: Graceful fallback if emails fail
- **Logging**: Comprehensive logging for debugging

### Email Content Features
- **German Language**: All content in German for local market
- **Professional Branding**: Consistent with YLA Umzug brand
- **Mobile Responsive**: Optimized for mobile email clients
- **Plain Text Versions**: Included for better deliverability

### Admin Features
- **Email Status Check**: View current email configuration
- **Test Email**: Send test emails to verify setup
- **Email Logging**: Track email delivery success/failure

## Troubleshooting

### Common Issues

#### 1. Authentication Failed
```
Error: Authentication failed
```
**Solution**: Check username/password and enable "Less secure app access" if using Gmail.

#### 2. Connection Timeout
```
Error: Connection timeout
```
**Solution**: Check MAIL_HOST and MAIL_PORT settings. Verify firewall allows outbound SMTP.

#### 3. TLS/SSL Issues
```
Error: TLS/SSL connection failed
```
**Solution**: Try different encryption settings (tls, ssl, or null).

#### 4. Emails Going to Spam
**Solutions**:
- Use proper FROM address with your domain
- Set up SPF, DKIM, and DMARC records
- Use reputable email provider
- Include unsubscribe links

### Testing Checklist

- [ ] Test email sends successfully
- [ ] Business owner receives quote notifications
- [ ] Customers receive confirmation emails
- [ ] Emails display correctly on mobile
- [ ] Plain text versions work
- [ ] Email logs show successful delivery

## Security Best Practices

### Email Security
1. **Use App Passwords**: For Gmail/Google Workspace, use app-specific passwords
2. **Secure Credentials**: Store email passwords securely, never in version control
3. **TLS Encryption**: Always use TLS encryption for SMTP connections
4. **Rate Limiting**: Implement rate limiting to prevent email abuse

### Privacy Compliance (GDPR)
1. **Data Retention**: Automatically delete old quote requests after specified period
2. **Consent**: Ensure customers consent to receiving emails
3. **Unsubscribe**: Provide easy unsubscribe mechanism
4. **Data Processing**: Document email data processing in privacy policy

## Monitoring and Maintenance

### Email Monitoring
- Monitor email delivery rates
- Check spam folder placement
- Track bounce rates
- Monitor server reputation

### Regular Maintenance
- Update email templates as needed
- Review and update email content
- Test email delivery monthly
- Monitor email provider limits

## Advanced Configuration

### Queue Configuration
For high-volume email sending, configure Laravel queues:

```bash
# In .env
QUEUE_CONNECTION=database

# Run queue worker
php artisan queue:work
```

### Email Service Providers
Consider using dedicated email services for better deliverability:
- **Mailgun**: Reliable transactional emails
- **SendGrid**: High deliverability rates
- **Amazon SES**: Cost-effective for high volume

## Support

If you encounter issues with email configuration:

1. Check Laravel logs: `storage/logs/laravel.log`
2. Test with different email providers
3. Verify DNS settings (SPF, DKIM, DMARC)
4. Contact your hosting provider for SMTP support

## Email Template Customization

Email templates are located in `resources/views/emails/`:
- `quote-request.blade.php` - Business owner notification (HTML)
- `quote-request-text.blade.php` - Business owner notification (Text)
- `quote-confirmation.blade.php` - Customer confirmation (HTML)
- `quote-confirmation-text.blade.php` - Customer confirmation (Text)

To customize:
1. Edit the Blade templates
2. Test changes with test emails
3. Ensure mobile responsiveness
4. Update both HTML and text versions