<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Bill;
use App\Models\PaymentType;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Livewire\Attributes\On;

class EditBillModal extends Component implements HasForms
{
    use InteractsWithForms;

    public ?Bill $bill = null;
    public bool $isOpen = false;
    
    public $payment_type_id;
    public $amount;
    public $due_date;
    public $status;
    public $paid_at;
    public $notes;

    protected $listeners = ['open-edit-bill-modal' => 'openModal'];

    protected function getFormSchema(): array
    {
        return [
            Forms\Components\Select::make('payment_type_id')
                ->label('Jenis Tagihan')
                ->options(PaymentType::all()->pluck('name', 'id'))
                ->required()
                ->searchable(),
            
            Forms\Components\TextInput::make('amount')
                ->label('Jumlah')
                ->required()
                ->numeric()
                ->prefix('Rp')
                ->minValue(0),
            
            Forms\Components\DatePicker::make('due_date')
                ->label('Jatuh Tempo')
                ->required()
                ->native(false),
            
            Forms\Components\Select::make('status')
                ->label('Status')
                ->options([
                    'unpaid' => 'Belum Bayar',
                    'paid' => 'Lunas',
                    'cancelled' => 'Dibatalkan',
                ])
                ->required()
                ->live()
                ->afterStateUpdated(function ($state) {
                    if ($state === 'paid' && !$this->paid_at) {
                        $this->paid_at = now()->format('Y-m-d\TH:i');
                    }
                }),
            
            Forms\Components\DateTimePicker::make('paid_at')
                ->label('Tanggal Bayar')
                ->visible(fn () => $this->status === 'paid')
                ->native(false),
            
            Forms\Components\Textarea::make('notes')
                ->label('Catatan')
                ->rows(3)
                ->columnSpanFull(),
        ];
    }

    #[On('open-edit-bill-modal')]
    public function openModal($billId)
    {
        $this->bill = Bill::with('paymentType')->find($billId);
        
        if ($this->bill) {
            $this->payment_type_id = $this->bill->payment_type_id;
            $this->amount = $this->bill->amount;
            $this->due_date = $this->bill->due_date?->format('Y-m-d');
            $this->status = $this->bill->status;
            $this->paid_at = $this->bill->paid_at?->format('Y-m-d\TH:i');
            $this->notes = $this->bill->notes ?? '';
            
            $this->isOpen = true;
        }
    }

    public function closeModal()
    {
        $this->isOpen = false;
        $this->bill = null;
        $this->reset(['payment_type_id', 'amount', 'due_date', 'status', 'paid_at', 'notes']);
    }

    public function save()
    {
        $this->validate([
            'payment_type_id' => 'required|exists:payment_types,id',
            'amount' => 'required|numeric|min:0',
            'due_date' => 'required|date',
            'status' => 'required|in:unpaid,paid,cancelled',
        ]);

        $data = [
            'payment_type_id' => $this->payment_type_id,
            'amount' => $this->amount,
            'due_date' => $this->due_date,
            'status' => $this->status,
            'notes' => $this->notes,
        ];
        
        if ($this->status === 'paid' && empty($this->paid_at)) {
            $data['paid_at'] = now();
        } elseif ($this->status === 'paid' && $this->paid_at) {
            $data['paid_at'] = $this->paid_at;
        } else {
            $data['paid_at'] = null;
        }

        $this->bill->update($data);

        Notification::make()
            ->title('Tagihan berhasil diperbarui')
            ->success()
            ->send();

        $this->dispatch('bill-updated');
        $this->closeModal();
    }

    public function render()
    {
        return view('livewire.edit-bill-modal');
    }
}