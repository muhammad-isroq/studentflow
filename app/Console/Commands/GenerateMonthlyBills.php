<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Siswa;
use App\Models\Bill;
use App\Models\PaymentType;
use Carbon\Carbon;

class GenerateMonthlyBills extends Command
{
    /**
     * The name and signature of the console command.
     * @var string
     */
    protected $signature = 'bills:generate-monthly';

    /**
     * The console command description.
     * @var string
     */
    protected $description = 'Generate monthly SPP bills for active students based on their individual billing day.';

    /**
     * Execute the console command.
     */
    public function handle()
{
    $this->info('Starting to generate monthly SPP bills...');

    $sppType = PaymentType::where('name', 'Monthly spp')->first();
    if (!$sppType) {
        $this->error('"Monthly spp" payment type not found!');
        return 1;
    }

    $studentsToBill = Siswa::where('status', 'active')
        ->where('billing_day', Carbon::now()->day)
        ->get();

    $generatedCount = 0;

    foreach ($studentsToBill as $siswa) {
        $billExists = Bill::where('siswa_id', $siswa->id)
            ->where('payment_type_id', $sppType->id)
            ->whereYear('due_date', Carbon::now()->year)
            ->whereMonth('due_date', Carbon::now()->month)
            ->exists();

        // Cek jika belum ada tagihan DAN siswa memiliki nominal SPP yang harus dibayar
        if (!$billExists && $siswa->spp_amount > 0) {
            Bill::create([
                'siswa_id' => $siswa->id,
                'payment_type_id' => $sppType->id,
                // ----- BARIS INI DIUBAH -----
                'amount' => $siswa->spp_amount, // <-- Mengambil dari data siswa, bukan program
                'due_date' => Carbon::now()->day($siswa->billing_day),
                'status' => 'unpaid',
            ]);
            $generatedCount++;
        }
    }

    $this->info("Successfully generated $generatedCount new bills.");
    return 0;
}
}