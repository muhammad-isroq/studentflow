<?php

namespace App\Filament\Resources\Programs\RelationManagers;

use App\Models\Grade;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

// Pemanggilan Infolist & Schema disesuaikan dengan Filament v4
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Support\Enums\FontWeight;
use Illuminate\Support\Str;

class AssessmentsRelationManager extends RelationManager
{
    protected static string $relationship = 'Assessments';

    protected static ?string $title = 'Daftar Ujian (Assessments)';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([ 
                TextInput::make('name')
                    ->required()
                    ->label('Nama Ujian')
                    ->placeholder('Contoh: Review Unit 1')
                    ->maxLength(255),
                
                TextInput::make('order')
                    ->numeric()
                    ->default(1)
                    ->label('Urutan Tampil')
                    ->helperText('Angka lebih kecil tampil duluan (1, 2, 3...)'),
            ]);
    }

    public function table(Table $table): Table
    {
        // =========================================================
        // FUNGSI HITUNG RATA-RATA: NILAI ASLI
        // =========================================================
        $calcRawClassAvg = function ($livewire, $column) {
            $program = $livewire->getOwnerRecord();
            if (!$program) return 0;

            $assessments = $program->assessments;
            $semesterTest = $assessments->first(fn($a) => \Illuminate\Support\Str::contains(strtolower($a->name), 'semester'));
            $semesterTestId = $semesterTest ? $semesterTest->id : null;
            $reviewIds = $assessments->reject(fn($a) => \Illuminate\Support\Str::contains(strtolower($a->name), 'semester'))->pluck('id');

            $relevantStudentIds = $program->siswas->pluck('id');

            $avgReviews = \App\Models\Grade::whereIn('student_id', $relevantStudentIds)->whereIn('assessment_id', $reviewIds)->avg($column) ?? 0;
            
            $avgSemester = 0;
            if ($semesterTestId) {
                $avgSemester = \App\Models\Grade::whereIn('student_id', $relevantStudentIds)->where('assessment_id', $semesterTestId)->avg($column) ?? 0;
            }

            return $semesterTestId ? ($avgReviews + $avgSemester) / 2 : $avgReviews;
        };

        // =========================================================
        // FUNGSI HITUNG RATA-RATA: NILAI RAPOR MANUAL
        // =========================================================
        $calcRaporClassAvg = function ($livewire, $column) {
            $program = $livewire->getOwnerRecord();
            if (!$program) return 0;

            $map = [
                'listening' => 'rapor_listening', 'reading' => 'rapor_reading',
                'writing' => 'rapor_writing', 'speaking' => 'rapor_speaking',
                'grammar' => 'rapor_grammar',
            ];
            $dbCol = $map[$column] ?? $column;
            
            return \App\Models\Siswa::where('program_id', $program->id)->avg($dbCol) ?? 0;
        };

        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('order')
                    ->label('No. Urut')
                    ->sortable(),
                    
                TextColumn::make('name')
                    ->label('Nama Ujian')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Tambah Ujian'),
                    
                Action::make('print_all_reviews')
                    ->label('Print All Reviews')
                    ->icon('heroicon-o-printer')
                    ->color('success')
                    ->url(fn () => route('print.all.reviews', ['program' => $this->getOwnerRecord()->id]), shouldOpenInNewTab: true),
                    
                Action::make('summary_average')
                    ->label('Report Scoring Sheet')
                    ->icon('heroicon-m-document-chart-bar')
                    ->color('success')
                    ->mountUsing(fn (Action $action, $livewire) => $action->record($livewire->getOwnerRecord()))
                    ->modalHeading('Report Scoring Sheet')
                    ->modalWidth('full')
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Tutup')
                    ->infolist([

                        // =========================================================
                        // SECTION 1: ORIGINAL SCORES TABLE (RAW DATA)
                        // =========================================================
                        Section::make('TABLE 1: ORIGINAL STUDENT SCORES (Average of Review & Semester Test)')
                            ->schema([
                                Grid::make(9)
                                    ->extraAttributes(['class' => 'border-b pb-2 mb-2'])
                                    ->schema([
                                        TextEntry::make('h_nama_raw')->default('STUDENT NAME')->hiddenLabel()->weight(FontWeight::Bold),
                                        TextEntry::make('h_l_raw')->default('LS')->hiddenLabel()->weight(FontWeight::Bold)->alignCenter(),
                                        TextEntry::make('h_r_raw')->default('RD')->hiddenLabel()->weight(FontWeight::Bold)->alignCenter(),
                                        TextEntry::make('h_w_raw')->default('WR')->hiddenLabel()->weight(FontWeight::Bold)->alignCenter(),
                                        TextEntry::make('h_s_raw')->default('SP')->hiddenLabel()->weight(FontWeight::Bold)->alignCenter(),
                                        TextEntry::make('h_g_raw')->default('GR')->hiddenLabel()->weight(FontWeight::Bold)->alignCenter(),
                                        TextEntry::make('h_total_raw')->default('TOTAL')->hiddenLabel()->weight(FontWeight::Bold)->alignCenter()->color('primary'),
                                        TextEntry::make('h_f_raw')->default('FINAL AV')->hiddenLabel()->weight(FontWeight::Bold)->alignCenter()->color('success'),
                                        TextEntry::make('h_rank_raw')->default('RANK')->hiddenLabel()->weight(FontWeight::Bold)->alignCenter(),
                                    ]),
            
                                RepeatableEntry::make('raw_summary_data')
                                    ->label('')
                                    ->state(function ($livewire) {
                                        $program = $livewire->getOwnerRecord();
                                        if (!$program) return [];
                                        
                                        $students = $program->siswas;
                                        if ($students->isEmpty()) return [];
                                        
                                        $assessments = $program->assessments;
                                        $semesterTest = $assessments->first(fn($a) => \Illuminate\Support\Str::contains(strtolower($a->name), 'semester'));
                                        $semesterTestId = $semesterTest ? $semesterTest->id : null;
                                        $reviewIds = $assessments->reject(fn($a) => \Illuminate\Support\Str::contains(strtolower($a->name), 'semester'))->pluck('id');

                                        $data = $students->map(function ($student) use ($reviewIds, $semesterTestId) {
                                            $allGrades = \App\Models\Grade::where('student_id', $student->id)->get();
                                            $reviewGrades = $allGrades->whereIn('assessment_id', $reviewIds);
                                            $semesterGrade = $semesterTestId ? $allGrades->where('assessment_id', $semesterTestId)->first() : null;

                                            $calc = function($col) use ($reviewGrades, $semesterGrade) {
                                                $avgReview = (float)($reviewGrades->avg($col) ?? 0);
                                                $scoreSem = (float)($semesterGrade->$col ?? 0);
                                                return $semesterGrade ? ($avgReview + $scoreSem) / 2 : $avgReview;
                                            };

                                            $l = $calc('listening'); $r = $calc('reading'); $w = $calc('writing');
                                            $s = $calc('speaking'); $g = $calc('grammar');
                                            $total = $l + $r + $w + $s + $g;
                                            $final = $total / 5;

                                            return [
                                                'raw_final_score' => $final,
                                                'display' => [
                                                    'raw_nama' => $student->nama ?? '-',
                                                    'raw_l' => number_format($l, 1),
                                                    'raw_r' => number_format($r, 1),
                                                    'raw_w' => number_format($w, 1),
                                                    'raw_s' => number_format($s, 1),
                                                    'raw_g' => number_format($g, 1),
                                                    'raw_total' => number_format($total, 1),
                                                    'raw_final' => number_format($final, 1),
                                                ]
                                            ];
                                        });

                                        return $data->sortByDesc('raw_final_score')->values()->map(function ($item, $index) {
                                            $d = $item['display'];
                                            $d['raw_rank'] = $index + 1;
                                            return $d;
                                        });
                                    })
                                    ->schema([
                                        Grid::make(9)->schema([
                                            TextEntry::make('raw_nama')->hiddenLabel()->weight(FontWeight::Medium),
                                            TextEntry::make('raw_l')->hiddenLabel()->alignCenter(),
                                            TextEntry::make('raw_r')->hiddenLabel()->alignCenter(),
                                            TextEntry::make('raw_w')->hiddenLabel()->alignCenter(),
                                            TextEntry::make('raw_s')->hiddenLabel()->alignCenter(),
                                            TextEntry::make('raw_g')->hiddenLabel()->alignCenter(),
                                            TextEntry::make('raw_total')->hiddenLabel()->alignCenter()->weight(FontWeight::Bold)->color('primary'),
                                            TextEntry::make('raw_final')->hiddenLabel()->alignCenter()->badge()
                                                ->color(fn ($state) => $state >= 80 ? 'success' : ($state >= 60 ? 'warning' : 'danger')),
                                            TextEntry::make('raw_rank')->hiddenLabel()->alignCenter()->weight(FontWeight::Black),
                                        ]),
                                    ]),
            
                                Grid::make(9)
                                    ->extraAttributes(['class' => 'border-t-2 border-gray-200 pt-4 mt-2 bg-gray-50 rounded-lg'])
                                    ->schema([
                                        TextEntry::make('f_lbl_raw')->default('CLASS AVG (ORIGINAL)')->hiddenLabel()->weight(FontWeight::Black)->color('primary'),
                                        TextEntry::make('c_raw_l')->hiddenLabel()->alignCenter()->weight(FontWeight::Bold)->state(fn ($livewire) => number_format($calcRawClassAvg($livewire, 'listening'), 1)),
                                        TextEntry::make('c_raw_r')->hiddenLabel()->alignCenter()->weight(FontWeight::Bold)->state(fn ($livewire) => number_format($calcRawClassAvg($livewire, 'reading'), 1)),
                                        TextEntry::make('c_raw_w')->hiddenLabel()->alignCenter()->weight(FontWeight::Bold)->state(fn ($livewire) => number_format($calcRawClassAvg($livewire, 'writing'), 1)),
                                        TextEntry::make('c_raw_s')->hiddenLabel()->alignCenter()->weight(FontWeight::Bold)->state(fn ($livewire) => number_format($calcRawClassAvg($livewire, 'speaking'), 1)),
                                        TextEntry::make('c_raw_g')->hiddenLabel()->alignCenter()->weight(FontWeight::Bold)->state(fn ($livewire) => number_format($calcRawClassAvg($livewire, 'grammar'), 1)),
                                        TextEntry::make('c_raw_total')->hiddenLabel()->alignCenter()->weight(FontWeight::Black)->color('primary')
                                            ->state(function ($livewire) use ($calcRawClassAvg) {
                                                $total = (float)$calcRawClassAvg($livewire, 'listening') + (float)$calcRawClassAvg($livewire, 'reading') + (float)$calcRawClassAvg($livewire, 'writing') + (float)$calcRawClassAvg($livewire, 'speaking') + (float)$calcRawClassAvg($livewire, 'grammar');
                                                return number_format($total, 1);
                                            }),
                                        TextEntry::make('c_raw_f')->hiddenLabel()->alignCenter()->weight(FontWeight::Black)->color('success')
                                            ->state(function ($livewire) use ($calcRawClassAvg) {
                                                $total = (float)$calcRawClassAvg($livewire, 'listening') + (float)$calcRawClassAvg($livewire, 'reading') + (float)$calcRawClassAvg($livewire, 'writing') + (float)$calcRawClassAvg($livewire, 'speaking') + (float)$calcRawClassAvg($livewire, 'grammar');
                                                return number_format($total / 5, 1);
                                            }),
                                        TextEntry::make('c_raw_rank')->default('')->hiddenLabel(),
                                    ])
                            ]),

                        // =========================================================
                        // SECTION 2: REPORT CARD SCORES TABLE (MANUAL INPUT)
                        // =========================================================
                        Section::make('TABLE 2: REPORT CARD SCORES (Manual Teacher Input)')
                            ->schema([
                                Grid::make(9)
                                    ->extraAttributes(['class' => 'border-b pb-2 mb-2'])
                                    ->schema([
                                        TextEntry::make('h_nama_rapor')->default('STUDENT NAME')->hiddenLabel()->weight(FontWeight::Bold),
                                        TextEntry::make('h_l_rapor')->default('LS')->hiddenLabel()->weight(FontWeight::Bold)->alignCenter()->color('warning'),
                                        TextEntry::make('h_r_rapor')->default('RD')->hiddenLabel()->weight(FontWeight::Bold)->alignCenter()->color('warning'),
                                        TextEntry::make('h_w_rapor')->default('WR')->hiddenLabel()->weight(FontWeight::Bold)->alignCenter()->color('warning'),
                                        TextEntry::make('h_s_rapor')->default('SP')->hiddenLabel()->weight(FontWeight::Bold)->alignCenter()->color('warning'),
                                        TextEntry::make('h_g_rapor')->default('GR')->hiddenLabel()->weight(FontWeight::Bold)->alignCenter()->color('warning'),
                                        TextEntry::make('h_total_rapor')->default('TOTAL')->hiddenLabel()->weight(FontWeight::Bold)->alignCenter()->color('warning'),
                                        TextEntry::make('h_f_rapor')->default('FINAL AV')->hiddenLabel()->weight(FontWeight::Bold)->alignCenter()->color('success'),
                                        TextEntry::make('h_rank_rapor')->default('RANK')->hiddenLabel()->weight(FontWeight::Bold)->alignCenter(),
                                    ]),
            
                                RepeatableEntry::make('rapor_summary_data')
                                    ->label('')
                                    ->state(function ($livewire) {
                                        $program = $livewire->getOwnerRecord();
                                        if (!$program) return [];

                                        $students = $program->siswas;
                                        if ($students->isEmpty()) return [];
            
                                        $data = $students->map(function ($student) {
                                            $l = (float)($student->rapor_listening ?? 0);
                                            $r = (float)($student->rapor_reading ?? 0);
                                            $w = (float)($student->rapor_writing ?? 0);
                                            $g = (float)($student->rapor_grammar ?? 0);
                                            $s = (float)($student->rapor_speaking ?? 0);
                                            
                                            $total = $l + $r + $w + $s + $g;
                                            $final = $total / 5;

                                            return [
                                                'rapor_final_score' => $final,
                                                'display' => [
                                                    'rapor_nama' => $student->nama ?? '-',
                                                    'rapor_l' => number_format($l, 1),
                                                    'rapor_r' => number_format($r, 1),
                                                    'rapor_w' => number_format($w, 1),
                                                    'rapor_s' => number_format($s, 1),
                                                    'rapor_g' => number_format($g, 1),
                                                    'rapor_total' => number_format($total, 1),
                                                    'rapor_final' => number_format($final, 1),
                                                ]
                                            ];
                                        });

                                        return $data->sortByDesc('rapor_final_score')->values()->map(function ($item, $index) {
                                            $d = $item['display'];
                                            $d['rapor_rank'] = $index + 1;
                                            return $d;
                                        });
                                    })
                                    ->schema([
                                        Grid::make(9)->schema([
                                            TextEntry::make('rapor_nama')->hiddenLabel()->weight(FontWeight::Medium),
                                            TextEntry::make('rapor_l')->hiddenLabel()->alignCenter()->color('warning'),
                                            TextEntry::make('rapor_r')->hiddenLabel()->alignCenter()->color('warning'),
                                            TextEntry::make('rapor_w')->hiddenLabel()->alignCenter()->color('warning'),
                                            TextEntry::make('rapor_s')->hiddenLabel()->alignCenter()->color('warning'),
                                            TextEntry::make('rapor_g')->hiddenLabel()->alignCenter()->color('warning'),
                                            TextEntry::make('rapor_total')->hiddenLabel()->alignCenter()->weight(FontWeight::Bold)->color('warning'),
                                            TextEntry::make('rapor_final')->hiddenLabel()->alignCenter()->badge()
                                                ->color(fn ($state) => $state >= 80 ? 'success' : ($state >= 60 ? 'warning' : 'danger')),
                                            TextEntry::make('rapor_rank')->hiddenLabel()->alignCenter()->weight(FontWeight::Black),
                                        ]),
                                    ]),
            
                                Grid::make(9)
                                    ->extraAttributes(['class' => 'border-t-2 border-orange-200 pt-4 mt-2 bg-orange-50 rounded-lg'])
                                    ->schema([
                                        TextEntry::make('f_lbl_rapor')->default('CLASS AVG (REPORT CARD)')->hiddenLabel()->weight(FontWeight::Black)->color('warning'),
                                        TextEntry::make('c_rapor_l')->hiddenLabel()->alignCenter()->weight(FontWeight::Bold)->state(fn ($livewire) => number_format($calcRaporClassAvg($livewire, 'listening'), 1)),
                                        TextEntry::make('c_rapor_r')->hiddenLabel()->alignCenter()->weight(FontWeight::Bold)->state(fn ($livewire) => number_format($calcRaporClassAvg($livewire, 'reading'), 1)),
                                        TextEntry::make('c_rapor_w')->hiddenLabel()->alignCenter()->weight(FontWeight::Bold)->state(fn ($livewire) => number_format($calcRaporClassAvg($livewire, 'writing'), 1)),
                                        TextEntry::make('c_rapor_s')->hiddenLabel()->alignCenter()->weight(FontWeight::Bold)->state(fn ($livewire) => number_format($calcRaporClassAvg($livewire, 'speaking'), 1)),
                                        TextEntry::make('c_rapor_g')->hiddenLabel()->alignCenter()->weight(FontWeight::Bold)->state(fn ($livewire) => number_format($calcRaporClassAvg($livewire, 'grammar'), 1)),
                                        TextEntry::make('c_rapor_total')->hiddenLabel()->alignCenter()->weight(FontWeight::Black)->color('warning')
                                            ->state(function ($livewire) use ($calcRaporClassAvg) {
                                                $total = (float)$calcRaporClassAvg($livewire, 'listening') + (float)$calcRaporClassAvg($livewire, 'reading') + (float)$calcRaporClassAvg($livewire, 'writing') + (float)$calcRaporClassAvg($livewire, 'speaking') + (float)$calcRaporClassAvg($livewire, 'grammar');
                                                return number_format($total, 1);
                                            }),
                                        TextEntry::make('c_rapor_f')->hiddenLabel()->alignCenter()->weight(FontWeight::Black)->color('success')
                                            ->state(function ($livewire) use ($calcRaporClassAvg) {
                                                $total = (float)$calcRaporClassAvg($livewire, 'listening') + (float)$calcRaporClassAvg($livewire, 'reading') + (float)$calcRaporClassAvg($livewire, 'writing') + (float)$calcRaporClassAvg($livewire, 'speaking') + (float)$calcRaporClassAvg($livewire, 'grammar');
                                                return number_format($total / 5, 1);
                                            }),
                                        TextEntry::make('c_rapor_rank')->default('')->hiddenLabel(),
                                    ])
                            ])
                    ]),
                    
                Action::make('print_scoring_sheet')
                    ->label('Print Scoring Sheet')
                    ->icon('heroicon-o-printer')
                    ->color('gray')
                    ->url(fn () => route('print.grades', ['program' => $this->getOwnerRecord()->id]), shouldOpenInNewTab: true),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
                Action::make('lihat_nilai')
                    ->label('Lihat Nilai')
                    ->icon('heroicon-m-clipboard-document-list')
                    ->color('info')
                    ->modalHeading(fn ($record) => 'Laporan: ' . $record->name)
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Tutup')
                    ->infolist([
                        Section::make('Ringkasan Kelas')
                            ->schema([
                                Grid::make(3)
                                    ->schema([
                                        TextEntry::make('class_average')
                                            ->label('Rata-rata Kelas')
                                            ->state(fn ($record) => number_format($record->grades->avg('average'), 2))
                                            ->badge()
                                            ->color('info'),
                                        
                                        TextEntry::make('max_score')
                                            ->label('Nilai Tertinggi')
                                            ->state(fn ($record) => $record->grades->max('average')),

                                        TextEntry::make('min_score')
                                            ->label('Nilai Terendah')
                                            ->state(fn ($record) => $record->grades->min('average')),
                                    ]),
                            ])
                            ->compact(),

                        Section::make('Detail Siswa')
                            ->schema([
                                RepeatableEntry::make('grades')
                                    ->label('')
                                    ->schema([
                                        TextEntry::make('student.nama') 
                                            ->label('Nama Siswa')
                                            ->icon('heroicon-m-user'),
                                        TextEntry::make('average') 
                                            ->label('Nilai Akhir')
                                            ->badge()
                                            ->color(fn (string $state): string => match (true) {
                                                (float)$state >= 80 => 'success',
                                                (float)$state >= 60 => 'warning',
                                                default => 'danger',
                                            })
                                            ->helperText(fn ($record) => 
                                                "L: {$record->listening} | R: {$record->reading} | W: {$record->writing} | S: {$record->speaking} | G: {$record->grammar}"
                                            ),
                                    ])
                                    ->grid(2) 
                                    ->columnSpanFull()
                            ])
                    ]),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('order', 'asc');
    }
}