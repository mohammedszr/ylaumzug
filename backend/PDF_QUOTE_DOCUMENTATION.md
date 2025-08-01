# PDF Quote Generation Documentation

## Overview

The PDF quote generation system allows administrators to create professional PDF quotes from quote requests and send them to customers via email.

## Features

- **Professional PDF Templates**: Clean, branded PDF quotes with company information
- **Automatic Data Population**: Quote details, customer information, and pricing automatically filled
- **Email Integration**: PDFs can be attached to emails and sent directly to customers
- **File Storage**: Generated PDFs are stored in `storage/app/quotes/` for record keeping
- **German Language Support**: All content is in German for the target market

## Components

### 1. PdfQuoteService (`app/Services/PdfQuoteService.php`)

Main service class that handles PDF generation:

- `generateQuotePdf(QuoteRequest $quote)`: Generates and saves PDF to storage
- `getPdfContent(QuoteRequest $quote)`: Returns PDF content as string for email attachments
- Translation helper methods for German content
- Cleanup methods for old PDF files

### 2. PDF Template (`resources/views/pdf/quote.blade.php`)

Professional Blade template with:

- Company branding and contact information
- Customer details section
- Service details with translations
- Pricing breakdown table
- Terms and conditions
- Professional styling with CSS

### 3. Email Integration

#### PdfQuoteMail (`app/Mail/PdfQuoteMail.php`)
- Sends PDF quotes to customers
- Attaches generated PDF automatically
- Professional email template

#### Email Templates
- `resources/views/emails/pdf-quote.blade.php`: HTML email template
- `resources/views/emails/pdf-quote-text.blade.php`: Plain text version

### 4. Controller Methods (`app/Http/Controllers/QuoteController.php`)

New PDF-related endpoints:

- `POST /api/quotes/{quote}/generate-pdf`: Generate PDF file
- `GET /api/quotes/{quote}/download-pdf`: Download PDF
- `GET /api/quotes/{quote}/preview-pdf`: Preview PDF in browser
- `POST /api/quotes/{quote}/send-pdf`: Send PDF quote via email

## Usage

### Admin Panel Integration

1. **Generate PDF**: Admin can generate PDF for any quote request
2. **Preview PDF**: View PDF in browser before sending
3. **Download PDF**: Download PDF for local storage
4. **Send to Customer**: Email PDF directly to customer with professional message

### API Endpoints

```php
// Generate PDF
POST /api/quotes/{quote}/generate-pdf
Response: { "success": true, "filename": "angebot-YLA-2024-0001.pdf" }

// Send PDF via email
POST /api/quotes/{quote}/send-pdf
Body: { "final_quote_amount": 1200.00, "admin_notes": "Final offer" }
Response: { "success": true, "message": "PDF sent successfully" }
```

### Testing

Use the test command to verify PDF generation:

```bash
php artisan test:pdf
```

This creates a mock quote request and generates a test PDF.

## Configuration

### DomPDF Settings (`config/dompdf.php`)

Key settings:
- Paper size: A4
- Orientation: Portrait
- Font: DejaVu Sans (supports German characters)
- DPI: 150 for high quality

### Storage

PDFs are stored in `storage/app/quotes/` with naming convention:
`angebot-{quote_number}-{date}.pdf`

## PDF Content Structure

1. **Header**: Company logo, contact info, quote number
2. **Customer Information**: Name, contact details, preferred date
3. **Service Details**: Detailed breakdown of requested services
4. **Pricing Table**: Itemized costs with totals
5. **Terms & Conditions**: Business terms and legal information
6. **Footer**: Company details and generation timestamp

## German Translations

The system includes comprehensive German translations for:

- Service types (Umzug, Entrümpelung, Putzservice)
- Object types (Wohnung, Haus, Keller, etc.)
- Cleaning intensities (Normalreinigung, Grundreinigung)
- Frequencies (Einmalig, Wöchentlich, Monatlich)
- Additional services and waste types

## Error Handling

- PDF generation failures are logged
- Email sending continues even if PDF attachment fails
- Graceful fallbacks for missing data
- User-friendly German error messages

## Security

- Admin-only access to PDF generation endpoints
- File storage in protected directory
- Input validation for all parameters
- CSRF protection on web routes

## Maintenance

### Cleanup Old PDFs

The service includes a cleanup method to remove old PDF files:

```php
$pdfService = app(PdfQuoteService::class);
$deletedCount = $pdfService->cleanupOldPdfs(90); // Delete files older than 90 days
```

This can be scheduled as a cron job for automatic maintenance.

## Future Enhancements

- Digital signatures for quotes
- Multiple template designs
- Batch PDF generation
- PDF password protection
- Integration with document management systems