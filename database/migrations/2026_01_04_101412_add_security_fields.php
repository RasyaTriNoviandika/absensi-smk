<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('face_descriptor_hash')->nullable()->after('face_descriptor');
            $table->timestamp('face_registered_at')->nullable()->after('face_descriptor_hash');
            $table->timestamp('last_login_at')->nullable();
            $table->string('last_login_ip', 45)->nullable();
        });
        
         Schema::table('attendances', function (Blueprint $table) {
            $table->decimal('latitude', 10, 8)->nullable()->after('notes');
            $table->decimal('longitude', 11, 8)->nullable()->after('latitude');
            $table->string('ip_address', 45)->nullable()->after('longitude');
            $table->text('user_agent')->nullable()->after('ip_address');

            $table->index(['user_id', 'created_at'], 'idx_user_gps_time');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['face_descriptor_hash', 'face_registered_at', 'last_login_at', 'last_login_ip']);
        });
        
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropColumn(['latitude', 'longitude', 'ip_address', 'user_agent']);
        });
    }
};