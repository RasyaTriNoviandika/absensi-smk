<?php

namespace App\Http\Livewire\Admin;

use Livewire\Component;
use App\Models\User;
use App\Models\Attendance;
use Illuminate\Support\Facades\Cache;

class Dashboard extends Component
{
    public $stats;
    public $weeklyData;
    public $recentAttendances;

    // Polling setiap 30 detik untuk auto-refresh
    protected $listeners = ['refreshDashboard' => '$refresh'];

    public function mount()
    {
        $this->loadStats();
        $this->loadWeeklyData();
        $this->loadRecentAttendances();
    }

    public function loadStats()
    {
        $this->stats = Cache::remember('dashboard_stats_' . today()->toDateString(), 300, function () {
            $totalStudents = User::where('role', 'student')
                ->where('status', 'approved')
                ->count();

            $todayStats = Attendance::whereDate('date', today())
                ->selectRaw('status, COUNT(*) as count')
                ->groupBy('status')
                ->pluck('count', 'status');

            return [
                'total_students' => $totalStudents,
                'present_today' => $todayStats->get('hadir', 0),
                'late_today' => $todayStats->get('terlambat', 0),
                'alpha_today' => $totalStudents - $todayStats->sum(),
                'pending_approval' => User::where('role', 'student')
                    ->where('status', 'pending')
                    ->count(),
            ];
        });
    }

    public function loadWeeklyData()
    {
        $this->weeklyData = Cache::remember('weekly_data_' . today()->toDateString(), 300, function () {
            $weeklyDataRaw = Attendance::selectRaw('DATE(date) as date, COUNT(*) as count')
                ->where('date', '>=', today()->subDays(6))
                ->groupBy('date')
                ->orderBy('date')
                ->pluck('count', 'date');

            $weeklyData = [];
            for ($i = 6; $i >= 0; $i--) {
                $d = today()->subDays($i);
                $key = $d->format('Y-m-d');
                $weeklyData[] = [
                    'date' => $d->format('d M'),
                    'count' => $weeklyDataRaw->get($key) ?? 0
                ];
            }
            return $weeklyData;
        });
    }

    public function loadRecentAttendances()
    {
        $this->recentAttendances = Attendance::with(['user:id,name,class'])
            ->select('id', 'user_id', 'status', 'created_at')
            ->latest()
            ->limit(10)
            ->get();
    }

    public function render()
    {
        return view('livewire.admin.dashboard')
            ->layout('layouts.admin', ['title' => 'Dashboard']);
    }
}