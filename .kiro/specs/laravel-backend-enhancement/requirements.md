# Requirements Document

## Introduction

This specification defines the requirements for transforming the existing React-based YLA Umzug application into a full-stack Laravel application with advanced Filament admin dashboard while preserving all existing frontend functionality. The project aims to create a production-ready moving services platform with comprehensive backend capabilities, API integration, and professional admin management tools.

## Requirements

### Requirement 1: Laravel Foundation & Database Architecture

**User Story:** As a system administrator, I want a robust Laravel backend with proper database architecture, so that the application can handle quote requests, service management, and user data efficiently.

#### Acceptance Criteria

1. WHEN the Laravel application is set up THEN it SHALL use Laravel 10 with proper project structure
2. WHEN the database is configured THEN it SHALL use SQLite for development/staging with migration system
3. WHEN database models are created THEN they SHALL include QuoteRequest, Service, Setting, and User models with proper relationships
4. WHEN data validation is implemented THEN it SHALL include comprehensive business logic and validation rules
5. WHEN the database schema is created THEN it SHALL support quote numbers (QR-YYYY-NNN format), service details, pricing, and status tracking

### Requirement 2: API Integration for React Frontend

**User Story:** As a frontend developer, I want RESTful API endpoints that match the existing React application structure, so that the frontend can communicate seamlessly with the Laravel backend without requiring changes.

#### Acceptance Criteria

1. WHEN the calculator API is implemented THEN it SHALL accept the existing calculatorData structure from React components
2. WHEN pricing calculation is requested THEN it SHALL return pricing breakdown with total, service details, and disclaimers
3. WHEN quote submission occurs THEN it SHALL process the existing form data structure from GeneralInfo component
4. WHEN API responses are returned THEN they SHALL match the expected format used by calculatorApi and quoteApi
5. WHEN API errors occur THEN they SHALL return user-friendly error messages in German
6. WHEN the API is called THEN it SHALL support CORS for frontend integration
7. WHEN services are requested THEN it SHALL return available services with pricing information

### Requirement 3: Filament Admin Dashboard

**User Story:** As a business administrator, I want a comprehensive German-localized admin panel, so that I can manage quote requests, services, settings, and users efficiently.

#### Acceptance Criteria

1. WHEN the admin panel is accessed THEN it SHALL display in German language
2. WHEN quote requests are viewed THEN they SHALL show angebotsnummer, customer details, services, status, and pricing
3. WHEN quote status is updated THEN it SHALL allow marking as reviewed, quoted, accepted, rejected, or completed
4. WHEN final quotes are created THEN it SHALL allow setting endgueltiger_angebotsbetrag and admin notes
5. WHEN services are managed THEN it SHALL allow CRUD operations on service configurations and pricing
6. WHEN settings are managed THEN it SHALL support grouped settings with different data types
7. WHEN users are managed THEN it SHALL support role-based access (admin, manager, employee)
8. WHEN the dashboard is displayed THEN it SHALL show pending quote count as navigation badge

### Requirement 4: Distance Calculation Integration

**User Story:** As a quote processor, I want automatic distance calculation between postal codes, so that I can provide accurate pricing based on moving distance.

#### Acceptance Criteria

1. WHEN postal codes are provided THEN the system SHALL calculate distance using OpenRouteService API
2. WHEN distance calculation succeeds THEN it SHALL store distance_km in the quote request
3. WHEN distance calculation fails THEN it SHALL log the error and continue without distance
4. WHEN distance is calculated THEN it SHALL cache results for 1 hour to avoid repeated API calls
5. WHEN distance-based pricing is applied THEN it SHALL add costs for distances over 30km at €1.50 per km

### Requirement 5: Email Notification System

**User Story:** As a customer, I want to receive email confirmations and quotes, so that I have written documentation of my requests and offers.

#### Acceptance Criteria

1. WHEN a quote is submitted THEN the customer SHALL receive a confirmation email with quote number
2. WHEN a quote is finalized THEN the customer SHALL receive an email with PDF attachment
3. WHEN emails are sent THEN they SHALL use professional templates with YLA Umzug branding
4. WHEN email sending fails THEN it SHALL log the error and allow manual retry
5. WHEN emails are configured THEN they SHALL support SMTP configuration for production

### Requirement 6: WhatsApp Business Integration

**User Story:** As a business owner, I want to send quotes via WhatsApp, so that I can reach customers through their preferred communication channel.

#### Acceptance Criteria

1. WHEN WhatsApp is configured THEN it SHALL support Meta WhatsApp Business API
2. WHEN quotes are ready THEN administrators SHALL be able to send them via WhatsApp from admin panel
3. WHEN WhatsApp messages are sent THEN they SHALL include quote details and PDF attachment
4. WHEN phone numbers are processed THEN they SHALL be cleaned and formatted for German numbers
5. WHEN WhatsApp templates are used THEN they SHALL be in German with proper formatting
6. WHEN WhatsApp sending fails THEN it SHALL provide error feedback and retry options

### Requirement 7: PDF Quote Generation

**User Story:** As a customer, I want to receive professional PDF quotes, so that I have formal documentation for my moving service request.

#### Acceptance Criteria

1. WHEN PDF quotes are generated THEN they SHALL include company branding and contact information
2. WHEN quote details are included THEN they SHALL show services, pricing breakdown, and terms
3. WHEN PDFs are created THEN they SHALL be properly formatted for A4 paper
4. WHEN PDFs are attached to emails THEN they SHALL use descriptive filenames with quote numbers
5. WHEN PDF generation fails THEN it SHALL log errors and provide fallback options

### Requirement 8: Price Calculator Service

**User Story:** As a system, I want accurate price calculations based on service selections and details, so that customers receive fair and consistent pricing estimates.

#### Acceptance Criteria

1. WHEN moving services are calculated THEN pricing SHALL include base price, room multiplier, and floor costs
2. WHEN cleaning services are calculated THEN pricing SHALL include base price and room-based costs
3. WHEN decluttering services are calculated THEN pricing SHALL include base price and volume-based costs
4. WHEN distance costs are applied THEN they SHALL be added for moving services over 30km
5. WHEN pricing breakdown is requested THEN it SHALL return detailed cost components
6. WHEN calculations fail THEN it SHALL provide fallback pricing with "Auf Anfrage" message

### Requirement 9: Settings Management System

**User Story:** As an administrator, I want to manage application settings through the admin panel, so that I can configure pricing, features, and system behavior without code changes.

#### Acceptance Criteria

1. WHEN settings are organized THEN they SHALL be grouped by category (general, pricing, email, api, ui)
2. WHEN setting values are stored THEN they SHALL support string, integer, decimal, boolean, and JSON types
3. WHEN public settings are marked THEN they SHALL be accessible via frontend API
4. WHEN settings are updated THEN they SHALL take effect immediately without restart
5. WHEN settings are managed THEN they SHALL include descriptions and validation

### Requirement 10: Production Readiness Features

**User Story:** As a system administrator, I want production-ready features like error handling, logging, and security, so that the application runs reliably in production.

#### Acceptance Criteria

1. WHEN errors occur THEN they SHALL be logged with appropriate detail levels
2. WHEN API requests are made THEN they SHALL include rate limiting protection
3. WHEN user data is processed THEN it SHALL include CSRF protection and validation
4. WHEN the application runs THEN it SHALL support queue processing for emails
5. WHEN backups are needed THEN the system SHALL support automated database backups
6. WHEN monitoring is required THEN it SHALL integrate with error tracking services

### Requirement 11: Frontend Compatibility Preservation

**User Story:** As a frontend user, I want all existing React functionality to work unchanged, so that the user experience remains consistent during the backend transition.

#### Acceptance Criteria

1. WHEN the React calculator is used THEN all existing components SHALL function without modification
2. WHEN API calls are made THEN they SHALL use the existing api.js structure and endpoints
3. WHEN form submissions occur THEN they SHALL process the existing data structures
4. WHEN pricing is calculated THEN it SHALL return data in the format expected by PriceSummary component
5. WHEN errors occur THEN they SHALL display using the existing toast notification system
6. WHEN the application loads THEN all existing routes and navigation SHALL work unchanged

### Requirement 12: Data Migration and Seeding

**User Story:** As a system administrator, I want proper data seeding and migration capabilities, so that the application starts with necessary configuration data.

#### Acceptance Criteria

1. WHEN the application is installed THEN it SHALL seed default services (umzug, putzservice, entruempelung)
2. WHEN settings are initialized THEN they SHALL include default pricing and configuration values
3. WHEN the database is migrated THEN it SHALL preserve any existing data
4. WHEN test data is needed THEN it SHALL provide factory classes for development
5. WHEN the application starts THEN it SHALL have all necessary configuration for immediate use