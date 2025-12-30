<?php

namespace App\Http\Livewire\Admin;

use Livewire\Component;
use App\Models\User;
use App\Models\Attendance;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class Reports extends Component
{
    public $month;
    public $year;
    public $class = '';
    public $classes = [];

    protected $queryString = ['month', 'year', 'class'];

    public function mount()
    {
        $this->month = request('month', now()->month);
        $this->year = request('year', now()->year);
        $this->classes = $this->getAvailableClasses();
    }

    private function getAvailableClasses()
    {
        $tingkat = [10, 11, 12];
        $jurusan = ['DKV', 'SIJA', 'PB'];
        $rombel = [1, 2, 3];

        $classes = [];
        foreach ($tingkat as $t) {
            foreach ($jurusan as $j) {
                foreach ($rombel as $r) {
                    $classes[] = "$t $j $r";
                }
            }
        }
        return $classes;
    }

    public function render()
    {
        $cacheKey = "reports_{$this->year}_{$this->month}_{$this->class}";
        
        $reportData = Cache::remember($cacheKey, 600, function () {
            $query = User::students()
                ->approved()
                ->select('id', 'name', 'nisn', 'class')
                ->when($this->class, fn($q) => $q->where('class', $this->class));

            $allAttendances = Attendance::whereMonth('date', $this->month)
                ->whereYear('date', $this->year)
                ->select('user_id', 'status')
                ->get()
                ->groupBy('user_id');

            $totalDays = Carbon::createFromDate($this->year, $this->month, 1)->daysInMonth;

            return $query->get()->map(function($student) use ($allAttendances, $totalDays) {
                $studentAttendances = $allAttendances->get($student->id, collect());
                
                $hadir = $studentAttendances->where('status', 'hadir')->count();
                $terlambat = $studentAttendances->where('status', 'terlambat')->count();
                $alpha = $totalDays - ($hadir + $terlambat);
                $percentage = $totalDays > 0 ? (($hadir + $terlambat) / $totalDays) * 100 : 0;

                return [
                    'student' => $student,
                    'hadir' => $hadir,
                    'terlambat' => $terlambat,
                    'alpha' => $alpha,
                    'percentage' => round($percentage, 1),
                ];
            });
        });

        return view('livewire.admin.reports', [
            'reportData' => $reportData
        ])->layout('layouts.admin', ['title' => 'Laporan Absensi']);
    }
}