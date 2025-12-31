<?php

namespace App\Http\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class Students extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind';
    protected $queryString = ['search', 'class'];

    public $search = '';
    public $class = '';
    
    // Properties untuk modal delete
    public $deleteId = null;
    public $showDeleteModal = false;
    
    // Properties untuk modal reset password
    public $resetPasswordId = null;
    public $showResetPasswordModal = false;
    public $newPassword = '';

    protected $listeners = ['refreshStudents' => '$refresh'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingClass()
    {
        $this->resetPage();
    }

    public function confirmDelete($userId)
    {
        $this->deleteId = $userId;
        $this->showDeleteModal = true;
    }

    public function cancelDelete()
    {
        $this->deleteId = null;
        $this->showDeleteModal = false;
    }

    public function deleteStudent()
    {
        try {
            $user = User::findOrFail($this->deleteId);
            
            if ($user->role !== 'student') {
                session()->flash('error', 'Hanya siswa yang bisa dihapus.');
                $this->cancelDelete();
                return;
            }
            
            $name = $user->name;
            $user->delete();
            
            session()->flash('success', "Siswa {$name} berhasil dihapus.");
            $this->cancelDelete();
            
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal menghapus siswa: ' . $e->getMessage());
            $this->cancelDelete();
        }
    }

    public function confirmResetPassword($userId)
    {
        $this->resetPasswordId = $userId;
        $this->newPassword = 'student' . rand(1000, 9999);
        $this->showResetPasswordModal = true;
    }

    public function cancelResetPassword()
    {
        $this->resetPasswordId = null;
        $this->newPassword = '';
        $this->showResetPasswordModal = false;
    }

    public function resetPassword()
    {
        try {
            $user = User::findOrFail($this->resetPasswordId);
            
            if ($user->role !== 'student') {
                session()->flash('error', 'Hanya password siswa yang bisa direset.');
                $this->cancelResetPassword();
                return;
            }
            
            $user->update(['password' => Hash::make($this->newPassword)]);
            
            session()->flash('success', "Password {$user->name} berhasil direset menjadi: {$this->newPassword}");
            $this->cancelResetPassword();
            
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal reset password: ' . $e->getMessage());
            $this->cancelResetPassword();
        }
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