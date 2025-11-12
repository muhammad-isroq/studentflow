<?php

namespace App\Observers;

use App\Models\Borrowing;
use App\Models\Inventory;
use App\Models\StockLog;

class BorrowingObserver
{
    /**
     * Handle the Borrowing "created" event.
     */
    public function created(Borrowing $borrowing): void
    {
        $inventory = $borrowing->inventory;
        if ($inventory) {
            // 1. Kurangi stok
            $inventory->decrement('jumlah', $borrowing->quantity); 

            // 2. Buat log (AKTIFKAN INI)
            StockLog::create([
                'inventory_id' => $inventory->id,
                'change_amount' => -$borrowing->quantity,
                'stock_after_change' => $inventory->jumlah,
                'reason' => "Dipinjam {$borrowing->quantity} unit oleh: " . $borrowing->borrower_name,
                'user_id' => auth()->id(),
            ]);
        }
    }

    public function updated(Borrowing $borrowing): void
    {
        if ($borrowing->isDirty('return_date') && $borrowing->return_date !== null) {
            $inventory = $borrowing->inventory;
            if ($inventory) {
                // 1. Tambah stok
                $inventory->increment('jumlah', $borrowing->quantity); 

                // 2. Buat log (AKTIFKAN INI)
                StockLog::create([
                    'inventory_id' => $inventory->id,
                    'change_amount' => +$borrowing->quantity,
                    'stock_after_change' => $inventory->jumlah,
                    'reason' => "Dikembalikan {$borrowing->quantity} unit oleh: " . $borrowing->borrower_name,
                    'user_id' => auth()->id(),
                ]);
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
