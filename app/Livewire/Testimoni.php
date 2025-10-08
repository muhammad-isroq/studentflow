<?php

namespace App\Livewire;
use App\Models\VideoTestimoni;

use Livewire\Component;

class Testimoni extends Component
{
    public $videoTestimonis;

    public function mount()
    {
        $this->videoTestimonis = VideoTestimoni::latest()->get();
    }

    public function render()
    {
        return view('livewire.testimoni');
    }
}
