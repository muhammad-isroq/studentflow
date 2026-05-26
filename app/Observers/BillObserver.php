<?php

namespace App\Observers;

use App\Models\Bill;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;

class BillObserver
{
    /**
     * Handle the Bill "saved" event.
     * Fungsi 'saved' dipanggil otomatis baik saat Create maupun Update.
     */
    public function saved(Bill $bill): void
    {
        // 1. Jika status diubah dari Lunas menjadi Belum Lunas, hapus dari Buku Kas
        if ($bill->isDirty('status') && $bill->getOriginal('status') === 'paid' && $bill->status !== 'paid') {
            Transaction::where('reference_type', Bill::class)
                ->where('reference_id', $bill->id)
                ->delete();
            return;
        }

        // 2. Jika statusnya Lunas, sinkronkan ke Buku Kas
        if ($bill->status === 'paid') {
            
            $namaKategori = $bill->paymentType?->name ?? 'Lainnya';
            $kategoriSlug = strtolower(str_replace(' ', '_', $namaKategori));
            
            if (empty($kategoriSlug)) {
                $kategoriSlug = 'lainnya';
            }

            // Menentukan subjek pihak transaksi (Siswa / Pihak Luar)
            $pihak = $bill->paid_by ?: ($bill->siswa ? $bill->siswa->nama : '-');
            
            // Menambahkan notes jika diisi
            $catatan = $bill->notes ? " | Catatan: {$bill->notes}" : '';

            // Merakit kalimat deskripsi cerdas berdasarkan Arus Kas
            if ($bill->transaction_type === 'expense') {
                $deskripsi = "Pengeluaran ({$namaKategori}) Kpd: {$pihak}{$catatan}";
            } else {
                $deskripsi = "Pemasukan ({$namaKategori}) Dari: {$pihak}{$catatan}";
            }

            // Gunakan updateOrCreate agar tidak terjadi duplikasi data 
            // jika admin menyimpan ulang tagihan yang sudah lunas
            Transaction::updateOrCreate(
                [
                    'reference_type' => Bill::class,
                    'reference_id'   => $bill->id,
                ],
                [
                    'type'        => $bill->transaction_type ?? 'income', 
                    'amount'      => $bill->amount,
                    'category'    => $kategoriSlug, 
                    'date'        => $bill->paid_at ?? now(),
                    'description' => $deskripsi,
                    'user_id'     => Auth::id() ?? 1,  
                    'proof_image' => $bill->proof_of_payment,
                ]
            );
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
}