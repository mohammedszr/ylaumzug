<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Setting;

class UpdateSettingsToGerman extends Command
{
    protected $signature = 'settings:german';
    protected $description = 'Update all setting descriptions to German';

    protected $germanDescriptions = [
        // General Settings
        'general.calculator_enabled' => 'Preisrechner aktivieren oder deaktivieren',
        'general.business_email' => 'Haupt-Geschäfts-E-Mail-Adresse',
        'general.business_phone' => 'Geschäftstelefonnummer',
        'general.service_areas' => 'Bediente Postleitzahlen',
        'general.max_service_distance' => 'Maximale Service-Entfernung in km',

        // Pricing Settings
        'pricing.minimum_order_value' => 'Mindestbestellwert für alle Services',
        'pricing.umzug.base_price' => 'Grundpreis für Umzugsservice',
        'pricing.umzug.price_per_room' => 'Preis pro Zimmer für Umzug',
        'pricing.umzug.floor_surcharge' => 'Zuschlag pro Etage ab 2. Stock',
        'pricing.umzug.free_distance_km' => 'Kostenlose Entfernung in km',
        'pricing.umzug.price_per_km' => 'Preis pro km nach kostenloser Entfernung',
        'pricing.putzservice.base_price' => 'Grundpreis für Putzservice',
        'pricing.putzservice.price_per_room' => 'Preis pro Zimmer für Reinigung',
        'pricing.putzservice.deep_cleaning_surcharge' => 'Zuschlag für Grundreinigung',
        'pricing.putzservice.construction_cleaning_surcharge' => 'Zuschlag für Baureinigung',
        'pricing.entruempelung.base_price' => 'Grundpreis für Entrümpelung',
        'pricing.entruempelung.price_per_volume' => 'Preis pro Volumen-Einheit',
        'pricing.entruempelung.house_surcharge' => 'Zuschlag für Haus-Entrümpelung',
        'pricing.entruempelung.basement_surcharge' => 'Zuschlag für Keller-Entrümpelung',
        'pricing.discounts.two_services' => 'Rabatt-Prozentsatz für 2 Services',
        'pricing.discounts.three_plus_services' => 'Rabatt-Prozentsatz für 3+ Services',
        'pricing.surcharges.express' => 'Express-Zuschlag Prozentsatz',
        'pricing.combination_discount_2_services' => 'Rabattsatz für 2 Services (10%)',
        'pricing.combination_discount_3_services' => 'Rabattsatz für 3+ Services (15%)',
        'pricing.express_surcharge' => 'Zuschlagssatz für Express-Service (20%)',

        // Moving Service Settings
        'moving.base_price_per_sqm' => 'Grundpreis pro Quadratmeter für Umzug',
        'moving.distance_rate' => 'Preis pro Kilometer Entfernung',
        'moving.minimum_distance_free' => 'Kostenlose Entfernung in km vor Berechnung',
        'moving.floor_surcharge' => 'Zuschlag pro Etage ab 2. Stock (ohne Aufzug)',
        'moving.minimum_moving_cost' => 'Mindestkosten für Umzugsservice',

        // Cleaning Service Settings
        'cleaning.cleaning_rate_normal' => 'Tarif pro m² für normale Reinigung',
        'cleaning.cleaning_rate_deep' => 'Tarif pro m² für Grundreinigung',
        'cleaning.cleaning_rate_construction' => 'Tarif pro m² für Baureinigung',
        'cleaning.minimum_cleaning_cost' => 'Mindestkosten für Reinigungsservice',
        'cleaning.frequency_discount_weekly' => 'Rabattsatz für wöchentliche Reinigung (20%)',
        'cleaning.frequency_discount_biweekly' => 'Rabattsatz für 14-tägige Reinigung (15%)',
        'cleaning.frequency_discount_monthly' => 'Rabattsatz für monatliche Reinigung (10%)',

        // Decluttering Service Settings
        'decluttering.declutter_volume_low' => 'Preis für geringes Volumen (1-2 Container)',
        'decluttering.declutter_volume_medium' => 'Preis für mittleres Volumen (3-5 Container)',
        'decluttering.declutter_volume_high' => 'Preis für hohes Volumen (6+ Container)',
        'decluttering.declutter_volume_extreme' => 'Preis für extremes Volumen (Messie-Wohnung)',
        'decluttering.hazardous_waste_surcharge' => 'Zuschlag für Sondermüll-Entsorgung',
        'decluttering.electronics_disposal_cost' => 'Kosten für Elektronik-Entsorgung',
        'decluttering.clean_handover_cost' => 'Kosten für besenreine Übergabe',

        // Email Settings
        'email.from_address' => 'Standard-Absender E-Mail-Adresse',
        'email.from_name' => 'Standard-Absender Name',
        'email.response_time' => 'Erwartete Antwortzeit für Angebote',

        // API Settings
        'api.openroute_api_key' => 'OpenRouteService API-Schlüssel für Entfernungsberechnung',
        'api.whatsapp_access_token' => 'WhatsApp Business API Access Token',
        'api.whatsapp_phone_number_id' => 'WhatsApp Business Telefonnummer ID',

        // UI Settings
        'ui.company_name' => 'Firmenname für Anzeige',
        'ui.company_address' => 'Firmenadresse für Anzeige',
        'ui.show_pricing_disclaimer' => 'Preishinweis im Rechner anzeigen',
    ];

    public function handle()
    {
        $this->info('Updating setting descriptions to German...');
        
        $updated = 0;
        $notFound = 0;
        
        foreach ($this->germanDescriptions as $key => $germanDescription) {
            $parts = explode('.', $key);
            if (count($parts) >= 2) {
                $group = $parts[0];
                $keyName = implode('.', array_slice($parts, 1));
                
                $setting = Setting::where('group_name', $group)
                    ->where('key_name', $keyName)
                    ->first();
                
                if ($setting) {
                    $setting->update(['description' => $germanDescription]);
                    $updated++;
                    $this->line("✓ Updated: {$key}");
                } else {
                    $notFound++;
                    $this->error("✗ Not found: {$key}");
                }
            }
        }
        
        $this->info("\nSummary:");
        $this->info("Updated: {$updated} settings");
        if ($notFound > 0) {
            $this->warn("Not found: {$notFound} settings");
        }
        
        $this->info("\nGerman descriptions have been applied to all settings!");
        
        return 0;
    }
}