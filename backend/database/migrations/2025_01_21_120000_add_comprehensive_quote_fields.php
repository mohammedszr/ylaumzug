<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('quote_requests', function (Blueprint $table) {
            // Moving Addresses - From
            if (!Schema::hasColumn('quote_requests', 'from_street')) {
                $table->string('from_street')->nullable();
            }
            if (!Schema::hasColumn('quote_requests', 'from_city')) {
                $table->string('from_city')->nullable();
            }
            if (!Schema::hasColumn('quote_requests', 'from_floor')) {
                $table->integer('from_floor')->nullable();
            }
            if (!Schema::hasColumn('quote_requests', 'from_elevator')) {
                $table->boolean('from_elevator')->default(false);
            }
            
            // Moving Addresses - To
            if (!Schema::hasColumn('quote_requests', 'to_street')) {
                $table->string('to_street')->nullable();
            }
            if (!Schema::hasColumn('quote_requests', 'to_city')) {
                $table->string('to_city')->nullable();
            }
            if (!Schema::hasColumn('quote_requests', 'to_floor')) {
                $table->integer('to_floor')->nullable();
            }
            if (!Schema::hasColumn('quote_requests', 'to_elevator')) {
                $table->boolean('to_elevator')->default(false);
            }
            
            // Apartment Details
            if (!Schema::hasColumn('quote_requests', 'flat_size_m2')) {
                $table->decimal('flat_size_m2', 8, 2)->nullable();
            }
            if (!Schema::hasColumn('quote_requests', 'flat_rooms')) {
                $table->integer('flat_rooms')->nullable();
            }
            if (!Schema::hasColumn('quote_requests', 'parking_options')) {
                $table->string('parking_options')->nullable();
            }
            
            // Transport Volume
            if (!Schema::hasColumn('quote_requests', 'boxes_count')) {
                $table->integer('boxes_count')->nullable();
            }
            if (!Schema::hasColumn('quote_requests', 'beds_count')) {
                $table->integer('beds_count')->nullable();
            }
            if (!Schema::hasColumn('quote_requests', 'wardrobes_count')) {
                $table->integer('wardrobes_count')->nullable();
            }
            if (!Schema::hasColumn('quote_requests', 'sofas_count')) {
                $table->integer('sofas_count')->nullable();
            }
            if (!Schema::hasColumn('quote_requests', 'tables_chairs_count')) {
                $table->integer('tables_chairs_count')->nullable();
            }
            if (!Schema::hasColumn('quote_requests', 'washing_machine_count')) {
                $table->integer('washing_machine_count')->nullable();
            }
            if (!Schema::hasColumn('quote_requests', 'fridge_count')) {
                $table->integer('fridge_count')->nullable();
            }
            if (!Schema::hasColumn('quote_requests', 'other_electronics_count')) {
                $table->integer('other_electronics_count')->nullable();
            }
            if (!Schema::hasColumn('quote_requests', 'furniture_disassembly')) {
                $table->boolean('furniture_disassembly')->default(false);
            }
            if (!Schema::hasColumn('quote_requests', 'fragile_items')) {
                $table->text('fragile_items')->nullable();
            }
            
            // Additional Services
            if (!Schema::hasColumn('quote_requests', 'service_furniture_assembly')) {
                $table->boolean('service_furniture_assembly')->default(false);
            }
            if (!Schema::hasColumn('quote_requests', 'service_packing')) {
                $table->boolean('service_packing')->default(false);
            }
            if (!Schema::hasColumn('quote_requests', 'service_no_parking_zone')) {
                $table->boolean('service_no_parking_zone')->default(false);
            }
            if (!Schema::hasColumn('quote_requests', 'service_storage')) {
                $table->boolean('service_storage')->default(false);
            }
            if (!Schema::hasColumn('quote_requests', 'service_disposal')) {
                $table->boolean('service_disposal')->default(false);
            }
            
            // Calculation Details
            if (!Schema::hasColumn('quote_requests', 'base_price')) {
                $table->decimal('base_price', 10, 2)->nullable();
            }
            if (!Schema::hasColumn('quote_requests', 'distance_price')) {
                $table->decimal('distance_price', 10, 2)->nullable();
            }
            if (!Schema::hasColumn('quote_requests', 'floor_price')) {
                $table->decimal('floor_price', 10, 2)->nullable();
            }
            if (!Schema::hasColumn('quote_requests', 'volume_price')) {
                $table->decimal('volume_price', 10, 2)->nullable();
            }
            if (!Schema::hasColumn('quote_requests', 'services_price')) {
                $table->decimal('services_price', 10, 2)->nullable();
            }
            if (!Schema::hasColumn('quote_requests', 'price_breakdown')) {
                $table->json('price_breakdown')->nullable();
            }
            
            // Enhanced Address Information
            if (!Schema::hasColumn('quote_requests', 'from_full_address')) {
                $table->string('from_full_address')->nullable();
            }
            if (!Schema::hasColumn('quote_requests', 'to_full_address')) {
                $table->string('to_full_address')->nullable();
            }
            if (!Schema::hasColumn('quote_requests', 'from_latitude')) {
                $table->decimal('from_latitude', 10, 8)->nullable();
            }
            if (!Schema::hasColumn('quote_requests', 'from_longitude')) {
                $table->decimal('from_longitude', 11, 8)->nullable();
            }
            if (!Schema::hasColumn('quote_requests', 'to_latitude')) {
                $table->decimal('to_latitude', 10, 8)->nullable();
            }
            if (!Schema::hasColumn('quote_requests', 'to_longitude')) {
                $table->decimal('to_longitude', 11, 8)->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quote_requests', function (Blueprint $table) {
            $columnsToCheck = [
                'from_street', 'from_city', 'from_floor', 'from_elevator',
                'to_street', 'to_city', 'to_floor', 'to_elevator',
                'flat_size_m2', 'flat_rooms', 'parking_options',
                'boxes_count', 'beds_count', 'wardrobes_count', 'sofas_count',
                'tables_chairs_count', 'washing_machine_count', 'fridge_count',
                'other_electronics_count', 'furniture_disassembly', 'fragile_items',
                'service_furniture_assembly', 'service_packing', 'service_no_parking_zone',
                'service_storage', 'service_disposal',
                'base_price', 'distance_price', 'floor_price', 'volume_price', 'services_price',
                'price_breakdown', 'from_full_address', 'to_full_address',
                'from_latitude', 'from_longitude', 'to_latitude', 'to_longitude'
            ];
            
            $columnsToDrop = [];
            foreach ($columnsToCheck as $column) {
                if (Schema::hasColumn('quote_requests', $column)) {
                    $columnsToDrop[] = $column;
                }
            }
            
            if (!empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }
};