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
        // Ambil semua user yang memiliki role 'staff' atau 'guru'
        // $this->staffs = User::role(['staff', 'editor'])->orderBy('name', 'asc')->get();

        // PERBAIKAN: Nama variabel diubah dari $teachers menjadi $staffs
        $this->staffs = User::role('staff')
            ->orderByRaw("
                CASE 
                    WHEN name LIKE '%Mr. Acca Manurung, S.IP%' THEN 1  
                    WHEN name LIKE '%Mr. Mantro%' THEN 2 
                    WHEN name LIKE '%Ms. Mega%' THEN 3  
                    WHEN name LIKE '%Ms. Riska%' THEN 3  
                    WHEN name LIKE '%Ms. Ulfa%' THEN 3  
                    WHEN name LIKE '%Ms. Ratyh%' THEN 3  
                    WHEN name LIKE '%Mr. Randy%' THEN 3  
                    WHEN name LIKE '%Ms. Fathiyya%' THEN 3  
                    WHEN name LIKE '%Ms. Dwi%' THEN 3  
                    WHEN name LIKE '%Mr. Isroq%' THEN 3  
                    ELSE 4 
                END
            ")
            ->get();
    }

    public function render()
    {
        return view('livewire.staff');
    }
}

