<?php

namespace App\Http\Livewire\Admin;

use Livewire\Component;
use App\Models\User;
use App\Models\Attendance;
use Illuminate\Support\Facades\Cache;

class Monitoring extends Component
{
    public $date;
    public $class = '';
    public $status = '';
    public $classes = [];
    
    public $monitoringData = [];
    public $total = 0;
    public $hadir = 0;
    public $terlambat = 0;
    public $alpha = 0;

    protected $queryString = ['date', 'class', 'status'];

    public function mount()
    {
        $this->date = request('date', today()->format('Y-m-d'));
        $this->classes = $this->getAvailableClasses();
        $this->loadData();
    }

    public function updatedDate()
    {
        $this->loadData();
    }

    public function updatedClass()
    {
        $this->loadData();
    }

    public function updatedStatus()
    {
        $this->loadData();
    }

    public function loadData()
    {
        $cacheKey = "monitoring_{$this->date}_{$this->class}_{$this->status}";
        
        // Cache untuk 2 menit karena data monitoring perlu relatif fresh
        $data = Cache::remember($cacheKey, 120, function () {
            $students = User::students()->approved()
                ->when($this->class, fn($q) => $q->where('class', $this->class))
                ->select('id', 'nisn', 'name', 'class')
                ->get();

            $attendances = Attendance::whereDate('date', $this->date)
                ->with('user:id,nisn,name,class')
                ->select('id', 'user_id', 'date', 'check_in', 'check_out', 'status', 'notes', 'early_checkout_photo')
                ->get()
                ->keyBy('user_id');

            return $students->map(function($student) use ($attendances) {
                $attendance = $attendances->get($student->id);
                
                return [
                    'student' => $student,
                    'attendance' => $attendance,
                    'status' => $attendance ? $attendance->status : 'alpha',
                    'check_in' => $attendance ? $attendance->check_in : null,
                    'check_out' => $attendance ? $attendance->check_out : null,
                ];
            });
        });

        // Filter by status jika ada
        if ($this->status) {
            $data = $data->filter(fn($item) => $item['status'] === $this->status);
        }

        $this->monitoringData = $data->values();
        
        // Hitung statistik
        $this->total = $data->count();
        $this->hadir = $data->where('status', 'hadir')->count();
        $this->terlambat = $data->where('status', 'terlambat')->count();
        $this->alpha = $data->where('status', 'alpha')->count();
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
        return view('livewire.admin.monitoring')
            ->layout('layouts.admin', ['title' => 'Monitoring Absensi']);
    }
}