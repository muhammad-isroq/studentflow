<?php

namespace App\Filament\Resources\Gurus\Pages;

use App\Filament\Resources\Gurus\GuruResource;
use Filament\Resources\Pages\Page;
use App\Models\Guru;
use App\Models\Program;
use Illuminate\Support\Collection;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;

class TeacherRecap extends Page
{
    protected static string $resource = GuruResource::class;
    
    protected static ?string $title = 'Rekap Mengajar';
    
    protected string $view = 'filament.resources.gurus.pages.teacher-recap';

    // Properti untuk menampung data yang akan ditampilkan
    public ?Guru $record = null;
     public Collection $programsWithTotals; // Ganti nama properti agar lebih jelas
     public int $grandTotalSessions = 0;
     public int $grandTotalTeachingMinutes = 0;

    // 1. TAMBAHKAN PROPERTI BARU UNTUK FILTER
    public $selectedMonth;
    public $selectedYear;
    public $months = [];
    public $years = [];

    public function mount(Guru $record): void
    {
        $this->record = $record;

        // Inisialisasi daftar bulan dan tahun untuk dropdown
        $this->months = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni',
            7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];
        $this->years = range(now()->year + 1, now()->year - 5); // 5 tahun ke belakang, 1 tahun ke depan

        // Set filter default ke bulan dan tahun saat ini
        $this->selectedMonth = now()->month;
        $this->selectedYear = now()->year;

        // Jalankan perhitungan awal
        $this->calculateRecap();
    }

    // 2. BUAT LIFECYCLE HOOK 'updated'
    // Method ini akan berjalan otomatis setiap kali properti $selectedMonth atau $selectedYear berubah
    public function updated($propertyName)
    {
        if (in_array($propertyName, ['selectedMonth', 'selectedYear'])) {
            $this->calculateRecap();
        }
    }

    // 3. PINDAHKAN LOGIKA PERHITUNGAN KE METHOD SENDIRI
    public function calculateRecap(): void
    {
        $guruId = $this->record->id;

        $programs = Program::whereHas('classSessions', function (Builder $query) use ($guruId) {
            $query->where(function ($q) use ($guruId) {
                $q->where('guru_id', $guruId)
                  ->orWhere('replacement_guru_id', $guruId);
            })
            ->whereYear('session_date', $this->selectedYear)
            ->whereMonth('session_date', $this->selectedMonth);
        })
        ->withCount('siswas')
        ->get();

        $this->programsWithTotals = $programs->map(function ($program) use ($guruId) {
            $mainSessionsCount = $program->classSessions()
                ->where('guru_id', $guruId)
                ->whereNull('replacement_guru_id')
                ->whereYear('session_date', $this->selectedYear)
                ->whereMonth('session_date', $this->selectedMonth)
                ->count();

            $replacementSessionsCount = $program->classSessions()
                ->where('replacement_guru_id', $guruId)
                ->whereYear('session_date', $this->selectedYear)
                ->whereMonth('session_date', $this->selectedMonth)
                ->count();

            $program->total_sessions = $mainSessionsCount + $replacementSessionsCount;
            $program->replacement_sessions_count = $replacementSessionsCount;
            $program->total_teaching_minutes = $program->total_sessions * $program->jam_pelajaran;

            return $program;
        });

        $this->grandTotalSessions = $this->programsWithTotals->sum('total_sessions');
        $this->grandTotalTeachingMinutes = $this->programsWithTotals->sum('total_teaching_minutes');
    }

    // Atur judul halaman secara dinamis
    public function getTitle(): string | Htmlable
    {
        if ($this->record) {
            return 'Rekap Mengajar untuk ' . $this->record->nama_guru;
        }
        
        return 'Rekap Mengajar';
    }
    
    // Atur breadcrumbs untuk navigasi yang mudah
    public function getBreadcrumbs(): array
    {
        return [
            GuruResource::getUrl('index') => 'Guru',
            '#' => 'Rekap Mengajar',
        ];
    }
}