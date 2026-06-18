<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;

use Filament\Forms\Components\Select;
use App\Models\Program;
use App\Models\SemesterReport;
use BackedEnum;
use UnitEnum;
use Filament\Support\Icons\Heroicon;
use Filament\Actions\Action;
use Filament\Notifications\Notification;

class ViewGlobalRekap extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationLabel = 'Arsip Nilai';
    protected static ?string $title = 'Arsip Rekap Nilai';
    protected static ?string $slug = 'arsip-nilai-global';
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedArchiveBox;
    
    protected static string | \UnitEnum | null $navigationGroup = 'Academic';


    protected string $view = 'filament.pages.view-global-rekap';

    protected function getHeaderActions(): array
    {
        return [
            Action::make('delete_archive')
                ->label('Hapus Arsip Ini')
                ->icon('heroicon-o-trash')
                ->color('danger')
                // Tombol HANYA muncul jika semester dan program sudah dipilih
                ->visible(fn () => filled($this->program_id) && filled($this->semester_name))
                ->requiresConfirmation()
                ->modalHeading('Hapus Arsip Nilai?')
                ->modalDescription('Apakah Anda yakin ingin menghapus permanen seluruh data nilai untuk program ini di semester tersebut? Anda bisa merekap ulangnya nanti di halaman Programs.')
                ->action(function () {
                    // Eksekusi penghapusan dari database
                    SemesterReport::where('program_id', $this->program_id)
                        ->where('semester_name', $this->semester_name)
                        ->delete();

                    // Notifikasi sukses
                    Notification::make()
                        ->title('Arsip berhasil dihapus!')
                        ->success()
                        ->send();

                    // Reset form program agar tabel menghilang dan kembali ke tampilan awal
                    $this->program_id = null;
                    $this->form->fill(['semester_name' => $this->semester_name, 'program_id' => null]);
                }),
        ];
    }

    public static function canAccess(): bool
    {

        return auth()->user()->hasRole(['admin', 'super_staff', 'staff']);
    }   

    // Variabel untuk menyimpan pilihan filter
    public ?int $program_id = null;
    public ?string $semester_name = null;

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
                        return SemesterReport::select('semester_name')
                            ->distinct()
                            ->pluck('semester_name', 'semester_name');
                    })
                    ->searchable()
                    ->live() 
                    // PERBAIKAN: Hapus kata 'callable'
                    ->afterStateUpdated(fn ($set) => $set('program_id', null)) 
                    ->required(),
                    
                Select::make('program_id')
                    ->label('2. Pilih Program Kelas')
                    // PERBAIKAN: Hapus kata 'Get'
                    ->options(function ($get) { 
                        $selectedSemester = $get('semester_name');
                        
                        if (!$selectedSemester) {
                            return [];
                        }

                        $activeProgramIds = SemesterReport::where('semester_name', $selectedSemester)
                            ->pluck('program_id')
                            ->unique(); 

                        return Program::whereIn('id', $activeProgramIds)
                            ->pluck('nama_program', 'id');
                    })
                    // PERBAIKAN: Hapus kata 'Get'
                    ->disabled(fn ($get) => blank($get('semester_name'))) 
                    ->searchable()
                    ->live()
                    ->required(),
            ])->columns(2);
    }
    // Fungsi untuk mengambil data ketika kedua filter sudah dipilih
    public function getReportDataProperty()
    {
        if (!$this->program_id || !$this->semester_name) {
            return null;
        }

        $program = Program::find($this->program_id);
        $reports = SemesterReport::with('siswa')
                    ->where('program_id', $this->program_id)
                    ->where('semester_name', $this->semester_name)
                    ->get();

        return [
            'program' => $program,
            'reports' => $reports,
        ];
    }
}