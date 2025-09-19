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
        Schema::table('quote_requests', function (Blueprint $table) {
            // Add German field names as aliases/additional fields
            $table->string('angebotsnummer')->unique()->after('id');
            $table->string('telefon')->after('phone');
            $table->enum('bevorzugter_kontakt', ['email', 'phone', 'whatsapp'])->default('email')->after('preferred_contact');
            
            // Add missing fields for enhanced functionality
            $table->string('from_postal_code', 10)->nullable()->after('message');
            $table->string('to_postal_code', 10)->nullable()->after('from_postal_code');
            $table->decimal('distance_km', 8, 2)->nullable()->after('to_postal_code');
            $table->enum('moving_type', ['local', 'long_distance', 'international'])->default('local')->after('distance_km');
            
            // Rename JSON fields to German names
            $table->json('ausgewaehlte_services')->after('moving_type');
            
            // Add WhatsApp tracking
            $table->timestamp('whatsapp_sent_at')->nullable()->after('email_status');
            
            // Add indexes for performance
            $table->index(['angebotsnummer']);
            $table->index(['from_postal_code', 'to_postal_code']);
            $table->index(['moving_type']);
        });
        
        // Copy data from old fields to new German fields
        DB::statement('UPDATE quote_requests SET angebotsnummer = quote_number WHERE angebotsnummer IS NULL OR angebotsnummer = ""');
        DB::statement('UPDATE quote_requests SET telefon = phone WHERE telefon IS NULL OR telefon = ""');
        DB::statement('UPDATE quote_requests SET bevorzugter_kontakt = preferred_contact WHERE bevorzugter_kontakt IS NULL OR bevorzugter_kontakt = ""');
        DB::statement('UPDATE quote_requests SET ausgewaehlte_services = selected_services WHERE ausgewaehlte_services IS NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quote_requests', function (Blueprint $table) {
            $table->dropIndex(['angebotsnummer']);
            $table->dropIndex(['from_postal_code', 'to_postal_code']);
            $table->dropIndex(['moving_type']);
            
            $table->dropColumn([
                'angebotsnummer',
                'telefon', 
                'bevorzugter_kontakt',
                'from_postal_code',
                'to_postal_code',
                'distance_km',
                'moving_type',
                'ausgewaehlte_services',
                'whatsapp_sent_at'
            ]);
        });
    }
};
