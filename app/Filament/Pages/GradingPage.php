<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Models\Siswa;
use App\Models\Assessment;
use App\Models\Grade;
use App\Models\Program;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Filament\Tables;
use Filament\Support\Enums\Alignment;
use BackedEnum;
use UnitEnum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\TextInputColumn;
use App\Filament\Resources\ProgramResource;
use Filament\Actions\Action;
use Filament\Support\Enums\FontWeight;
use Filament\Schemas\Components\Grid;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Schemas\Components\Section;
use Illuminate\Support\Str;

class GradingPage extends Page implements HasTable
{
    use InteractsWithTable;

    protected static BackedEnum | string | null $navigationIcon = 'heroicon-o-document-text';
    protected string $view = 'filament.pages.grading-page';
    protected static bool $shouldRegisterNavigation = false;
    protected static ?string $slug = 'grading';

    public $program_id;
    public $activeAssessmentId = null; 
    
    protected function getHeaderActions(): array
    {
        return [
            Action::make('toggle_summary')
                ->label(fn () => $this->activeAssessmentId === 'summary' ? 'Back to Daily Input' : 'Fill Report Card (Summary)')
                ->icon('heroicon-o-table-cells')
                ->color(fn () => $this->activeAssessmentId === 'summary' ? 'gray' : 'primary')
                ->action(function () {
                    if ($this->activeAssessmentId === 'summary') {
                        $firstAssessment = \App\Models\Assessment::where('program_id', $this->program_id)
                            ->orderBy('order', 'asc')
                            ->first();
                        $this->activeAssessmentId = $firstAssessment ? $firstAssessment->id : null;
                    } else {
                        $this->activeAssessmentId = 'summary';
                    }
                }),

            Action::make('print_scoring_sheet')
                ->label('Print Scoring Sheet')
                ->icon('heroicon-o-printer')
                ->color('gray')
                ->url(function () {
                    if (!$this->program_id) return '#';
                    return route('print.grades', ['program' => $this->program_id]);
                }, shouldOpenInNewTab: true),
                
            Action::make('summary_average')
                ->label('Report Scoring Sheet')
                ->icon('heroicon-m-chart-bar')
                ->color('success')
                ->mountUsing(function (Action $action) {
                    if ($this->program_id) {
                        $program = \App\Models\Program::find($this->program_id);
                        if ($program) {
                            $action->record($program);
                        }
                    }
                })
                ->modalHeading('Report Scoring Sheet')
                ->modalWidth('full')
                ->modalSubmitAction(false)
                ->modalCancelActionLabel('Close')
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
                                    $students = \App\Models\Siswa::where('program_id', $livewire->program_id)->get();
                                    if ($students->isEmpty()) return [];
                                    
                                    $program = \App\Models\Program::find($livewire->program_id);
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
                                    TextEntry::make('c_raw_l')->hiddenLabel()->alignCenter()->weight(FontWeight::Bold)->state(fn ($livewire) => $this->calcRawClassAvg($livewire, 'listening')),
                                    TextEntry::make('c_raw_r')->hiddenLabel()->alignCenter()->weight(FontWeight::Bold)->state(fn ($livewire) => $this->calcRawClassAvg($livewire, 'reading')),
                                    TextEntry::make('c_raw_w')->hiddenLabel()->alignCenter()->weight(FontWeight::Bold)->state(fn ($livewire) => $this->calcRawClassAvg($livewire, 'writing')),
                                    TextEntry::make('c_raw_s')->hiddenLabel()->alignCenter()->weight(FontWeight::Bold)->state(fn ($livewire) => $this->calcRawClassAvg($livewire, 'speaking')),
                                    TextEntry::make('c_raw_g')->hiddenLabel()->alignCenter()->weight(FontWeight::Bold)->state(fn ($livewire) => $this->calcRawClassAvg($livewire, 'grammar')),
                                    TextEntry::make('c_raw_total')->hiddenLabel()->alignCenter()->weight(FontWeight::Black)->color('primary')
                                        ->state(function ($livewire) {
                                            $total = (float)$this->calcRawClassAvg($livewire, 'listening') + (float)$this->calcRawClassAvg($livewire, 'reading') + (float)$this->calcRawClassAvg($livewire, 'writing') + (float)$this->calcRawClassAvg($livewire, 'speaking') + (float)$this->calcRawClassAvg($livewire, 'grammar');
                                            return number_format($total, 1);
                                        }),
                                    TextEntry::make('c_raw_f')->hiddenLabel()->alignCenter()->weight(FontWeight::Black)->color('success')
                                        ->state(function ($livewire) {
                                            $total = (float)$this->calcRawClassAvg($livewire, 'listening') + (float)$this->calcRawClassAvg($livewire, 'reading') + (float)$this->calcRawClassAvg($livewire, 'writing') + (float)$this->calcRawClassAvg($livewire, 'speaking') + (float)$this->calcRawClassAvg($livewire, 'grammar');
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
                                    $students = \App\Models\Siswa::where('program_id', $livewire->program_id)->get();
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
                                    TextEntry::make('c_rapor_l')->hiddenLabel()->alignCenter()->weight(FontWeight::Bold)->state(fn ($livewire) => $this->calcRaporClassAvg($livewire, 'listening')),
                                    TextEntry::make('c_rapor_r')->hiddenLabel()->alignCenter()->weight(FontWeight::Bold)->state(fn ($livewire) => $this->calcRaporClassAvg($livewire, 'reading')),
                                    TextEntry::make('c_rapor_w')->hiddenLabel()->alignCenter()->weight(FontWeight::Bold)->state(fn ($livewire) => $this->calcRaporClassAvg($livewire, 'writing')),
                                    TextEntry::make('c_rapor_s')->hiddenLabel()->alignCenter()->weight(FontWeight::Bold)->state(fn ($livewire) => $this->calcRaporClassAvg($livewire, 'speaking')),
                                    TextEntry::make('c_rapor_g')->hiddenLabel()->alignCenter()->weight(FontWeight::Bold)->state(fn ($livewire) => $this->calcRaporClassAvg($livewire, 'grammar')),
                                    TextEntry::make('c_rapor_total')->hiddenLabel()->alignCenter()->weight(FontWeight::Black)->color('warning')
                                        ->state(function ($livewire) {
                                            $total = (float)$this->calcRaporClassAvg($livewire, 'listening') + (float)$this->calcRaporClassAvg($livewire, 'reading') + (float)$this->calcRaporClassAvg($livewire, 'writing') + (float)$this->calcRaporClassAvg($livewire, 'speaking') + (float)$this->calcRaporClassAvg($livewire, 'grammar');
                                            return number_format($total, 1);
                                        }),
                                    TextEntry::make('c_rapor_f')->hiddenLabel()->alignCenter()->weight(FontWeight::Black)->color('success')
                                        ->state(function ($livewire) {
                                            $total = (float)$this->calcRaporClassAvg($livewire, 'listening') + (float)$this->calcRaporClassAvg($livewire, 'reading') + (float)$this->calcRaporClassAvg($livewire, 'writing') + (float)$this->calcRaporClassAvg($livewire, 'speaking') + (float)$this->calcRaporClassAvg($livewire, 'grammar');
                                            return number_format($total / 5, 1);
                                        }),
                                    TextEntry::make('c_rapor_rank')->default('')->hiddenLabel(),
                                ])
                        ])
                ])
        ];
    }

    public function calcRawClassAvg($livewire, $column)
    {
        $program = \App\Models\Program::find($livewire->program_id);
        if (!$program) return 0;
        
        $assessments = $program->assessments;
        $semesterTest = $assessments->first(fn($a) => \Illuminate\Support\Str::contains(strtolower($a->name), 'semester'));
        $semesterTestId = $semesterTest ? $semesterTest->id : null;
        $reviewIds = $assessments->reject(fn($a) => \Illuminate\Support\Str::contains(strtolower($a->name), 'semester'))->pluck('id');
        
        $relevantStudentIds = \App\Models\Siswa::query()->where('program_id', $program->id)->pluck('id');

        $avgReviews = \App\Models\Grade::whereIn('student_id', $relevantStudentIds)->whereIn('assessment_id', $reviewIds)->avg($column) ?? 0;
        $avgSemester = 0;
        if ($semesterTestId) {
            $avgSemester = \App\Models\Grade::whereIn('student_id', $relevantStudentIds)->where('assessment_id', $semesterTestId)->avg($column) ?? 0;
        }
        
        return number_format($semesterTestId ? ($avgReviews + $avgSemester) / 2 : $avgReviews, 1);
    }

    public function calcRaporClassAvg($livewire, $column)
    {
        $map = [
            'listening' => 'rapor_listening', 'reading' => 'rapor_reading',
            'writing' => 'rapor_writing', 'speaking' => 'rapor_speaking',
            'grammar' => 'rapor_grammar',
        ];
        $dbCol = $map[$column] ?? $column;
        return number_format(\App\Models\Siswa::where('program_id', $livewire->program_id)->avg($dbCol) ?? 0, 1);
    }

    public function mount()
    {
        $this->program_id = request()->query('program_id');
        if (!$this->program_id) return redirect()->back();

        $firstAssessment = Assessment::where('program_id', $this->program_id)->orderBy('order', 'asc')->first();
        if ($firstAssessment) {
            $this->activeAssessmentId = $firstAssessment->id;
        }
    }

    public function getTitle(): string
    {
        $programName = Program::find($this->program_id)->nama_program ?? '-';
        return $this->activeAssessmentId === 'summary' ? "Summary & Final Input: " . $programName : "Input Value: " . $programName;
    }

    public function table(Table $table): Table
    {
        return $table 
            ->query(Siswa::query()->where('program_id', $this->program_id))
            ->columns(array_merge(
                [
                    Tables\Columns\TextColumn::make('nama')
                        ->label('Student Name')
                        ->sortable()
                        ->searchable()
                        ->weight('bold'),
                ],
                $this->getDynamicColumns()
            ))
            ->paginated(false);
    }

    protected function getDynamicColumns(): array
    {
        $skills = [
            'listening' => 'Listening', 'reading' => 'Reading',
            'writing' => 'Writing', 'grammar' => 'Grammar',
            'speaking' => 'Speaking',
        ];

        $columns = [];

        foreach ($skills as $field => $label) {
            $columns[] = $this->makeInputColumn($field, substr($label, 0, 2))
                ->visible(fn() => $this->activeAssessmentId !== 'summary'); 

            $columns[] = Tables\Columns\TextInputColumn::make('rapor_' . $field)
                ->label(strtoupper(substr($label, 0, 2)))
                ->alignment(Alignment::Center)
                ->type('number')
                ->extraAttributes(['class' => 'min-w-[80px]'])
                ->visible(fn() => $this->activeAssessmentId === 'summary'); 
        }

        $columns[] = Tables\Columns\TextColumn::make('unit_average')
            ->label('AVG Unit')
            ->alignment(Alignment::Center)
            ->state(fn (Siswa $record) => $this->getGradeValue($record, 'average'))
            ->formatStateUsing(fn ($state) => number_format((float)$state, 1))
            ->badge()
            ->color('gray')
            ->visible(fn() => $this->activeAssessmentId !== 'summary');

        $columns[] = Tables\Columns\TextColumn::make('rapor_total')
            ->label('TOTAL')
            ->alignment(Alignment::Center)
            ->state(fn (Siswa $record) => 
                (float)$record->rapor_listening + (float)$record->rapor_reading + 
                (float)$record->rapor_writing + (float)$record->rapor_grammar + (float)$record->rapor_speaking
            )
            ->formatStateUsing(fn ($state) => number_format((float)$state, 1))
            ->weight('bold')
            ->color('primary')
            ->visible(fn() => $this->activeAssessmentId === 'summary');

        $columns[] = Tables\Columns\TextColumn::make('rapor_final')
            ->label('FINAL AV')
            ->alignment(Alignment::Center)
            ->state(fn (Siswa $record) => 
                ((float)$record->rapor_listening + (float)$record->rapor_reading + 
                 (float)$record->rapor_writing + (float)$record->rapor_grammar + (float)$record->rapor_speaking) / 5
            )
            ->formatStateUsing(fn ($state) => number_format((float)$state, 1))
            ->badge()
            ->color(fn ($state) => $state < 70 ? 'danger' : 'success')
            ->visible(fn() => $this->activeAssessmentId === 'summary');

        return $columns;
    }

    protected function makeInputColumn(string $field, string $label): Tables\Columns\TextInputColumn
    {
        return Tables\Columns\TextInputColumn::make('grade_' . $field)
            ->label(strtoupper($label))
            ->type('number')
            ->extraInputAttributes(['class' => 'text-center']) 
            ->extraAttributes(['class' => 'min-w-[80px]'])
            ->state(fn (Siswa $record) => $this->getGradeValue($record, $field))
            ->updateStateUsing(function (Siswa $record, $state) use ($field) {
                if (!$this->activeAssessmentId || $this->activeAssessmentId === 'summary') return;
                Grade::updateOrCreate(
                    ['student_id' => $record->id, 'assessment_id' => $this->activeAssessmentId],
                    [$field => $state]
                );
            });
    }

    protected function getGradeValue($siswa, $field)
    {
        if (!$this->activeAssessmentId || $this->activeAssessmentId === 'summary') return null;
        $grade = Grade::where('student_id', $siswa->id)->where('assessment_id', $this->activeAssessmentId)->first();
        return $grade ? $grade->$field : null;
    }
}