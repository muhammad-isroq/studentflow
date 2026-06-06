<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Siswa;
use App\Models\PaymentType;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Schemas\Schema;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Livewire\Attributes\On;
use Filament\Actions\Contracts\HasActions;
use Filament\Actions\Concerns\InteractsWithActions;

class InputMultiplePaymentsModal extends Component implements HasForms, HasActions
{
    use InteractsWithForms;
    use InteractsWithActions;

    public $siswaId;
    public ?array $data = [];
    // HAPUS variabel public $show = false; 

    #[On('open-input-multiple-payments-modal')]
    public function openModal($siswaId)
    {
        $this->siswaId = $siswaId;
        $this->form->fill([
            'payments' => [
                ['payment_type_id' => null, 'amount' => null, 'due_date' => now()->format('Y-m-d'), 'notes' => 'Cash']
            ]
        ]);
        
        // INI KUNCI UTAMANYA: Panggil event native Filament untuk membuka modal
        $this->dispatch('open-modal', id: 'input-multiple-payments-modal');
    }

    public function closeModal()
    {
        // Panggil event native Filament untuk menutup modal
        $this->dispatch('close-modal', id: 'input-multiple-payments-modal');
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Repeater::make('payments')
                    ->label('Daftar Pembayaran Baru')
                    ->schema([
                        Select::make('payment_type_id')
                            ->label('Kategori Tagihan')
                            ->options(PaymentType::pluck('name', 'id'))
                            ->required()
                            ->searchable()
                            ->live()
                            ->afterStateUpdated(function (callable $set, $state) {
                                if ($state && $this->siswaId) {
                                    $paymentType = PaymentType::find($state);
                                    if ($paymentType && stripos($paymentType->name, 'spp') !== false) {
                                        $siswa = Siswa::find($this->siswaId);
                                        if ($siswa) $set('amount', $siswa->spp_amount);
                                    }
                                }
                            }),

                        TextInput::make('amount')
                            ->label('Nominal (Rp)')
                            ->numeric()
                            ->required(),

                        DatePicker::make('due_date')
                            ->label('Bulan / Periode')
                            ->required(),

                        Textarea::make('notes')
                            ->label('Catatan (Opsional)')
                            ->default('Cash')
                            ->rows(1),
                    ])
                    ->columns(2)
                    ->addActionLabel('+ Tambah Kategori (Buku, Registrasi, dll)')
            ])
            ->statePath('data');
    }

    public function save()
    {
        $data = $this->form->getState();
        $siswa = Siswa::find($this->siswaId);
        
        $createdIds = [];
        $waktuLunas = now();

        foreach ($data['payments'] as $payment) {
            $bill = $siswa->bills()->create([
                'payment_type_id' => $payment['payment_type_id'],
                'amount' => $payment['amount'],
                'due_date' => $payment['due_date'],
                'status' => 'paid', 
                'paid_at' => $waktuLunas,
                'notes' => $payment['notes'],
                'transaction_type' => 'income', 
            ]);
            $createdIds[] = $bill->id;
        }

        Notification::make()
            ->title('Berhasil mencatat ' . count($data['payments']) . ' pembayaran!')
            ->success()
            ->send();

        $this->dispatch('bill-updated');
        $this->closeModal();
        if (count($createdIds) > 0) {
            $idsString = implode(',', $createdIds);
            
            // UBAH BARIS INI: Gunakan nama rute asli Anda
            $url = route('print.receipt.collective', ['ids' => $idsString]);
            
            $this->js("window.open('{$url}', '_blank');");
        }
    }

    public function render()
    {
        return view('livewire.input-multiple-payments-modal');
    }
}