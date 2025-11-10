<?php

namespace App\Filament\Resources\Borrowings\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Table;
use App\Models\Borrowing;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Actions\Action;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\Indicator;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class BorrowingsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('inventory.nama_barang')
                ->label('Barang')
                ->searchable()
                ->sortable(),
            TextColumn::make('borrower_name')
                ->label('Peminjam')
                ->searchable()
                ->sortable(),
            TextColumn::make('quantity')
                ->label('Jumlah')
                ->numeric()
                ->sortable(),
            TextColumn::make('borrow_date')
                ->label('Tgl. Pinjam')
                ->date('d M Y')
                ->sortable(),
            TextColumn::make('due_date')
                ->label('Jatuh Tempo')
                ->date('d M Y')
                ->sortable(),
            
            // Kolom Status (Otomatis)
            IconColumn::make('return_date')
                ->label('Status')
                ->boolean()
                ->trueIcon('heroicon-o-check-circle') // Sudah kembali
                ->trueColor('success')
                ->falseIcon('heroicon-o-clock') // Belum kembali
                ->falseColor('warning')
                ->getStateUsing(fn ($record): bool => $record->return_date !== null)
                ->tooltip(fn ($record): string => $record->return_date ? 'Dikembalikan: '.$record->return_date->format('d M Y') : 'Belum Kembali'),
            ])
            ->filters([
            Filter::make('belum_dikembalikan')
                ->label('Peminjaman Aktif')
                ->query(fn (Builder $query): Builder => $query->whereNull('return_date'))
                ->default(), // Filter ini aktif secara default

            Filter::make('jatuh_tempo')
                ->label('Jatuh Tempo')
                ->query(fn (Builder $query): Builder => 
                    $query->whereNull('return_date')->where('due_date', '<', Carbon::today())
                )
                ->indicator(Indicator::make('Jatuh Tempo')->color('danger')),

            Filter::make('sudah_dikembalikan')
                ->label('Sudah Dikembalikan')
                ->query(fn (Builder $query): Builder => $query->whereNotNull('return_date')),
        ])
            ->recordActions([
                EditAction::make(),
                Action::make('returnItem')
                ->label('Kembalikan Barang')
                ->icon('heroicon-o-check-badge')
                ->color('success')
                // Hanya tampilkan tombol ini jika barang BELUM dikembalikan
                ->visible(fn (Borrowing $record): bool => $record->return_date === null) 
                ->action(function (Borrowing $record) {
                    $record->update([
                        'return_date' => now()
                    ]);
                    // Observer Anda akan otomatis menambah stok barang
                })
                ->requiresConfirmation()
                ->modalHeading('Konfirmasi Pengembalian')
                ->modalDescription('Anda yakin barang ini sudah dikembalikan? Stok barang akan otomatis bertambah.'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
