<?php

namespace App\Observers;

use App\Models\Borrowing;
use App\Models\Inventory;
// use App\Models\StockLog;

class BorrowingObserver
{
    /**
     * Handle the Borrowing "created" event.
     */
    public function created(Borrowing $borrowing): void
    {
        $inventory = $borrowing->inventory;
        if ($inventory) {
            // 1. Kurangi stok (SESUAI JUMLAH)
            $inventory->decrement('jumlah', $borrowing->quantity); // <-- UBAH INI

            // 2. Buat log (SESUAI JUMLAH)
            // StockLog::create([
            //     'inventory_id' => $inventory->id,
            //     'change_amount' => -$borrowing->quantity, // <-- UBAH INI
            //     'stock_after_change' => $inventory->jumlah,
            //     'reason' => "Dipinjam {$borrowing->quantity} unit oleh: " . $borrowing->borrower_name, // <-- UBAH INI
            // ]);
        }
    }

    /**
     * Handle the Borrowing "updated" event.
     */
    public function updated(Borrowing $borrowing): void
    {
        // Cek apakah 'return_date' baru saja diisi
        if ($borrowing->isDirty('return_date') && $borrowing->return_date !== null) {
            
            $inventory = $borrowing->inventory;
            if ($inventory) {
                // 1. Tambah stok (SESUAI JUMLAH)
                $inventory->increment('jumlah', $borrowing->quantity); // <-- UBAH INI
                
                // 2. Buat log (SESUAI JUMLAH)
                // StockLog::create([
                //     'inventory_id' => $inventory->id,
                //     'change_amount' => +$borrowing->quantity, // <-- UBAH INI
                //     'stock_after_change' => $inventory->jumlah,
                //     'reason' => "Dikembalikan {$borrowing->quantity} unit oleh: " . $borrowing->borrower_name, // <-- UBAH INI
                // ]);
            }
        }
    }

    /**
     * Handle the Borrowing "deleted" event.
     */
    public function deleted(Borrowing $borrowing): void
    {
        //
    }

    /**
     * Handle the Borrowing "restored" event.
     */
    public function restored(Borrowing $borrowing): void
    {
        //
    }

    /**
     * Handle the Borrowing "force deleted" event.
     */
    public function forceDeleted(Borrowing $borrowing): void
    {
        //
    }
}
