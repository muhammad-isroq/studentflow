<?php

namespace App\Filament\Resources\Gurus\Pages;

use App\Filament\Resources\Gurus\GuruResource;
use Filament\Resources\Pages\Page;
use App\Models\Guru;
use App\Models\Program;
use Illuminate\Support\Collection;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Notifications\Notification;

class TeacherRecap extends Page
{
    protected static string $resource = GuruResource::class;
    
    protected static ?string $title = 'Rekap Mengajar';
    
    protected string $view = 'filament.resources.gurus.pages.teacher-recap';

    public ?Guru $record = null;
    public Collection $programsWithTotals;
    public int $grandTotalSessions = 0;
    public int $grandTotalTeachingMinutes = 0;
    
    public $selectedMonth;
    public $selectedYear;
    public $months = [];
    public $years = [];

    public function mount(Guru $record): void
    {
        $this->record = $record;

        $this->months = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni',
            7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];
        $this->years = range(now()->year + 1, now()->year - 5);

        $this->selectedMonth = now()->month;
        $this->selectedYear = now()->year;

        $this->calculateRecap();
    }

    public function updated($propertyName)
    {
        if (in_array($propertyName, ['selectedMonth', 'selectedYear'])) {
            $this->calculateRecap();
        }
    }

    public function calculateRecap(): void
    {
        $guruId = $this->record->id;

        // 1. TENTUKAN RENTANG WAKTU (Tanggal 1 - 28)
        $startDate = Carbon::create($this->selectedYear, $this->selectedMonth, 1)->startOfDay();
        $endDate = Carbon::create($this->selectedYear, $this->selectedMonth, 28)->endOfDay();

        // 2. Ambil program dengan menggunakan whereBetween untuk memfilter rentang tanggal
        $programs = Program::whereHas('classSessions', function (Builder $query) use ($guruId, $startDate, $endDate) {
            $query->where(function ($q) use ($guruId) {
                $q->where('guru_id', $guruId)
                  ->orWhere('replacement_guru_id', $guruId);
            })
            // FILTER RENTANG TANGGAL 1 - 28
            ->whereBetween('session_date', [$startDate, $endDate])
            ->whereNotNull('activity')
            ->where('activity', '!=', '')
            ->has('attendances'); 
        })
        ->withCount('siswas')
        ->get();

        $this->programsWithTotals = $programs->map(function ($program) use ($guruId, $startDate, $endDate) {
            
            // Base Query untuk sesi yang valid dengan filter tanggal 1 - 28
            $validSessionsQuery = $program->classSessions()
                ->whereBetween('session_date', [$startDate, $endDate])
                ->whereNotNull('activity')
                ->where('activity', '!=', '')
                ->has('attendances');

            $mainSessionsCount = (clone $validSessionsQuery)
                ->where('guru_id', $guruId)
                ->whereNull('replacement_guru_id')
                ->count();

            $replacementSessionsCount = (clone $validSessionsQuery)
                ->where('replacement_guru_id', $guruId)
                ->count();

            $program->total_sessions = $mainSessionsCount + $replacementSessionsCount;
            $program->replacement_sessions_count = $replacementSessionsCount;
            $program->total_teaching_minutes = $program->total_sessions * $program->jam_pelajaran;

            return $program;
        });

        $this->grandTotalSessions = $this->programsWithTotals->sum('total_sessions');
        $this->grandTotalTeachingMinutes = $this->programsWithTotals->sum('total_teaching_minutes');
    }

    // protected function getHeaderActions(): array
    // {
    //     return [
    //         Action::make('archive_recap')
    //             ->label('Arsipkan Rekap')
    //             ->icon('heroicon-o-archive-box-arrow-down')
    //             ->color('success')
    //             ->requiresConfirmation()
    //             ->modalHeading('Arsipkan Rekap Bulan Ini?')
    //             ->modalDescription(fn() => 'Apakah Anda yakin ingin menyimpan rekap bulan ' . $this->months[$this->selectedMonth] . ' ' . $this->selectedYear . ' ke dalam arsip permanen?')
    //             ->action(function () {
                    
    //                 // Pastikan ada data yang diarsipkan
    //                 if ($this->grandTotalSessions === 0) {
    //                     Notification::make()
    //                         ->title('Gagal Arsip')
    //                         ->body('Tidak ada data mengajar (0 pertemuan) pada bulan ini.')
    //                         ->warning()
    //                         ->send();
    //                     return;
    //                 }

    //                 // Gunakan updateOrCreate agar 1 guru hanya punya 1 arsip per bulan
    //                 \App\Models\TeacherRecapArchive::updateOrCreate(
    //                     [
    //                         'guru_id' => $this->record->id,
    //                         'month' => $this->selectedMonth,
    //                         'year' => $this->selectedYear,
    //                     ],
    //                     [
    //                         'total_sessions' => $this->grandTotalSessions,
    //                         'total_teaching_minutes' => $this->grandTotalTeachingMinutes,
    //                         // Convert detail program menjadi array JSON
    //                         'program_details' => $this->programsWithTotals->toArray(),
    //                     ]
    //                 );

    //                 Notification::make()
    //                     ->title('Rekap Berhasil Diarsipkan')
    //                     ->body('Data rekap mengajar bulan ' . $this->months[$this->selectedMonth] . ' ' . $this->selectedYear . ' telah disimpan permanen.')
    //                     ->success()
    //                     ->send();
    //             }),
    //     ];
    // }

    public function getTitle(): string | Htmlable
    {
        if ($this->record) {
            return 'Rekap Mengajar untuk ' . $this->record->nama_guru;
        }
        
        return 'Rekap Mengajar';
    }
    
    public function getBreadcrumbs(): array
    {
        return [
            GuruResource::getUrl('index') => 'Guru',
            '#' => 'Rekap Mengajar',
        ];
    }
}