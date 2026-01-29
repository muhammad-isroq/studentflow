<?php

namespace App\Filament\Resources\Todos\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Table;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn; // <--- GANTI CHECKBOX JADI TOGGLE
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Support\Str;

class TodosTable
{
    public static function configure(Table $table): Table
    {
        return $table
            // 1. MATIKAN GRID (Agar jadi List/Tabel baris ke bawah)
            ->contentGrid(null) 
            
            ->columns([

                TextColumn::make('task')
                    ->label('Task Details')
                    ->weight(FontWeight::Bold)
                    ->searchable()
                    ->description(fn ($record) => Str::limit(strip_tags($record->description), 50)) 
                    ->wrap(),


                TextColumn::make('user.name')
                    ->label('Assigned To')
                    ->icon('heroicon-m-user-circle')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('category')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'urgent' => 'URGENT',
                        'important' => 'IMPORTANT',
                        'not_urgent' => 'Not Urgent',
                        'not_important' => 'Not Important',
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'urgent' => 'danger',
                        'important' => 'warning',
                        'not_urgent' => 'info',
                        default => 'gray',
                    })
                    ->sortable(),

                TextColumn::make('due_date')
                    ->date('d M Y')
                    ->sortable()
                    ->color(fn ($record) => $record->due_date < now() && !$record->is_completed ? 'danger' : 'gray'),

                ToggleColumn::make('is_completed')
                    ->label('Done?')
                    ->onColor('success')
                    ->offColor('danger')
                    // Logic: Disable jika user yang login BUKAN pemilik tugas
                    ->disabled(fn ($record) => $record->user_id !== auth()->id()),
            ])
            ->filters([
                // Filter User (Berguna saat di tab Our Staff)
                SelectFilter::make('user')
                    ->relationship('user', 'name')
                    ->label('Filter by Staff')
                    ->searchable(),
            ])
            ->actions([
                EditAction::make()
                    ->button(), // Ubah jadi tombol kecil agar rapi di tabel
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}