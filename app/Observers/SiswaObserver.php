<?php

namespace App\Observers;

use App\Models\Siswa;
use App\Models\Bill;
use App\Models\PaymentType;
use App\Models\Program;

class SiswaObserver
{
    /**
     * Handle the Siswa "created" event.
     */
    public function created(Siswa $siswa): void
    {
        
        if ($siswa->registration_fee && $siswa->registration_fee > 0) {
            
            
            $paymentType = PaymentType::where('name', 'Registration')->first();

            
            if ($paymentType) {
                Bill::create([
                    'siswa_id' => $siswa->id,
                    'payment_type_id' => $paymentType->id,
                    'amount' => $siswa->registration_fee,
                    'due_date' => $siswa->tgl_registrasi, 
                    'status' => 'paid', 
                    'paid_at' => $siswa->tgl_registrasi, 
                    'proof_of_payment' => $siswa->registration_proof, 
                ]);
            }
        }

        
    }

    /**
     * Handle the Siswa "updated" event.
     */
    public function updated(Siswa $siswa): void
    {
        //
    }

    /**
     * Handle the Siswa "deleted" event.
     */
    public function deleted(Siswa $siswa): void
    {
        //
    }

    /**
     * Handle the Siswa "restored" event.
     */
    public function restored(Siswa $siswa): void
    {
        //
    }

    /**
     * Handle the Siswa "force deleted" event.
     */
    public function forceDeleted(Siswa $siswa): void
    {
        //
    }
}
