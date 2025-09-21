<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Setting;

class ValidateSettings extends Command
{
    protected $signature = 'settings:validate';
    protected $description = 'Validate that all required settings exist';

    protected $requiredSettings = [
        'general.calculator_enabled' => 'boolean',
        'general.business_phone' => 'string',
        'general.business_email' => 'string',
        'pricing.umzug.base_price' => 'decimal',
        'pricing.putzservice.base_price' => 'decimal',
        'pricing.entruempelung.base_price' => 'decimal',
        'ui.company_name' => 'string',
        'email.from_address' => 'string',
        'email.from_name' => 'string',
    ];

    public function handle()
    {
        $this->info('Validating settings...');
        
        $missing = [];
        $invalid = [];
        
        foreach ($this->requiredSettings as $key => $expectedType) {
            $setting = Setting::where('group_name', explode('.', $key)[0])
                ->where('key_name', implode('.', array_slice(explode('.', $key), 1)))
                ->first();
            
            if (!$setting) {
                $missing[] = $key;
                continue;
            }
            
            if ($setting->type !== $expectedType) {
                $invalid[] = "{$key} (expected: {$expectedType}, got: {$setting->type})";
            }
        }
        
        if (empty($missing) && empty($invalid)) {
            $this->info('✅ All required settings are present and valid!');
            return 0;
        }
        
        if (!empty($missing)) {
            $this->error('❌ Missing settings:');
            foreach ($missing as $setting) {
                $this->line("  - {$setting}");
            }
        }
        
        if (!empty($invalid)) {
            $this->error('❌ Invalid setting types:');
            foreach ($invalid as $setting) {
                $this->line("  - {$setting}");
            }
        }
        
        $this->info('Run "php artisan db:seed --class=SettingsSeeder" to fix missing settings.');
        
        return 1;
    }
}