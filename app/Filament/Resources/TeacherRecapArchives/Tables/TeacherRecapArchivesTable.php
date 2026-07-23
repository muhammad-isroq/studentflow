<?php

namespace App\Filament\Resources\TeacherRecapArchives\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;

class TeacherRecapArchivesTable
{
    public static array $months = [
        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni',
        7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
    ];
    
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('guru.nama_guru')
                    ->label('Nama Guru')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                
                TextColumn::make('month')
                    ->label('Bulan')
                    ->formatStateUsing(fn (int $state): string => self::$months[$state] ?? '-')
                    ->sortable(),
                
                TextColumn::make('year')
                    ->label('Tahun')
                    ->sortable(),

                TextColumn::make('total_sessions')
                    ->label('Total Pertemuan')
                    ->badge()
                    ->color('info'),

                TextColumn::make('total_teaching_minutes')
                    ->label('Total Jam Mengajar')
                    ->formatStateUsing(function ($state) {
                        $jam = floor($state / 60);
                        $menit = $state % 60;
                        return "{$jam} Jam {$menit} Menit";
                    }),
            ])
            ->filters([
                SelectFilter::make('year')
                    ->label('Filter Tahun')
                    ->options(array_combine(
                        range(now()->year - 2, now()->year + 1),
                        range(now()->year - 2, now()->year + 1)
                    )),
                SelectFilter::make('month')
                    ->label('Filter Bulan')
                    ->options(self::$months),
            ])
            ->actions([
                ViewAction::make()->label('Lihat Detail'),
            ])
            ->defaultSort('year', 'desc');
    }
}
