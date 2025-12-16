<?php
namespace App\Http\Controllers;

use App\Models\Attendance;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function student()
    {
        $user = auth()->user();
        
        // Today's attendance
        $todayAttendance = $user->todayAttendance();
        
        // This month statistics
        $thisMonthAttendances = $user->attendances()
            ->whereMonth('date', now()->month)
            ->whereYear('date', now()->year)
            ->get();

        $stats = [
            'hadir' => $thisMonthAttendances->where('status', 'hadir')->count(),
            'terlambat' => $thisMonthAttendances->where('status', 'terlambat')->count(),
            'alpha' => $thisMonthAttendances->where('status', 'alpha')->count(),
        ];

        // Recent attendances
        $recentAttendances = $user->attendances()
            ->orderBy('date', 'desc')
            ->limit(7)
            ->get();

        return view('student.dashboard', compact('todayAttendance', 'stats', 'recentAttendances'));
    }
}