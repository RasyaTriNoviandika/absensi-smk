<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Models\User;
use App\Helpers\PhoneHelper;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\StudentsExport;
use App\Imports\StudentsImport;

class Students extends Component
{
    use WithPagination, WithFileUploads;

    protected $paginationTheme = 'tailwind';
    protected $queryString = ['search', 'class', 'status'];

    // Filters
    public $search = '';
    public $class = '';
    public $status = 'approved';
    
    // Modals
    public $showDeleteModal = false;
    public $showResetPasswordModal = false;
    public $showEditModal = false;
    public $showImportModal = false;
    public $showBulkActionModal = false;
    
    // Selected items
    public $deleteId = null;
    public $resetPasswordId = null;
    public $editId = null;
    public $selectedIds = [];
    public $selectAll = false;
    
    // Edit form
    public $name = '';
    public $nisn = '';
    public $email = '';
    public $phone = '';
    public $editClass = '';
    public $username = '';
    
    // Reset password
    public $newPassword = '';
    
    // Import
    public $importFile;
    public $importResults = [];
    
    // Bulk action
    public $bulkAction = '';

    protected $listeners = ['refreshStudents' => '$refresh'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingClass()
    {
        $this->resetPage();
    }

    public function updatingStatus()
    {
        $this->resetPage();
    }

    // ==================== DELETE ====================
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

    // ==================== RESET PASSWORD ====================
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

    // ==================== EDIT ====================
    public function openEditModal($userId)
    {
        $user = User::findOrFail($userId);
        
        $this->editId = $user->id;
        $this->name = $user->name;
        $this->nisn = $user->nisn;
        $this->email = $user->email;
        $this->phone = $user->phone;
        $this->editClass = $user->class;
        $this->username = $user->username;
        
        $this->showEditModal = true;
    }

    public function cancelEdit()
    {
        $this->resetEditForm();
        $this->showEditModal = false;
    }

    public function updateStudent()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'nisn' => 'required|digits:10|unique:users,nisn,' . $this->editId,
            'editClass' => 'required|string',
            'email' => 'nullable|email|unique:users,email,' . $this->editId,
            'phone' => 'nullable|string|min:10|max:15',
            'username' => 'required|string|max:255|unique:users,username,' . $this->editId,
        ]);

        try {
            $user = User::findOrFail($this->editId);
            
            $user->update([
                'name' => $this->name,
                'nisn' => $this->nisn,
                'class' => $this->editClass,
                'email' => $this->email,
                'phone' => PhoneHelper::normalize($this->phone),
                'username' => $this->username,
            ]);
            
            session()->flash('success', 'Data siswa berhasil diupdate.');
            $this->cancelEdit();
            
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal update data: ' . $e->getMessage());
        }
    }

    private function resetEditForm()
    {
        $this->editId = null;
        $this->name = '';
        $this->nisn = '';
        $this->email = '';
        $this->phone = '';
        $this->editClass = '';
        $this->username = '';
    }

    // ==================== IMPORT ====================
    public function openImportModal()
    {
        $this->showImportModal = true;
    }

    public function cancelImport()
    {
        $this->importFile = null;
        $this->importResults = [];
        $this->showImportModal = false;
    }

    public function importStudents()
    {
        $this->validate([
            'importFile' => 'required|mimes:xlsx,xls,csv|max:5120'
        ]);

        try {
            $import = new StudentsImport();
            Excel::import($import, $this->importFile);
            
            $this->importResults = $import->getResults();
            
            $successCount = collect($this->importResults)->where('status', 'Berhasil')->count();
            
            session()->flash('success', "Import selesai! {$successCount} siswa berhasil ditambahkan.");
            
        } catch (\Exception $e) {
            session()->flash('error', 'Import gagal: ' . $e->getMessage());
        }
    }

    public function downloadTemplate()
    {
        return Excel::download(new \App\Exports\StudentsTemplateExport(), 'template_siswa.xlsx');
    }

    // ==================== EXPORT ====================
    public function exportExcel()
    {
        return Excel::download(
            new StudentsExport($this->search, $this->class, $this->status), 
            'siswa_' . now()->format('Y-m-d_His') . '.xlsx'
        );
    }

    // ==================== BULK ACTIONS ====================
    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selectedIds = $this->getStudentsQuery()->pluck('id')->toArray();
        } else {
            $this->selectedIds = [];
        }
    }

    public function openBulkActionModal()
    {
        if (empty($this->selectedIds)) {
            session()->flash('error', 'Pilih siswa terlebih dahulu.');
            return;
        }
        
        $this->showBulkActionModal = true;
    }

    public function cancelBulkAction()
    {
        $this->bulkAction = '';
        $this->showBulkActionModal = false;
    }

    public function executeBulkAction()
    {
        if (empty($this->selectedIds)) {
            session()->flash('error', 'Tidak ada siswa yang dipilih.');
            return;
        }

        DB::beginTransaction();
        
        try {
            $count = count($this->selectedIds);
            
            switch ($this->bulkAction) {
                case 'delete':
                    User::whereIn('id', $this->selectedIds)
                        ->where('role', 'student')
                        ->delete();
                    session()->flash('success', "{$count} siswa berhasil dihapus.");
                    break;
                    
                case 'approve':
                    User::whereIn('id', $this->selectedIds)
                        ->where('role', 'student')
                        ->update(['status' => 'approved']);
                    session()->flash('success', "{$count} siswa berhasil di-approve.");
                    break;
                    
                case 'reject':
                    User::whereIn('id', $this->selectedIds)
                        ->where('role', 'student')
                        ->update(['status' => 'rejected']);
                    session()->flash('success', "{$count} siswa berhasil di-reject.");
                    break;
                    
                default:
                    session()->flash('error', 'Aksi tidak valid.');
            }
            
            DB::commit();
            
            $this->selectedIds = [];
            $this->selectAll = false;
            $this->cancelBulkAction();
            
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Bulk action gagal: ' . $e->getMessage());
        }
    }

    // ==================== HELPERS ====================
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

    private function getStudentsQuery()
    {
        $query = User::students();

        if ($this->status) {
            if ($this->status === 'approved') {
                $query->approved();
            } elseif ($this->status === 'pending') {
                $query->pending();
            } elseif ($this->status === 'rejected') {
                $query->where('status', 'rejected');
            }
        }

        if ($this->class) {
            $query->where('class', $this->class);
        }

        if ($this->search) {
            $query->where(function($q) {
                $q->where('name', 'like', "%{$this->search}%")
                  ->orWhere('nisn', 'like', "%{$this->search}%")
                  ->orWhere('username', 'like', "%{$this->search}%");
            });
        }

        return $query;
    }

    // ==================== RENDER ====================
    public function render()
    {
        $students = $this->getStudentsQuery()->orderBy('name')->paginate(20);
        $classes = $this->getAvailableClasses();

        return view('livewire.admin.students', [
            'students' => $students,
            'classes' => $classes
        ])->layout('layouts.admin', ['title' => 'Data Siswa']);
    }
}