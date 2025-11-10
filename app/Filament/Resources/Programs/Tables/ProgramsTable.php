<?php

namespace App\Filament\Resources\Programs\Tables;

use App\Filament\Resources\Siswas\SiswaResource;
use App\Models\Program;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Actions\Action;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\TextInput;
use App\Models\Siswa;
use App\Models\ClassSession;
use App\Models\Attendance;
use App\Models\AttendanceRecap as RecapModel; // Ganti nama agar tidak bentrok
use Illuminate\Support\Facades\DB;
use Filament\Notifications\Notification; // Untuk memberi notifikasi
use App\Models\Guru; // <-- DIUBAH DARI USER

class ProgramsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nama_program')
                    ->label('Program name')
                    ->searchable(),
                TextColumn::make('nama_ruangan')
                    ->label('Room name')
                    ->searchable(),
                TextColumn::make('jadwal_program')
                    ->label('Program schedule')
                    ->searchable(),
                
                // Ini sudah benar (menggunakan relasi dari Program.php)
                TextColumn::make('guru.nama_guru') 
                    ->label('Teachers name')
                    ->searchable(),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->paginated([10, 25, 50, 75, 100, 'all'])
            ->recordActions([
                EditAction::make(),
                Action::make('archiveAttendance')
                    ->label('Arsipkan Rekap Absen')
                    ->icon('heroicon-o-archive-box')
                    ->color('warning')
                    ->visible(fn () => auth()->user()->hasRole(['admin', 'staff']))
                    ->form([
                        TextInput::make('semester_name')
                            ->label('Nama Semester/Periode')
                            ->placeholder('Contoh: Ganjil 2024/2025')
                            ->required(),
                    ])
                    ->action(function (Program $program, array $data): void {
                        $siswas = Siswa::where('program_id', $program->id)->get();
                        $sessions = ClassSession::where('program_id', $program->id)->get();
                        $totalSessions = $sessions->count();
                        
                        if ($totalSessions == 0) {
                            Notification::make()->title('Gagal Mengarsip')->body('Program ini tidak memiliki sesi untuk diarsip.')->danger()->send();
                            return; 
                        }

                        $attendances = Attendance::whereIn('class_session_id', $sessions->pluck('id'))->get();
                        $attendanceData = [];
                        foreach ($attendances as $attendance) {
                            $attendanceData[$attendance->siswa_id][$attendance->class_session_id] = $attendance->status;
                        }

                        // <-- LOGIKA VALIDASI DIPERBAIKI -->
                        $validGuruId = $program->guru_id;
                        if ($validGuruId && !\App\Models\Guru::find($validGuruId)) { // Cek ke model Guru
                            $validGuruId = null; 
                        }

                        // <-- $validGuruId ditambahkan ke 'use'
                        DB::transaction(function () use ($siswas, $attendanceData, $totalSessions, $program, $data, $validGuruId) {
                            foreach ($siswas as $siswa) {
                                $hadirCount = 0;
                                if (isset($attendanceData[$siswa->id])) {
                                    $hadirCount = count(array_filter($attendanceData[$siswa->id], fn($status) => $status === 'Hadir'));
                                }
                                $score = ($totalSessions > 0) ? ($hadirCount / $totalSessions) * 100 : 0;
                                $roundedScore = round($score);

                                RecapModel::create([
                                    'program_id' => $program->id,
                                    'siswa_id' => $siswa->id,
                                    'semester_name' => $data['semester_name'],
                                    'total_hadir' => $hadirCount,
                                    'total_sesi' => $totalSessions,
                                    'percentage' => $roundedScore,
                                    'nama_program' => $program->nama_program,
                                    'nama_ruangan' => $program->nama_ruangan,
                                    'jadwal_program' => $program->jadwal_program,
                                    'guru_id' => $validGuruId, 
                                    'jam_pelajaran' => $program->jam_pelajaran,
                                ]);
                            }
                        });
                        
                        Notification::make()->title('Rekap Berhasil Diarsip')->body("Rekap absensi untuk '{$program->nama_program}' semester '{$data['semester_name']}' telah disimpan.")->success()->send();
                    })
                    ->modalDescription('Ini akan menghitung dan menyimpan rekap absensi saat ini secara permanen. Setelah ini, Anda aman untuk menghapus sesi semester ini.')
                    ->requiresConfirmation(),
                
                Action::make('viewRecap')
                    ->label('Lihat Rekap')
                    ->icon('heroicon-o-document-chart-bar')
                    ->color('gray')
                    ->url(function (Program $program) {
                        $user = auth()->user();
                        if ($user->hasRole(['admin', 'staff'])) {
                            return url("/studentflow/attendance-recap-archive/{$program->id}");
                        }
                        return url("/studentflow/attendance-recap/{$program->id}");
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}