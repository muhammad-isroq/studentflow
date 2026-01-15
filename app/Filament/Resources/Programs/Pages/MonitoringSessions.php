<?php

namespace App\Filament\Resources\Programs\Pages;

use Filament\Actions\Action;
use App\Filament\Resources\Programs\ProgramResource;
use App\Models\ClassSession;
use Filament\Resources\Pages\Page;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Pages\ViewAttendance;
use App\Filament\Pages\ViewLessonPlan;
use Filament\Tables\Grouping\Group;

class MonitoringSessions extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string $resource = ProgramResource::class;

    protected string $view = 'filament.resources.programs.pages.monitoring-sessions';

    protected static ?string $title = 'Monitoring Absensi';
    protected static ?string $navigationLabel = 'Monitoring';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                ClassSession::query()
                    ->where('session_date', '<=', now())
                    ->where(function (Builder $query) {
                        $query->whereDoesntHave('attendances')
                              ->orWhereNull('topic')
                              ->orWhere('topic', '');
                    })
                    ->orderBy('session_date', 'desc')
            )
            ->columns([
                TextColumn::make('session_date')
                    ->label('Tanggal')
                    ->date('l, d F Y') // Format di baris tabel
                    ->sortable()
                    ->description(fn (ClassSession $record) => $record->session_date->diffForHumans()),

                TextColumn::make('program.nama_program')
                    ->label('Program Class')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('guru.nama_guru')
                    ->label('Guru')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('status_absen')
                    ->label('Status Absensi')
                    ->badge()
                    ->state(fn (ClassSession $record) => $record->attendances()->exists() ? 'Sudah Diisi' : 'BELUM DIISI')
                    ->color(fn (string $state) => $state === 'Sudah Diisi' ? 'success' : 'danger')
                    ->icon(fn (string $state) => $state === 'Sudah Diisi' ? 'heroicon-o-check-circle' : 'heroicon-o-x-circle'),

                TextColumn::make('status_lesson_plan')
                    ->label('Status Lesson Plan')
                    ->badge()
                    ->state(fn (ClassSession $record) => !empty($record->topic) ? 'Sudah Diisi' : 'BELUM DIISI')
                    ->color(fn (string $state) => $state === 'Sudah Diisi' ? 'success' : 'danger')
                    ->icon(fn (string $state) => $state === 'Sudah Diisi' ? 'heroicon-o-check-circle' : 'heroicon-o-x-circle'),
            ])
            // --- PERBAIKAN DI SINI ---
            ->groups([
                Group::make('session_date')
                    ->label('Tanggal Sesi') // Label header grup
                    ->date('l, d F Y')      // <--- KUNCI: Memformat tanggal & membuang jam
                    ->collapsible(),        // Agar bisa di-collapse (opsional)
            ])
            ->defaultGroup('session_date') // Set default grouping ke definisi di atas
            // -------------------------
            ->filters([
                SelectFilter::make('guru')
                    ->relationship('guru', 'nama_guru')
                    ->label('Filter Guru'),
                
                Filter::make('belum_absen')
                    ->label('Hanya yang Belum Absen')
                    ->query(fn (Builder $query) => $query->whereDoesntHave('attendances')),

                Filter::make('belum_lesson_plan')
                    ->label('Hanya yang Belum Lesson Plan')
                    ->query(fn (Builder $query) => $query->whereNull('topic')->orWhere('topic', '')),
            ])
            ->actions([
                Action::make('cek_absen')
                    ->icon('heroicon-m-user-group')
                    ->color('gray')
                    ->url(fn (ClassSession $record) => ViewAttendance::getUrl(['record' => $record]))
                    ->openUrlInNewTab()
                    ->visible(fn (ClassSession $record) => !$record->attendances()->exists()),

                Action::make('cek_lesson_plan')
                    ->icon('heroicon-m-book-open')
                    ->color('gray')
                    ->url(fn (ClassSession $record) => ViewLessonPlan::getUrl(['record' => $record->id]))
                    ->openUrlInNewTab()
                    ->visible(fn (ClassSession $record) => empty($record->topic)),
            ]);
    }
}