<?php

namespace App\Filament\Resources\Siswas\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Columns\ImageColumn;
use Filament\Actions\ViewAction;
use Filament\Actions\Action;
use App\Filament\Pages\StudentAttendanceHistory;

class SiswasTable
{

    protected static ?string $defaultSort = 'created_at, desc';

    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nama')
                    ->label('Name')
                    ->searchable(),
                // ImageColumn::make('foto')
                //     ->label('Image')
                //     ->imageWidth(100)
                //     ->imageHeight(100)
                //     ->circular(),
                TextColumn::make('program.nama_program')
                    ->label('Program')
                    ->searchable(),
                TextColumn::make('program.nama_ruangan')
                    ->label('Room Name')
                    ->searchable(),
                // TextColumn::make('kelas_disekolah')
                //     ->label('Grade')
                //     ->searchable(),
                TextColumn::make('status')
                    ->label('status')
                    ->searchable(),
                TextColumn::make('no_wali')
                    ->label('Parents number')
                    ->searchable(),
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
            ->paginated([10, 25, 50, 75, 100, 'all'])
            ->recordActions([
                EditAction::make(),
                ViewAction::make(),
                    Action::make('attendance_history')
                        ->label('Lihat Absensi')
                        ->icon('heroicon-o-calendar-days')
                        ->color('success')
                        // Arahkan ke halaman riwayat absensi dengan membawa record Siswa saat ini
                        ->url(fn (\App\Models\Siswa $record): string => StudentAttendanceHistory::getUrl(['siswa' => $record])),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
