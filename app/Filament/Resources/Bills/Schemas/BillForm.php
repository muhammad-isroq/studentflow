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

class BillForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // 1. KONTROL UTAMA: JENIS TRANSAKSI
                Radio::make('transaction_type')
                    ->label('Arus Kas (Cash Flow)')
                    ->options([
                        'income' => '🟢 Pemasukan (Income)',
                        'expense' => '🔴 Pengeluaran (Expense)',
                    ])
                    ->default('income')
                    ->inline()
                    ->live()
                    // Jika tipe arus kas berubah, reset pilihan di bawahnya agar tidak bentrok
                    ->afterStateUpdated(function (callable $set) {
                        $set('payment_type_id', null);
                        $set('siswa_id', null);
                        $set('paid_by', null);
                    })
                    ->required()
                    ->columnSpanFull(),

                // 2. KATEGORI PEMBAYARAN
                Select::make('payment_type_id')
                    ->relationship('paymentType', 'name')
                    ->label('Kategori Transaksi')
                    ->live()
                    ->afterStateUpdated(fn (callable $set) => $set('siswa_id', null))
                    ->required(),

                // 3. LOGIKA DINAMIS KOLOM SISWA
                Select::make('siswa_id')
                    ->relationship('siswa', 'nama')
                    ->label('Nama Siswa')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->visible(function ($get) {
                        // Jika ini adalah pengeluaran (expense), PASTI sembunyikan kolom siswa
                        if ($get('transaction_type') === 'expense') {
                            return false;
                        }

                        $paymentTypeId = $get('payment_type_id');
                        if (!$paymentTypeId) {
                            return false; 
                        }

                        $paymentType = \App\Models\PaymentType::find($paymentTypeId);
                        if (!$paymentType) {
                            return false;
                        }

                        $name = strtolower($paymentType->name);
                        $keywords = ['spp', 'buku', 'registration', 'certificate'];

                        foreach ($keywords as $keyword) {
                            if (str_contains($name, $keyword)) {
                                return true;
                            }
                        }

                        return false;
                    }),

                // 4. LABEL DINAMIS: DIBAYAR OLEH / DIBAYARKAN KEPADA
                TextInput::make('paid_by')
                    ->label(fn ($get) => $get('transaction_type') === 'expense' ? 'Dibayarkan Kepada (Penerima)' : 'Diterima Dari (Penyetor)')
                    ->placeholder(fn ($get) => $get('transaction_type') === 'expense' ? 'Contoh: PLN / Toko ATK' : 'Contoh: Ayah Budi / PT Sponsor')
                    ->helperText(fn ($get) => $get('transaction_type') === 'expense' ? 'Tuliskan nama pihak yang menerima uang pengeluaran ini.' : 'Kosongkan jika dibayar langsung oleh siswa terkait.')
                    ->maxLength(255),

                // 5. DATA NOMINAL & BUKTI
                TextInput::make('amount')
                    ->label('Nominal (Rp)')
                    ->prefix('Rp')
                    ->required()
                    ->afterStateUpdated(fn ($state, callable $set) =>
                        $set('registration_fee', $state ? number_format((int) str_replace('.', '', $state), 0, ',', '.') : null)
                    )
                    ->dehydrateStateUsing(fn ($state) =>
                        $state ? (int) str_replace('.', '', $state) : null
                    )
                    ->formatStateUsing(fn ($state) =>
                        $state ? number_format($state, 0, ',', '.') : null
                    ),

                FileUpload::make('proof_of_payment')
                    ->label(fn ($get) => $get('transaction_type') === 'expense' ? 'Bukti Nota/Struk Keluar' : 'Bukti Transfer/Pembayaran')
                    ->imagePreviewHeight('250')
                    ->disk('public')
                    ->directory('proofs')
                    ->downloadable()
                    ->openable(),

                DatePicker::make('due_date')
                    ->label(fn ($get) => $get('transaction_type') === 'expense' ? 'Tanggal Pengeluaran' : 'Due Date (Jatuh Tempo)')
                    ->required(),

                Select::make('status')
                    ->options([
                        'unpaid' => 'Belum Lunas / Pending',
                        'paid' => 'Selesai / Lunas',
                    ])
                    ->required()
                    ->default('unpaid'),

                DateTimePicker::make('paid_at')
                    ->label('Waktu Transaksi Selesai'),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('print_receipt')
                ->label('Print Struk')
                ->icon('heroicon-o-printer')
                ->color('info')
                ->url(fn () => route('print.receipt', ['bill' => $this->record->id]))
                ->openUrlInNewTab()
                // Tombol cetak struk hanya muncul jika status Lunas DAN transaksi ini adalah Pemasukan
                ->visible(fn () => $this->record->status === 'paid' && $this->record->transaction_type === 'income'),
            DeleteAction::make(),
        ];
    }
}