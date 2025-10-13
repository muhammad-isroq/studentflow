<?php

namespace App\Livewire;

use App\Models\User;
use Livewire\Component;
use Illuminate\Support\Facades\DB; 

class Staff extends Component
{

    public $staffs;

    public function mount()
    {
        
        $this->staffs = User::role('staff')
            ->orderByRaw("
                CASE 
                    WHEN name LIKE '%Mr. Acca Manurung, S.IP%' THEN 1  
                    WHEN name LIKE '%Mr. Mantro%' THEN 2 
                    WHEN name LIKE '%Ms. Mega%' THEN 3  
                    WHEN name LIKE '%Ms. Riska%' THEN 4  
                    WHEN name LIKE '%Ms. Ulfa%' THEN 5  
                    WHEN name LIKE '%Ms. Ratyh%' THEN 6  
                    WHEN name LIKE '%Mr. Randy%' THEN 7  
                    WHEN name LIKE '%Ms. Fathiyya%' THEN 8  
                    WHEN name LIKE '%Ms. Dwi%' THEN 9  
                    WHEN name LIKE '%Mr. Isroq%' THEN 10  
                    ELSE 11 
                END
            ")
            ->get();
    }

    public function render()
    {
        return view('livewire.staff');
    }
}

