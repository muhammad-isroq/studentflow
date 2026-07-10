<?php

namespace App\Filament\Resources\Siswas\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\On;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Illuminate\Support\Facades\Redirect;
use App\Filament\Resources\Siswas\SiswaResource;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\Contracts\HasActions;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;

class BillsRelationManager extends RelationManager implements HasActions
{
    use InteractsWithActions;
    protected static string $relationship = 'bills';
    protected static ?string $title = 'Riwayat Tagihan';
    protected string $view = 'filament.relation-managers.bills-grouped';

    public $selectedYear;

    // Tambahkan listener untuk event dari child component
    protected $listeners = [
        'bill-updated' => 'refreshAfterUpdate',
        'open-edit-bill-modal' => '$refresh'
    ];

    public function mount(): void
    {
        // Atur tahun default ke tahun saat ini
        $this->selectedYear = date('Y');
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('paymentType.name')
                    ->label('Jenis Tagihan'),
                Tables\Columns\TextColumn::make('amount')
                    ->label('Jumlah')
                    ->money('IDR'),
                Tables\Columns\TextColumn::make('due_date')
                    ->label('Jatuh Tempo')
                    ->date('d M Y'),
                Tables\Columns\TextColumn::make('status')
                    ->badge(),
                Tables\Columns\TextColumn::make('paid_at')
                    ->label('Tanggal Lunas')
                    ->dateTime('d M Y, H:i'),
            ])
            ->defaultSort('due_date', 'desc')
            ->actions([
            // TOMBOL PRINT PER BARIS
            Action::make('print')
                ->label('Print')
                ->color('info')
                ->icon('heroicon-o-printer')
                // Menggunakan variabel $record (model Bill) secara otomatis
                ->url(fn (Bill $record) => route('print.receipt', ['bill' => $record]))
                ->openUrlInNewTab()
                // Tombol hanya muncul jika statusnya sudah lunas (paid)
                ->visible(fn (Bill $record) => $record->status === 'paid'),
                
            EditAction::make()
                ->url(fn (Bill $record) => SiswaResource::getUrl('edit-bill', [
                    'record' => $this->getOwnerRecord()->id,
                    'billRecord' => $record->id,
                ])),
            DeleteAction::make(),
        ])
            ->bulkActions([
            BulkActionGroup::make([
                BulkAction::make('markAsPaidBulk')
                    ->label('Bayar Sekaligus')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(function (\Illuminate\Database\Eloquent\Collection $records) {
                        $records->each(function ($bill) {
                            if ($bill->status !== 'paid') {
                                $bill->update([
                                    'status' => 'paid',
                                    'paid_at' => now(),
                                    // Anda bisa menambahkan catatan "Pembayaran kolektif" jika perlu
                                ]);
                            }
                        });

                        \Filament\Notifications\Notification::make()
                            ->title('Pembayaran Kolektif Berhasil')
                            ->success()
                            ->send();
                    })
                    ->deselectRecordsAfterCompletion(),
                
                DeleteBulkAction::make(),
                Action::make('print')
            ->label('Print Struk')
            ->color('info')
            ->icon('heroicon-o-printer')
            ->url(fn () => route('print.receipt', ['bill' => $this->billRecord]))
            ->openUrlInNewTab()
            // Perbaikan pemanggilan status di sini
            ->visible(fn () => ($this->form->getRawState()['status'] ?? null) === 'paid'),

            ]),
        ]);
    }

    public function paySppAction(): \Filament\Actions\Action
{
    return \Filament\Actions\Action::make('payMultiple')
        ->label('Bayar Banyak Bulan')
        ->icon('heroicon-m-plus-circle')
        ->color('success')
        ->extraAttributes(['class' => 'w-full md:w-auto'])
        ->form([
            \Filament\Forms\Components\TextInput::make('month_count')
                ->label('Jumlah Bulan yang Ingin Dibayar')
                ->numeric()
                ->default(1)
                ->minValue(1)
                ->required(),
        ])
        ->action(function (array $data) {
            $this->processMultiplePayments($data['month_count']);
        });
}

public function newPaymentAction(): \Filament\Actions\Action
    {
        return \Filament\Actions\Action::make('newPayment')
            ->label('Input Pembayaran')
            ->icon('heroicon-m-plus-circle')
            ->color('primary')
            ->extraAttributes(['class' => 'w-full md:w-auto'])
            ->form([
                \Filament\Forms\Components\Repeater::make('payments')
                    ->label('Daftar Tagihan')
                    ->schema([
                        \Filament\Forms\Components\Select::make('payment_type_id')
                            ->label('Kategori Tagihan')
                            ->options(\App\Models\PaymentType::pluck('name', 'id'))
                            ->required()
                            ->searchable()
                            ->live()
                            ->afterStateUpdated(function (callable $set, $state) {
                                // Opsional: Set default amount berdasarkan jenis tagihan
                                // Misalnya, jika SPP terpilih, ambil amount dari profil siswa
                                if ($state) {
                                    $paymentType = \App\Models\PaymentType::find($state);
                                    if ($paymentType && stripos($paymentType->name, 'spp') !== false) {
                                        $set('amount', $this->getOwnerRecord()->spp_amount);
                                    }
                                }
                            }),

                        \Filament\Forms\Components\TextInput::make('amount')
                            ->label('Nominal (Rp)')
                            ->numeric()
                            ->required(),

                        \Filament\Forms\Components\DatePicker::make('due_date')
                            ->label('Tanggal Jatuh Tempo/Periode')
                            ->default(now())
                            ->required(),

                        \Filament\Forms\Components\Textarea::make('notes')
                            ->label('Catatan (Opsional)')
                            ->rows(2),
                    ])
                    ->columns(2)
                    ->defaultItems(1) // Munculkan 1 baris secara default
                    ->addActionLabel('+ Tambah Kategori Pembayaran (Buku, dll)'),
            ])
            ->action(function (array $data) {
    $siswaIds = $data['siswa_ids']; // Array ID siswa
    $months = (int)($data['months_count'] ?? 1);
    $paymentTypeId = $data['payment_type_id'];
    $amountPerMonth = $data['amount'] / $months; // Mengembalikan nilai per bulan
    
    foreach ($siswaIds as $siswaId) {
        $siswa = \App\Models\Siswa::find($siswaId);
        
        for ($i = 0; $i < $months; $i++) {
            // Tentukan tanggal jatuh tempo bulan ke-i
            $dueDate = \Carbon\Carbon::parse($data['due_date'])->addMonths($i);
            
            $bill = \App\Models\Bill::create([
                'payment_type_id'  => $paymentTypeId,
                'amount'           => $amountPerMonth,
                'due_date'         => $dueDate,
                'status'           => $data['status'],
                'paid_at'          => $data['paid_at'] ?? now(),
                'notes'            => ($data['notes'] ?? '') . " (Bulan ke-" . ($i + 1) . ")",
                'transaction_type' => $data['transaction_type'],
            ]);
            
            $bill->siswa()->attach($siswaId);
        }
    }

    \Filament\Notifications\Notification::make()->title('Tagihan berhasil dibuat untuk ' . $months . ' bulan')->success()->send();
    $this->dispatch('bill-updated');
});
    }

protected function getActions(): array
{
    return [
        $this->newPaymentAction(),
        $this->paySppAction(),
    ];
}

private function processMultiplePayments($count)
{
    $siswa = $this->getOwnerRecord();
    $sppPaymentType = \App\Models\PaymentType::where('name', 'like', '%SPP%')->first();
    $waktuLunas = now();
    for ($i = 0; $i < $count; $i++) {
        // 1. Cari apakah ada tagihan SPP yang statusnya belum lunas
        $bill = $siswa->bills()
            ->where('payment_type_id', $sppPaymentType->id)
            ->where('status', '!=', 'paid')
            ->orderBy('due_date', 'asc')
            ->first();

        if ($bill) {
            $bill->update([
                'status' => 'paid',
                'paid_at' => $waktuLunas,
                'notes' => ($bill->notes ? $bill->notes . ' ' : '') . 'Dibayar kolektif',
            ]);
        } else {
            // 2. Jika tidak ada tagihan unpaid, buat tagihan baru untuk bulan selanjutnya
            $lastBill = $siswa->bills()
                ->where('payment_type_id', $sppPaymentType->id)
                ->orderBy('due_date', 'desc')
                ->first();

            $nextDate = $lastBill 
                ? $lastBill->due_date->addMonth() 
                : now();

            $siswa->bills()->create([
                'payment_type_id' => $sppPaymentType->id,
                'amount' => $siswa->spp_amount ?? 500000,
                'due_date' => $nextDate->setDay($siswa->billing_day ?? 10),
                'status' => 'paid',
                'paid_at' => $waktuLunas,
                'notes' => 'Pembayaran di muka (Advance)',
            ]);
        }
    }

    $this->dispatch('bill-updated');
    
    \Filament\Notifications\Notification::make()
        ->title("Berhasil memproses $count bulan")
        ->success()
        ->send();
}

    // Method untuk mendapatkan bills berdasarkan payment type
    public function getBillsByPaymentType()
    {
        $bills = $this->getOwnerRecord()->bills()
            ->with('paymentType')
            ->get()
            ->groupBy('paymentType.name');
            
        return $bills;
    }

    public function getMonthlySppBills()
{
    $owner = $this->getOwnerRecord();
    
    $sppBills = $owner->bills()
        ->whereHas('paymentType', function ($query) {
            $query->where('name', 'like', '%spp%')
                  ->orWhere('name', 'like', '%SPP%')
                  ->orWhere('name', 'like', '%Monthly%');
        })
        ->with('paymentType')
        ->get();

    $months = [];
    $currentYear = $this->selectedYear;
    
    for ($i = 1; $i <= 12; $i++) {
        $monthName = date('F', mktime(0, 0, 0, $i, 1));
        $monthDate = $currentYear . '-' . str_pad($i, 2, '0', STR_PAD_LEFT);
        
        $monthBill = $sppBills->first(function ($bill) use ($monthDate) {
            return $bill->due_date && $bill->due_date->format('Y-m') === $monthDate;
        });
        
        $months[] = [
            'month' => $monthName,
            'month_number' => $i,
            'year' => $currentYear,
            'bill' => $monthBill,
            'status' => $monthBill ? $monthBill->status : 'not_generated',
            // Nominal di sini sekarang akan selalu Rp 500.000 (per bulan)
            'amount' => $monthBill ? $monthBill->amount : ($owner->spp_amount ?? 0),
            'due_date' => $monthBill ? $monthBill->due_date : null,
            'paid_at' => $monthBill ? $monthBill->paid_at : null,
        ];
    }
    
    return collect($months);
}

    // Method untuk mendapatkan payment types lainnya   
    public function getNonSppBills()
    {
        return $this->getOwnerRecord()->bills()
            ->whereHas('paymentType', function ($query) {
                $query->where('name', 'not like', '%spp%')
                      ->where('name', 'not like', '%SPP%')
                      ->where('name', 'not like', '%Monthly%');
            })
            ->with('paymentType')
            ->get()
            ->groupBy('paymentType.name');
    }

    // Actions untuk handle button clicks
    public function markAsPaid($billId)
    {
        $bill = \App\Models\Bill::find($billId);
        if ($bill) {
            $bill->update([
                'status' => 'paid',
                'paid_at' => now(),
            ]);
            
            \Filament\Notifications\Notification::make()
                ->title('Tagihan berhasil ditandai sebagai lunas')
                ->success()
                ->send();

            $this->dispatch('bill-updated');
        }
    }

    public function generateSppBill($month, $year)
    {
        $siswa = $this->getOwnerRecord();
        $sppPaymentType = \App\Models\PaymentType::firstOrCreate(['name' => 'Monthly SPP']);
        $billingDay = $siswa->billing_day ?? 10;
        $dueDate = \Carbon\Carbon::create($year, $month, $billingDay);

        // Buat bill
        $bill = \App\Models\Bill::create([
            'payment_type_id' => $sppPaymentType->id,
            'amount' => $siswa->spp_amount ?? 500000,
            'due_date' => $dueDate,
            'status' => 'unpaid',
        ]);

        // Hubungkan ke siswa
        $bill->siswas()->attach($siswa->id);
        
        \Filament\Notifications\Notification::make()
            ->title('Tagihan SPP berhasil dibuat')
            ->body("Tagihan untuk bulan " . date('F', mktime(0, 0, 0, $month, 1)) . " $year")
            ->success()
            ->send();
        
            $this->dispatch('bill-updated');
    }

    public function editBill($billId)
    {
        // Redirect ke halaman edit bill
        return redirect()->to(
            SiswaResource::getUrl('edit-bill', [
                'record' => $this->getOwnerRecord()->id,
                'billRecord' => $billId,
            ])
        );
    }
    public function viewBill($billId)
    {
        // Redirect ke halaman view Bill Resource
        return redirect()->route('filament.admin.resources.bills.view', ['record' => $billId]);
    }

    #[On('bill-updated')]
    public function refreshAfterUpdate()
    {
        // Refresh component setelah bill diupdate
        // Livewire 3 otomatis refresh setelah event
    }

    public function deleteBill($billId)
    {
        $bill = \App\Models\Bill::find($billId);
        if ($bill) {
            $bill->delete();
            
            \Filament\Notifications\Notification::make()
                ->title('Tagihan berhasil dihapus')
                ->success()
                ->send();
            $this->dispatch('bill-updated');
        }
    }

    public function createBill($paymentTypeName)
    {
        $this->dispatch('open-modal', [
            'id' => 'create-bill-modal',
            'paymentTypeName' => $paymentTypeName,
            'siswaId' => $this->getOwnerRecord()->id
        ]);
    }

    public function createNewPaymentTypeBill()
    {
        $this->dispatch('open-modal', [
            'id' => 'create-new-payment-type-modal',
            'siswaId' => $this->getOwnerRecord()->id
        ]);
    }

    public function refreshBills(): void
    {
        // Method untuk refresh data
    }

    public function getTotalArrearsProperty()
    {
        return $this->getOwnerRecord()->bills()
            ->where('status', ['overdue', 'unpaid']) 
            ->whereDate('due_date', '>=', '2025-07-01') 
            ->sum('amount'); 
    }

    public function openPayMultipleModal()
    {
        $this->dispatch('open-pay-multiple-modal', siswaId: $this->getOwnerRecord()->id);
    }

    public function openInputMultiplePaymentsModal()
    {
        $this->dispatch('open-input-multiple-payments-modal', siswaId: $this->getOwnerRecord()->id);
    }
}