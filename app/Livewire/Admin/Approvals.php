<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;

class Approvals extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind';

    public function approve($userId)
    {
        $user = User::findOrFail($userId);
        
        if ($user->status !== 'pending') {
            session()->flash('error', 'User sudah di-approve atau reject.');
            return;
        }

        $user->update(['status' => 'approved']);
        session()->flash('success', "Akun {$user->name} berhasil di-approve.");
    }

    public function reject($userId)
    {
        $user = User::findOrFail($userId);
        
        if ($user->status !== 'pending') {
            session()->flash('error', 'User sudah di-approve atau reject.');
            return;
        }

        $user->update(['status' => 'rejected']);
        session()->flash('success', "Akun {$user->name} ditolak.");
    }

    public function render()
    {
        return view('livewire.admin.approvals', [
            'pendingUsers' => User::students()
                ->pending()
                ->orderBy('created_at', 'desc')
                ->paginate(20)
        ])->layout('layouts.admin', ['title' => 'Approval Siswa']);
    }
}