<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\Action;
use App\Models\ClassSession;
use Filament\Support\Icons\Heroicon;
use App\Filament\Pages\FillAttendance;
use Illuminate\Support\Facades\Auth;
use Filament\Tables\Columns\TextInputColumn;
use BackedEnum;

class ReplacementSessions extends Page implements HasTable
{
    use InteractsWithTable;

    protected string $view = 'filament.pages.replacement-sessions';
    
    // protected static ?string $navigationIcon = 'heroicon-o-arrow-path-rounded-square';
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedArrowPathRoundedSquare;
    
    protected static ?string $navigationLabel = 'Sesi Pengganti';
    
    protected static ?string $title = 'Sesi Penggantian Guru';
    
    protected static ?int $navigationSort = 2;
    
    public static function getNavigationGroup(): ?string
    {
        return 'Jadwal Program Saya';
    }

    // Method untuk mengatur apakah menu muncul di navigasi
    public static function shouldRegisterNavigation(): bool
    {
        // Hanya tampilkan menu jika user memiliki role 'guru'
        return auth()->check() && auth()->user()->hasRole('guru');
    }

    // Method untuk mengatur akses halaman
    public static function canAccess(): bool
    {
        // Hanya user dengan role 'guru' yang bisa akses
        return auth()->check() && auth()->user()->hasRole('guru');
    }

    public function table(Table $table): Table
    {
        // Dapatkan ID guru yang terhubung dengan user yang login
        $user = Auth::user();
        $guruId = $user->guru_id;

        return $table
            ->query(
                ClassSession::query()
                    ->where('replacement_guru_id', $guruId) 
                    ->whereNotNull('replacement_guru_id') 
                    ->with(['guru', 'program', 'replacementGuru']) 
            )
            ->columns([
                TextColumn::make('session_date')
                    ->label('Session Date')
                    ->date('l, d M Y')
                    ->sortable(),
                // TextColumn::make('guru.nama_guru')
                //     ->label('Guru Asli')
                //     ->badge()
                //     ->color('success'),
                // TextColumn::make('replacementGuru.nama_guru')
                //     ->label('Guru Pengganti (Anda)')
                //     ->badge()
                //     ->color('success'),
                TextColumn::make('program.nama_program')
                    ->label('Program'),
                TextColumn::make('program.nama_ruangan')
                    ->label('Room Name'),
                // TextColumn::make('program.jadwal_program')
                //     ->label('Program schedule'),
                TextColumn::make('program.lesson_time')
                    ->label('Lesson Time')
                    ->icon('heroicon-o-clock')
                    ->badge()
                    ->color('success'),
                TextInputColumn::make('topic')
                    ->label('Topik')
                    ->placeholder('-'),
            ])
            ->actions([
                Action::make('fill_attendance')
                    ->label('Isi Absensi')
                    ->icon('heroicon-o-pencil-square')
                    ->color('warning')
                    ->url(fn (ClassSession $record): string => FillAttendance::getUrl(['record' => $record])),
            ])
            ->defaultSort('session_date', 'asc')
            ->emptyStateHeading('Tidak ada sesi penggantian')
            ->emptyStateDescription('Anda belum ditugaskan untuk menggantikan guru lain.')
            ->emptyStateIcon('heroicon-o-calendar-days');
    }
}