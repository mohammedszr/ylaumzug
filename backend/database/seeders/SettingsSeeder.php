<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * This seeder creates the initial system settings for the YLA Umzug application.
     * 
     * @see backend/ADMIN_CONFIGURATION.md for customization instructions
     */
    public function run(): void
    {
        $settings = [
            // Calculator Settings
            [
                'key' => 'calculator_enabled',
                'value' => '1',
                'type' => 'boolean',
                'group' => 'calculator',
                'description' => 'Enable or disable the cost calculator',
                'is_public' => true
            ],
            [
                'key' => 'calculator_maintenance_message',
                'value' => 'Der Kostenrechner ist vorübergehend nicht verfügbar. Bitte kontaktieren Sie uns direkt für ein Angebot.',
                'type' => 'string',
                'group' => 'calculator',
                'description' => 'Message shown when calculator is disabled',
                'is_public' => true
            ],
            
            // Business Information
            [
                'key' => 'business_name',
                'value' => 'YLA Umzug',
                'type' => 'string',
                'group' => 'business',
                'description' => 'Company name',
                'is_public' => true
            ],
            [
                'key' => 'business_email',
                'value' => 'info@yla-umzug.de',
                'type' => 'string',
                'group' => 'business',
                'description' => 'Main business email address',
                'is_public' => false
            ],
            [
                'key' => 'business_phone',
                'value' => '+49 123 456789',
                'type' => 'string',
                'group' => 'business',
                'description' => 'Main business phone number',
                'is_public' => true
            ],
            [
                'key' => 'business_address',
                'value' => 'Musterstraße 123, 66111 Saarbrücken',
                'type' => 'string',
                'group' => 'business',
                'description' => 'Business address',
                'is_public' => true
            ],
            
            // Email Settings
            [
                'key' => 'quote_notification_email',
                'value' => 'quotes@yla-umzug.de',
                'type' => 'string',
                'group' => 'email',
                'description' => 'Email address for quote notifications',
                'is_public' => false
            ],
            [
                'key' => 'auto_reply_enabled',
                'value' => '1',
                'type' => 'boolean',
                'group' => 'email',
                'description' => 'Send automatic confirmation emails to customers',
                'is_public' => false
            ],
            
            // Pricing Settings
            [
                'key' => 'combination_discount_2_services',
                'value' => '0.10',
                'type' => 'decimal',
                'group' => 'pricing',
                'description' => 'Discount percentage for 2 services (0.10 = 10%)',
                'is_public' => false
            ],
            [
                'key' => 'combination_discount_3_services',
                'value' => '0.15',
                'type' => 'decimal',
                'group' => 'pricing',
                'description' => 'Discount percentage for 3+ services (0.15 = 15%)',
                'is_public' => false
            ],
            [
                'key' => 'express_surcharge',
                'value' => '0.20',
                'type' => 'decimal',
                'group' => 'pricing',
                'description' => 'Express service surcharge (0.20 = 20%)',
                'is_public' => false
            ],
            [
                'key' => 'minimum_order_value',
                'value' => '150.00',
                'type' => 'decimal',
                'group' => 'pricing',
                'description' => 'Minimum order value in EUR',
                'is_public' => false
            ],
            
            // Service Area Settings
            [
                'key' => 'service_areas',
                'value' => '["66111", "66112", "66113", "67655", "67656", "54290", "54292"]',
                'type' => 'json',
                'group' => 'service',
                'description' => 'Postal codes within service area',
                'is_public' => true
            ],
            [
                'key' => 'max_service_distance',
                'value' => '100',
                'type' => 'integer',
                'group' => 'service',
                'description' => 'Maximum service distance in kilometers',
                'is_public' => false
            ],
            
            // Website Settings
            [
                'key' => 'maintenance_mode',
                'value' => '0',
                'type' => 'boolean',
                'group' => 'website',
                'description' => 'Enable maintenance mode',
                'is_public' => true
            ],
            [
                'key' => 'maintenance_message',
                'value' => 'Unsere Website wird gerade gewartet. Bitte versuchen Sie es später erneut.',
                'type' => 'string',
                'group' => 'website',
                'description' => 'Maintenance mode message',
                'is_public' => true
            ]
        ];

        foreach ($settings as $settingData) {
            Setting::create($settingData);
        }
    }
}