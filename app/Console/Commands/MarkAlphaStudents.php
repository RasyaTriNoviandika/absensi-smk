<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class MarkAlphaStudents extends Command
{
    protected $signature = 'attendance:mark-alpha';

    public function handle()
    {
        $today = now('Asia/Jakarta')->toDateString();

        // Ambil semua siswa approved
        $students = User::students()->approved()->pluck('id');

        // Ambil user_id yang SUDAH absen hari ini
        $alreadyPresent = Attendance::whereDate('date', $today)
            ->whereNotNull('check_in')
            ->pluck('user_id');

        // Siswa yang BELUM absen
        $alphaStudents = $students->diff($alreadyPresent);

        foreach ($alphaStudents as $userId) {
            Attendance::updateOrCreate(
                ['user_id' => $userId, 'date' => $today],
                ['status' => 'alpha']
            );
        }

        $this->info('Alpha students marked successfully.');
    }
}
