<?php

namespace App\Filament\Pages;

use App\Models\Siswa;
use Filament\Pages\Page;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\SelectColumn;
use App\Models\ClassSession;
use App\Models\Attendance;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextInputColumn;

class FillAttendance extends Page implements HasForms, HasTable
{
    use InteractsWithForms, InteractsWithTable;

    protected static ?string $slug = 'class-sessions/{record}/attendance';
    protected string $view = 'filament.pages.fill-attendance';
    protected static bool $shouldRegisterNavigation = false;

    public ClassSession $record;
    public $attendances = [];

    public function mount(ClassSession $record): void
    {
        $this->record = $record;
        
        $students = $this->record->program->siswas;
        
        foreach ($students as $student) {
            $this->record->attendances()->firstOrCreate(
                ['siswa_id' => $student->id],
                // DB: Simpan 'Hadir' (Indonesia)
                ['status' => 'Hadir'] 
            );
        }
        
        // Update default jika masih 'Belum Diisi' (Indonesia)
        $this->record->attendances()
            ->where('status', 'Belum Diisi')
            ->update(['status' => 'Hadir']);
        
        $this->attendances = $this->record->attendances()->with('siswa')->get();
    }

    public function getTitle(): string
    {
        return 'Absensi untuk ' . ($this->record->program->nama_program ?? 'Unknown Program');
    }
    
    public function getSubheading(): string
    {
        return 'Session: ' . $this->record->session_date->format('l, d M Y');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Attendance::query()
                    ->where('class_session_id', $this->record->id)
                    ->with('siswa')
            )
            ->columns([
                TextColumn::make('siswa.nama')
                    ->label('Student Name')
                    ->searchable()
                    ->sortable(),
                    
                SelectColumn::make('status')
                    ->label('Attendance Status')
                    ->options([
                        // Format: 'VALUE_DI_DATABASE' => 'LABEL_TAMPILAN'
                        // Database (Indonesia) => Tampilan (Inggris)
                        'Hadir'       => 'Present',
                        'Absen'       => 'Alpha',
                        'Izin'        => 'Permission',
                        'Sakit'       => 'Sick',
                        'Belum Diisi' => 'Not Recorded',
                    ])
                    ->selectablePlaceholder(false)
                    ->updateStateUsing(function ($record, $state) {
                        // $state yang masuk di sini adalah KEY (Bahasa Indonesia: 'Hadir', 'Sakit', dll)
                        $record->update(['status' => $state]);
                        
                        Notification::make()
                            ->title('Attendance status updated')
                            ->success()
                            ->send();
                            
                        return $state;
                    }),

                TextInputColumn::make('notes')
                    ->label('Notes')
                    // Cek kondisi pakai Bahasa Indonesia ('Hadir')
                    ->disabled(fn ($record) => $record->status === 'Hadir') 
                    ->updateStateUsing(function ($record, $state) {
                        $record->update(['notes' => $state]);
                        return $state;
                    }),

                TextColumn::make('created_at')
                    ->label('Recorded At')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
                    
                TextColumn::make('updated_at')
                    ->label('Last Updated')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->striped()
            ->paginated(false);
    }
    
    public function setAllPresent()
    {
        // PERBAIKAN UTAMA:
        // Update database menggunakan Bahasa Indonesia ('Hadir')
        // Meskipun tampilannya 'Present', database WAJIB 'Hadir' agar valid
        $this->record->attendances()->update(['status' => 'Hadir']);
        
        Notification::make()
            ->title('All students marked as Present')
            ->success()
            ->send();
            
        return redirect(static::getUrl(['record' => $this->record]));
    }
    
    public function resetAttendance()
    {
        // PERBAIKAN: Gunakan 'Belum Diisi' (Indonesia)
        $this->record->attendances()->update(['status' => 'Belum Diisi']);
        
        Notification::make()
            ->title('Attendance reset successfully')
            ->success()
            ->send();
            
        return redirect(static::getUrl(['record' => $this->record]));
    }
    
    public function getAttendanceStats()
    {
        $attendances = $this->record->attendances;
        
        // Perhitungan Statistik tetap menggunakan Key Database (Indonesia)
        return [
            'hadir' => $attendances->where('status', 'Hadir')->count(),
            'absen' => $attendances->where('status', 'Absen')->count(),
            'izin'  => $attendances->where('status', 'Izin')->count(),
            'sakit' => $attendances->where('status', 'Sakit')->count(),
            'belum_diisi' => $attendances->where('status', 'Belum Diisi')->count(),
        ];
    }
    
    public function backToList()
    {
        return redirect()->route('filament.admin.pages.dashboard');
    }
}