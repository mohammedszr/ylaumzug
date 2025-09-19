<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // For SQLite, we need to recreate the table to properly handle column changes
        Schema::rename('services', 'services_old');
        
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->decimal('base_price', 8, 2)->default(0);
            $table->decimal('price_per_unit', 10, 2)->default(0);
            $table->enum('unit_type', ['hour', 'room', 'sqm', 'item', 'fixed'])->default('hour');
            $table->boolean('is_active')->default(true);
            $table->json('pricing_config')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            
            $table->index(['slug']);
            $table->index(['is_active']);
        });
        
        // Migrate existing data
        DB::statement("
            INSERT INTO services (id, name, slug, description, base_price, is_active, sort_order, created_at, updated_at)
            SELECT id, name, LOWER(REPLACE(name, ' ', '-')), description, base_price, is_active, sort_order, created_at, updated_at
            FROM services_old
        ");
        
        // Drop old table
        Schema::dropIfExists('services_old');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Recreate old table structure
        Schema::rename('services', 'services_new');
        
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('base_price', 8, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->json('configuration')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
        
        // Migrate data back
        DB::statement("
            INSERT INTO services (id, key, name, description, base_price, is_active, sort_order, created_at, updated_at)
            SELECT id, slug, name, description, base_price, is_active, sort_order, created_at, updated_at
            FROM services_new
        ");
        
        Schema::dropIfExists('services_new');
    }
};
