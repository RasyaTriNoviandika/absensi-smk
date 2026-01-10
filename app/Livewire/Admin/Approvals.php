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
            session()->flash('error', 'User sudah di-approve atau ditolak.');
            return;
        }

        $user->status = 'approved';
        $user->save();

        session()->flash('success', "Akun {$user->name} telah di-approve.");

        // update notif menu navigasi approval
        $this->dispatch('approval-updated');

    }

    public function reject($userId)
    {
        $user = User::findOrFail($userId);

        if ($user->status !== 'pending') {
            session()->flash('error', 'User sudah di-approve atau ditolak.');
            return;
        }

        $user->status = 'rejected';
        $user->save();

        session()->flash('success', "Akun {$user->name} berhasil ditolak.");

        // update notif menu navigasi approval
        $this->dispatch('approval-updated');
    }

    public function approveAll()
{
    $count = User::where('status', 'pending')->count();

    if ($count == 0) {
        session()->flash('error', 'Tidak ada akun pending.');
        return;
    }

    User::where('status', 'pending')->update(['status' => 'approved']);
    session()->flash('success', "$count akun berhasil di-approve.");

    // update notif menu navigasi approval
    $this->dispatch('approval-updated');
}

public function rejectAll()
{
    $count = User::where('status', 'pending')->count();

    if ($count == 0) {
        session()->flash('error', 'Tidak ada akun pending.');
        return;
    }

    User::where('status', 'pending')->update(['status' => 'rejected']);
    session()->flash('success', "$count akun berhasil di-reject.");

    // update notif menu navigasi approval
    $this->dispatch('approval-updated');
}

    public function render()
    {
        return view('livewire.admin.approvals', [
            'pendingUsers' => User::students()
                ->pending()
                ->latest()
                ->paginate(20)
        ])->layout('layouts.admin', [
            'title' => 'Approval Siswa'
        ]);
    }
}
