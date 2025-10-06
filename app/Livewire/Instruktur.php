<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\User;

class Instruktur extends Component
{
    public $teachers;

    public function mount()
    {
        // Ambil semua user yang hanya memiliki role 'guru'
        $this->teachers = User::role('guru')->orderBy('name', 'asc')->get();
    }
    
    public function render()
    {
        return view('livewire.instruktur');
    }
}
