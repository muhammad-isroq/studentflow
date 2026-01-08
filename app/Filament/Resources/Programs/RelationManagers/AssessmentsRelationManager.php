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
        $calcClassAvg = function ($livewire, $column) {
            $program = $livewire->getOwnerRecord();
            if (!$program) return 0;

            // Pisahkan Review vs Semester
            $assessments = $program->assessments;
            $semesterTest = $assessments->first(fn($a) => \Illuminate\Support\Str::contains(strtolower($a->name), 'semester'));
            $semesterTestId = $semesterTest ? $semesterTest->id : null;
            $reviewIds = $assessments->reject(fn($a) => \Illuminate\Support\Str::contains(strtolower($a->name), 'semester'))->pluck('id');

            // Hitung Rata-rata Review (Semua Siswa)
            $avgReviews = \App\Models\Grade::whereIn('student_id', $program->siswas->pluck('id'))
                ->whereIn('assessment_id', $reviewIds)
                ->avg($column) ?? 0;

            // Hitung Rata-rata Semester (Semua Siswa)
            $avgSemester = 0;
            if ($semesterTestId) {
                $avgSemester = \App\Models\Grade::whereIn('student_id', $program->siswas->pluck('id'))
                    ->where('assessment_id', $semesterTestId)
                    ->avg($column) ?? 0;
            }

            // Rumus: (AvgReview + AvgSemester) / 2
            return ($avgReviews + $avgSemester) / 2;
        };
        $programRecord = $this->getOwnerRecord();
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
                Action::make('summary_average')
                    ->label('Report Scoring Sheet')
                    ->icon('heroicon-m-document-chart-bar')
                    ->color('success')
                    
                    // Mount Record
                    ->mountUsing(fn (Action $action, $livewire) => $action->record($livewire->getOwnerRecord()))
                    
                    ->modalHeading('Report Scoring Sheet (Rumus: [Avg Review + Semester] / 2)')
                    ->modalWidth('full')
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Tutup')
                    
                    ->infolist([
                        Section::make()->schema([
                            // A. HEADER (9 Kolom)
                            Grid::make(9)
                                ->extraAttributes(['class' => 'border-b pb-2 mb-2'])
                                ->schema([
                                    TextEntry::make('h_nama')->default('NAMA')->hiddenLabel()->weight(FontWeight::Bold),
                                    TextEntry::make('h_l')->default('LS')->hiddenLabel()->weight(FontWeight::Bold)->alignCenter(),
                                    TextEntry::make('h_r')->default('RD')->hiddenLabel()->weight(FontWeight::Bold)->alignCenter(),
                                    TextEntry::make('h_w')->default('WR')->hiddenLabel()->weight(FontWeight::Bold)->alignCenter(),
                                    TextEntry::make('h_s')->default('SP')->hiddenLabel()->weight(FontWeight::Bold)->alignCenter(),
                                    TextEntry::make('h_g')->default('GR')->hiddenLabel()->weight(FontWeight::Bold)->alignCenter(),
                                    TextEntry::make('h_total')->default('TL (Total)')->hiddenLabel()->weight(FontWeight::Bold)->alignCenter()->color('primary'),
                                    TextEntry::make('h_f')->default('AV (Final)')->hiddenLabel()->weight(FontWeight::Bold)->alignCenter()->color('success'),
                                    TextEntry::make('h_rank')->default('RANK')->hiddenLabel()->weight(FontWeight::Bold)->alignCenter(),
                                ]),

                            // B. ISI DATA SISWA
                            RepeatableEntry::make('summary_data')
                                ->label('')
                                ->state(function ($livewire) {
                                    $program = $livewire->getOwnerRecord();
                                    if (!$program) return [];

                                    $assessments = $program->assessments;
                                    $semesterTest = $assessments->first(fn($a) => \Illuminate\Support\Str::contains(strtolower($a->name), 'semester'));
                                    $semesterTestId = $semesterTest ? $semesterTest->id : null;
                                    $reviewIds = $assessments->reject(fn($a) => \Illuminate\Support\Str::contains(strtolower($a->name), 'semester'))->pluck('id');

                                    $students = $program->siswas;
                                    if (!$students) return [];

                                    $data = $students->map(function ($student) use ($reviewIds, $semesterTestId) {
                                        $allGrades = \App\Models\Grade::where('student_id', $student->id)->get();
                                        $reviewGrades = $allGrades->whereIn('assessment_id', $reviewIds);
                                        $semesterGrade = $semesterTestId ? $allGrades->where('assessment_id', $semesterTestId)->first() : null;

                                        $calc = function($col) use ($reviewGrades, $semesterGrade) {
                                            $avgReview = (float)($reviewGrades->avg($col) ?? 0);
                                            $scoreSem  = (float)($semesterGrade->$col ?? 0);
                                            return ($avgReview + $scoreSem) / 2;
                                        };

                                        $l = $calc('listening'); $r = $calc('reading'); $w = $calc('writing');
                                        $s = $calc('speaking'); $g = $calc('grammar');
                                        
                                        $total = $l + $r + $w + $s + $g;
                                        $final = $total / 5;

                                        return [
                                            'raw_final' => $final,
                                            'display' => [
                                                'nama' => $student->nama ?? '-',
                                                'avg_l' => number_format($l, 1),
                                                'avg_r' => number_format($r, 1),
                                                'avg_w' => number_format($w, 1),
                                                'avg_s' => number_format($s, 1),
                                                'avg_g' => number_format($g, 1),
                                                'total' => number_format($total, 1),
                                                'final' => number_format($final, 1),
                                            ]
                                        ];
                                    });

                                    return $data->sortByDesc('raw_final')->values()->map(function ($item, $index) {
                                        $d = $item['display'];
                                        $d['rank'] = $index + 1;
                                        return $d;
                                    });
                                })
                                ->schema([
                                    Grid::make(9)
                                        ->schema([
                                            TextEntry::make('nama')->hiddenLabel()->weight(FontWeight::Medium),
                                            TextEntry::make('avg_l')->hiddenLabel()->alignCenter(),
                                            TextEntry::make('avg_r')->hiddenLabel()->alignCenter(),
                                            TextEntry::make('avg_w')->hiddenLabel()->alignCenter(),
                                            TextEntry::make('avg_s')->hiddenLabel()->alignCenter(),
                                            TextEntry::make('avg_g')->hiddenLabel()->alignCenter(),
                                            TextEntry::make('total')->hiddenLabel()->alignCenter()->weight(FontWeight::Bold)->color('primary'),
                                            TextEntry::make('final')->hiddenLabel()->alignCenter()->badge()->color(fn ($state) => $state >= 80 ? 'success' : ($state >= 60 ? 'warning' : 'danger')),
                                            TextEntry::make('rank')->hiddenLabel()->alignCenter()->weight(FontWeight::Black),
                                        ]),
                                ]),

                            // C. FOOTER (CLASS AVERAGE) - MENGGUNAKAN VARIABEL $calcClassAvg
                            Grid::make(9)
                                ->extraAttributes(['class' => 'border-t-2 border-gray-200 pt-4 mt-2 bg-gray-50 rounded-lg'])
                                ->schema([
                                    TextEntry::make('footer_label')->default('CLASS AVG')->hiddenLabel()->weight(FontWeight::Black)->color('primary'),
                                    
                                    // Panggil variabel $calcClassAvg menggunakan 'use'
                                    TextEntry::make('c_l')->hiddenLabel()->alignCenter()->weight(FontWeight::Bold)
                                        ->state(fn ($livewire) => number_format($calcClassAvg($livewire, 'listening'), 1)),

                                    TextEntry::make('c_r')->hiddenLabel()->alignCenter()->weight(FontWeight::Bold)
                                        ->state(fn ($livewire) => number_format($calcClassAvg($livewire, 'reading'), 1)),

                                    TextEntry::make('c_w')->hiddenLabel()->alignCenter()->weight(FontWeight::Bold)
                                        ->state(fn ($livewire) => number_format($calcClassAvg($livewire, 'writing'), 1)),

                                    TextEntry::make('c_s')->hiddenLabel()->alignCenter()->weight(FontWeight::Bold)
                                        ->state(fn ($livewire) => number_format($calcClassAvg($livewire, 'speaking'), 1)),

                                    TextEntry::make('c_g')->hiddenLabel()->alignCenter()->weight(FontWeight::Bold)
                                        ->state(fn ($livewire) => number_format($calcClassAvg($livewire, 'grammar'), 1)),

                                    // Total Class Avg
                                    TextEntry::make('c_total')->hiddenLabel()->alignCenter()->weight(FontWeight::Black)->color('primary')
                                        ->state(function ($livewire) use ($calcClassAvg) {
                                            $total = $calcClassAvg($livewire, 'listening') + 
                                                     $calcClassAvg($livewire, 'reading') +
                                                     $calcClassAvg($livewire, 'writing') +
                                                     $calcClassAvg($livewire, 'speaking') +
                                                     $calcClassAvg($livewire, 'grammar');
                                            return number_format($total, 1);
                                        }),

                                    // Final Class Avg
                                    TextEntry::make('c_f')->hiddenLabel()->alignCenter()->weight(FontWeight::Black)->color('success')
                                        ->state(function ($livewire) use ($calcClassAvg) {
                                            $total = $calcClassAvg($livewire, 'listening') + 
                                                     $calcClassAvg($livewire, 'reading') +
                                                     $calcClassAvg($livewire, 'writing') +
                                                     $calcClassAvg($livewire, 'speaking') +
                                                     $calcClassAvg($livewire, 'grammar');
                                            return number_format($total / 5, 1);
                                        }),

                                    TextEntry::make('c_rank')->default('')->hiddenLabel(),
                                ])
                        ])
                    ]),
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