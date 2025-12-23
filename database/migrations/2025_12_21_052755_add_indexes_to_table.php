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
        Schema::table('attendances', function (Blueprint $table) {
            // Index untuk query berdasarkan tanggal
            $table->index('date', 'idx_attendances_date');
            
            // Index untuk query berdasarkan user_id dan date (composite)
            $table->index(['user_id', 'date'], 'idx_attendances_user_date');
            
            // Index untuk query berdasarkan status
            $table->index('status', 'idx_attendances_status');
        });

        Schema::table('users', function (Blueprint $table) {
            // Index untuk query berdasarkan status
            $table->index('status', 'idx_users_status');
            
            // Index untuk query berdasarkan role
            $table->index('role', 'idx_users_role');
            
            // Index untuk query berdasarkan class
            $table->index('class', 'idx_users_class');
            
            // Composite index untuk student queries
            $table->index(['role', 'status'], 'idx_users_role_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropIndex('idx_attendances_date');
            $table->dropIndex('idx_attendances_user_date');
            $table->dropIndex('idx_attendances_status');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('idx_users_status');
            $table->dropIndex('idx_users_role');
            $table->dropIndex('idx_users_class');
            $table->dropIndex('idx_users_role_status');
        });
    }
};