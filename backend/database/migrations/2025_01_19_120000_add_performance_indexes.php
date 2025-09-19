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
            // Add indexes for frequently queried columns
            $table->index(['status', 'created_at'], 'idx_status_created');
            $table->index(['moving_date', 'status'], 'idx_moving_date_status');
            $table->index(['email', 'created_at'], 'idx_email_created');
            $table->index(['created_at', 'status'], 'idx_created_status');
            
            // Composite index for admin filtering
            $table->index(['status', 'moving_date', 'created_at'], 'idx_admin_filter');
        });

        Schema::table('settings', function (Blueprint $table) {
            // Add composite index for settings lookup
            $table->index(['group_name', 'key_name'], 'idx_group_key');
            $table->index(['is_public', 'group_name'], 'idx_public_group');
        });

        Schema::table('services', function (Blueprint $table) {
            // Add indexes for service queries
            $table->index(['is_active', 'sort_order'], 'idx_active_sort');
            $table->index('slug', 'idx_slug');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quote_requests', function (Blueprint $table) {
            $table->dropIndex('idx_status_created');
            $table->dropIndex('idx_moving_date_status');
            $table->dropIndex('idx_email_created');
            $table->dropIndex('idx_created_status');
            $table->dropIndex('idx_admin_filter');
        });

        Schema::table('settings', function (Blueprint $table) {
            $table->dropIndex('idx_group_key');
            $table->dropIndex('idx_public_group');
        });

        Schema::table('services', function (Blueprint $table) {
            $table->dropIndex('idx_active_sort');
            $table->dropIndex('idx_slug');
        });
    }
};