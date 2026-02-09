<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Attendance;

class MarkAlphaStudents extends Command
{
    protected $signature = 'attendance:mark-alpha';
    protected $description = 'Menandai siswa yang tidak absen sebagai alpha';

    public function handle()
    {
        $today = now('Asia/Jakarta')->toDateString();

        $students = User::students()->approved()->pluck('id');

        foreach ($students as $userId) {

            $attendance = Attendance::where('user_id', $userId)
                ->whereDate('date', $today)
                ->first();

            // SUDAH ADA BUKTI HADIR (masuk ATAU pulang) → JANGAN alpha
            if ($attendance && ($attendance->check_in || $attendance->check_out)) {
                continue;
            }

            //  Belum ada record sama sekali → buat alpha
            if (!$attendance) {
                Attendance::create([
                    'user_id' => $userId,
                    'date' => $today,
                    'status' => 'alpha'
                ]);
            }
        }

        $this->info('Alpha students marked safely.');
    }
}
