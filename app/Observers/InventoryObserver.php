<?php

namespace App\Observers;

use App\Models\Inventory;
use App\Models\StockLog;
use Illuminate\Support\Facades\Auth;

class InventoryObserver
{
    /**
     * Handle the Inventory "created" event.
     */
    public function created(Inventory $inventory): void
    {
        // Hanya catat jika stok awal lebih dari 0
        if ($inventory->jumlah > 0) {
            StockLog::create([
                'inventory_id' => $inventory->id,
                'change_amount' => $inventory->jumlah, // Positif karena stok awal
                'stock_after_change' => $inventory->jumlah,
                'reason' => 'Stok Awal (Barang Baru Dibuat)',
                'user_id' => Auth::id(), // Mencatat siapa yang membuat barang ini
            ]);
        }
    }

    /**
     * Handle the Inventory "updated" event.
     */
    public function updated(Inventory $inventory): void
    {
       //
    }

    /**
     * Handle the Inventory "deleted" event.
     */
    public function deleted(Inventory $inventory): void
    {
        //
    }

    /**
     * Handle the Inventory "restored" event.
     */
    public function restored(Inventory $inventory): void
    {
        //
    }

    /**
     * Handle the Inventory "force deleted" event.
     */
    public function forceDeleted(Inventory $inventory): void
    {
        //
    }
}
