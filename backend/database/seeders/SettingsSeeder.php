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
            // Calculator Control
            [
                'key' => 'calculator_enabled',
                'value' => '1',
                'type' => 'boolean',
                'group' => 'calculator',
                'description' => 'Enable or disable the pricing calculator',
                'is_public' => true
            ],
            
            // Business Information
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
                'value' => '+49 1575 0693353',
                'type' => 'string',
                'group' => 'business',
                'description' => 'Business phone number',
                'is_public' => true
            ],
            
            // Service Areas
            [
                'key' => 'service_areas',
                'value' => '["66111", "66112", "66113", "67655", "67656", "54290", "54292"]',
                'type' => 'json',
                'group' => 'business',
                'description' => 'Postal codes served',
                'is_public' => false
            ],
            [
                'key' => 'max_service_distance',
                'value' => '100',
                'type' => 'integer',
                'group' => 'business',
                'description' => 'Maximum service distance in km',
                'is_public' => false
            ],
            
            // Moving Service Pricing
            [
                'key' => 'base_price_per_sqm',
                'value' => '8.0',
                'type' => 'decimal',
                'group' => 'moving',
                'description' => 'Base price per square meter for moving',
                'is_public' => false
            ],
            [
                'key' => 'distance_rate',
                'value' => '2.0',
                'type' => 'decimal',
                'group' => 'moving',
                'description' => 'Price per kilometer distance',
                'is_public' => false
            ],
            [
                'key' => 'box_handling_rate',
                'value' => '3.0',
                'type' => 'decimal',
                'group' => 'moving',
                'description' => 'Price per moving box',
                'is_public' => false
            ],
            [
                'key' => 'floor_surcharge',
                'value' => '50.0',
                'type' => 'decimal',
                'group' => 'moving',
                'description' => 'Surcharge per floor above 2nd (no elevator)',
                'is_public' => false
            ],
            [
                'key' => 'minimum_moving_cost',
                'value' => '300',
                'type' => 'decimal',
                'group' => 'moving',
                'description' => 'Minimum cost for moving service',
                'is_public' => false
            ],
            
            // Moving Additional Services
            [
                'key' => 'furniture_assembly_cost',
                'value' => '200.0',
                'type' => 'decimal',
                'group' => 'moving',
                'description' => 'Cost for furniture assembly/disassembly',
                'is_public' => false
            ],
            [
                'key' => 'packing_service_cost',
                'value' => '150.0',
                'type' => 'decimal',
                'group' => 'moving',
                'description' => 'Cost for packing service',
                'is_public' => false
            ],
            [
                'key' => 'parking_permit_cost',
                'value' => '80.0',
                'type' => 'decimal',
                'group' => 'moving',
                'description' => 'Cost for parking permit arrangement',
                'is_public' => false
            ],
            [
                'key' => 'storage_service_cost',
                'value' => '100.0',
                'type' => 'decimal',
                'group' => 'moving',
                'description' => 'Cost for storage service per m²',
                'is_public' => false
            ],
            [
                'key' => 'disposal_service_cost',
                'value' => '120.0',
                'type' => 'decimal',
                'group' => 'moving',
                'description' => 'Cost for disposal service',
                'is_public' => false
            ],
            
            // Decluttering Service Pricing
            [
                'key' => 'declutter_volume_low',
                'value' => '300',
                'type' => 'decimal',
                'group' => 'decluttering',
                'description' => 'Price for low volume decluttering (1-2 containers)',
                'is_public' => false
            ],
            [
                'key' => 'declutter_volume_medium',
                'value' => '600',
                'type' => 'decimal',
                'group' => 'decluttering',
                'description' => 'Price for medium volume decluttering (3-5 containers)',
                'is_public' => false
            ],
            [
                'key' => 'declutter_volume_high',
                'value' => '1200',
                'type' => 'decimal',
                'group' => 'decluttering',
                'description' => 'Price for high volume decluttering (6+ containers)',
                'is_public' => false
            ],
            [
                'key' => 'declutter_volume_extreme',
                'value' => '2000',
                'type' => 'decimal',
                'group' => 'decluttering',
                'description' => 'Price for extreme volume decluttering (hoarding)',
                'is_public' => false
            ],
            [
                'key' => 'minimum_declutter_cost',
                'value' => '300',
                'type' => 'decimal',
                'group' => 'decluttering',
                'description' => 'Minimum cost for decluttering service',
                'is_public' => false
            ],
            
            // Decluttering Surcharges
            [
                'key' => 'hazardous_waste_surcharge',
                'value' => '150.0',
                'type' => 'decimal',
                'group' => 'decluttering',
                'description' => 'Surcharge for hazardous waste disposal',
                'is_public' => false
            ],
            [
                'key' => 'electronics_disposal_cost',
                'value' => '100.0',
                'type' => 'decimal',
                'group' => 'decluttering',
                'description' => 'Cost for electronics disposal',
                'is_public' => false
            ],
            [
                'key' => 'construction_waste_cost',
                'value' => '200.0',
                'type' => 'decimal',
                'group' => 'decluttering',
                'description' => 'Cost for construction waste disposal',
                'is_public' => false
            ],
            [
                'key' => 'furniture_disposal_cost',
                'value' => '80.0',
                'type' => 'decimal',
                'group' => 'decluttering',
                'description' => 'Cost for furniture disposal',
                'is_public' => false
            ],
            [
                'key' => 'declutter_floor_rate',
                'value' => '30.0',
                'type' => 'decimal',
                'group' => 'decluttering',
                'description' => 'Floor surcharge rate for decluttering',
                'is_public' => false
            ],
            [
                'key' => 'access_difficulty_surcharge',
                'value' => '100.0',
                'type' => 'decimal',
                'group' => 'decluttering',
                'description' => 'Surcharge for difficult access',
                'is_public' => false
            ],
            [
                'key' => 'clean_handover_cost',
                'value' => '150.0',
                'type' => 'decimal',
                'group' => 'decluttering',
                'description' => 'Cost for clean handover service',
                'is_public' => false
            ],
            
            // Cleaning Service Pricing
            [
                'key' => 'cleaning_rate_normal',
                'value' => '3.0',
                'type' => 'decimal',
                'group' => 'cleaning',
                'description' => 'Rate per m² for normal cleaning',
                'is_public' => false
            ],
            [
                'key' => 'cleaning_rate_deep',
                'value' => '5.0',
                'type' => 'decimal',
                'group' => 'cleaning',
                'description' => 'Rate per m² for deep cleaning',
                'is_public' => false
            ],
            [
                'key' => 'cleaning_rate_construction',
                'value' => '7.0',
                'type' => 'decimal',
                'group' => 'cleaning',
                'description' => 'Rate per m² for construction cleaning',
                'is_public' => false
            ],
            [
                'key' => 'cleaning_rate_moveout',
                'value' => '6.0',
                'type' => 'decimal',
                'group' => 'cleaning',
                'description' => 'Rate per m² for move-out cleaning',
                'is_public' => false
            ],
            [
                'key' => 'minimum_cleaning_cost',
                'value' => '150',
                'type' => 'decimal',
                'group' => 'cleaning',
                'description' => 'Minimum cost for cleaning service',
                'is_public' => false
            ],
            
            // Cleaning Room Surcharges
            [
                'key' => 'window_cleaning_rate',
                'value' => '2.0',
                'type' => 'decimal',
                'group' => 'cleaning',
                'description' => 'Rate per m² for window cleaning',
                'is_public' => false
            ],
            [
                'key' => 'kitchen_deep_clean_cost',
                'value' => '80.0',
                'type' => 'decimal',
                'group' => 'cleaning',
                'description' => 'Fixed cost for deep kitchen cleaning',
                'is_public' => false
            ],
            [
                'key' => 'bathroom_deep_clean_cost',
                'value' => '60.0',
                'type' => 'decimal',
                'group' => 'cleaning',
                'description' => 'Fixed cost for deep bathroom cleaning',
                'is_public' => false
            ],
            [
                'key' => 'balcony_clean_cost',
                'value' => '40.0',
                'type' => 'decimal',
                'group' => 'cleaning',
                'description' => 'Fixed cost for balcony cleaning',
                'is_public' => false
            ],
            [
                'key' => 'basement_clean_cost',
                'value' => '50.0',
                'type' => 'decimal',
                'group' => 'cleaning',
                'description' => 'Fixed cost for basement cleaning',
                'is_public' => false
            ],
            
            // Cleaning Frequency Discounts
            [
                'key' => 'frequency_discount_weekly',
                'value' => '0.20',
                'type' => 'decimal',
                'group' => 'cleaning',
                'description' => 'Discount rate for weekly cleaning (20%)',
                'is_public' => false
            ],
            [
                'key' => 'frequency_discount_biweekly',
                'value' => '0.15',
                'type' => 'decimal',
                'group' => 'cleaning',
                'description' => 'Discount rate for bi-weekly cleaning (15%)',
                'is_public' => false
            ],
            [
                'key' => 'frequency_discount_monthly',
                'value' => '0.10',
                'type' => 'decimal',
                'group' => 'cleaning',
                'description' => 'Discount rate for monthly cleaning (10%)',
                'is_public' => false
            ],
            
            // Combination Discounts
            [
                'key' => 'combination_discount_2_services',
                'value' => '0.10',
                'type' => 'decimal',
                'group' => 'discounts',
                'description' => 'Discount rate for 2 services (10%)',
                'is_public' => false
            ],
            [
                'key' => 'combination_discount_3_services',
                'value' => '0.15',
                'type' => 'decimal',
                'group' => 'discounts',
                'description' => 'Discount rate for 3+ services (15%)',
                'is_public' => false
            ],
            [
                'key' => 'moving_cleaning_bonus',
                'value' => '50.0',
                'type' => 'decimal',
                'group' => 'discounts',
                'description' => 'Extra bonus for moving + cleaning combination',
                'is_public' => false
            ],
            [
                'key' => 'declutter_cleaning_bonus',
                'value' => '75.0',
                'type' => 'decimal',
                'group' => 'discounts',
                'description' => 'Extra bonus for decluttering + cleaning combination',
                'is_public' => false
            ],
            
            // Express Surcharge
            [
                'key' => 'express_surcharge',
                'value' => '0.20',
                'type' => 'decimal',
                'group' => 'surcharges',
                'description' => 'Surcharge rate for express service (20%)',
                'is_public' => false
            ],
            
            // Minimum Order Values
            [
                'key' => 'minimum_order_value',
                'value' => '150',
                'type' => 'decimal',
                'group' => 'pricing',
                'description' => 'Minimum order value for any service',
                'is_public' => false
            ],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
}