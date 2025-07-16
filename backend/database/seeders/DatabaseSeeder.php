<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     * 
     * This is the main seeder that runs all other seeders in the correct order.
     * Run with: php artisan db:seed
     */
    public function run(): void
    {
        $this->call([
            ServicesSeeder::class,
            SettingsSeeder::class,
        ]);
    }
}