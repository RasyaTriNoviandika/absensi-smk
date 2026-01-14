<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Tambahkan field jika belum ada
            if (!Schema::hasColumn('users', 'face_descriptor_hash')) {
                $table->text('face_descriptor_hash')->nullable()->after('face_descriptor');
            }
            if (!Schema::hasColumn('users', 'last_login_at')) {
                $table->timestamp('last_login_at')->nullable();
            }
            if (!Schema::hasColumn('users', 'last_login_ip')) {
                $table->string('last_login_ip')->nullable();
            }
            if (!Schema::hasColumn('users', 'face_registered_at')) {
                $table->timestamp('face_registered_at')->nullable();
            }
            if (!Schema::hasColumn('users', 'qr_token')) {
                $table->string('qr_token')->nullable();
            }
            if (!Schema::hasColumn('users', 'qr_generated_at')) {
                $table->timestamp('qr_generated_at')->nullable();
            }
            if (!Schema::hasColumn('users', 'qr_token_used_at')) {
                $table->timestamp('qr_token_used_at')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'face_descriptor_hash',
                'last_login_at',
                'last_login_ip',
                'face_registered_at',
                'qr_token',
                'qr_generated_at',
                'qr_token_used_at'
            ]);
        });
    }
};