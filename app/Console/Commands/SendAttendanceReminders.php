<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Attendance;
use App\Notifications\AttendanceReminder;
use Carbon\Carbon;

class SendAttendanceReminders extends Command
{
    protected $signature = 'attendance:remind';
    protected $description = 'Send reminder to students who haven\'t checked in';

    public function handle()
    {
        // Jam 07:20 (10 menit sebelum deadline)
        $now = Carbon::now('Asia/Jakarta');
        
        if ($now->format('H:i') !== '07:20') {
            $this->info('Reminder hanya dikirim jam 07:20 WIB');
            return 0;
        }

        // Ambil siswa yang belum absen hari ini
        $studentsNotCheckedIn = User::students()
            ->approved()
            ->whereDoesntHave('attendances', function($q) {
                $q->whereDate('date', today())
                  ->whereNotNull('check_in');
            })
            ->get();

        $count = 0;
        foreach ($studentsNotCheckedIn as $student) {
            $student->notify(new AttendanceReminder());
            $count++;
        }

        $this->info("✅ Reminder terkirim ke {$count} siswa");
        return 0;
    }
}