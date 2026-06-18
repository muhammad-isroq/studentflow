<?php

namespace App\Filament\Resources\Programs\Tables;

use App\Filament\Resources\Siswas\SiswaResource;
use App\Models\Program;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Actions\Action;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\TextInput;
use App\Models\Siswa;
use App\Models\ClassSession;
use App\Models\Attendance;
use App\Models\AttendanceRecap as RecapModel; // Ganti nama agar tidak bentrok
use Illuminate\Support\Facades\DB;
use Filament\Notifications\Notification; // Untuk memberi notifikasi
use App\Models\Guru; // <-- DIUBAH DARI USER

class ProgramsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nama_program')
                    ->label('Program name')
                    ->searchable(),
                TextColumn::make('nama_ruangan')
                    ->label('Room name')
                    ->searchable(),
                TextColumn::make('jadwal_program')
                    ->label('Program schedule')
                    ->searchable(),
                TextColumn::make('lesson_time')
                    ->label('Lesson Time')
                    ->icon('heroicon-o-clock')
                    ->searchable(),
                TextColumn::make('guru.nama_guru') 
                    ->label('Teachers name')
                    ->searchable(),
                TextColumn::make('siswas_count')
                ->counts('siswas')
                ->label('Total Students')
                ->badge()
                ->color('primary')
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
            ->paginated([10, 25, 50, 75, 100, 'all'])
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