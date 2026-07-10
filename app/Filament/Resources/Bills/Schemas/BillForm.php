<?php

namespace App\Filament\Resources\Bills\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Radio;
use Filament\Schemas\Schema;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\Textarea;
use App\Models\Siswa;
use App\Models\PaymentType;

class BillForm
{
    public static function configure(Schema $schema): Schema
    {
        $hitungTotal = function ($get, $set) {
            $siswaIds = $get('siswa_ids') ?? [];
            $totalSppPerSiswa = \App\Models\Siswa::whereIn('id', $siswaIds)->sum('spp_amount');
            
            // Set amount = SPP per bulan (tidak dikalikan bulan)
            $set('amount', $totalSppPerSiswa); 
        };

        return $schema
            ->components([
                Radio::make('transaction_type')
                ->label('Arus Kas (Cash Flow)')
                ->options([
                    'income' => '🟢 Pemasukan (Income)',
                    'expense' => '🔴 Pengeluaran (Expense)',
                ])
                ->default('income')
                ->inline()
                ->live()
                ->afterStateUpdated(function ($set, $state) {
                    $set('siswa_ids', null);
                    $set('paid_by', null);
                    
                    // LOGIKA OTOMATIS KATEGORI
                    if ($state === 'expense') {
                        $kategoriLain = PaymentType::where('name', 'like', '%Lain-lain%')->first();
                        if ($kategoriLain) {
                            $set('payment_type_id', $kategoriLain->id);
                        }
                    } else {
                        $set('payment_type_id', null);
                    }
                })
                ->required()
                ->columnSpanFull(),

            // 2. KATEGORI PEMBAYARAN
            Select::make('payment_type_id')
                ->relationship('paymentType', 'name', function ($query, $get) {
                    if ($get('transaction_type') === 'expense') {
                        $query->where('name', 'like', '%Lain-lain%');
                    }
                })
                ->label('Kategori Transaksi')
                ->live()
                // TAMBAHKAN dehydrated(true) agar data tetap terkirim ke DB meski disabled
                ->dehydrated(true) 
                // ->disabled(fn ($get) => $get('transaction_type') === 'expense')
                ->afterStateUpdated(function ($set) {
                    $set('siswa_ids', null);
                    $set('months_count', 1);
                })
                ->required(),

                // 4. NAMA SISWA (Multiple)
                Select::make('siswa_ids')
                    ->relationship('siswa', 'nama')
                    ->label('Nama Siswa')
                    ->multiple()
                    ->searchable()
                    ->preload()
                    ->required()
                    ->live()
                    ->saveRelationshipsUsing(fn ($record, $state) => $record?->siswa()->sync($state ?? []))
                    ->afterStateHydrated(fn ($component, $record) => $component->state($record?->siswa->pluck('id')->toArray() ?? []))
                    ->afterStateUpdated($hitungTotal)
                    ->visible(fn ($get) => str_contains(strtolower(PaymentType::find($get('payment_type_id'))?->name ?? ''), 'spp')),

                TextInput::make('months_count')
                    ->label('Jumlah Bulan yang Dibayar')
                    ->numeric()
                    ->default(1)
                    ->minValue(1)
                    ->live()
                    ->dehydrated(false) // Data ini hanya untuk logika, tidak disimpan ke kolom tabel bills
                    ->afterStateUpdated(function ($state, $get, $set) {
                        $siswaIds = $get('siswa_ids') ?? [];
                        // Hitung total: (Jumlah Siswa * SPP Per Siswa) * Bulan
                        $sppPerBulanPerSiswa = \App\Models\Siswa::whereIn('id', $siswaIds)->sum('spp_amount');
                        $set('amount', $sppPerBulanPerSiswa * (int)$state);
                    })
                    ->visible(fn ($get) => str_contains(strtolower(PaymentType::find($get('payment_type_id'))?->name ?? ''), 'spp')),

                // 5. DATA LAINNYA
                Textarea::make('notes')
                    ->label('Catatan / Keterangan')
                    ->rows(3)
                    ->columnSpanFull(),

                

                TextInput::make('paid_by')
                    ->label(fn ($get) => $get('transaction_type') === 'expense' ? 'Dibayarkan Kepada' : 'Diterima Dari'),

                TextInput::make('amount')
                    ->label('Nominal (Rp)')
                    ->prefix('Rp')
                    ->required()
                    
                    ->dehydrateStateUsing(fn ($state) => (int) str_replace(['Rp', '.', ','], '', $state))
                    ->formatStateUsing(fn ($state) => number_format($state, 0, ',', '.')),

                FileUpload::make('proof_of_payment')
                ->label('Proof of payment')
                ->disk('public'),

                FileUpload::make('transfer_proof')
                    ->label('Bukti Transfer Bank (Internal Staff)')
                    ->helperText('Diunggah oleh staf untuk verifikasi pembayaran via transfer bank.')
                    ->disk('public')
                    ->directory('transfer-proofs')
                    ->image()
                    ->maxSize(5120) // Maksimal 5MB
                    ->downloadable()
                    ->openable(),

                DatePicker::make('due_date')
                    ->label('Tanggal Awal Pembayaran')
                    ->disabled(fn ($get) => (int)$get('months_count') > 1),

                Select::make('status')
                    ->options(['unpaid' => 'Belum Lunas', 'paid' => 'Lunas'])
                    ->default('unpaid')
                    ->required(),

                DateTimePicker::make('paid_at')->label('Waktu Transaksi'),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('print_receipt')
                ->label('Print Struk')
                ->icon('heroicon-o-printer')
                ->color('info')
                ->url(fn ($livewire) => route('print.receipt', ['bill' => $livewire->record->id]))
                ->openUrlInNewTab()
                ->visible(fn ($livewire) => $livewire->record->status === 'paid'),
            DeleteAction::make(),
        ];
    }
}