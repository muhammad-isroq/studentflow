<?php

namespace App\Filament\Resources\Bills\Tables;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Columns\ImageColumn;

use App\Models\Bill;
use Carbon\Carbon;
use Filament\Notifications\Notification;

class BillsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('siswa.nama')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('paymentType.name')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('amount')
                    ->numeric()
                    ->sortable()
                    ->money('idr', locale: 'id'),
                ImageColumn::make('proof_of_payment')
                    ->label('Proof of payment')
                    ->disk('public')
                    // ->directory('proofs')
                    ->imageWidth(100)
                    ->imageHeight(100),
                TextColumn::make('due_date')
                    ->date()
                    ->sortable(),
                TextColumn::make('status')
                    ->searchable(),
                TextColumn::make('paid_at')
                    ->dateTime()
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
                Action::make('markAsPaid')
                    ->label('Mark paid off')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation() // Meminta konfirmasi admin
                    ->action(function (Bill $record) {
                        // 1. Update status tagihan
                        $record->update([
                            'status' => 'paid',
                            'paid_at' => now(),
                        ]);

                        // 2. Update penanda di data siswa
                        $siswa = $record->siswa;
                        if ($siswa) {
                            $currentPaidUntil = Carbon::parse($siswa->spp_paid_until ?? now()->subMonth());
                            $siswa->update([
                                // Set lunas sampai akhir bulan berikutnya dari pembayaran terakhir
                                'spp_paid_until' => $currentPaidUntil->addMonth()->endOfMonth(),
                            ]);
                        }
                        
                        Notification::make()
                            ->title('Pembayaran berhasil dicatat')
                            ->success()
                            ->send();
                    })
                    // Hanya tampilkan tombol ini jika tagihan belum lunas
                    ->visible(fn (Bill $record): bool => $record->status === 'unpaid'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->headerActions([
                // TOMBOL HAPUS SEMUA DATA TAGIHAN
                Action::make('deleteAllBills')
                    ->label('Kosongkan Data Tagihan')
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Hapus SEMUA Data Tagihan?')
                    ->modalDescription('PERINGATAN: Apakah Anda yakin ingin menghapus SELURUH data tagihan (baik yang sudah lunas maupun belum)? Tindakan ini akan membersihkan data secara permanen dan tidak dapat dibatalkan.')
                    ->modalSubmitActionLabel('Ya, Hapus Semua Permanen')
                    ->action(function () {
                        // 1. Menghitung total data sebelum dihapus (untuk notifikasi)
                        $count = Bill::count();

                        // 2. Menghapus semua data di tabel bills
                        Bill::query()->delete(); 
                        
                        // Opsi Alternatif: 
                        // Jika Anda ingin menghapus semuanya DAN mereset ID kembali ke 1,
                        // Anda bisa mematikan baris delete() di atas, lalu menyalakan baris di bawah ini:
                        // Bill::truncate();

                        // 3. (Opsional) Reset status spp_paid_until pada tabel Siswa
                        // Jika Anda mereset tagihan, Anda mungkin juga ingin mereset penanda lunas di data siswa
                        \App\Models\Siswa::query()->update(['spp_paid_until' => null]);

                        // 4. Menampilkan notifikasi sukses
                        Notification::make()
                            ->title("Berhasil membersihkan seluruh data ($count tagihan).")
                            ->success()
                            ->send();
                    }),
            ]);
    }
}
