<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Bill;
use App\Models\Siswa;
use App\Models\PaymentType;
use Filament\Notifications\Notification;
use Livewire\Attributes\On;

class PayMultipleBills extends Component
{
    public $isOpen = false;
    public $siswaId;
    public $monthCount = 1;

    #[On('open-pay-multiple-modal')]
    public function openModal($siswaId)
    {
        $this->siswaId = $siswaId;
        $this->isOpen = true;
    }

    public function closeModal()
    {
        $this->isOpen = false;
        $this->reset(['monthCount']);
    }

    public function processPayment()
    {
        $siswa = Siswa::find($this->siswaId);
        $sppPaymentType = PaymentType::where('name', 'like', '%SPP%')->first();
        
        $paidBillIds = [];

        for ($i = 0; $i < $this->monthCount; $i++) {
            // 1. Cari tagihan SPP tertua yang sudah ada tapi belum lunas
            $bill = $siswa->bills()
                ->where('payment_type_id', $sppPaymentType->id)
                ->where('status', '!=', 'paid') 
                ->orderBy('due_date', 'asc')
                ->first();

            if ($bill) {
                $bill->update([
                    'status' => 'paid', 
                    'paid_at' => now(), 
                    'notes' => 'Bayar Kolektif'
                ]);
                $paidBillIds[] = $bill->id;
            } else {
                // 2. LOGIKA BARU: Jika tidak ada tunggakan, cari bulan kosong pertama di tahun ini
                $currentYear = now()->year;
                
                // Ambil semua bulan yang sudah punya tagihan di tahun ini
                $existingMonths = $siswa->bills()
                    ->where('payment_type_id', $sppPaymentType->id)
                    ->whereYear('due_date', $currentYear)
                    ->pluck('due_date')
                    ->map(fn($date) => (int)$date->format('m'))
                    ->toArray();

                // Cari bulan pertama (1-12) yang belum ada di database
                $targetMonth = 1; 
                for ($m = 1; $m <= 12; $m++) {
                    if (!in_array($m, $existingMonths)) {
                        $targetMonth = $m;
                        break;
                    }
                }

                // Jika tahun ini sudah penuh semua (Jan-Des), baru pindah ke tahun depan
                if (count($existingMonths) >= 12) {
                    $lastBillGlobal = $siswa->bills()
                        ->where('payment_type_id', $sppPaymentType->id)
                        ->orderBy('due_date', 'desc')
                        ->first();
                    $nextDate = $lastBillGlobal->due_date->copy()->addMonth();
                } else {
                    $nextDate = \Carbon\Carbon::create($currentYear, $targetMonth, 1);
                }

                $newBill = $siswa->bills()->create([
                    'payment_type_id' => $sppPaymentType->id,
                    'amount' => $siswa->spp_amount ?? 500000,
                    'due_date' => $nextDate->setDay($siswa->billing_day ?? 10),
                    'status' => 'paid',
                    'paid_at' => now(),
                    'notes' => 'Bayar di Muka',
                ]);
                $paidBillIds[] = $newBill->id;
            }
        }

        Notification::make()->title("Berhasil memproses $this->monthCount bulan")->success()->send();
        $this->dispatch('print-collective-receipt', billIds: $paidBillIds);
        $this->dispatch('bill-updated');
        $this->closeModal();
    }

    public function render()
    {
        return view('livewire.pay-multiple-bills');
    }
}