<?php

namespace App\Http\Livewire\Student;

use Livewire\Component;
use Livewire\WithPagination;

class AttendanceHistory extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind';
    protected $queryString = ['month', 'year'];

    public $month;
    public $year;

    public function mount()
    {
        $this->month = request('month', now()->month);
        $this->year = request('year', now()->year);
    }

    public function updatingMonth()
    {
        $this->resetPage();
    }

    public function updatingYear()
    {
        $this->resetPage();
    }

    public function render()
    {
        $user = auth()->user();
        
        $attendances = $user->attendances()
            ->whereMonth('date', $this->month)
            ->whereYear('date', $this->year)
            ->orderBy('date', 'desc')
            ->paginate(20);

        return view('livewire.student.attendance-history', [
            'attendances' => $attendances
        ])->layout('layouts.app', ['title' => 'Riwayat Absensi']);
    }
}