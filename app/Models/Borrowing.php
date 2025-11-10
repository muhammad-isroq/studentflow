<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
// Impor model yang akan kita hubungkan
use App\Models\Inventory;
// use App\Models\Siswa; // <-- KITA HAPUS INI
// Tambahkan ini untuk Observer
use App\Observers\BorrowingObserver; 
use Illuminate\Database\Eloquent\Attributes\ObservedBy;

// Daftarkan Observer
#[ObservedBy([BorrowingObserver::class])]
class Borrowing extends Model
{
    use HasFactory;

    protected $fillable = [
        'inventory_id',
        'borrower_name', 
        'quantity',
        'borrow_date',
        'due_date',
        'return_date',
    ];


    protected $casts = [
        'borrow_date' => 'date',
        'due_date' => 'date',
        'return_date' => 'date',
    ];

    public function inventory(): BelongsTo
    {
        return $this->belongsTo(Inventory::class);
    }


}