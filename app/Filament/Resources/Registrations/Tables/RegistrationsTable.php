<?php

namespace App\Filament\Resources\Registrations\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Table;

class RegistrationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('id', 'desc')
            ->columns([
                TextColumn::make('nama')->searchable()->sortable()->description(fn ($record): string => "ID: REG-{$record->id}"),
                TextColumn::make('grade')->label('Grade'),
                TextColumn::make('no_wa_wali')->label('WA Wali'),
                
                
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'gray',
                        'waiting_verification' => 'info',
                        'paid' => 'success',
                        'selection' => 'warning',
                        'announced' => 'success', 
                        'rejected' => 'danger',
                        default => 'gray',
                    }),
                    
                // ImageColumn::make('bukti_pembayaran')
                //     ->label('Bukti Bayar')
                //     ->circular(),
                    
                TextColumn::make('tgl_registrasi')
                    ->date()
                    ->sortable(),
                    ])
                    ->filters([
                        SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'waiting_verification' => 'Waiting Verification',
                        'paid' => 'Paid',
                        'rejected' => 'Rejected',
                    ])
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
