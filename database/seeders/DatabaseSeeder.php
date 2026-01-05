<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Setting;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Hanya 1 Akun Admin Default
       User::firstOrCreate(
    ['username' => 'gurusija'], // Unique username admin
    [
        'name' => 'Administrator',
        'email' => 'gurusija@gmail.com',
        'password' => Hash::make(env('ADMIN_PASSWORD', 'sijamabar')),
        'role' => 'admin',
        'status' => 'approved',
    ]
    );

        // // Create Sample Students (Optional)
        // User::create([
        //     'nisn' => '0012345678',
        //     'username' => 'siswa001',
        //     'name' => 'Budi Santoso',
        //     'email' => 'budi@student.sch.id',
        //     'password' => Hash::make('password123'),
        //     'role' => 'student',
        //     'class' => '10 DKV 1',
        //     'phone' => '081234567890',
        //     'status' => 'approved',
        // ]);

        // User::create([
        //     'nisn' => '0012345679',
        //     'username' => 'siswa002',
        //     'name' => 'Siti Aminah',
        //     'email' => 'siti@student.sch.id',
        //     'password' => Hash::make('password123'),
        //     'role' => 'student',
        //     'class' => '11 SIJA 2',
        //     'phone' => '081234567891',
        //     'status' => 'approved',
        // ]);

        // Settings
        $settings = [
            [
                'key' => 'check_in_time_limit',
                'value' => '07:30',
                'type' => 'time',
                'description' => 'Batas waktu absen masuk (lebih dari ini = terlambat)',
            ],
            [
                'key' => 'check_out_time_min',
                'value' => '14:00',
                'type' => 'time',
                'description' => 'Jam minimal untuk absen pulang',
            ],
            [
                'key' => 'school_name',
                'value' => 'SMK Negeri 1 Jakarta',
                'type' => 'text',
                'description' => 'Nama sekolah',
            ],
            [
                'key' => 'school_address',
                'value' => 'Jl. Pendidikan No. 123, Jakarta',
                'type' => 'text',
                'description' => 'Alamat sekolah',
            ],
            [
                'key' => 'face_match_threshold',
                'value' => '0.6',
                'type' => 'number',
                'description' => 'Threshold untuk face matching (0-1, semakin kecil semakin strict)',
            ],
        ];

       foreach ($settings as $setting) {
    Setting::updateOrCreate(
        ['key' => $setting['key']], // pencarian hanya berdasarkan key
        [
            'value' => $setting['value'],
            'type' => $setting['type'],
            'description' => $setting['description'],
        ]
    );
}
    }
}
