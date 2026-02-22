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

    protected static ?string $slug = 'attendance-recap/{program}';

    protected static bool $shouldRegisterNavigation = false;

    public Program $program;
    public Collection $siswas;
    public Collection $sessions;
    public array $attendanceData = [];
    public array $attendanceScores = [];

    public function mount(Program $program): void
    {
        $this->program = $program;

        // 1. Ambil semua sesi untuk program ini
        $this->sessions = ClassSession::where('program_id', $this->program->id)
            ->orderBy('session_date')
            ->get();

        $sessionIds = $this->sessions->pluck('id');

        // 2. Ambil siswa yang saat ini di program ini ATAU yang pernah hadir di sesi program ini
        $this->siswas = Siswa::query()
            ->where('program_id', $this->program->id)
            ->orWhereHas('attendances', function ($query) use ($sessionIds) {
                $query->whereIn('class_session_id', $sessionIds);
            })
            ->orderBy('nama')
            ->get();
            
        // 3. Ambil semua data kehadiran untuk sesi di program ini
        $attendances = Attendance::whereIn('class_session_id', $sessionIds)->get();

        foreach ($attendances as $attendance) {
            $this->attendanceData[$attendance->siswa_id][$attendance->class_session_id] = $attendance->status;
        }

        foreach ($this->siswas as $siswa) {
            $tanggalMasuk = $siswa->created_at; 

            // LOGIKA SESI EFEKTIF:
            // Sesi dianggap efektif jika:
            // - Tanggal sesi >= tanggal masuk siswa
            // - DAN (Siswa masih di program ini ATAU siswa punya catatan kehadiran di sesi tersebut)
            // Ini penting agar siswa yang pindah "keluar" saat Ramadan tidak dihitung Alpha di kelas aslinya.
            $sesiEfektifSiswa = $this->sessions->filter(function ($session) use ($tanggalMasuk, $siswa) {
                $hasAttendanceRecord = isset($this->attendanceData[$siswa->id][$session->id]);
                $isStillInProgram = $siswa->program_id === $this->program->id;

                return $session->session_date >= $tanggalMasuk && ($isStillInProgram || $hasAttendanceRecord);
            });

            $totalSesiSiswa = $sesiEfektifSiswa->count();

            $hadirCount = 0;
            if (isset($this->attendanceData[$siswa->id])) {
                $sesiEfektifIds = $sesiEfektifSiswa->pluck('id');
                
                $hadirCount = count(array_filter($this->attendanceData[$siswa->id], function($status, $sessionId) use ($sesiEfektifIds) {
                    return $status === 'Hadir' && $sesiEfektifIds->contains($sessionId);
                }, ARRAY_FILTER_USE_BOTH));
            }

            // Hitung Skor (Persentase Kehadiran)
            if ($totalSesiSiswa > 0) {
                $score = ($hadirCount / $totalSesiSiswa) * 100;
                $score = min($score, 100);
            } else {
                $score = 0; 
            }

            $this->attendanceScores[$siswa->id] = round($score);
        }
    }
    
    public function getTitle(): string
    {
        return 'Attendance Recap: ' . $this->program->nama_program;
    }
}