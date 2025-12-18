<?php

namespace App\Filament\Resources\Transactions\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Forms\Components\Textarea; // Tambahkan ini jika perlu
use Filament\Forms\Get;
use Filament\Forms\Components\Hidden;
use Illuminate\Support\Facades\Auth;

class TransactionForm

{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('type')
                    ->label('Jenis')
                    ->options([
                    'income' => 'Pemasukan (Income)',
                    'expense' => 'Pengeluaran (Expense)',])
                     ->required()
                     ->native(false)
                     ->live(),
                Select::make('category')
                        ->label('Kategori')
                        ->options(fn ($get) => match ($get('type')) {
                            // Jika pilih "Pemasukan", tampilkan opsi ini (Sesuai Bills Anda)
                            'income' => [
                                'monthly_spp' => 'SPP Bulanan',
                                'registration' => 'Biaya Pendaftaran',
                                'buku' => 'Penjualan Buku/Modul',
                                'certificate' => 'Biaya Sertifikat',
                                'Lainnya' => 'Pemasukan Lainnya',
                            ],
                            
                            // Jika pilih "Pengeluaran", tampilkan opsi ini
                            'expense' => [
                                'Operasional' => 'Operasional Kantor (Listrik/Air)',
                                'Gaji' => 'Gaji Guru & Staff',
                                'Aset' => 'Belanja Aset/Inventaris',
                                'Marketing' => 'Biaya Iklan/Promosi',
                                'Lainnya' => 'Pengeluaran Lainnya',
                            ],

                            // Default (jika belum pilih jenis)
                            default => [],
                        })
                        // Buat kategori wajib diisi & bisa dicari
                        ->required()
                        ->searchable()
                        ->preload(),
                TextInput::make('amount')
                    ->label('Nominal (Rp)')
                    ->numeric()
                    ->prefix('Rp')
                    ->required(),
                TextInput::make('description')
                    ->default(null),
                FileUpload::make('proof_image')
                    ->image(),
                DatePicker::make('date')
                    ->label('Tanggal')
                    ->default(now())
                    ->required(),
                Hidden::make('user_id')
                    ->default(fn () => Auth::id()),
                // TextInput::make('reference_type')
                //     ->default(null),
                // TextInput::make('reference_id')
                //     ->numeric()
                //     ->default(null),
            ]);
    }
}
