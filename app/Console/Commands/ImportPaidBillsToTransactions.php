<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Bill;
use App\Models\Transaction;
use App\Models\User; // <--- TAMBAHAN 1
use Illuminate\Support\Str;

class ImportPaidBillsToTransactions extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'bills:import-paid';

    /**
     * The console command description.
     */
    protected $description = 'Menyalin data Bill status PAID ke tabel Transactions agar masuk Buku Kas';

    public function handle()
    {
        $this->info('Memulai proses import data tagihan lunas ke buku kas...');

        // 1. Ambil User ID untuk mengisi kolom 'user_id'
        // Kita ambil user pertama di database (biasanya Super Admin)
        $admin = User::first();
        $adminId = $admin ? $admin->id : 1; // Jika tidak ada user, paksa ID 1

        // 2. Ambil semua Bill yang statusnya 'paid'
        $paidBills = Bill::where('status', 'paid')
            ->with(['siswa', 'paymentType'])
            ->get();

        $count = 0;
        $skipped = 0;

        $bar = $this->output->createProgressBar(count($paidBills));
        $bar->start();

        foreach ($paidBills as $bill) {
            // Buat Deskripsi Unik
            $description = "Pembayaran " . ($bill->paymentType->name ?? 'Tagihan') . 
                           " - " . ($bill->siswa->nama ?? 'Siswa') . 
                           " (Ref Bill #{$bill->id})";

            // Cek duplikat
            $exists = Transaction::where('description', $description)->exists();

            if ($exists) {
                $skipped++;
                $bar->advance();
                continue;
            }

            $transactionDate = $bill->updated_at; 

            // Simpan Transaksi
            Transaction::create([
                'user_id'     => $adminId, // <--- TAMBAHAN 2 (Solusi Error Anda)
                'type'        => 'income',
                'amount'      => $bill->amount,
                'category'    => Str::slug($bill->paymentType->name ?? 'general'),
                'date'        => $transactionDate,
                'description' => $description,
                'created_at'  => $transactionDate,
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