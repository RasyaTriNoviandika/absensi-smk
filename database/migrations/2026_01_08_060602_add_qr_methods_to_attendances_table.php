<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Field ini untuk tracking (optional), tapi QR logic tidak bergantung padanya lagi
            if (!Schema::hasColumn('users', 'qr_token_used_at')) {
                $table->timestamp('qr_token_used_at')->nullable()->after('qr_generated_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'qr_token_used_at')) {
                $table->dropColumn('qr_token_used_at');
            }
        });
    }
};