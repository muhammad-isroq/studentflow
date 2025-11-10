<?php

namespace App\Filament\Resources\Borrowings\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use App\Models\Inventory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Utilities\Get;

class BorrowingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('inventory_id')
                ->label('Barang')
                ->relationship(
                    name: 'inventory',
                    titleAttribute: 'nama_barang',
                    // Hanya tampilkan barang yang stoknya > 0
                    modifyQueryUsing: function (Builder $query, string $operation, ?Model $record) {
                        $query = $query->where('jumlah', '>', 0);
                        
                        // Jika 'edit' (meskipun kita akan sembunyikan),
                        // tetap tampilkan barang yg sedang dipilih walau stok 0
                        if ($operation === 'edit' && $record) {
                            $query->orWhere('id', $record->inventory_id);
                        }
                        return $query;
                    }
                )
                // Tampilkan nama barang DAN sisa stoknya
                ->getOptionLabelFromRecordUsing(fn (Inventory $record) => "{$record->nama_barang} (Stok: {$record->jumlah})")
                ->searchable()
                ->preload()
                ->required()
                ->disabledOn('edit')
                ->live(), // Tidak bisa ganti barang setelah dipinjam
            TextInput::make('quantity')
                ->label('Jumlah Pinjam')
                ->numeric()
                ->required()
                ->minValue(1)
                // Hanya tampilkan jika barang sudah dipilih
                ->hidden(fn (Get $get) => !$get('inventory_id')) 
                // Validasi dinamis: tidak boleh pinjam > stok
                ->maxValue(function (Get $get) {
                    $inventory = Inventory::find($get('inventory_id'));
                    // Izinkan pinjam maksimal sejumlah stok yang ada
                    return $inventory ? $inventory->jumlah : 1; 
                })
                ->disabledOn('edit'),
            TextInput::make('borrower_name')
                ->label('Nama Peminjam')
                ->required()
                ->maxLength(255)
                ->disabledOn('edit'), // Tidak bisa ganti nama peminjam

            DatePicker::make('borrow_date')
                ->label('Tanggal Pinjam')
                ->default(now())
                ->required()
                ->disabledOn('edit'),

            DatePicker::make('due_date')
                ->label('Tanggal Kembali (Jatuh Tempo)')
                ->default(now()->addDays(7)) // Default 1 minggu
                ->required(),
                
            // Kolom ini hanya akan muncul di form 'Edit'
            DatePicker::make('return_date')
                ->label('Tanggal Aktual Dikembalikan')
                ->hiddenOn('create') // Sembunyikan saat membuat peminjaman baru
            ]);
    }
}
