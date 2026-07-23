<?php

namespace App\Filament\Resources\Gurus\Pages;

use App\Filament\Resources\Gurus\GuruResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions;
use Filament\Forms\Components\Select;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;
use App\Models\Guru;
use App\Models\Program;
use App\Models\TeacherRecapArchive;
use Filament\Notifications\Notification;

class ListGurus extends ListRecords
{
    protected static string $resource = GuruResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            
            // TOMBOL BARU: ARSIPKAN SEMUA GURU
            Actions\Action::make('archive_all_recaps')
                ->label('Arsipkan Rekap Semua Guru')
                ->icon('heroicon-o-archive-box-arrow-down')
                ->color('success')
                ->form([
                    Select::make('month')
                        ->label('Bulan')
                        ->options([
                            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni',
                            7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
                        ])
                        ->default(now()->month)
                        ->required(),
                        
                    Select::make('year')
                        ->label('Tahun')
                        ->options(array_combine(
                            range(now()->year - 2, now()->year + 1),
                            range(now()->year - 2, now()->year + 1)
                        ))
                        ->default(now()->year)
                        ->required(),
                ])
                ->modalHeading('Arsipkan Semua Rekap Guru')
                ->modalDescription('Sistem akan menghitung kelas yang valid (tanggal 1-28, lesson plan terisi, absensi terisi) untuk semua guru dan menyimpannya ke arsip permanen.')
                ->modalSubmitActionLabel('Proses Arsip Massal')
                ->action(function (array $data) {
                    
                    // 1. Tentukan Rentang Waktu (1 - 28)
                    $startDate = Carbon::create($data['year'], $data['month'], 1)->startOfDay();
                    $endDate = Carbon::create($data['year'], $data['month'], 28)->endOfDay();

                    // 2. Ambil Semua Guru
                    $gurus = Guru::all();
                    $archivedCount = 0;

                    // 3. Looping perhitungan untuk setiap guru
                    foreach ($gurus as $guru) {
                        $guruId = $guru->id;

                        // Ambil program dengan menggunakan whereBetween untuk memfilter rentang tanggal
                        $programs = Program::whereHas('classSessions', function (Builder $query) use ($guruId, $startDate, $endDate) {
                            $query->where(function ($q) use ($guruId) {
                                $q->where('guru_id', $guruId)
                                  ->orWhere('replacement_guru_id', $guruId);
                            })
                            ->whereBetween('session_date', [$startDate, $endDate])
                            ->whereNotNull('activity')
                            ->where('activity', '!=', '')
                            ->has('attendances'); 
                        })
                        ->withCount('siswas')
                        ->get();

                        $programsWithTotals = $programs->map(function ($program) use ($guruId, $startDate, $endDate) {
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

                        $grandTotalSessions = $programsWithTotals->sum('total_sessions');
                        $grandTotalTeachingMinutes = $programsWithTotals->sum('total_teaching_minutes');

                        // 4. Simpan ke Arsip JIKA guru tersebut memiliki jadwal mengajar di bulan itu
                        if ($grandTotalSessions > 0) {
                            TeacherRecapArchive::updateOrCreate(
                                [
                                    'guru_id' => $guruId,
                                    'month' => $data['month'],
                                    'year' => $data['year'],
                                ],
                                [
                                    'total_sessions' => $grandTotalSessions,
                                    'total_teaching_minutes' => $grandTotalTeachingMinutes,
                                    'program_details' => $programsWithTotals->toArray(),
                                ]
                            );
                            
                            $archivedCount++;
                        }
                    }

                    // 5. Tampilkan Notifikasi Sukses
                    Notification::make()
                        ->title('Arsip Massal Selesai')
                        ->body("Berhasil mengarsipkan data rekap untuk {$archivedCount} guru.")
                        ->success()
                        ->send();
                }),
        ];
    }
}
