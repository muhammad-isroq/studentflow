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

        // 1. Ambil semua sesi untuk program ini urut tanggal
        $this->sessions = ClassSession::where('program_id', $this->program->id)
            ->orderBy('session_date')
            ->get();

        $sessionIds = $this->sessions->pluck('id');

        // 2. Ambil data kehadiran (Eager Load untuk performa)
        $attendances = Attendance::whereIn('class_session_id', $sessionIds)->get();

        // Map data agar mudah diakses: $this->attendanceData[siswa_id][session_id] = status
        $this->attendanceData = $attendances->groupBy('siswa_id')
            ->map(fn($item) => $item->pluck('status', 'class_session_id'))
            ->toArray();

        // 3. Ambil siswa (Aktif di program ini ATAU pernah punya record absen di sini)
        $this->siswas = Siswa::query()
            ->where('program_id', $this->program->id)
            ->orWhereHas('attendances', fn($q) => $q->whereIn('class_session_id', $sessionIds))
            ->orderBy('nama')
            ->get();

        foreach ($this->siswas as $siswa) {
            $dataAbsenSiswa = $this->attendanceData[$siswa->id] ?? [];
            $sessionIdsPernahAbsen = array_keys($dataAbsenSiswa);

            // --- LOGIKA JENDELA WAKTU (START & END LIMIT) ---

            // A. START LIMIT: Sesi pertama dia ikut ATAU tanggal akun dibuat
            $tanggalSesiPertama = $this->sessions->whereIn('id', $sessionIdsPernahAbsen)->min('session_date');
            $startLimit = $tanggalSesiPertama ?: $siswa->created_at->format('Y-m-d');

            // B. END LIMIT: 
            if ($siswa->program_id === $this->program->id) {
                // Jika masih di kelas ini, batas akhirnya adalah sesi terakhir yang ada
                $endLimit = $this->sessions->max('session_date') ?: now()->format('Y-m-d');
            } else {
                // Jika sudah pindah, batas akhirnya adalah tanggal sesi terakhir dia absen di kelas ini
                $tanggalSesiTerakhir = $this->sessions->whereIn('id', $sessionIdsPernahAbsen)->max('session_date');
                $endLimit = $tanggalSesiTerakhir ?: $startLimit;
            }

            // C. FILTER SESI EFEKTIF
            $sesiEfektif = $this->sessions->filter(function ($session) use ($startLimit, $endLimit) {
                return $session->session_date >= $startLimit && $session->session_date <= $endLimit;
            });

            $totalSesiEfektif = $sesiEfektif->count();
            $sesiEfektifIds = $sesiEfektif->pluck('id');

            // D. HITUNG KEHADIRAN
            $hadirCount = collect($dataAbsenSiswa)
                ->filter(fn($status, $sessionId) => $status === 'Hadir' && $sesiEfektifIds->contains($sessionId))
                ->count();

            // E. SKOR AKHIR
            $score = $totalSesiEfektif > 0 ? ($hadirCount / $totalSesiEfektif) * 100 : 0;
            
            $this->attendanceScores[$siswa->id] = [
                'score' => round(min($score, 100)),
                'total' => $totalSesiEfektif,
                'hadir' => $hadirCount,
                'is_moved' => $siswa->program_id !== $this->program->id // Label jika siswa sudah pindah
            ];
        }
    }

    public function getTitle(): string
    {
        return 'Rekap Absensi: ' . $this->program->nama_program;
    }
}