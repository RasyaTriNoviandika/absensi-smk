<?php

namespace App\Livewire\Student;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class UserStatus extends Component
{
    public $status = null;

    public function mount()
    {
        if (Auth::check()) {
            $this->status = Auth::user()->status;
        } else {
            $this->status = 'guest';
        }
    }

    public function render()
    {
        return view('livewire.student.user-status');
    }
}
