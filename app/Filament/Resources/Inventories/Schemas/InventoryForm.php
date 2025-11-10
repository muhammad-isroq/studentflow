<?php

namespace App\Filament\Resources\Inventories\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Columns\ImageColumn;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Grid;
use Filament\Support\RawJs;

class InventoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2) 
            ->components([
                

                TextInput::make('nama_barang')
                    ->required(),
                TextInput::make('kode_aset')
                    ->default(null),

                Select::make('kategori')
                    ->options([
                        'Elektronik' => 'Elektronik',
                        'Furnitur' => 'Furnitur',
                        'Bahan Ajar' => 'Bahan Ajar',
                        'ATK' => 'ATK (Alat Tulis Kantor)',
                        'Lainnya' => 'Lainnya',
                    ])
                    ->required()
                    ->searchable()
                    ->label('Kategori'),

                TextInput::make('jumlah')
                    ->required()
                    ->numeric()
                    ->default(1),
                TextInput::make('lokasi')
                    ->default(null),
                    
                Select::make('status')
                    ->options(['Baik' => 'Baik', 'Rusak' => 'Rusak', 'Dipinjam' => 'Dipinjam', 'Perbaikan' => 'Perbaikan'])
                    ->default('Baik')
                    ->required(),

                DatePicker::make('tanggal_beli')
                    ->label('Tanggal Beli'),

                TextInput::make('harga')
                    ->prefix('Rp')
                    ->numeric()
                    ->minValue(0)
                    ->step(1000),
                
                Select::make('user_id')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->label('Penanggung Jawab')
                    ->default(null)
                    ->columnSpanFull(), 

                FileUpload::make('bukti_pembelian')
                    ->label('Bukti Pembelian (Faktur/Kuitansi)')
                    ->downloadable()
                    ->openable()
                    ->maxSize(10240)
                    ->columnSpanFull(), 

                Textarea::make('keterangan')
                    ->default(null)
                    ->columnSpanFull(), 

                FileUpload::make('gambar')
                    ->label('Foto Barang')
                    ->maxSize(10240)
                    ->image()
                    ->downloadable()
                    ->openable()
                    ->columnSpanFull(), 
            ]);
    }
}
