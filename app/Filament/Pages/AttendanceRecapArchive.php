<?php

namespace App\Filament\Pages;

use App\Models\Program;
use App\Models\AttendanceRecap as RecapModel; // Model Arsip
use Filament\Pages\Page;
use Illuminate\Support\Collection;
use BackedEnum;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Builder;

class AttendanceRecapArchive extends Page
{
    // Ganti ikon agar berbeda
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedArchiveBox;
    
    // Ini sudah OTOMATIS menunjuk ke file ...attendance-recap-archive.blade.php
    protected  string $view = 'filament.pages.attendance-recap-archive';

    // Ganti slug agar unik
    protected static ?string $slug = 'attendance-recap-archive/{program}';
    protected static bool $shouldRegisterNavigation = false;

    // Properti publik
    public Program $program;
    public Collection $recapData;
    public ?string $selectedSemester = '';

    // Tambahkan ini: Hanya Admin/Staff yang bisa mengakses
    public static function canAccess(): bool
    {
        return auth()->user()->hasRole(['admin', 'staff']);
    }

    public function getSemesterOptions(): Collection
    {
        return RecapModel::where('program_id', $this->program->id)
            ->select('semester_name')
            ->distinct()
            ->pluck('semester_name', 'semester_name');
    }

    public function mount(Program $program): void
    {
        $this->program = $program;
        $latestSemester = RecapModel::where('program_id', $this->program->id)
                            ->latest('created_at')
                            ->first();
        $this->selectedSemester = $latestSemester?->semester_name ?? '';
        $this->loadRecapData();
    }

    
    public function updatedSelectedSemester(): void
    {
        $this->loadRecapData();
    }
    
    protected function loadRecapData(): void
    {
        $this->recapData = RecapModel::query()
            ->with(['siswa', 'guru'])
            ->where('attendance_recaps.program_id', $this->program->id) 
            ->when($this->selectedSemester, function (Builder $query) {
                
                $query->where('attendance_recaps.semester_name', $this->selectedSemester);
            })
            ->join('siswas', 'attendance_recaps.siswa_id', '=', 'siswas.id')
            ->orderBy('siswas.nama')
            ->select('attendance_recaps.*')
            ->get();
    }
    
    public function getTitle(): string
    {
        return 'Arsip Rekap Absensi: ' . $this->program->nama_program;
    }
}