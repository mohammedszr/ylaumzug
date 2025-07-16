src/
├── components/
│   ├── calculator/          # Multi-step calculator components
│   │   ├── Calculator.jsx
│   │   ├── ServiceSelection.jsx
│   │   ├── MovingDetails.jsx
│   │   ├── CleaningDetails.jsx
│   │   ├── DeclutterDetails.jsx
│   │   ├── GeneralInfo.jsx
│   │   ├── PriceSummary.jsx
│   │   └── CalculatorStepper.jsx
│   └── ui/                  # Reusable UI components
│       ├── card.jsx
│       ├── toaster.jsx
│       ├── cookie-banner.jsx
│       ├── privacy-checkbox.jsx
│       ├── whatsapp-float.jsx    # NEW: Floating WhatsApp button
│       └── breadcrumbs.jsx       # NEW: SEO breadcrumb navigation
├── pages/                   # All website pages
│   ├── HomePage.jsx              # Enhanced with FAQ section
│   ├── CalculatorPage.jsx        # Main calculator interface
│   ├── ServicesPage.jsx
│   ├── DeclutterPage.jsx
│   ├── BlogPage.jsx              # Enhanced ratgeber overview
│   ├── ContactPage.jsx           # Enhanced with WhatsApp integration
│   ├── EntruempelungRatgeberPage.jsx    # NEW: Comprehensive decluttering guide
│   ├── UmzugRatgeberPage.jsx            # NEW: Complete moving guide
│   ├── HausreinigungRatgeberPage.jsx    # NEW: House cleaning guide
│   ├── HartzIVUmzugRatgeberPage.jsx     # NEW: Bürgergeld moving guide
│   ├── AGBPage.jsx               # Enhanced with robots meta tag
│   ├── DatenschutzPage.jsx       # Enhanced with robots meta tag
│   └── ImpressumPage.jsx         # Enhanced with robots meta tag
└── lib/
    ├── api.js              # Laravel backend integration
    └── payload-api.js      # Payload CMS integration




Backend (Laravel API)

backend/
├── app/
│   ├── Http/Controllers/
│   │   ├── CalculatorController.php    # Pricing engine
│   │   ├── QuoteController.php         # Quote processing
│   │   └── AdminController.php         # Email testing
│   ├── Models/
│   │   ├── QuoteRequest.php
│   │   ├── Service.php
│   │   └── PricingRule.php
│   ├── Mail/                          # Email system
│   │   ├── QuoteRequestMail.php
│   │   └── QuoteConfirmationMail.php
│   └── Services/
│       └── EmailNotificationService.php
├── database/
│   ├── migrations/                    # Database structure
│   └── seeders/                       # Sample data
└── resources/views/emails/            # Email templates
    ├── quote-request.blade.php
    └── quote-confirmation.blade.php



Content Management (Payload CMS)


payload/
├── src/
│   ├── payload.config.ts              # CMS configuration
│   └── server.ts                      # CMS server
└── Admin Interface at localhost:3001/admin

## SEO & Content Optimization (NEW)

### SEO Files & Features
```
public/
├── sitemap.xml                        # Complete XML sitemap
├── llms.txt                          # AI assistant indexing file
└── robots.txt                        # Search engine directives

SEO Features Implemented:
├── Structured Data (JSON-LD)
│   ├── LocalBusiness schema (HomePage)
│   ├── HowTo schemas (all ratgeber pages)
│   ├── FAQ schemas (HomePage + ratgeber)
│   └── BreadcrumbList schema (service pages)
├── Meta Tag Optimization
│   ├── Unique titles & descriptions
│   ├── German keyword optimization
│   ├── Canonical URLs
│   └── Robots directives (legal pages)
├── Content Marketing
│   ├── 4 comprehensive ratgeber pages (15,000+ words)
│   ├── Regional keyword targeting
│   ├── Internal linking strategy
│   └── FAQ sections optimized for AI search
└── WhatsApp Integration
    ├── Floating WhatsApp button
    ├── Contact page integration
    ├── Pre-filled message templates
    └── Service-specific quick actions
```

### Content Pages Structure
```
Ratgeber Content:
├── /ratgeber                         # Overview page with all guides
├── /ratgeber-entruempelung-5-schritte    # Decluttering guide
├── /ratgeber-umzug-checkliste            # Moving checklist
├── /ratgeber-hausreinigung-endreinigung  # House cleaning guide
└── /ratgeber-hartz-iv-umzug-jobcenter    # Bürgergeld moving guide

Each ratgeber page includes:
├── 5-step process guides
├── Regional service information
├── FAQ sections
├── Internal linking
├── WhatsApp CTAs
├── Structured data markup
└── Breadcrumb navigation
```

### Regional SEO Targeting
```
Primary Regions:
├── Saarbrücken (Entrümpelung, Haushaltsauflösung)
├── Trier (Messi-Wohnung Hilfe, Umzugsservice)
├── Kaiserslautern (Umzugsfirma, Möbellift)
├── Mainz (Regional coverage)
├── Koblenz (Regional coverage)
└── Ludwigshafen (Regional coverage)

Keywords Optimized:
├── Short-tail: "Entrümpelung Saarbrücken"
├── Long-tail: "Entrümpelung in 5 Schritten Saarland"
├── LSI: "Sperrmüll entsorgen", "Kellerentrümpelung"
└── Bürgergeld: "Bürgergeld Umzug Jobcenter"
```
