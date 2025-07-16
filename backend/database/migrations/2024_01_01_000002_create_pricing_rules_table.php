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
        Schema::create('pricing_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->constrained()->onDelete('cascade');
            $table->string('rule_type'); // base, distance, volume, size, addon, modifier
            $table->string('rule_key'); // apartment_size, distance_km, volume_level, etc.
            $table->string('condition_operator')->nullable(); // >, <, =, between, in
            $table->json('condition_values')->nullable(); // [50, 100] for between, ["low", "medium"] for in
            $table->decimal('price_value', 8, 2); // Price or multiplier
            $table->string('price_type')->default('fixed'); // fixed, multiplier, per_unit
            $table->string('unit')->nullable(); // m2, km, piece, etc.
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('priority')->default(0); // For rule ordering
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pricing_rules');
    }
};