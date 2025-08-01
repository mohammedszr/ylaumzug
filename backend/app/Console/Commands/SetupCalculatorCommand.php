<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class SetupCalculatorCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'calculator:setup';

    /**
     * The console command description.
     */
    protected $description = 'Set up the YLA Umzug calculator system';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Setting up YLA Umzug Calculator System...');
        
        // Run migrations
        $this->info('Running migrations...');
        Artisan::call('migrate', ['--force' => true]);
        $this->info('✓ Migrations completed');
        
        // Seed settings
        $this->info('Seeding default settings...');
        Artisan::call('db:seed', ['--class' => 'SettingsSeeder', '--force' => true]);
        $this->info('✓ Settings seeded');
        
        // Seed services
        $this->info('Seeding services...');
        Artisan::call('db:seed', ['--class' => 'ServicesSeeder', '--force' => true]);
        $this->info('✓ Services seeded');
        
        // Clear caches
        $this->info('Clearing caches...');
        Artisan::call('cache:clear');
        Artisan::call('config:clear');
        $this->info('✓ Caches cleared');
        
        $this->info('');
        $this->info('🎉 Calculator system setup completed!');
        $this->info('');
        $this->info('Next steps:');
        $this->info('1. Visit /admin/settings to configure pricing');
        $this->info('2. Update business information and service areas');
        $this->info('3. Test the calculator at /api/calculator/calculate');
        $this->info('');
        $this->info('Admin panel: ' . url('/admin/settings'));
        
        return 0;
    }
}