<?php

namespace App\Observers;

use App\Models\Bill;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;

class BillObserver
{
    /**
     * Handle the Bill "created" event.
     */
    public function created(Bill $bill): void
    {
        //
    }

    /**
     * Handle the Bill "updated" event.
     */
    public function updated(Bill $bill): void
    {
        if ($bill->isDirty('status') && $bill->status === 'paid') {
            
            $namaKategori = $bill->paymentType?->name ?? 'Lainnya';



            $kategoriSlug = strtolower(str_replace(' ', '_', $namaKategori));
            

            if (empty($kategoriSlug)) {
                $kategoriSlug = 'Lainnya';
            }

            Transaction::create([
                'type'          => 'income',
                'amount'        => $bill->amount,
                

                'category'      => $kategoriSlug, 
                
                'date'          => now(),
                

                'description'   => "Tagihan: {$namaKategori} dari siswa " . ($bill->siswa->nama ?? 'Siswa'),
                
                'user_id'       => Auth::id() ?? 1,
                'reference_type' => Bill::class,
                'reference_id'   => $bill->id,
                'proof_image'    => $bill->proof_of_payment,
            ]);
        }


        if ($bill->isDirty('status') && $bill->getOriginal('status') === 'paid' && $bill->status !== 'paid') {
            Transaction::where('reference_type', Bill::class)
                ->where('reference_id', $bill->id)
                ->delete();
        }
    }

    /**
     * Handle the Bill "deleted" event.
     */
    public function deleted(Bill $bill): void
    {
        Transaction::where('reference_type', Bill::class)
            ->where('reference_id', $bill->id)
            ->delete();
    }

    /**
     * Handle the Bill "restored" event.
     */
    public function restored(Bill $bill): void
    {
        //
    }

    /**
     * Handle the Bill "force deleted" event.
     */
    public function forceDeleted(Bill $bill): void
    {
        //
    }
}
