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

    // 1. Ambil sesi reguler kelas ini DAN semua sesi berlabel Ramadhan
    $this->sessions = ClassSession::where('program_id', $this->program->id)
        ->orWhere('is_ramadhan_session', true)
        ->orderBy('session_date')
        ->get();

    $sessionIds = $this->sessions->pluck('id');

    // 2. Ambil siswa aktif di program ini
    $this->siswas = Siswa::where('program_id', $this->program->id)
        ->orderBy('nama')
        ->get();

    // 3. Ambil data kehadiran siswa kelas ini di SEMUA sesi (reguler + ramadhan)
    $attendances = Attendance::whereIn('siswa_id', $this->siswas->pluck('id'))
        ->whereIn('class_session_id', $sessionIds)
        ->get();

    $this->attendanceData = $attendances->groupBy('siswa_id')
        ->map(fn($item) => $item->pluck('status', 'class_session_id'))
        ->toArray();

    foreach ($this->siswas as $siswa) {
        $dataAbsenSiswa = $this->attendanceData[$siswa->id] ?? [];
        $sessionIdsPernahAbsen = array_keys($dataAbsenSiswa);

        // --- LOGIKA JENDELA WAKTU ---
        // Sesi pertama kali dia muncul (baik reguler atau ramadhan)
        $tanggalSesiPertama = $this->sessions->whereIn('id', $sessionIdsPernahAbsen)->min('session_date');
        $startLimit = $tanggalSesiPertama ?: $siswa->created_at->format('Y-m-d');

        // Batas akhir: Selalu sesi terakhir yang tersedia di list (reguler + ramadhan)
        $endLimit = $this->sessions->max('session_date') ?: now()->format('Y-m-d');

        // C. FILTER SESI EFEKTIF
        // Kita hanya menghitung sesi jika:
        // - Sesi itu adalah sesi reguler kelasnya
        // - ATAU Sesi itu ramadhan DAN dia punya record absen di sana (artinya dia ikut mutasi)
        $sesiEfektif = $this->sessions->filter(function ($session) use ($startLimit, $endLimit, $siswa, $sessionIdsPernahAbsen) {
            $isDateValid = $session->session_date >= $startLimit && $session->session_date <= $endLimit;
            
            $isRegularSession = $session->program_id === $this->program->id;
            $isFollowedRamadhan = $session->is_ramadhan_session && in_array($session->id, $sessionIdsPernahAbsen);

            return $isDateValid && ($isRegularSession || $isFollowedRamadhan);
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
            'is_moved' => false // Di sini siswa dianggap tetap di kelas aslinya
        ];
    }
}

    public function getTitle(): string
    {
        return 'Rekap Absensi: ' . $this->program->nama_program;
    }
}