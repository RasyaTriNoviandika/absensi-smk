<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        DB::statement("
            ALTER TABLE attendances 
            MODIFY status ENUM(
                'hadir',
                'terlambat',
                'pulang',
                'pulang_cepat',
                'alpha'
            ) NOT NULL DEFAULT 'alpha'
        ");
    }

    public function down(): void
    {
        DB::statement("
            ALTER TABLE attendances 
            MODIFY status ENUM(
                'hadir',
                'terlambat',
                'alpha'
            ) NOT NULL DEFAULT 'alpha'
        ");
    }
};
