<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\User;
use App\Models\Attendance;
use Illuminate\Support\Facades\Cache;

class Dashboard extends Component
{
    public $stats = [];
    public $weeklyData = [];
    public $recentAttendances = [];

    protected $listeners = ['forceRefresh' => 'loadData'];

    public function mount()
    {
        $this->loadData();
    }

    public function loadData()
    {
        $this->stats = Cache::remember('admin_dashboard_stats_' . today()->toDateString(), 300, function () {
            $totalStudents = User::students()->approved()->count();

            $todayStats = Attendance::whereDate('date', today())
                ->selectRaw('status, COUNT(*) as count')
                ->groupBy('status')
                ->pluck('count', 'status');

            return [
                'total_students' => $totalStudents,
                'present_today' => $todayStats->get('hadir', 0),
                'late_today' => $todayStats->get('terlambat', 0),
                'alpha_today' => $totalStudents - $todayStats->sum(),
                'pending_approval' => User::students()->pending()->count(),
            ];
        });

        $this->weeklyData = Cache::remember('admin_weekly_data_' . today()->toDateString(), 300, function () {
            $raw = Attendance::selectRaw('DATE(date) as date, COUNT(*) as count')
                ->where('date', '>=', today()->subDays(6))
                ->groupBy('date')
                ->orderBy('date')
                ->pluck('count', 'date');

            $data = [];
            for ($i = 6; $i >= 0; $i--) {
                $d = today()->subDays($i);
                $key = $d->format('Y-m-d');

                $data[] = [
                    'date' => $d->format('d M'),
                    'count' => $raw->get($key, 0)
                ];
            }
            return $data;
        });

        $this->recentAttendances = Attendance::with(['user:id,name,class'])
            ->select('id', 'user_id', 'status', 'created_at')
            ->latest()
            ->limit(10)
            ->get();

        // chart update
        $this->dispatch('updateChart', $this->weeklyData);
    }

    public function render()
    {
        return view('livewire.admin.dashboard')
            ->layout('layouts.admin', ['title' => 'Dashboard']);
    }
}
