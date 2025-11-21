<?php
namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use App\Models\ClassSession;
use App\Models\Program;
use BackedEnum;
use Filament\Support\Icons\Heroicon;
use App\Filament\Pages\FillAttendance;
use Filament\Actions\Action;
use Illuminate\Support\Facades\Auth;
use Filament\Tables\Columns\TextInputColumn;

class ProgramSchedule extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserGroup;

    // Route halaman ini akan menerima parameter {program}
    protected static ?string $slug = 'program-schedule/{program}';

    protected string $view = 'filament.pages.program-schedule';
    
    protected function getHeaderActions(): array
    {
        return [
            Action::make('Rekap Absen')
            ->label('Rekap Absen')
            ->icon('heroicon-o-document-chart-bar')
            // Arahkan ke halaman AttendanceRecap dengan membawa ID Program saat ini
            ->url(fn (): string => AttendanceRecap::getUrl(['program' => $this->program->id])),
        ];
    }

    // Jangan daftarkan halaman ini ke navigasi secara otomatis
    protected static bool $shouldRegisterNavigation = false;

    public Program $program;

    // Method 'mount' akan mengambil data program dari URL
    public function mount(Program $program): void
    {
        $this->program = $program;
    }

    // Atur judul halaman secara dinamis
    public function getTitle(): string
    {
        return 'Jadwal untuk Program: ' . $this->program->nama_program;
    }

    public function table(Table $table): Table
    {
        // Dapatkan ID guru yang terhubung dengan user yang login
        $user = Auth::user();
        $guruId = $user->guru_id; // User memiliki guru_id yang merujuk ke tabel gurus

        return $table
            // Query dengan filter: hanya tampilkan sesi milik guru yang sedang login
            ->query(
                ClassSession::query()
                    ->where('program_id', $this->program->id)
                    ->when($guruId, function ($query) use ($guruId) {
                        // Hanya filter jika user memiliki guru_id
                        $query->where('guru_id', $guruId);
                    })
            )
            ->columns([
                TextColumn::make('session_date')->label('Meeting Date')->date('l, d M Y')->sortable(),
                TextColumn::make('guru.nama_guru')->label('Teacher'),
                TextColumn::make('program.nama_ruangan')->label('Room Name'),
                TextColumn::make('program.jadwal_program')->label('Program schedule'),
                TextColumn::make('program.lesson_time')->label('Lesson Time'),
                TextInputColumn::make('topic')->label('Topic'),
            ])
            ->actions([
                Action::make('fill_attendance')
                    ->label('Isi Absensi')
                    ->icon('heroicon-o-pencil-square')
                    // Arahkan ke halaman FillAttendance dengan membawa ID Sesi
                    ->url(fn (ClassSession $record): string => FillAttendance::getUrl(['record' => $record])),
            ])
            ->defaultSort('session_date', 'asc');
    }
}