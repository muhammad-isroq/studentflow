<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use App\Models\ClassSession;

class TeacherSchedule extends Page implements HasTable
{
    use InteractsWithTable;


    protected string $view = 'filament.pages.teacher-schedule';

    protected static ?string $navigationLabel = 'Jadwal & Absensi Saya';
    protected static ?string $title = 'Jadwal & Absensi Mengajar';

    // Method ini memastikan menu hanya muncul untuk guru
    public static function shouldRegisterNavigation(): bool
    {
        // return auth()->user()->hasRole('guru');
        return false;
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                // Query hanya mengambil sesi milik guru yang sedang login
                ClassSession::query()->where('guru_id', auth()->user()->guru_id)
            )
            ->columns([
                TextColumn::make('session_date')->label('Tanggal Sesi')->date('l, d M Y')->sortable(),
                TextColumn::make('program.nama_program')->label('Program'),
                TextColumn::make('program.nama_ruangan')->label('Ruangan'),
            ])
            ->actions([
                // Nanti kita akan tambahkan tombol "Isi Absensi" di sini
            ])
            ->defaultSort('session_date', 'asc');
    }
}