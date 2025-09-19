<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\QuoteRequest;
use App\Models\Service;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class TestDataSeeder extends Seeder
{
    /**
     * Run the database seeds for testing environment.
     */
    public function run(): void
    {
        $this->seedUsers();
        $this->seedServices();
        $this->seedSettings();
        $this->seedQuoteRequests();
    }

    /**
     * Seed test users
     */
    private function seedUsers(): void
    {
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@yla-umzug.de',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'is_active' => true
        ]);

        User::create([
            'name' => 'Manager User',
            'email' => 'manager@yla-umzug.de',
            'password' => Hash::make('password'),
            'role' => 'manager',
            'is_active' => true
        ]);

        User::create([
            'name' => 'Employee User',
            'email' => 'employee@yla-umzug.de',
            'password' => Hash::make('password'),
            'role' => 'employee',
            'is_active' => true
        ]);
    }

    /**
     * Seed test services
     */
    private function seedServices(): void
    {
        Service::create([
            'name' => 'Umzug',
            'slug' => 'umzug',
            'description' => 'Professioneller Umzugsservice für Privat- und Geschäftskunden',
            'base_price' => 150.00,
            'price_per_unit' => 50.00,
            'unit_type' => 'room',
            'is_active' => true,
            'sort_order' => 1,
            'pricing_config' => [
                'floor_surcharge' => 25.00,
                'distance_free_km' => 30,
                'distance_rate' => 1.50
            ]
        ]);

        Service::create([
            'name' => 'Putzservice',
            'slug' => 'putzservice',
            'description' => 'Professionelle Reinigungsdienstleistungen',
            'base_price' => 80.00,
            'price_per_unit' => 30.00,
            'unit_type' => 'room',
            'is_active' => true,
            'sort_order' => 2,
            'pricing_config' => [
                'deep_cleaning_surcharge' => 50.00,
                'construction_cleaning_surcharge' => 100.00
            ]
        ]);

        Service::create([
            'name' => 'Entrümpelung',
            'slug' => 'entruempelung',
            'description' => 'Professionelle Entrümpelung und Entsorgung',
            'base_price' => 120.00,
            'price_per_unit' => 40.00,
            'unit_type' => 'item',
            'is_active' => true,
            'sort_order' => 3,
            'pricing_config' => [
                'volume_pricing' => [
                    'low' => 300.00,
                    'medium' => 600.00,
                    'high' => 1200.00,
                    'extreme' => 2000.00
                ]
            ]
        ]);
    }

    /**
     * Seed test settings
     */
    private function seedSettings(): void
    {
        $settings = [
            // General settings
            ['general', 'calculator_enabled', '1', 'boolean', 'Enable calculator', true],
            ['general', 'business_email', 'info@yla-umzug.de', 'string', 'Business email', false],
            ['general', 'business_phone', '+49 1575 0693353', 'string', 'Business phone', true],
            
            // Pricing settings
            ['pricing', 'minimum_order_value', '150', 'decimal', 'Minimum order value', false],
            ['pricing', 'combination_discount_2_services', '0.10', 'decimal', '10% discount for 2 services', false],
            ['pricing', 'combination_discount_3_services', '0.15', 'decimal', '15% discount for 3+ services', false],
            ['pricing', 'express_surcharge', '0.20', 'decimal', '20% surcharge for express service', false],
            
            // Email settings
            ['email', 'from_address', 'noreply@yla-umzug.de', 'string', 'From email address', false],
            ['email', 'from_name', 'YLA Umzug', 'string', 'From name', false],
            ['email', 'response_time', '24 Stunden', 'string', 'Response time', true],
            
            // UI settings
            ['ui', 'company_name', 'YLA Umzug', 'string', 'Company name', true],
            ['ui', 'company_address', 'Musterstraße 123, 66111 Saarbrücken', 'string', 'Company address', true],
        ];

        foreach ($settings as [$group, $key, $value, $type, $description, $isPublic]) {
            Setting::create([
                'group_name' => $group,
                'key_name' => $key,
                'value' => $value,
                'type' => $type,
                'description' => $description,
                'is_public' => $isPublic
            ]);
        }
    }

    /**
     * Seed test quote requests
     */
    private function seedQuoteRequests(): void
    {
        $quotes = [
            [
                'name' => 'Anna Schmidt',
                'email' => 'anna.schmidt@example.com',
                'telefon' => '+49 123 456789',
                'moving_date' => now()->addDays(7),
                'bevorzugter_kontakt' => 'email',
                'ausgewaehlte_services' => ['umzug'],
                'service_details' => [
                    'movingDetails' => [
                        'rooms' => 3,
                        'floors' => 2,
                        'fromAddress' => ['street' => 'Alte Straße 1', 'postalCode' => '66111', 'city' => 'Saarbrücken'],
                        'toAddress' => ['street' => 'Neue Straße 5', 'postalCode' => '66112', 'city' => 'Saarbrücken']
                    ]
                ],
                'estimated_total' => 650.00,
                'status' => 'pending'
            ],
            [
                'name' => 'Peter Müller',
                'email' => 'peter.mueller@example.com',
                'telefon' => '+49 987 654321',
                'moving_date' => now()->addDays(14),
                'bevorzugter_kontakt' => 'phone',
                'ausgewaehlte_services' => ['umzug', 'putzservice'],
                'service_details' => [
                    'movingDetails' => [
                        'rooms' => 4,
                        'floors' => 1,
                        'fromAddress' => ['street' => 'Hauptstraße 10', 'postalCode' => '54290', 'city' => 'Trier'],
                        'toAddress' => ['street' => 'Nebenstraße 3', 'postalCode' => '54292', 'city' => 'Trier']
                    ],
                    'cleaningDetails' => [
                        'objectType' => 'apartment',
                        'size' => '4-rooms',
                        'cleaningIntensity' => 'deep'
                    ]
                ],
                'estimated_total' => 850.00,
                'endgueltiger_angebotsbetrag' => 820.00,
                'status' => 'quoted'
            ],
            [
                'name' => 'Maria Weber',
                'email' => 'maria.weber@example.com',
                'telefon' => '+49 555 123456',
                'moving_date' => now()->addDays(21),
                'bevorzugter_kontakt' => 'whatsapp',
                'ausgewaehlte_services' => ['entruempelung'],
                'service_details' => [
                    'declutterDetails' => [
                        'objectType' => 'house',
                        'volume' => 'high',
                        'address' => ['street' => 'Dorfstraße 7', 'postalCode' => '67655', 'city' => 'Kaiserslautern']
                    ]
                ],
                'estimated_total' => 1300.00,
                'endgueltiger_angebotsbetrag' => 1250.00,
                'status' => 'accepted',
                'email_sent_at' => now()->subHours(2)
            ],
            [
                'name' => 'Thomas Klein',
                'email' => 'thomas.klein@example.com',
                'telefon' => '+49 777 888999',
                'moving_date' => now()->subDays(3), // Past date - completed service
                'bevorzugter_kontakt' => 'email',
                'ausgewaehlte_services' => ['umzug', 'putzservice', 'entruempelung'],
                'service_details' => [
                    'movingDetails' => ['rooms' => 5, 'floors' => 2],
                    'cleaningDetails' => ['objectType' => 'house', 'size' => '5-rooms', 'cleaningIntensity' => 'construction'],
                    'declutterDetails' => ['objectType' => 'basement', 'volume' => 'medium']
                ],
                'estimated_total' => 1800.00,
                'endgueltiger_angebotsbetrag' => 1650.00,
                'status' => 'completed',
                'email_sent_at' => now()->subDays(5),
                'admin_notizen' => 'Großer Auftrag erfolgreich abgeschlossen. Kunde sehr zufrieden.'
            ],
            [
                'name' => 'Sandra Hoffmann',
                'email' => 'sandra.hoffmann@example.com',
                'telefon' => '+49 444 555666',
                'moving_date' => now()->addDays(30),
                'bevorzugter_kontakt' => 'email',
                'ausgewaehlte_services' => ['putzservice'],
                'service_details' => [
                    'cleaningDetails' => [
                        'objectType' => 'office',
                        'size' => '6-rooms',
                        'cleaningIntensity' => 'normal'
                    ]
                ],
                'estimated_total' => 280.00,
                'status' => 'rejected',
                'admin_notizen' => 'Außerhalb unseres Servicegebiets.'
            ]
        ];

        foreach ($quotes as $quoteData) {
            QuoteRequest::create($quoteData);
        }
    }
}