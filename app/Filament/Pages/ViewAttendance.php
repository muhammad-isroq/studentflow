<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use App\Models\ClassSession;
use App\Models\Attendance;
use App\Models\Guru;

class ViewAttendance extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $slug = 'class-sessions/{record}/view-attendance';
    protected string $view = 'filament.pages.view-attendance';
    protected static bool $shouldRegisterNavigation = false; // Sembunyikan dari menu utama

    public ClassSession $record;

    public function mount(ClassSession $record): void
    {
        $this->record = $record;
    }

    public function getTitle(): string
    {
        return 'Detail Absensi: ' . $this->record->program->nama_program;
    }
    
    public function getSubheading(): string
    {
        return 'Tanggal: ' . $this->record->session_date->format('l, d M Y');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                // Query mengambil data absensi yang terkait dengan sesi kelas ini
                Attendance::query()->where('class_session_id', $this->record->id)
            )
            ->columns([
                TextColumn::make('siswa.nama')
                    ->label('Nama Siswa')
                    ->searchable(),
                TextColumn::make('status')
                    ->label('Status Kehadiran')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Hadir' => 'success',
                        'Izin', 'Sakit' => 'warning',
                        'Absen' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('classSession.guru.nama_guru')
                ->label('Diabsen oleh'),
                TextColumn::make('notes')
                ->label('Notes'),
                TextColumn::make('updated_at')
                    ->label('Diperbarui pada')
                    ->dateTime('d M Y, H:i')
                    ->sortable(),
            ])
            ->paginated(false);
    }
}