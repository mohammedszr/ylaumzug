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
        // Drop and recreate the settings table with the new structure
        Schema::dropIfExists('settings');
        
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key_name');
            $table->text('value');
            $table->enum('type', ['string', 'integer', 'decimal', 'boolean', 'json'])->default('string');
            $table->string('group_name')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_public')->default(false);
            $table->timestamps();

            $table->unique(['group_name', 'key_name']);
            $table->index(['group_name']);
            $table->index(['is_public']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
        
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value');
            $table->enum('type', ['string', 'integer', 'float', 'decimal', 'boolean', 'json', 'array'])->default('string');
            $table->string('group')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_public')->default(false);
            $table->timestamps();

            $table->index(['group']);
            $table->index(['is_public']);
        });
    }
};