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
    $waktuLunas = now();
    
    // Titik awal pencarian: Juli 2026
    $startDate = \Carbon\Carbon::create(2026, 7, 1);
    $paidBillIds = [];

    // Cari tagihan terakhir yang sudah ada di database (setelah Juli 2026)
    // untuk menentukan di mana kita harus melanjutkan pembayaran
    $lastBill = $siswa->bills()
        ->where('payment_type_id', $sppPaymentType->id)
        ->where('due_date', '>=', $startDate)
        ->orderBy('due_date', 'desc')
        ->first();

    // Jika sudah ada tagihan, mulai dari bulan setelah tagihan terakhir
    // Jika belum ada tagihan, mulai dari Juli 2026
    $currentDate = $lastBill ? $lastBill->due_date->copy()->addMonth() : $startDate->copy();

    for ($i = 0; $i < $this->monthCount; $i++) {
        // Cari apakah tagihan untuk bulan ini sudah ada
        $bill = $siswa->bills()
            ->where('payment_type_id', $sppPaymentType->id)
            ->whereMonth('due_date', $currentDate->month)
            ->whereYear('due_date', $currentDate->year)
            ->first();

        if ($bill) {
            $bill->update([
                'status' => 'paid',
                'paid_at' => $waktuLunas,
                'notes' => 'Bayar Kolektif',
            ]);
            $paidBillIds[] = $bill->id;
        } else {
            $newBill = $siswa->bills()->create([
                'payment_type_id' => $sppPaymentType->id,
                'amount' => $siswa->spp_amount ?? 500000,
                'due_date' => $currentDate->copy()->setDay($siswa->billing_day ?? 10),
                'status' => 'paid',
                'paid_at' => $waktuLunas,
                'notes' => 'Bayar Kolektif',
            ]);
            $paidBillIds[] = $newBill->id;
        }
        
        // Geser ke bulan berikutnya untuk iterasi selanjutnya
        $currentDate->addMonth();
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