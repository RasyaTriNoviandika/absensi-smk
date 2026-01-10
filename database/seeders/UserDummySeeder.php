<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class UserDummySeeder extends Seeder
{
    public function run(): void
    {
        // Membuat 20 user dummy
        User::factory()->count(20)->create([
            'role' => 'student',
            'status' => 'pending',
        ]);
    }
}
