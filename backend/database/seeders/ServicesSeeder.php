<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Service;

class ServicesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * This seeder creates the initial services with pricing configuration
     * for the YLA Umzug calculator system.
     */
    public function run(): void
    {
        // Create main services with pricing configuration
        $this->createServices();
    }

    /**
     * Create the main services (Umzug, Entrümpelung, Putzservice)
     */
    private function createServices(): void
    {
        $services = [
            [
                'name' => 'Umzug',
                'slug' => 'umzug',
                'description' => 'Professioneller Umzugsservice mit Verpackung und Transport',
                'base_price' => 300.00,
                'price_per_unit' => 8.00,
                'unit_type' => 'sqm',
                'is_active' => true,
                'sort_order' => 1,
                'pricing_config' => [
                    'room_multiplier' => 0.2,
                    'floor_cost' => 50.00,
                    'distance_rate' => 2.00,
                    'minimum_distance_free' => 30,
                    'size_multipliers' => [
                        'studio' => 0.8,
                        '1-room' => 1.0,
                        '2-rooms' => 1.3,
                        '3-rooms' => 1.6,
                        '4-rooms' => 2.0,
                        '5-rooms' => 2.5,
                        'house' => 3.0
                    ]
                ]
            ],
            [
                'name' => 'Entrümpelung',
                'slug' => 'entruempelung',
                'description' => 'Haushaltsauflösung und fachgerechte Entsorgung',
                'base_price' => 300.00,
                'price_per_unit' => 0.00,
                'unit_type' => 'fixed',
                'is_active' => true,
                'sort_order' => 2,
                'pricing_config' => [
                    'volume_pricing' => [
                        'low' => 300.00,
                        'medium' => 600.00,
                        'high' => 1200.00,
                        'extreme' => 2000.00
                    ],
                    'surcharges' => [
                        'hazardous_waste' => 150.00,
                        'electronics' => 100.00,
                        'clean_handover' => 150.00
                    ]
                ]
            ],
            [
                'name' => 'Putzservice',
                'slug' => 'putzservice',
                'description' => 'Grundreinigung und besenreine Übergabe',
                'base_price' => 150.00,
                'price_per_unit' => 3.00,
                'unit_type' => 'sqm',
                'is_active' => true,
                'sort_order' => 3,
                'pricing_config' => [
                    'intensity_multipliers' => [
                        'normal' => 1.0,
                        'deep' => 1.67,
                        'construction' => 2.33
                    ],
                    'frequency_discounts' => [
                        'weekly' => 0.20,
                        'biweekly' => 0.15,
                        'monthly' => 0.10
                    ],
                    'room_surcharges' => [
                        'windows' => 2.00,
                        'kitchen_deep' => 80.00,
                        'bathroom_deep' => 60.00
                    ]
                ]
            ]
        ];

        foreach ($services as $serviceData) {
            Service::create($serviceData);
        }
    }


}