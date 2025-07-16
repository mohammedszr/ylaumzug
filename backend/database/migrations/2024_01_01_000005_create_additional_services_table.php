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
        Schema::create('additional_services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->constrained()->onDelete('cascade');
            $table->string('key')->unique(); // assembly, packing, parking, storage, disposal
            $table->string('name'); // German display name
            $table->text('description')->nullable();
            $table->decimal('price', 8, 2);
            $table->string('price_type')->default('fixed'); // fixed, per_hour, per_m2, per_km
            $table->string('unit')->nullable(); // hour, m2, km, piece
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('additional_services');
    }
};