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
        Schema::create('quote_requests', function (Blueprint $table) {
            $table->id();
            $table->string('quote_number')->unique(); // Auto-generated quote reference
            
            // Customer Information
            $table->string('name');
            $table->string('email');
            $table->string('phone');
            $table->date('preferred_date')->nullable();
            $table->enum('preferred_contact', ['email', 'phone'])->default('email');
            $table->text('message')->nullable();
            
            // Service Information
            $table->json('selected_services'); // Array of service keys
            $table->json('service_details'); // All form data from calculator
            $table->json('pricing_data')->nullable(); // Calculated pricing breakdown
            
            // Quote Status
            $table->enum('status', ['pending', 'reviewed', 'quoted', 'accepted', 'rejected', 'completed'])
                  ->default('pending');
            $table->text('admin_notes')->nullable();
            $table->decimal('final_quote_amount', 10, 2)->nullable();
            $table->timestamp('quoted_at')->nullable();
            $table->timestamp('responded_at')->nullable();
            
            // Tracking
            $table->string('source')->default('website'); // website, phone, email
            $table->string('user_agent')->nullable();
            $table->string('ip_address')->nullable();
            
            $table->timestamps();
            
            // Indexes for performance
            $table->index(['status', 'created_at']);
            $table->index(['email']);
            $table->index(['quote_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quote_requests');
    }
};