<?php

namespace App\Filament\Pages;

use App\Models\Siswa;
use App\Models\Attendance;
use Filament\Pages\Page;
use Illuminate\Database\Eloquent\Collection;
use BackedEnum;
use Filament\Support\Icons\Heroicon;

class StudentAttendanceHistory extends Page
{
    
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentText;

    protected string $view = 'filament.pages.student-attendance-history';

    // URL akan menerima parameter {siswa}
    protected static ?string $slug = 'student-attendance-history/{siswa}';

    // Sembunyikan dari menu navigasi
    protected static bool $shouldRegisterNavigation = false;

    public Siswa $siswa;
    public Collection $attendances;
    public ?int $attendanceScore = null;

    public function mount(Siswa $siswa): void
    {
        $this->siswa = $siswa;
        // Ambil semua data absensi untuk siswa ini, urutkan dari yang terbaru
        // Muat juga relasi 'classSession.program' untuk ditampilkan
        $this->attendances = Attendance::where('siswa_id', $this->siswa->id)
            ->with(['classSession.program'])
            ->join('class_sessions', 'attendances.class_session_id', '=', 'class_sessions.id')
            ->orderBy('class_sessions.session_date', 'desc')
            ->get();

            $totalSessions = $this->attendances->count();

        // Hanya hitung jika ada sesi
        if ($totalSessions > 0) {
            $presentCount = $this->attendances->where('status', 'Hadir')->count();
            // Hitung persentase dan bulatkan
            $this->attendanceScore = round(($presentCount / $totalSessions) * 100);
        }
    }

    public function getTitle(): string
    {
        return 'Riwayat Absensi untuk: ' . $this->siswa->nama;
    }
}
