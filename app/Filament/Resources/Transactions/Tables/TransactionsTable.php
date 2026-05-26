<?php

namespace App\Filament\Resources\Transactions\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use App\Models\Bill; 
use App\Filament\Resources\Bills\BillResource;
use Filament\Actions\Action; 

class TransactionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'income' => 'success',
                        'expense' => 'danger',
                    }),

                TextColumn::make('category')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('amount')
                    ->money('IDR')
                    ->sortable(),

                TextColumn::make('description')
                    ->limit(30)
                    ->searchable(),

                ImageColumn::make('proof_image')
                    ->label('Bukti')        
                    ->width(60)
                    ->disk('public')
                    ->height(60)
                    ->square()
                    ->getStateUsing(function ($record) {
                        return $record->proof_image;
                    }),
                TextColumn::make('date')
                    ->date('d M Y')
                    ->sortable(),

                // USER FRIENDLY: Nama User
                TextColumn::make('user.name') 
                    ->label('Dicatat Oleh')
                    ->icon('heroicon-m-user-circle')
                    ->sortable()
                    ->toggleable(),

                // USER FRIENDLY: Sumber Transaksi (Smart Badge)
                TextColumn::make('reference_type')
                    ->label('Sumber')
                    ->badge()
                    ->formatStateUsing(function ($state, $record) {
                        if (!$record->reference_id) {
                            return 'Input Manual';
                        }
                        
                        // Jika referensinya adalah Bill
                        if ($state === Bill::class) {
                            // Gunakan operator aman (?->)
                            $namaSiswa = $record->reference?->siswa?->nama ?? 'Siswa';
                            return "Tagihan SPP {$namaSiswa}";
                        }

                        return 'System';
                    })
                    ->color(fn ($state, $record) => $record->reference_id ? 'info' : 'gray')
                    ->url(function ($record) {
                        // Pastikan ID ada dan class BillResource valid
                        if ($record->reference_type === Bill::class && $record->reference_id) {
                            return BillResource::getUrl('edit', ['record' => $record->reference_id]);
                        }
                        return null;
                    })
                    ->openUrlInNewTab(),
                    
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->headerActions([
                // TOMBOL HAPUS SEMUA TRANSAKSI
                Action::make('deleteAllTransactions')
                    ->label('Kosongkan Data Transaksi')
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Hapus SEMUA Data Transaksi?')
                    ->modalDescription('PERINGATAN: Apakah Anda yakin ingin menghapus SELURUH data transaksi? Tindakan ini akan membersihkan data riwayat kas secara permanen dan tidak dapat dibatalkan.')
                    ->modalSubmitActionLabel('Ya, Hapus Semua Permanen')
                    ->action(function () {
                        // 1. Menghitung jumlah data untuk ditampilkan di notifikasi
                        $count = \App\Models\Transaction::count();

                        // 2. Menghapus semua baris data di tabel transactions
                        \App\Models\Transaction::query()->delete();
                        
                        // Opsi Alternatif (sama seperti Bills):
                        // Gunakan truncate() jika Anda ingin mereset ID kembali ke 1.
                        // \App\Models\Transaction::truncate();

                        // 3. Menampilkan notifikasi sukses
                        \Filament\Notifications\Notification::make()
                            ->title("Berhasil membersihkan seluruh data ($count transaksi).")
                            ->success()
                            ->send();
                    }),
            ]);
    }
}