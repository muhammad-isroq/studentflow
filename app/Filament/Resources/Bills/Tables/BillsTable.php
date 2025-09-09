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
            ]);
    }
}
