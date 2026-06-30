<?php

namespace App\Filament\Resources\Programs\Pages;

use App\Filament\Resources\Programs\ProgramResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Forms\Components\TextInput;
use App\Models\Program;
use App\Models\Siswa;
use App\Models\Grade;
use App\Models\SemesterReport;
use Filament\Actions\ActionGroup;
use App\Models\AttendanceRecap;
use App\Models\ClassSession;

class ListPrograms extends ListRecords
{
    protected static string $resource = ProgramResource::class;

    protected function getHeaderActions(): array
    {
        $isDeadlineEnabled = cache()->get('global_deadline_status', true);

        return [
            Action::make('delete_meeting_by_date')
    ->label('Hapus Sesi (Tanggal Merah)')
    ->icon('heroicon-o-trash')
    ->color('danger')
    ->form([
        \Filament\Forms\Components\DatePicker::make('target_date')
            ->label('Pilih Tanggal Sesi yang Ingin Dihapus')
            ->required(),
    ])
    ->modalHeading('Hapus Seluruh Sesi pada Tanggal Ini?')
    ->modalDescription('Tindakan ini akan menghapus semua sesi dan data absensi terkait pada tanggal yang dipilih.')
    ->requiresConfirmation()
    ->action(function (array $data) {
        $targetDate = $data['target_date'];

        // 1. Ambil semua sesi yang sesuai tanggal
        $sessions = \App\Models\ClassSession::whereDate('session_date', $targetDate)->get();

        $deletedCount = 0;
        foreach ($sessions as $session) {
            // 2. Hapus data kehadiran yang berelasi dengan sesi tersebut terlebih dahulu
            $session->attendances()->delete(); 
            
            // 3. Hapus sesi itu sendiri
            $session->delete();
            $deletedCount++;
        }

        \Filament\Notifications\Notification::make()
            ->title('Penghapusan Sesi Berhasil')
            ->body("Sebanyak {$deletedCount} sesi beserta data absensi terkait telah dihapus.")
            ->success()
            ->send();
    }),
            Action::make('toggleGlobalDeadline')
                ->label($isDeadlineEnabled ? 'Deadline: ACTIVE' : 'Deadline: DISABLED')
                ->icon($isDeadlineEnabled ? 'heroicon-m-clock' : 'heroicon-m-no-symbol')
                ->color($isDeadlineEnabled ? 'success' : 'danger')
                ->requiresConfirmation()
                ->modalHeading($isDeadlineEnabled ? 'Disable Deadline Globally?' : 'Enable Deadline Globally?')
                ->modalDescription('This will affect all teachers and programs in StudentFlow.')
                ->action(function () use ($isDeadlineEnabled) {
                    cache()->put('global_deadline_status', !$isDeadlineEnabled);

                    Notification::make()
                        ->title('Deadline Setting Updated')
                        ->body('Global deadline is now ' . (!$isDeadlineEnabled ? 'Enabled' : 'Disabled'))
                        ->success()
                        ->send();
                }),
                
            CreateAction::make(),
            
            Action::make('monitoring')
                ->label('Attendance Monitoring')
                ->icon('heroicon-o-exclamation-triangle')
                ->color('success')
                ->url(ProgramResource::getUrl('monitoring')),

            // ============================================================
            // ACTION GROUP: MENU ARSIP & REKAP
            // ============================================================
            ActionGroup::make([
                
                // 1. Eksekusi Rekap Absensi
                Action::make('generate_attendance_rekap')
                    ->label('Tutup & Rekap Absensi')
                    ->icon('heroicon-o-archive-box-arrow-down')
                    ->color('warning')
                    ->form([
                        TextInput::make('semester_name')
                            ->label('Nama Periode Semester')
                            ->placeholder('Contoh: ganjil 2026')
                            ->required()
                            ->maxLength(255),
                    ])
                    ->requiresConfirmation()
                    ->modalHeading('Kunci & Arsipkan Absensi?')
                    ->modalDescription('Proses ini akan menghitung total kehadiran seluruh siswa dari awal semester hingga hari ini dan menguncinya.')
                    ->action(function (array $data) {
                        $semesterName = $data['semester_name'];
                        $programs = Program::all();

                        foreach($programs as $program) {
                            $students = Siswa::where('program_id', $program->id)->get();

                            foreach($students as $student) {
                                // Template hitungan sementara (silakan ganti dengan query db asli Anda nanti)
                                $totalSesi = 24; 
                                $totalHadir = 24; 
                                
                                $percentage = $totalSesi > 0 ? round(($totalHadir / $totalSesi) * 100) : 0;

                                AttendanceRecap::updateOrCreate(
                                    [
                                        'siswa_id' => $student->id,
                                        'program_id' => $program->id,
                                        'semester_name' => $semesterName,
                                    ],
                                    [
                                        'total_hadir' => $totalHadir,
                                        'total_sesi' => $totalSesi,
                                        'percentage' => $percentage,
                                        'nama_program' => $program->nama_program,
                                        'nama_ruangan' => $program->nama_ruangan,
                                        'jadwal_program' => $program->jadwal_program,
                                        'guru_id' => $program->guru_id,
                                        'jam_pelajaran' => $program->lesson_time,
                                    ]
                                );
                            }
                        }

                        Notification::make()
                            ->title('Arsip Absensi Berhasil!')
                            ->body("Seluruh absensi periode {$semesterName} telah dikunci.")
                            ->success()
                            ->send();
                    }),

                // 2. Eksekusi Rekap Nilai
                Action::make('generate_semester_rekap')
                    ->label('Tutup & Rekap Nilai')
                    ->icon('heroicon-o-calculator')
                    ->color('warning')
                    ->form([
                        TextInput::make('semester_name')
                            ->label('Nama Periode Semester')
                            ->placeholder('Contoh: Semester Genap 2025/2026')
                            ->required()
                            ->maxLength(255),
                    ])
                    ->requiresConfirmation()
                    ->modalHeading('Rekap Nilai Semester Ini?')
                    ->modalDescription('Masukkan nama semester. Proses ini akan mengkalkulasi dan mengunci seluruh nilai akhir siswa di semua program.')
                    ->action(function (array $data) {
                        $semesterName = $data['semester_name'];
                        $programs = \App\Models\Program::with('assessments')->get();
                        
                        foreach($programs as $program) {
                            $students = \App\Models\Siswa::where('program_id', $program->id)
                                ->orWhereHas('grades', function ($q) use ($program) {
                                    $q->whereIn('assessment_id', $program->assessments->pluck('id'));
                                })->get();
                                
                            $assessments = $program->assessments;
                            $semesterTestId = $assessments->first(fn($a) => \Illuminate\Support\Str::contains(strtolower($a->name), 'semester'))?->id;
                            $reviewIds = $assessments->reject(fn($a) => \Illuminate\Support\Str::contains(strtolower($a->name), 'semester'))->pluck('id');
    
                            $tempResults = [];
                            
                            foreach($students as $student) {
                                $allGrades = \App\Models\Grade::where('student_id', $student->id)->get();
                                $reviewGrades = $allGrades->whereIn('assessment_id', $reviewIds);
                                $semesterGrade = $semesterTestId ? $allGrades->where('assessment_id', $semesterTestId)->first() : null;
    
                                $calc = function($col) use ($reviewGrades, $semesterGrade) {
                                    $avgReview = (float)($reviewGrades->avg($col) ?? 0);
                                    $scoreSem = (float)($semesterGrade->$col ?? 0);
                                    return $semesterGrade ? ($avgReview + $scoreSem) / 2 : $avgReview;
                                };
    
                                // Nilai Asli
                                $raw_l = $calc('listening'); $raw_r = $calc('reading'); $raw_w = $calc('writing');
                                $raw_s = $calc('speaking'); $raw_g = $calc('grammar');
                                $raw_total = $raw_l + $raw_r + $raw_w + $raw_s + $raw_g;
                                $raw_final = $raw_total / 5;
    
                                // Nilai Rapor Manual
                                $rap_l = (float)($student->rapor_listening ?? 0);
                                $rap_r = (float)($student->rapor_reading ?? 0);
                                $rap_w = (float)($student->rapor_writing ?? 0);
                                $rap_g = (float)($student->rapor_grammar ?? 0);
                                $rap_s = (float)($student->rapor_speaking ?? 0);
                                $rap_total = $rap_l + $rap_r + $rap_w + $rap_g + $rap_s;
                                $rap_final = $rap_total / 5;
    
                                $tempResults[] = [
                                    'student_id' => $student->id,
                                    'raw_l' => $raw_l, 'raw_r' => $raw_r, 'raw_w' => $raw_w, 'raw_s' => $raw_s, 'raw_g' => $raw_g,
                                    'raw_total' => $raw_total, 'raw_final' => $raw_final,
                                    'rap_l' => $rap_l, 'rap_r' => $rap_r, 'rap_w' => $rap_w, 'rap_s' => $rap_s, 'rap_g' => $rap_g,
                                    'rap_final' => $rap_final // Digunakan untuk sorting
                                ];
                            }
    
                            // Urutkan berdasarkan Nilai Rapor (Tertinggi ke Terendah)
                            usort($tempResults, function($a, $b) {
                                return $b['rap_final'] <=> $a['rap_final'];
                            });
    
                            foreach($tempResults as $index => $res) {
                                \App\Models\SemesterReport::updateOrCreate(
                                    [
                                        'siswa_id' => $res['student_id'],
                                        'program_id' => $program->id,
                                        'semester_name' => $semesterName,
                                    ],
                                    [
                                        // Simpan Nilai Asli
                                        'avg_listening' => $res['raw_l'],
                                        'avg_reading' => $res['raw_r'],
                                        'avg_writing' => $res['raw_w'],
                                        'avg_speaking' => $res['raw_s'],
                                        'avg_grammar' => $res['raw_g'],
                                        'total_score' => $res['raw_total'],
                                        'final_score' => $res['raw_final'],
                                        // Simpan Nilai Rapor
                                        'rapor_listening' => $res['rap_l'],
                                        'rapor_reading' => $res['rap_r'],
                                        'rapor_writing' => $res['rap_w'],
                                        'rapor_speaking' => $res['rap_s'],
                                        'rapor_grammar' => $res['rap_g'],
                                        'rank' => $index + 1,
                                    ]
                                );
                            }
                        }
    
                        \Filament\Notifications\Notification::make()
                            ->title('Rekapitulasi Berhasil!')
                            ->body("Data nilai {$semesterName} berhasil dihitung dan diarsipkan dengan format rapor terbaru.")
                            ->success()
                            ->send();
                    }),

                // 3. Lihat Arsip Absensi
                Action::make('view_global_attendance')
                    ->label('Lihat Arsip Absensi')
                    ->icon('heroicon-o-clipboard-document-check')
                    ->color('info')
                    ->url(fn () => \App\Filament\Pages\AttendanceRecapArchive::getUrl()),

                // 4. Lihat Arsip Nilai
                Action::make('view_global_rekap')
                    ->label('Lihat Arsip Nilai')
                    ->icon('heroicon-o-academic-cap')
                    ->color('info')
                    ->url(fn () => \App\Filament\Pages\ViewGlobalRekap::getUrl()), 
                    
            ])
            ->label('Menu Arsip & Rekap')
            ->icon('heroicon-m-folder-open')
            ->button() 
            ->color('gray'),
        ];
    }
}