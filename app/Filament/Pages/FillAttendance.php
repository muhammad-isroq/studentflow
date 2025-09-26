<?php

namespace App\Filament\Pages;

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
        
        // Buat attendance record untuk semua siswa jika belum ada
        $students = $this->record->program->siswas;
        
        foreach ($students as $student) {
            $this->record->attendances()->firstOrCreate(
                ['siswa_id' => $student->id],
                ['status' => 'Hadir'] // Default untuk record baru
            );
        }
        
        // Update existing records yang masih 'Belum Diisi' menjadi 'Hadir'
        $this->record->attendances()
            ->where('status', 'Belum Diisi')
            ->update(['status' => 'Hadir']);
        
        // Load attendances
        $this->attendances = $this->record->attendances()->with('siswa')->get();
    }

    public function getTitle(): string
    {
        return 'Absensi untuk ' . ($this->record->program->nama_program ?? 'Unknown Program');
    }
    
    public function getSubheading(): string
    {
        return 'Tanggal: ' . $this->record->session_date->format('l, d M Y');
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
                    ->label('Nama Siswa')
                    ->searchable()
                    ->sortable(),
                    
                SelectColumn::make('status')
                    ->label('Status Kehadiran')
                    ->options([
                        'Hadir' => 'Hadir',
                        'Absen' => 'Absen',
                        'Izin' => 'Izin',
                        'Sakit' => 'Sakit',
                        'Belum Diisi' => 'Belum Diisi',
                    ])
                    ->selectablePlaceholder(false)
                    ->updateStateUsing(function ($record, $state) {
                        $record->update(['status' => $state]);
                        
                        Notification::make()
                            ->title('Status kehadiran berhasil diperbarui')
                            ->success()
                            ->send();
                            
                        return $state;
                    })
                    ->beforeStateUpdated(function ($record) {
                        // Jika masih 'Belum Diisi', ubah ke 'Hadir' sebagai default
                        if ($record->status === 'Belum Diisi') {
                            $record->update(['status' => 'Hadir']);
                        }
                    }),
                TextInputColumn::make('notes')
                ->label('Notes')
                ->disabled(fn ($record) => $record->status === 'Hadir')
                ->updateStateUsing(function ($record, $state) {
                    $record->update(['notes' => $state]);
                    return $state;
                }),
                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
                    
                TextColumn::make('updated_at')
                    ->label('Diperbarui')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->striped()
            ->paginated(false);
    }
    
    public function saveAll()
    {
        Notification::make()
            ->title('Semua perubahan telah disimpan otomatis!')
            ->success()
            ->send();
    }
    
    public function setAllPresent()
    {
        $this->record->attendances()->update(['status' => 'Hadir']);
        
        Notification::make()
            ->title('Semua siswa berhasil diubah menjadi "Hadir"!')
            ->success()
            ->send();
            
        // Refresh table
        return redirect(static::getUrl(['record' => $this->record]));
    }
    
    public function resetAttendance()
    {
        $this->record->attendances()->update(['status' => 'Belum Diisi']);
        
        Notification::make()
            ->title('Status kehadiran berhasil direset!')
            ->success()
            ->send();
            
        // Refresh table
        return redirect(static::getUrl(['record' => $this->record]));
    }
    
    public function getAttendanceStats()
    {
        $attendances = $this->record->attendances;
        
        return [
            'hadir' => $attendances->where('status', 'Hadir')->count(),
            'absen' => $attendances->where('status', 'Absen')->count(),
            'izin' => $attendances->where('status', 'Izin')->count(),
            'sakit' => $attendances->where('status', 'Sakit')->count(),
            'belum_diisi' => $attendances->where('status', 'Belum Diisi')->count(),
        ];
    }

    public function backToList()
    {
        // Pilih salah satu dari opsi ini:
        
        // Opsi 1: Redirect ke dashboard
        return redirect()->route('filament.admin.pages.dashboard');
        
        // Opsi 2: Redirect ke halaman lain yang ada
        // return redirect()->to('/admin');
        
        // Opsi 3: Jika Anda punya page untuk list class sessions
        // return redirect()->route('filament.admin.pages.class-sessions-list');
    }
}