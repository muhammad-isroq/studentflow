<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Bill;
use App\Models\Transaction;
use Illuminate\Support\Str;

class ImportPaidBillsToTransactions extends Command
{
    /**
     * Nama perintah yang akan diketik di terminal
     */
    protected $signature = 'bills:import-paid';

    /**
     * Deskripsi perintah
     */
    protected $description = 'Menyalin data Bill status PAID ke tabel Transactions agar masuk Buku Kas';

    public function handle()
    {
        $this->info('Memulai proses import data tagihan lunas ke buku kas...');

        // 1. Ambil semua Bill yang statusnya 'paid'
        // Kita gunakan with() agar query lebih cepat (Eager Loading)
        $paidBills = Bill::where('status', 'paid')
            ->with(['siswa', 'paymentType'])
            ->get();

        $count = 0;
        $skipped = 0;

        $bar = $this->output->createProgressBar(count($paidBills));
        $bar->start();

        foreach ($paidBills as $bill) {
            // 2. Buat Deskripsi Unik untuk mencegah Duplikat
            // Kita akan menandai transaksi ini berasal dari ID Bill sekian
            $description = "Pembayaran " . ($bill->paymentType->name ?? 'Tagihan') . 
                           " - " . ($bill->siswa->nama ?? 'Siswa') . 
                           " (Ref Bill #{$bill->id})";

            // 3. Cek apakah transaksi ini sudah pernah di-import sebelumnya?
            // Kita cek berdasarkan deskripsi yang mengandung ID unik tadi
            $exists = Transaction::where('description', $description)->exists();

            if ($exists) {
                $skipped++;
                $bar->advance();
                continue;
            }

            // 4. Tentukan Tanggal Transaksi
            // Idealnya pakai 'updated_at' (waktu saat status berubah jadi paid)
            // Jika Anda punya kolom 'paid_at' di tabel bills, ganti '$bill->updated_at' dengan '$bill->paid_at'
            $transactionDate = $bill->updated_at; 

            // 5. Buat Data Transaksi Baru
            Transaction::create([
                'type'        => 'income', // Pasti pemasukan
                'amount'      => $bill->amount,
                'category'    => Str::slug($bill->paymentType->name ?? 'general'), // Format kategori biar rapi (misal: monthly-spp)
                'date'        => $transactionDate,
                'description' => $description,
                'created_at'  => $transactionDate, // Samakan waktu buatnya
                'updated_at'  => $transactionDate,
            ]);

            $count++;
            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);
        
        $this->info("Selesai!");
        $this->info("Berhasil import: {$count} data.");
        $this->comment("Dilewati (sudah ada): {$skipped} data.");
    }
}