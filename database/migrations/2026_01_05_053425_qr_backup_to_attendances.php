<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('qr_token')->unique()->nullable()->after('face_descriptor_hash');
            $table->timestamp('qr_generated_at')->nullable()->after('qr_token');
        });
        
        Schema::table('attendances', function (Blueprint $table) {
            $table->enum('check_in_method', ['face', 'qr'])->default('face')->after('check_in_status');
            $table->enum('check_out_method', ['face', 'qr'])->default('face')->after('check_out_photo');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['qr_token', 'qr_generated_at']);
        });
        
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropColumn(['check_in_method', 'check_out_method']);
        });
    }
};