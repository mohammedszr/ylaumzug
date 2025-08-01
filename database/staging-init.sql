-- Staging database initialization script

-- Create database if not exists
CREATE DATABASE IF NOT EXISTS yla_staging CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Use the staging database
USE yla_staging;

-- Create user if not exists
CREATE USER IF NOT EXISTS 'yla_user'@'%' IDENTIFIED BY 'staging_password';
GRANT ALL PRIVILEGES ON yla_staging.* TO 'yla_user'@'%';
FLUSH PRIVILEGES;

-- Insert test data for staging environment
-- This will be populated after Laravel migrations run

-- Test services
INSERT IGNORE INTO services (id, `key`, name, description, base_price, is_active, sort_order, created_at, updated_at) VALUES
(1, 'umzug', 'Umzug', 'Professioneller Umzugsservice', 300.00, 1, 1, NOW(), NOW()),
(2, 'entruempelung', 'Entrümpelung', 'Haushaltsauflösung und Entsorgung', 300.00, 1, 2, NOW(), NOW()),
(3, 'putzservice', 'Putzservice', 'Grundreinigung und besenreine Übergabe', 150.00, 1, 3, NOW(), NOW());

-- Test settings
INSERT IGNORE INTO settings (`key`, value, type, created_at, updated_at) VALUES
('calculator_enabled', '1', 'boolean', NOW(), NOW()),
('minimum_order_value', '150', 'integer', NOW(), NOW()),
('distance_rate_per_km', '2.0', 'decimal', NOW(), NOW()),
('floor_surcharge_rate', '50.0', 'decimal', NOW(), NOW()),
('declutter_floor_rate', '30.0', 'decimal', NOW(), NOW()),
('combination_discount_2_services', '0.10', 'decimal', NOW(), NOW()),
('combination_discount_3_services', '0.15', 'decimal', NOW(), NOW()),
('express_surcharge', '0.20', 'decimal', NOW(), NOW()),
('hazardous_waste_surcharge', '150.0', 'decimal', NOW(), NOW()),
('electronics_disposal_cost', '100.0', 'decimal', NOW(), NOW()),
('furniture_disposal_cost', '80.0', 'decimal', NOW(), NOW()),
('access_difficulty_surcharge', '100.0', 'decimal', NOW(), NOW()),
('cleaning_rate_normal', '3.0', 'decimal', NOW(), NOW()),
('cleaning_rate_deep', '5.0', 'decimal', NOW(), NOW()),
('cleaning_rate_construction', '7.0', 'decimal', NOW(), NOW()),
('window_cleaning_rate', '2.0', 'decimal', NOW(), NOW()),
('regular_cleaning_discount', '0.15', 'decimal', NOW(), NOW()),
('declutter_volume_low', '300', 'integer', NOW(), NOW()),
('declutter_volume_medium', '600', 'integer', NOW(), NOW()),
('declutter_volume_high', '1200', 'integer', NOW(), NOW()),
('declutter_volume_extreme', '2000', 'integer', NOW(), NOW());

-- Test quote requests for staging
INSERT IGNORE INTO quote_requests (id, quote_number, name, email, phone, message, selected_services, service_details, estimated_total, pricing_breakdown, status, created_at, updated_at) VALUES
(1, 'Q-STAGING-001', 'Test User', 'test@staging.com', '+49 123 456789', 'Test quote for staging', '["umzug"]', '{"movingDetails":{"apartmentSize":80,"fromAddress":{"city":"Berlin"},"toAddress":{"city":"München"}}}', 450.00, '[]', 'new', NOW(), NOW()),
(2, 'Q-STAGING-002', 'Demo Customer', 'demo@staging.com', '+49 987 654321', 'Demo multi-service quote', '["umzug","putzservice"]', '{"movingDetails":{"apartmentSize":100},"cleaningDetails":{"size":100}}', 650.00, '[]', 'processing', NOW(), NOW());