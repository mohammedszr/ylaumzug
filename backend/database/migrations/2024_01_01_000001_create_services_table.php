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
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique(); // umzug, entruempelung, putzservice
            $table->string('name'); // Display name in German
            $table->text('description')->nullable();
            $table->decimal('base_price', 8, 2)->default(0); // Base price in EUR
            $table->boolean('is_active')->default(true);
            $table->json('configuration')->nullable(); // Service-specific config
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};