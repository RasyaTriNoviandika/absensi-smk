<?php

namespace App\Http\Livewire\Student;

use Livewire\Component;
use App\Models\Attendance;

class Dashboard extends Component
{
    public $todayAttendance;
    public $stats;
    public $recentAttendances;

    protected $listeners = ['attendanceUpdated' => 'refreshData'];

    public function mount()
    {
        $this->loadData();
    }

    public function loadData()
    {
        $user = auth()->user();
        
        // Today's attendance
        $this->todayAttendance = $user->todayAttendance();
        
        // This month statistics - Optimized dengan select minimal
        $thisMonthAttendances = $user->attendances()
            ->whereMonth('date', now()->month)
            ->whereYear('date', now()->year)
            ->select('status')
            ->get();

        $this->stats = [
            'hadir' => $thisMonthAttendances->where('status', 'hadir')->count(),
            'terlambat' => $thisMonthAttendances->where('status', 'terlambat')->count(),
            'alpha' => $thisMonthAttendances->where('status', 'alpha')->count(),
        ];

        // Recent attendances - Hanya ambil 7 terakhir
        $this->recentAttendances = $user->attendances()
            ->select('id', 'date', 'check_in', 'check_out', 'status')
            ->orderBy('date', 'desc')
            ->limit(7)
            ->get();
    }

    public function refreshData()
    {
        $this->loadData();
    }

    public function render()
    {
        return view('livewire.student.dashboard')
            ->layout('layouts.app', ['title' => 'Dashboard Siswa']);
    }
}