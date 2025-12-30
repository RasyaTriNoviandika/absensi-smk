<?php

namespace App\Http\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Attendance;
use App\Models\User;

class History extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind';
    protected $queryString = ['start_date', 'end_date', 'class', 'student_id', 'status'];

    public $start_date = '';
    public $end_date = '';
    public $class = '';
    public $student_id = '';
    public $status = '';
    public $classes = [];
    public $students = [];

    public function mount()
    {
        $this->classes = $this->getAvailableClasses();
        $this->students = User::students()->approved()
            ->select('id', 'name', 'nisn')
            ->orderBy('name')
            ->get();
    }

    public function updatingStartDate()
    {
        $this->resetPage();
    }

    public function updatingEndDate()
    {
        $this->resetPage();
    }

    public function updatingClass()
    {
        $this->resetPage();
    }

    public function updatingStudentId()
    {
        $this->resetPage();
    }

    public function updatingStatus()
    {
        $this->resetPage();
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
        $query = Attendance::with(['user:id,name,nisn,class'])
            ->select('id', 'user_id', 'date', 'check_in', 'check_out', 'status', 'notes', 'early_checkout_photo');

        // Filter date range
        if ($this->start_date && $this->end_date) {
            $query->whereBetween('date', [$this->start_date, $this->end_date]);
        } else {
            $query->where('date', '>=', now()->subMonth());
        }

        // Filter class
        if ($this->class) {
            $query->whereHas('user', fn($q) => $q->where('class', $this->class));
        }

        // Filter student
        if ($this->student_id) {
            $query->where('user_id', $this->student_id);
        }

        // Filter status
        if ($this->status) {
            $query->where('status', $this->status);
        }

        $attendances = $query->orderBy('date', 'desc')
            ->orderBy('check_in', 'asc')
            ->paginate(25);

        return view('livewire.admin.history', [
            'attendances' => $attendances
        ])->layout('layouts.admin', ['title' => 'History Absensi']);
    }
}