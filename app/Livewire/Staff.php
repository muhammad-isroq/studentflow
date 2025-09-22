<?php

namespace App\Livewire;

use App\Models\User;
use Livewire\Component;

class Staff extends Component
{

    public $staffs;

    public function mount()
    {
        // Ambil semua user yang memiliki role 'staff' atau 'guru'
        $this->staffs = User::role(['staff', 'editor'])->get();
    }

    public function render()
    {
        return view('livewire.staff');
    }
}
