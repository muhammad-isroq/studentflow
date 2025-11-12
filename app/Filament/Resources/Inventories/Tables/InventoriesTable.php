<?php

namespace App\Filament\Resources\Inventories\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Columns\ImageColumn;
use Filament\Actions\ViewAction;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use App\Models\StockLog;
use App\Models\Inventory;

class InventoriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('gambar')
                    ->label('Foto')
                    ->width(100)
                    ->height(100)
                    ->circular(),
                TextColumn::make('nama_barang')
                    ->searchable(),
                TextColumn::make('kode_aset')
                    ->searchable(),
                TextColumn::make('kategori')
                    ->searchable(),
                TextColumn::make('jumlah')
                    ->label('Jumlah saat ini')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('harga')
                    ->numeric()
                    ->sortable()
                    ->money('idr', locale: 'id'),
                TextColumn::make('lokasi')
                    ->searchable(),
                TextColumn::make('status'),
                TextColumn::make('user.name')
                    ->label('Pengguna')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
                ViewAction::make(),
                Action::make('stockIn')
        ->label('Barang Masuk')
        ->icon('heroicon-o-plus-circle')
        ->color('success')
        ->form([
            TextInput::make('quantity')
                ->label('Jumlah Masuk')
                ->numeric()
                ->required()
                ->minValue(1)
                ->default(1),
            Textarea::make('reason')
                ->label('Alasan')
                ->placeholder('Contoh: Pembelian baru dari supplier')
                ->required(),
        ])
        ->action(function (Inventory $record, array $data): void {
            $quantity = (int)$data['quantity'];

            // 1. Tambah stok di tabel inventory
            $record->increment('jumlah', $quantity);

            // 2. Buat log
            StockLog::create([
                'inventory_id' => $record->id,
                'change_amount' => +$quantity,
                'stock_after_change' => $record->jumlah, // Stok baru setelah increment
                'reason' => $data['reason'],
                'user_id' => auth()->id(),
            ]);
        }),

    // ===============================================
    //       AKSI BARANG KELUAR (STOCK OUT)
    // ===============================================
    Action::make('stockOut')
        ->label('Barang Keluar')
        ->icon('heroicon-o-minus-circle')
        ->color('danger')
        ->form(fn (Inventory $record): array => [
                TextInput::make('quantity')
                    ->label('Jumlah Keluar')
                    ->numeric()
                    ->required()
                    ->minValue(1)
                    ->default(1)
                    // SEKARANG $record SUDAH TERDEFINISI DI SINI
                    ->maxValue($record->jumlah)
                    ->helperText("Stok saat ini: {$record->jumlah}"), // Bonus: tampilkan sisa stok
                Textarea::make('reason')
                    ->label('Alasan')
                    ->placeholder('Contoh: Barang rusak, hilang, dll.')
                    ->required(),
        ])
        ->action(function (Inventory $record, array $data): void {
            $quantity = (int)$data['quantity'];

            // 1. Kurangi stok di tabel inventory
            $record->decrement('jumlah', $quantity);

            // 2. Buat log
            StockLog::create([
                'inventory_id' => $record->id,
                'change_amount' => -$quantity, // Negatif
                'stock_after_change' => $record->jumlah, // Stok baru setelah decrement
                'reason' => $data['reason'],
                'user_id' => auth()->id(),
            ]);
        }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
