<?php

namespace App\Http\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;

class Students extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind';
    protected $queryString = ['search', 'class'];

    public $search = '';
    public $class = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingClass()
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
        $query = User::students()->approved();

        if ($this->class) {
            $query->where('class', $this->class);
        }

        if ($this->search) {
            $query->where(function($q) {
                $q->where('name', 'like', "%{$this->search}%")
                  ->orWhere('nisn', 'like', "%{$this->search}%");
            });
        }

        $students = $query->orderBy('name')->paginate(20);
        $classes = $this->getAvailableClasses();

        return view('livewire.admin.students', [
            'students' => $students,
            'classes' => $classes
        ])->layout('layouts.admin', ['title' => 'Data Siswa']);
    }
}
