<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\Siswa;
use App\Models\PaymentType;
use App\Models\Bill;
use Filament\Notifications\Notification;

class CreateBillModal extends Component
{
    public bool $show = false;
    public ?Siswa $siswa;

    // Tambahkan properti ini untuk membedakan mode
    public bool $isCreatingNewType = false;

    // Properti untuk form
    public ?string $paymentTypeName = '';
    public ?int $paymentTypeId = null;
    public ?float $amount = null;
    public ?string $dueDate = null;
    public string $status = 'unpaid';

    #[On('open-modal')]
    public function openModal($id, $paymentTypeName = null, $siswaId = null)
    {
        // Sekarang modal akan merespon kedua ID
        if ($id === 'create-bill-modal' || $id === 'create-new-payment-type-modal') {
            $this->resetValidation();
            $this->reset('amount', 'dueDate', 'paymentTypeName'); // Reset semua field form

            $this->siswa = Siswa::find($siswaId);
            
            // Logika untuk membedakan mode
            if ($id === 'create-new-payment-type-modal') {
                $this->isCreatingNewType = true;
            } else {
                $this->isCreatingNewType = false;
                $this->paymentTypeName = $paymentTypeName;
            }
            
            $this->show = true;
        }
    }

    public function save()
    {
        // Tambahkan validasi untuk nama jenis tagihan baru
        $this->validate([
            'paymentTypeName' => 'required_if:isCreatingNewType,true|string|max:255',
            'amount' => 'required|numeric|min:0',
            'dueDate' => 'required|date',
        ]);

        if (!$this->siswa) {
            return;
        }

        // Logika ini sudah fleksibel, akan membuat baru jika belum ada
        $paymentType = PaymentType::firstOrCreate(['name' => $this->paymentTypeName]);

        $this->siswa->bills()->create([
            'payment_type_id' => $paymentType->id,
            'amount' => $this->amount,
            'due_date' => $this->dueDate,
            'status' => $this->status,
        ]);

        Notification::make()
            ->title('Tagihan berhasil dibuat')
            ->success()
            ->send();
        $this->dispatch('bill-updated');
        $this->closeModal();
        // $this->dispatch('bill-created');
    }

    public function closeModal()
    {
        $this->show = false;
    }

    public function render()
    {
        return view('livewire.create-bill-modal');
    }
}