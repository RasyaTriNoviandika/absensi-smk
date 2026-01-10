<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\User;

class ApprovalBadge extends Component
{
    protected $listeners = ['approval-updated' => '$refresh'];

    public function render()
    {
        return view('livewire.admin.approval-badge', [
            'count' => User::where('status', 'pending')->count()
        ]);
    }
}
