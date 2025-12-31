<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\Setting;

return new class extends Migration
{
    public function up(): void
    {
        //  Tambahkan setting koordinat sekolah
        $newSettings = [
            [
                'key' => 'school_latitude',
                'value' => '-6.2706589',
                'type' => 'number',
                'description' => 'Latitude koordinat sekolah',
            ],
            [
                'key' => 'school_longitude',
                'value' => '106.9593685',
                'type' => 'number',
                'description' => 'Longitude koordinat sekolah',
            ],
            [
                'key' => 'max_distance_meters',
                'value' => '100',
                'type' => 'number',
                'description' => 'Radius maksimal absen dari sekolah (dalam meter)',
            ],
        ];

        foreach ($newSettings as $setting) {
            Setting::firstOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }

    public function down(): void
    {
        Setting::whereIn('key', [
            'school_latitude',
            'school_longitude',
            'max_distance_meters'
        ])->delete();
    }
};