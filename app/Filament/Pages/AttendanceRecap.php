<?php

namespace App\Filament\Pages;

use App\Models\Attendance;
use App\Models\Program;
use App\Models\Siswa;
use App\Models\ClassSession;
use Filament\Pages\Page;
use Illuminate\Support\Collection;
use BackedEnum;
use Filament\Support\Icons\Heroicon;

class AttendanceRecap extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentChartBar;
    

    protected string $view = 'filament.pages.attendance-recap';

    // Slug URL akan menjadi /attendance-recap/{program}
    protected static ?string $slug = 'attendance-recap/{program}';

    // Jangan tampilkan di menu navigasi utama
    protected static bool $shouldRegisterNavigation = false;

    // Properti publik untuk menampung data yang akan dikirim ke view
    public Program $program;
    public Collection $siswas;
    public Collection $sessions;
    public array $attendanceData = [];
    public array $attendanceScores = [];

    // Method mount untuk mengambil dan memproses data dari URL
    public function mount(Program $program): void
    {
        $this->program = $program;
        $this->siswas = Siswa::where('program_id', $this->program->id)->orderBy('nama')->get();
        $this->sessions = ClassSession::where('program_id', $this->program->id)->orderBy('session_date')->get();
        $totalSessions = $this->sessions->count();

        // Ambil semua data absensi untuk sesi-sesi di program ini
        $attendances = Attendance::whereIn('class_session_id', $this->sessions->pluck('id'))->get();

        // Susun data absensi ke dalam format yang mudah diakses: [siswa_id][session_id] => status
        foreach ($attendances as $attendance) {
            $this->attendanceData[$attendance->siswa_id][$attendance->class_session_id] = $attendance->status;
        }

        foreach ($this->siswas as $siswa) {
            $hadirCount = 0;
            if (isset($this->attendanceData[$siswa->id])) {
                // Hitung jumlah 'Hadir' untuk siswa ini
                $hadirCount = count(array_filter($this->attendanceData[$siswa->id], fn($status) => $status === 'Hadir'));
            }

            // Hitung persentase skor
            if ($totalSessions > 0) {
                $score = ($hadirCount / $totalSessions) * 100;
            } else {
                $score = 0;
            }

            // Simpan skor yang sudah dibulatkan ke dalam array
            $this->attendanceScores[$siswa->id] = round($score);
        }
    }
    
    // Atur judul halaman secara dinamis
    public function getTitle(): string
    {
        return 'Rekap Absensi: ' . $this->program->nama_program;
    }
}

