<?php

namespace App\Filament\Pages;

use App\Models\Siswa;
use App\Filament\Pages\ProgramSchedule;
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
                ['status' => 'Belum Diisi'] 
            );
        }
        
        $this->attendances = $this->record->attendances()->with('siswa')->get();
    }

    public function getTitle(): string
    {
        return 'Attendance for ' . ($this->record->program->nama_program ?? 'Unknown Program');
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
                    ->disabled(fn () => $this->record->isAccessExpired())
                    ->options([
                        'Hadir'       => 'Present',
                        'Absen'       => 'Alpha',
                        'Izin'        => 'Permission',
                        'Sakit'       => 'Sick',
                        'Belum Diisi' => 'Not Recorded',
                    ])
                    ->selectablePlaceholder(false)
                    ->updateStateUsing(function ($record, $state) {
                        
                        if ($this->record->isAccessExpired()) return $state;

                        $record->update(['status' => $state]);
                        
                        Notification::make()
                            ->title('Attendance status updated')
                            ->success()
                            ->send();
                            
                        return $state;
                    }),

                TextInputColumn::make('notes')
                    ->label('Notes')
                    ->disabled(fn ($record) => $record->status === 'Hadir' || $this->record->isAccessExpired()) 
                    ->updateStateUsing(function ($record, $state) {
                        if ($this->record->isAccessExpired()) {
                            return $record->notes; 
                        }

                        $record->update(['notes' => $state]);
                        
                        Notification::make()
                            ->title('Notes updated')
                            ->success()
                            ->send();

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
        if ($this->record->isAccessExpired()) return;

        $this->record->attendances()->update(['status' => 'Hadir']);
        $this->dispatch('attendance-updated');
        
        Notification::make()
            ->title('Success!')
            ->success()
            ->send();
    }
    
    public function resetAttendance()
    {
        $this->record->attendances()->update(['status' => 'Belum Diisi', 'notes' => null]);
        
        $this->dispatch('attendance-updated');
        
        Notification::make()
            ->title('Reset Complete!')
            ->body('All attendance has been reset to "Not Recorded"')
            ->success()
            ->send();
    }
    
    public function saveAll()
    {
        Notification::make()
            ->title('Attendance Saved Successfully!')
            ->body('All attendance data has been recorded')
            ->success()
            ->duration(3000)
            ->send();

        return redirect(ProgramSchedule::getUrl(['program' => $this->record->program_id]));
    }

    public function getAttendanceStats()
    {
        $attendances = $this->record->attendances;
        
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