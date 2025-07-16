<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Service;
use App\Models\PricingRule;
use App\Models\AdditionalService;

class ServicesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * This seeder creates the initial services, pricing rules, and additional services
     * for the YLA Umzug calculator system.
     * 
     * @see backend/ADMIN_CONFIGURATION.md for customization instructions
     */
    public function run(): void
    {
        // Create main services
        $this->createServices();
        
        // Create pricing rules for each service
        $this->createPricingRules();
        
        // Create additional services
        $this->createAdditionalServices();
    }

    /**
     * Create the main services (Umzug, Entrümpelung, Putzservice)
     */
    private function createServices(): void
    {
        $services = [
            [
                'key' => 'umzug',
                'name' => 'Umzug',
                'description' => 'Professioneller Umzugsservice mit Verpackung und Transport',
                'base_price' => 300.00,
                'is_active' => true,
                'sort_order' => 1,
                'configuration' => [
                    'requires_addresses' => true,
                    'requires_apartment_size' => true,
                    'supports_additional_services' => true
                ]
            ],
            [
                'key' => 'entruempelung',
                'name' => 'Entrümpelung',
                'description' => 'Haushaltsauflösung und fachgerechte Entsorgung',
                'base_price' => 300.00,
                'is_active' => true,
                'sort_order' => 2,
                'configuration' => [
                    'requires_volume_estimation' => true,
                    'supports_waste_types' => true,
                    'supports_clean_handover' => true
                ]
            ],
            [
                'key' => 'putzservice',
                'name' => 'Putzservice',
                'description' => 'Grundreinigung und besenreine Übergabe',
                'base_price' => 150.00,
                'is_active' => true,
                'sort_order' => 3,
                'configuration' => [
                    'requires_size' => true,
                    'supports_intensity_levels' => true,
                    'supports_frequency' => true
                ]
            ]
        ];

        foreach ($services as $serviceData) {
            Service::create($serviceData);
        }
    }

    /**
     * Create pricing rules for all services
     * 
     * These rules define how prices are calculated based on various factors.
     * See ADMIN_CONFIGURATION.md for how to modify these rules.
     */
    private function createPricingRules(): void
    {
        $umzugService = Service::where('key', 'umzug')->first();
        $entruempelungService = Service::where('key', 'entruempelung')->first();
        $putzService = Service::where('key', 'putzservice')->first();

        // Umzug pricing rules
        $this->createUmzugPricingRules($umzugService);
        
        // Entrümpelung pricing rules
        $this->createEntruempelungPricingRules($entruempelungService);
        
        // Putzservice pricing rules
        $this->createPutzservicePricingRules($putzService);
    }

    /**
     * Create pricing rules for Umzug service
     */
    private function createUmzugPricingRules(Service $service): void
    {
        $rules = [
            // Base price per square meter
            [
                'rule_type' => 'base',
                'rule_key' => 'apartmentSize',
                'price_value' => 8.00,
                'price_type' => 'per_unit',
                'unit' => 'm²',
                'description' => 'Grundpreis pro Quadratmeter',
                'priority' => 100
            ],
            // Distance calculation
            [
                'rule_type' => 'distance',
                'rule_key' => 'distance_km',
                'price_value' => 2.00,
                'price_type' => 'per_unit',
                'unit' => 'km',
                'description' => 'Entfernungskosten',
                'priority' => 90
            ],
            // Box handling
            [
                'rule_type' => 'addon',
                'rule_key' => 'boxes',
                'price_value' => 3.00,
                'price_type' => 'per_unit',
                'unit' => 'Stück',
                'description' => 'Karton-Service',
                'priority' => 80
            ],
            // Floor surcharge (no elevator)
            [
                'rule_type' => 'modifier',
                'rule_key' => 'floor_surcharge',
                'condition_operator' => '>',
                'condition_values' => [2],
                'price_value' => 50.00,
                'price_type' => 'per_unit',
                'unit' => 'Etage',
                'description' => 'Treppenaufschlag ab 3. Stock',
                'priority' => 70
            ]
        ];

        foreach ($rules as $ruleData) {
            PricingRule::create(array_merge($ruleData, [
                'service_id' => $service->id,
                'is_active' => true
            ]));
        }
    }

    /**
     * Create pricing rules for Entrümpelung service
     */
    private function createEntruempelungPricingRules(Service $service): void
    {
        $rules = [
            // Volume-based pricing
            [
                'rule_type' => 'base',
                'rule_key' => 'volume',
                'condition_operator' => '=',
                'condition_values' => ['low'],
                'price_value' => 300.00,
                'price_type' => 'fixed',
                'description' => 'Geringes Volumen (1-2 Container)',
                'priority' => 100
            ],
            [
                'rule_type' => 'base',
                'rule_key' => 'volume',
                'condition_operator' => '=',
                'condition_values' => ['medium'],
                'price_value' => 600.00,
                'price_type' => 'fixed',
                'description' => 'Mittleres Volumen (3-5 Container)',
                'priority' => 100
            ],
            [
                'rule_type' => 'base',
                'rule_key' => 'volume',
                'condition_operator' => '=',
                'condition_values' => ['high'],
                'price_value' => 1200.00,
                'price_type' => 'fixed',
                'description' => 'Hohes Volumen (6+ Container)',
                'priority' => 100
            ],
            [
                'rule_type' => 'base',
                'rule_key' => 'volume',
                'condition_operator' => '=',
                'condition_values' => ['extreme'],
                'price_value' => 2000.00,
                'price_type' => 'fixed',
                'description' => 'Extremes Volumen (Messi-Haushalt)',
                'priority' => 100
            ],
            // Hazardous waste surcharge
            [
                'rule_type' => 'addon',
                'rule_key' => 'hazardous_waste',
                'price_value' => 150.00,
                'price_type' => 'fixed',
                'description' => 'Sondermüll-Zuschlag',
                'priority' => 90
            ],
            // Electronics disposal
            [
                'rule_type' => 'addon',
                'rule_key' => 'electronics',
                'price_value' => 100.00,
                'price_type' => 'fixed',
                'description' => 'Elektrogeräte-Entsorgung',
                'priority' => 85
            ],
            // Clean handover
            [
                'rule_type' => 'addon',
                'rule_key' => 'clean_handover',
                'price_value' => 150.00,
                'price_type' => 'fixed',
                'description' => 'Besenreine Übergabe',
                'priority' => 80
            ]
        ];

        foreach ($rules as $ruleData) {
            PricingRule::create(array_merge($ruleData, [
                'service_id' => $service->id,
                'is_active' => true
            ]));
        }
    }

    /**
     * Create pricing rules for Putzservice
     */
    private function createPutzservicePricingRules(Service $service): void
    {
        $rules = [
            // Normal cleaning
            [
                'rule_type' => 'base',
                'rule_key' => 'size',
                'condition_operator' => '=',
                'condition_values' => ['normal'],
                'price_value' => 3.00,
                'price_type' => 'per_unit',
                'unit' => 'm²',
                'description' => 'Normalreinigung',
                'priority' => 100
            ],
            // Deep cleaning
            [
                'rule_type' => 'base',
                'rule_key' => 'size',
                'condition_operator' => '=',
                'condition_values' => ['deep'],
                'price_value' => 5.00,
                'price_type' => 'per_unit',
                'unit' => 'm²',
                'description' => 'Grundreinigung',
                'priority' => 100
            ],
            // Construction cleaning
            [
                'rule_type' => 'base',
                'rule_key' => 'size',
                'condition_operator' => '=',
                'condition_values' => ['construction'],
                'price_value' => 7.00,
                'price_type' => 'per_unit',
                'unit' => 'm²',
                'description' => 'Bauschlussreinigung',
                'priority' => 100
            ],
            // Window cleaning addon
            [
                'rule_type' => 'addon',
                'rule_key' => 'windows',
                'price_value' => 2.00,
                'price_type' => 'per_unit',
                'unit' => 'm²',
                'description' => 'Fensterreinigung',
                'priority' => 90
            ],
            // Regular service discount
            [
                'rule_type' => 'modifier',
                'rule_key' => 'frequency_discount',
                'condition_operator' => 'not_in',
                'condition_values' => ['once'],
                'price_value' => -0.15,
                'price_type' => 'multiplier',
                'description' => 'Regelmäßigkeitsrabatt (15%)',
                'priority' => 80
            ]
        ];

        foreach ($rules as $ruleData) {
            PricingRule::create(array_merge($ruleData, [
                'service_id' => $service->id,
                'is_active' => true
            ]));
        }
    }

    /**
     * Create additional services for each main service
     */
    private function createAdditionalServices(): void
    {
        $umzugService = Service::where('key', 'umzug')->first();
        
        $additionalServices = [
            [
                'service_id' => $umzugService->id,
                'key' => 'assembly',
                'name' => 'Möbelabbau & Aufbau',
                'description' => 'Professioneller Abbau und Aufbau Ihrer Möbel',
                'price' => 200.00,
                'price_type' => 'fixed',
                'sort_order' => 1
            ],
            [
                'service_id' => $umzugService->id,
                'key' => 'packing',
                'name' => 'Verpackungsservice',
                'description' => 'Professionelle Verpackung Ihrer Gegenstände',
                'price' => 150.00,
                'price_type' => 'fixed',
                'sort_order' => 2
            ],
            [
                'service_id' => $umzugService->id,
                'key' => 'parking',
                'name' => 'Halteverbotszone beantragen',
                'description' => 'Beantragung einer Halteverbotszone',
                'price' => 80.00,
                'price_type' => 'fixed',
                'sort_order' => 3
            ],
            [
                'service_id' => $umzugService->id,
                'key' => 'storage',
                'name' => 'Einlagerung',
                'description' => 'Temporäre Einlagerung Ihrer Gegenstände',
                'price' => 100.00,
                'price_type' => 'per_m2',
                'unit' => 'm²',
                'sort_order' => 4
            ],
            [
                'service_id' => $umzugService->id,
                'key' => 'disposal',
                'name' => 'Entsorgung alter Möbel',
                'description' => 'Fachgerechte Entsorgung nicht benötigter Möbel',
                'price' => 120.00,
                'price_type' => 'fixed',
                'sort_order' => 5
            ]
        ];

        foreach ($additionalServices as $serviceData) {
            AdditionalService::create(array_merge($serviceData, [
                'is_active' => true
            ]));
        }
    }
}