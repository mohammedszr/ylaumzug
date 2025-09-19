<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            // General Settings
            [
                'group_name' => 'general',
                'key_name' => 'calculator_enabled',
                'value' => '1',
                'type' => 'boolean',
                'description' => 'Enable or disable the pricing calculator',
                'is_public' => true
            ],
            [
                'group_name' => 'general',
                'key_name' => 'business_email',
                'value' => 'info@yla-umzug.de',
                'type' => 'string',
                'description' => 'Main business email address',
                'is_public' => false
            ],
            [
                'group_name' => 'general',
                'key_name' => 'business_phone',
                'value' => '+49 1575 0693353',
                'type' => 'string',
                'description' => 'Business phone number',
                'is_public' => true
            ],
            [
                'group_name' => 'general',
                'key_name' => 'service_areas',
                'value' => '["66111", "66112", "66113", "67655", "67656", "54290", "54292"]',
                'type' => 'json',
                'description' => 'Postal codes served',
                'is_public' => false
            ],
            [
                'group_name' => 'general',
                'key_name' => 'max_service_distance',
                'value' => '100',
                'type' => 'integer',
                'description' => 'Maximum service distance in km',
                'is_public' => false
            ],

            // Pricing Settings
            [
                'group_name' => 'pricing',
                'key_name' => 'minimum_order_value',
                'value' => '150',
                'type' => 'decimal',
                'description' => 'Minimum order value for any service',
                'is_public' => false
            ],
            
            // Umzug Pricing
            [
                'group_name' => 'pricing',
                'key_name' => 'umzug.base_price',
                'value' => '150',
                'type' => 'decimal',
                'description' => 'Base price for moving service',
                'is_public' => false
            ],
            [
                'group_name' => 'pricing',
                'key_name' => 'umzug.price_per_room',
                'value' => '50',
                'type' => 'decimal',
                'description' => 'Price per room for moving',
                'is_public' => false
            ],
            [
                'group_name' => 'pricing',
                'key_name' => 'umzug.floor_surcharge',
                'value' => '25',
                'type' => 'decimal',
                'description' => 'Surcharge per floor above 2nd',
                'is_public' => false
            ],
            [
                'group_name' => 'pricing',
                'key_name' => 'umzug.free_distance_km',
                'value' => '30',
                'type' => 'integer',
                'description' => 'Free distance in km',
                'is_public' => false
            ],
            [
                'group_name' => 'pricing',
                'key_name' => 'umzug.price_per_km',
                'value' => '1.5',
                'type' => 'decimal',
                'description' => 'Price per km after free distance',
                'is_public' => false
            ],
            
            // Putzservice Pricing
            [
                'group_name' => 'pricing',
                'key_name' => 'putzservice.base_price',
                'value' => '80',
                'type' => 'decimal',
                'description' => 'Base price for cleaning service',
                'is_public' => false
            ],
            [
                'group_name' => 'pricing',
                'key_name' => 'putzservice.price_per_room',
                'value' => '30',
                'type' => 'decimal',
                'description' => 'Price per room for cleaning',
                'is_public' => false
            ],
            [
                'group_name' => 'pricing',
                'key_name' => 'putzservice.deep_cleaning_surcharge',
                'value' => '50',
                'type' => 'decimal',
                'description' => 'Surcharge for deep cleaning',
                'is_public' => false
            ],
            [
                'group_name' => 'pricing',
                'key_name' => 'putzservice.construction_cleaning_surcharge',
                'value' => '100',
                'type' => 'decimal',
                'description' => 'Surcharge for construction cleaning',
                'is_public' => false
            ],
            
            // Entrümpelung Pricing
            [
                'group_name' => 'pricing',
                'key_name' => 'entruempelung.base_price',
                'value' => '120',
                'type' => 'decimal',
                'description' => 'Base price for decluttering service',
                'is_public' => false
            ],
            [
                'group_name' => 'pricing',
                'key_name' => 'entruempelung.price_per_volume',
                'value' => '40',
                'type' => 'decimal',
                'description' => 'Price per volume unit',
                'is_public' => false
            ],
            [
                'group_name' => 'pricing',
                'key_name' => 'entruempelung.house_surcharge',
                'value' => '100',
                'type' => 'decimal',
                'description' => 'Surcharge for house decluttering',
                'is_public' => false
            ],
            [
                'group_name' => 'pricing',
                'key_name' => 'entruempelung.basement_surcharge',
                'value' => '50',
                'type' => 'decimal',
                'description' => 'Surcharge for basement decluttering',
                'is_public' => false
            ],
            
            // Discount Settings
            [
                'group_name' => 'pricing',
                'key_name' => 'discounts.two_services',
                'value' => '10',
                'type' => 'integer',
                'description' => 'Discount percentage for 2 services',
                'is_public' => false
            ],
            [
                'group_name' => 'pricing',
                'key_name' => 'discounts.three_plus_services',
                'value' => '15',
                'type' => 'integer',
                'description' => 'Discount percentage for 3+ services',
                'is_public' => false
            ],
            [
                'group_name' => 'pricing',
                'key_name' => 'surcharges.express',
                'value' => '20',
                'type' => 'integer',
                'description' => 'Express surcharge percentage',
                'is_public' => false
            ],
            [
                'group_name' => 'pricing',
                'key_name' => 'combination_discount_2_services',
                'value' => '0.10',
                'type' => 'decimal',
                'description' => 'Discount rate for 2 services (10%)',
                'is_public' => false
            ],
            [
                'group_name' => 'pricing',
                'key_name' => 'combination_discount_3_services',
                'value' => '0.15',
                'type' => 'decimal',
                'description' => 'Discount rate for 3+ services (15%)',
                'is_public' => false
            ],
            [
                'group_name' => 'pricing',
                'key_name' => 'express_surcharge',
                'value' => '0.20',
                'type' => 'decimal',
                'description' => 'Surcharge rate for express service (20%)',
                'is_public' => false
            ],

            // Moving Service Settings
            [
                'group_name' => 'moving',
                'key_name' => 'base_price_per_sqm',
                'value' => '8.0',
                'type' => 'decimal',
                'description' => 'Base price per square meter for moving',
                'is_public' => false
            ],
            [
                'group_name' => 'moving',
                'key_name' => 'distance_rate',
                'value' => '2.0',
                'type' => 'decimal',
                'description' => 'Price per kilometer distance',
                'is_public' => false
            ],
            [
                'group_name' => 'moving',
                'key_name' => 'minimum_distance_free',
                'value' => '30',
                'type' => 'integer',
                'description' => 'Free distance in km before charges apply',
                'is_public' => false
            ],
            [
                'group_name' => 'moving',
                'key_name' => 'floor_surcharge',
                'value' => '50.0',
                'type' => 'decimal',
                'description' => 'Surcharge per floor above 2nd (no elevator)',
                'is_public' => false
            ],
            [
                'group_name' => 'moving',
                'key_name' => 'minimum_moving_cost',
                'value' => '300',
                'type' => 'decimal',
                'description' => 'Minimum cost for moving service',
                'is_public' => false
            ],

            // Cleaning Service Settings
            [
                'group_name' => 'cleaning',
                'key_name' => 'cleaning_rate_normal',
                'value' => '3.0',
                'type' => 'decimal',
                'description' => 'Rate per m² for normal cleaning',
                'is_public' => false
            ],
            [
                'group_name' => 'cleaning',
                'key_name' => 'cleaning_rate_deep',
                'value' => '5.0',
                'type' => 'decimal',
                'description' => 'Rate per m² for deep cleaning',
                'is_public' => false
            ],
            [
                'group_name' => 'cleaning',
                'key_name' => 'cleaning_rate_construction',
                'value' => '7.0',
                'type' => 'decimal',
                'description' => 'Rate per m² for construction cleaning',
                'is_public' => false
            ],
            [
                'group_name' => 'cleaning',
                'key_name' => 'minimum_cleaning_cost',
                'value' => '150',
                'type' => 'decimal',
                'description' => 'Minimum cost for cleaning service',
                'is_public' => false
            ],
            [
                'group_name' => 'cleaning',
                'key_name' => 'frequency_discount_weekly',
                'value' => '0.20',
                'type' => 'decimal',
                'description' => 'Discount rate for weekly cleaning (20%)',
                'is_public' => false
            ],
            [
                'group_name' => 'cleaning',
                'key_name' => 'frequency_discount_biweekly',
                'value' => '0.15',
                'type' => 'decimal',
                'description' => 'Discount rate for bi-weekly cleaning (15%)',
                'is_public' => false
            ],
            [
                'group_name' => 'cleaning',
                'key_name' => 'frequency_discount_monthly',
                'value' => '0.10',
                'type' => 'decimal',
                'description' => 'Discount rate for monthly cleaning (10%)',
                'is_public' => false
            ],

            // Decluttering Service Settings
            [
                'group_name' => 'decluttering',
                'key_name' => 'declutter_volume_low',
                'value' => '300',
                'type' => 'decimal',
                'description' => 'Price for low volume decluttering (1-2 containers)',
                'is_public' => false
            ],
            [
                'group_name' => 'decluttering',
                'key_name' => 'declutter_volume_medium',
                'value' => '600',
                'type' => 'decimal',
                'description' => 'Price for medium volume decluttering (3-5 containers)',
                'is_public' => false
            ],
            [
                'group_name' => 'decluttering',
                'key_name' => 'declutter_volume_high',
                'value' => '1200',
                'type' => 'decimal',
                'description' => 'Price for high volume decluttering (6+ containers)',
                'is_public' => false
            ],
            [
                'group_name' => 'decluttering',
                'key_name' => 'declutter_volume_extreme',
                'value' => '2000',
                'type' => 'decimal',
                'description' => 'Price for extreme volume decluttering (hoarding)',
                'is_public' => false
            ],
            [
                'group_name' => 'decluttering',
                'key_name' => 'hazardous_waste_surcharge',
                'value' => '150.0',
                'type' => 'decimal',
                'description' => 'Surcharge for hazardous waste disposal',
                'is_public' => false
            ],
            [
                'group_name' => 'decluttering',
                'key_name' => 'electronics_disposal_cost',
                'value' => '100.0',
                'type' => 'decimal',
                'description' => 'Cost for electronics disposal',
                'is_public' => false
            ],
            [
                'group_name' => 'decluttering',
                'key_name' => 'clean_handover_cost',
                'value' => '150.0',
                'type' => 'decimal',
                'description' => 'Cost for clean handover service',
                'is_public' => false
            ],

            // Email Settings
            [
                'group_name' => 'email',
                'key_name' => 'from_address',
                'value' => 'noreply@yla-umzug.de',
                'type' => 'string',
                'description' => 'Default from email address',
                'is_public' => false
            ],
            [
                'group_name' => 'email',
                'key_name' => 'from_name',
                'value' => 'YLA Umzug',
                'type' => 'string',
                'description' => 'Default from name',
                'is_public' => false
            ],
            [
                'group_name' => 'email',
                'key_name' => 'response_time',
                'value' => '24 Stunden',
                'type' => 'string',
                'description' => 'Expected response time for quotes',
                'is_public' => true
            ],

            // API Settings
            [
                'group_name' => 'api',
                'key_name' => 'openroute_api_key',
                'value' => '',
                'type' => 'string',
                'description' => 'OpenRouteService API key for distance calculation',
                'is_public' => false
            ],
            [
                'group_name' => 'api',
                'key_name' => 'whatsapp_access_token',
                'value' => '',
                'type' => 'string',
                'description' => 'WhatsApp Business API access token',
                'is_public' => false
            ],
            [
                'group_name' => 'api',
                'key_name' => 'whatsapp_phone_number_id',
                'value' => '',
                'type' => 'string',
                'description' => 'WhatsApp Business phone number ID',
                'is_public' => false
            ],

            // UI Settings
            [
                'group_name' => 'ui',
                'key_name' => 'company_name',
                'value' => 'YLA Umzug',
                'type' => 'string',
                'description' => 'Company name for display',
                'is_public' => true
            ],
            [
                'group_name' => 'ui',
                'key_name' => 'company_address',
                'value' => 'Musterstraße 123, 66111 Saarbrücken',
                'type' => 'string',
                'description' => 'Company address for display',
                'is_public' => true
            ],
            [
                'group_name' => 'ui',
                'key_name' => 'show_pricing_disclaimer',
                'value' => '1',
                'type' => 'boolean',
                'description' => 'Show pricing disclaimer on calculator',
                'is_public' => true
            ]
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(
                [
                    'group_name' => $setting['group_name'],
                    'key_name' => $setting['key_name']
                ],
                $setting
            );
        }
    }
}