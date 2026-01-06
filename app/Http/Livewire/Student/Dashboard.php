<?php

namespace App\Http\Livewire\Student;

use Livewire\Component;
use App\Models\Attendance;

class Dashboard extends Component
{
    public $todayAttendance;
    public $stats;
    public $recentAttendances;

    // Listener untuk refresh dari JavaScript
    protected $listeners = ['attendanceSubmitted' => 'refreshData'];

    public function mount()
    {
        $this->loadData();
    }

    public function loadData()
    {
        $user = auth()->user();
        
        $this->todayAttendance = $user->todayAttendance();
        
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