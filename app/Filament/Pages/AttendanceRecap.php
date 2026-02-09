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
        $this->siswas = Siswa::where('program_id', $this->program->id)->orderBy('nama')->get();
        
        $this->sessions = ClassSession::where('program_id', $this->program->id)
            ->orderBy('session_date')
            ->get();
            
        $attendances = Attendance::whereIn('class_session_id', $this->sessions->pluck('id'))->get();

        foreach ($attendances as $attendance) {
            $this->attendanceData[$attendance->siswa_id][$attendance->class_session_id] = $attendance->status;
        }

        foreach ($this->siswas as $siswa) {

            $tanggalMasuk = $siswa->created_at; // tanggal masuk diambail dari data siswa berdasarkan tanggal data dibuat

            $sesiEfektifSiswa = $this->sessions->filter(function ($session) use ($tanggalMasuk) {

                return $session->session_date >= $tanggalMasuk; 
            });

            $totalSesiSiswa = $sesiEfektifSiswa->count();


            $hadirCount = 0;
            if (isset($this->attendanceData[$siswa->id])) {

                $hadirCount = count(array_filter($this->attendanceData[$siswa->id], fn($status) => $status === 'Hadir'));
            }

            
            if ($totalSesiSiswa > 0) {

                $score = ($hadirCount / $totalSesiSiswa) * 100;
                

                $score = min($score, 100);
            } else {
                $score = 0; 
            }

            $this->attendanceScores[$siswa->id] = round($score);
            
        }
    }
    
    // Atur judul halaman secara dinamis
    public function getTitle(): string
    {
        return 'Attendance Recap: ' . $this->program->nama_program;
    }
}

