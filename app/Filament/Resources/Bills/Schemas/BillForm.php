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
                    ->afterStateUpdated(function ($set, $state) {
                        // 1. Reset field lain agar bersih
                        $set('siswa_id', null);
                        $set('paid_by', null);

                        // 2. Logika Auto-Select Default Kategori
                        if ($state === 'expense') {
                            // Cari kategori 'Lain-lain' di database
                            $kategoriLain = \App\Models\PaymentType::where('name', 'like', '%Lain-lain%')->first();

                            // Jika ketemu, set sebagai default
                            if ($kategoriLain) {
                                $set('payment_type_id', $kategoriLain->id);
                            }
                        } else {
                            // Jika user klik Pemasukan lagi, kosongkan field-nya
                            $set('payment_type_id', null);
                        }
                    })
                    ->required()
                    ->columnSpanFull(),

                // 2. KATEGORI PEMBAYARAN
                Select::make('payment_type_id')
                    ->relationship(
                        name: 'paymentType',
                        titleAttribute: 'name',
                        modifyQueryUsing: function (\Illuminate\Database\Eloquent\Builder $query, $livewire, $get) {
                            if ($get('transaction_type') === 'expense') {
                                // Saat expense, hanya tampilkan & kunci ke 'Lain-lain'
                                $query->where('name', 'like', '%Lain-lain%');
                            } else {
                                // Saat income, jalankan logika lama
                                $isRelationManager = str_contains(class_basename($livewire), 'RelationManager');

                                if (!$isRelationManager) {
                                    $query->where('name', 'not like', '%spp%');
                                }
                            }
                        }
                    )
                    ->label('Kategori Transaksi')
                    ->live()
                    ->disabled(fn ($get) => $get('transaction_type') === 'expense') // Field terkunci saat expense
                    ->dehydrated(true) // Nilai tetap dikirim ke server walau field disabled
                    ->afterStateUpdated(fn (callable $set) => $set('siswa_id', null))
                    ->required(),

                Textarea::make('notes')
                    ->label(fn ($get) => $get('transaction_type') === 'expense' ? 'Rincian Keterangan Pengeluaran' : 'Catatan / Keterangan Tambahan')
                    ->placeholder(fn ($get) => $get('transaction_type') === 'expense' ? 'Contoh: Pembelian 2 buah lampu LED untuk ruang kelas.' : 'Tambahkan catatan jika diperlukan (Opsional).')
                    ->rows(3)
                    ->columnSpanFull(),

                // 3. LOGIKA DINAMIS KOLOM SISWA
                Select::make('siswa_id')
                    ->relationship('siswa', 'nama')
                    ->label('Nama Siswa')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->visible(function ($get) {
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
                ->visible(fn () => $this->record->status === 'paid' && $this->record->transaction_type === 'income'),
            DeleteAction::make(),
        ];
    }
}