<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\User; 

class Home extends Component
{
    public $teachers;

    public function mount()
    {
        $this->teachers = User::role('guru')->orderBy('name', 'asc')->get();
    }

    public function render()
    {
        return view('livewire.home');
    }
}
