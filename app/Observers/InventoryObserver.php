<?php

namespace App\Observers;

use App\Models\Inventory;
use App\Models\StockMovement;

class InventoryObserver
{
    /**
     * Handle the Inventory "created" event.
     */
    public function created(Inventory $inventory): void
    {
        if ($inventory->jumlah > 0) {
            StockMovement::create([
                'inventory_id' => $inventory->id,
                'user_id' => auth()->id(), // Ambil ID user yang sedang login
                'quantity' => $inventory->jumlah, // Kuantitas awal adalah barang masuk
                'notes' => 'Barang baru ditambahkan',
            ]);
        }
    }

    /**
     * Handle the Inventory "updated" event.
     */
    public function updated(Inventory $inventory): void
    {
       if ($inventory->isDirty('jumlah')) {

            // Ambil jumlah asli (sebelum diubah)
            $originalQuantity = $inventory->getOriginal('jumlah') ?? 0;

            // Ambil jumlah baru (dari form)
            $newQuantity = $inventory->jumlah;

            // Hitung perbedaannya
            $difference = $newQuantity - $originalQuantity;

            // Jika ada perbedaan, buat log
            if ($difference != 0) {
                StockMovement::create([
                    'inventory_id' => $inventory->id,
                    'user_id' => auth()->id(),
                    'quantity' => $difference, // Bisa positif (masuk) atau negatif (keluar)
                    'notes' => 'Stok diupdate manual',
                ]);
            }
        }
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
