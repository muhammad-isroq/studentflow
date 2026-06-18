<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Components\Select;
use App\Models\Program;
use App\Models\AttendanceRecap;
use BackedEnum;
use UnitEnum;
use Filament\Support\Icons\Heroicon;

class AttendanceRecapArchive extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedArchiveBox;
    protected static ?string $navigationLabel = 'Arsip Absensi';
    protected static ?string $title = 'Arsip Rekap Absensi';
    
    // UBAH SLUG MENJADI GLOBAL (tanpa {program})
    protected static ?string $slug = 'arsip-absensi-global';
    
    // Tampilkan di menu sidebar agar mudah diakses
    protected static bool $shouldRegisterNavigation = true; 
    protected static string | \UnitEnum | null $navigationGroup = 'Academic';

    protected string $view = 'filament.pages.attendance-recap-archive';

    // Variabel filter
    public ?int $program_id = null;
    public ?string $semester_name = null;

    public static function canAccess(): bool
    {
        return auth()->user()->hasRole(['admin', 'staff']);
    }

    public function mount()
    {
        $this->form->fill();
    }

    public function form($form) 
    {
        return $form
            ->schema([
                Select::make('semester_name')
                    ->label('1. Pilih Semester')
                    ->options(function () {
                        return AttendanceRecap::select('semester_name')
                            ->distinct()
                            ->pluck('semester_name', 'semester_name');
                    })
                    ->searchable()
                    ->live()
                    ->afterStateUpdated(fn ($set) => $set('program_id', null)) 
                    ->required(),
                    
                Select::make('program_id')
                    ->label('2. Pilih Program Kelas')
                    ->options(function ($get) { 
                        $selectedSemester = $get('semester_name');
                        if (!$selectedSemester) return [];

                        $activeProgramIds = AttendanceRecap::where('semester_name', $selectedSemester)
                            ->pluck('program_id')
                            ->unique(); 

                        return Program::whereIn('id', $activeProgramIds)
                            ->pluck('nama_program', 'id');
                    })
                    ->disabled(fn ($get) => blank($get('semester_name'))) 
                    ->searchable()
                    ->live()
                    ->required(),
            ])->columns(2);
    }

    public function getReportDataProperty()
    {
        if (!$this->program_id || !$this->semester_name) {
            return null;
        }

        $program = Program::find($this->program_id);
        
        // Ambil data absensi berdasarkan filter
        $reports = AttendanceRecap::with(['siswa', 'guru'])
                    ->where('program_id', $this->program_id)
                    ->where('semester_name', $this->semester_name)
                    ->get()
                    ->sortBy(fn($report) => $report->siswa->nama ?? '');

        return [
            'program' => $program,
            'reports' => $reports,
        ];
    }
}