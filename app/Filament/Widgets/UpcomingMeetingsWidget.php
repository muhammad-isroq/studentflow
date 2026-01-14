<?php

namespace App\Filament\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use App\Models\ClassSession;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Pages\ProgramSchedule; // Import halaman ProgramSchedule

class UpcomingMeetingsWidget extends BaseWidget
{
    protected static ?int $sort = 2; // Atur agar widget ini tampil di bagian atas
    protected int | string | array $columnSpan = 'full';

    /**
     * Kontrol visibilitas widget.
     * Widget ini hanya akan muncul jika user yang login memiliki peran 'guru'.
     */
    public static function canView(): bool
    {
        return auth()->user()->hasRole('guru');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(function (): Builder {
                // Ambil ID guru dari user yang sedang login
                $guruId = auth()->user()->guru_id;

                return ClassSession::query()
                    // Hanya ambil sesi yang tanggalnya hari ini atau di masa depan
                    ->where('session_date', '>=', now()->startOfDay())
                    // Hanya ambil sesi milik guru yang sedang login
                    ->where('guru_id', $guruId)
                    // Urutkan berdasarkan tanggal terdekat
                    ->orderBy('session_date', 'asc')
                    // Batasi hanya 10 hasil
                    ->limit(10);
            })
            ->heading('10 Upcoming Meetings')
            ->columns([
                Tables\Columns\TextColumn::make('session_date')
                    ->label('Session Date')
                    ->date('l, d M Y'), // Format: Sunday, 17 Oct 2025

                Tables\Columns\TextColumn::make('program.nama_program')
                    ->label('Program')
                    ->description(fn (ClassSession $record): string => "Room Name: {$record->program->nama_ruangan}")
                    ->url(fn (ClassSession $record): string => ProgramSchedule::getUrl(['program' => $record->program_id]))
                    ->openUrlInNewTab(),
                Tables\Columns\TextColumn::make('unit')
                    ->label('Unit')
                    ->badge()
                    ->color('info') // Warna biru muda
                    ->placeholder('-'),
                Tables\Columns\TextColumn::make('program.lesson_time')
                ->label('Lesson Time')
                ->icon('heroicon-o-clock')
                ->badge()
                ->color('success'),
                Tables\Columns\TextColumn::make('topic')
                    ->label('topic')
                    ->html() 
                    ->wrap()
                    ->placeholder('no topics yet'),
            ])
            ->paginated(false) // Nonaktifkan paginasi untuk widget
            ->emptyStateHeading('No upcoming schedule')
            ->emptyStateDescription('There are currently no meetings scheduled in the near future.');
    }
}
