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
    
    protected function getHeaderActions(): array
    {
        return [
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
                
                ->modalHeading('Report Scoring Sheet ( [Avg Review + Period Test Score] / 2)')
                ->modalWidth('full')
                ->modalSubmitAction(false)
                ->modalCancelActionLabel('Tutup')
                
                ->infolist([
                    Section::make()
                        ->schema([
                            Grid::make(9)
                                ->extraAttributes(['class' => 'border-b pb-2 mb-2'])
                                ->schema([
                                    TextEntry::make('h_nama')->default('NAME')->hiddenLabel()->weight(FontWeight::Bold),
                                    TextEntry::make('h_l')->default('LS')->hiddenLabel()->weight(FontWeight::Bold)->alignCenter(),
                                    TextEntry::make('h_r')->default('RD')->hiddenLabel()->weight(FontWeight::Bold)->alignCenter(),
                                    TextEntry::make('h_w')->default('WR')->hiddenLabel()->weight(FontWeight::Bold)->alignCenter(),
                                    TextEntry::make('h_s')->default('SP')->hiddenLabel()->weight(FontWeight::Bold)->alignCenter(),
                                    TextEntry::make('h_g')->default('GR')->hiddenLabel()->weight(FontWeight::Bold)->alignCenter(),
                                    TextEntry::make('h_total')->default('TOTAL')->hiddenLabel()->weight(FontWeight::Bold)->alignCenter()->color('primary'),
                                    TextEntry::make('h_f')->default('AV (Final)')->hiddenLabel()->weight(FontWeight::Bold)->alignCenter()->color('success'),
                                    TextEntry::make('h_rank')->default('RANK')->hiddenLabel()->weight(FontWeight::Bold)->alignCenter(),
                                ]),
            
                            // B. ISI DATA SISWA (LOGIKA RUMUS BARU)
                            RepeatableEntry::make('summary_data')
                                ->label('')
                                ->state(function ($livewire) {
                                    $program = \App\Models\Program::find($livewire->program_id);
                                    if (!$program) return [];
            
                                    // 1. PISAHKAN ID ASSESSMENT (REVIEW VS SEMESTER)
                                    // Asumsi: Semester Test mengandung kata "Semester" pada namanya
                                    $assessments = $program->assessments;
                                    
                                    $semesterTest = $assessments->first(function ($a) {
                                        return \Illuminate\Support\Str::contains(strtolower($a->name), 'semester');
                                    });
                                    $semesterTestId = $semesterTest ? $semesterTest->id : null;

                                    // Review adalah semua yang BUKAN semester
                                    $reviewIds = $assessments->reject(function ($a) {
                                        return \Illuminate\Support\Str::contains(strtolower($a->name), 'semester');
                                    })->pluck('id');

                                    $students = $program->siswas; 
                                    if (!$students) return [];
            
                                    // 2. PROSES DATA SISWA
                                    $data = $students->map(function ($student) use ($reviewIds, $semesterTestId) {
                                        // Ambil semua nilai siswa ini sekaligus biar hemat query
                                        $allGrades = Grade::where('student_id', $student->id)->get();

                                        // Filter Nilai Review & Nilai Semester
                                        $reviewGrades = $allGrades->whereIn('assessment_id', $reviewIds);
                                        $semesterGrade = $semesterTestId ? $allGrades->where('assessment_id', $semesterTestId)->first() : null;

                                        // Fungsi Helper untuk Rumus: (AvgReview + SemTest) / 2
                                        $calc = function($col) use ($reviewGrades, $semesterGrade) {
                                            $avgReview = (float)($reviewGrades->avg($col) ?? 0);
                                            $scoreSem  = (float)($semesterGrade->$col ?? 0);
                                            
                                            // Jika tidak ada Semester Test, pembagi tetap 2 (sesuai request) atau 1?
                                            // Di sini saya set tetap dibagi 2 agar konsisten dengan rumus.
                                            // Jika ingin berubah, ubah logika di sini.
                                            return ($avgReview + $scoreSem) / 2;
                                        };

                                        // Hitung Nilai Per Skill dengan Rumus Baru
                                        $final_l = $calc('listening');
                                        $final_r = $calc('reading');
                                        $final_w = $calc('writing');
                                        $final_s = $calc('speaking');
                                        $final_g = $calc('grammar');
                                        
                                        // Total adalah penjumlahan hasil rumus baru tadi
                                        $final_total = $final_l + $final_r + $final_w + $final_s + $final_g;
                                        
                                        // Average (Nilai Rapor) = Total / 5 (Jumlah mapel)
                                        // Atau bisa pakai rumus calc('average') jika kolom average di DB sudah benar
                                        // Tapi lebih aman hitung manual dari total di atas:
                                        $final_score_av = $final_total / 5; 
            
                                        return [
                                            'raw_final' => $final_score_av, // Untuk sorting
                                            'display' => [
                                                'nama' => $student->nama ?? '-',
                                                'avg_l' => number_format($final_l, 1),
                                                'avg_r' => number_format($final_r, 1),
                                                'avg_w' => number_format($final_w, 1),
                                                'avg_s' => number_format($final_s, 1),
                                                'avg_g' => number_format($final_g, 1),
                                                'total' => number_format($final_total, 1),
                                                'final' => number_format($final_score_av, 1),
                                            ]
                                        ];
                                    });

                                    // 3. SORTING RANKING
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
                                            TextEntry::make('final')->hiddenLabel()->alignCenter()->badge()
                                                ->color(fn ($state) => $state >= 80 ? 'success' : ($state >= 60 ? 'warning' : 'danger')),
                                            TextEntry::make('rank')->hiddenLabel()->alignCenter()->weight(FontWeight::Black),
                                        ]),
                                ]),
            
                            // C. FOOTER (RATA-RATA KELAS DENGAN RUMUS BARU)
                            Grid::make(9)
                                ->extraAttributes(['class' => 'border-t-2 border-gray-200 pt-4 mt-2 bg-gray-50 rounded-lg'])
                                ->schema([
                                    TextEntry::make('footer_label')->default('CLASS AVG')->hiddenLabel()->weight(FontWeight::Black)->color('primary'),
                                    
                                    // Gunakan logic kalkulasi class level
                                    TextEntry::make('c_l')->hiddenLabel()->alignCenter()->weight(FontWeight::Bold)
                                        ->state(fn ($livewire) => $this->calcClassAvg($livewire, 'listening')),
                                    TextEntry::make('c_r')->hiddenLabel()->alignCenter()->weight(FontWeight::Bold)
                                        ->state(fn ($livewire) => $this->calcClassAvg($livewire, 'reading')),
                                    TextEntry::make('c_w')->hiddenLabel()->alignCenter()->weight(FontWeight::Bold)
                                        ->state(fn ($livewire) => $this->calcClassAvg($livewire, 'writing')),
                                    TextEntry::make('c_s')->hiddenLabel()->alignCenter()->weight(FontWeight::Bold)
                                        ->state(fn ($livewire) => $this->calcClassAvg($livewire, 'speaking')),
                                    TextEntry::make('c_g')->hiddenLabel()->alignCenter()->weight(FontWeight::Bold)
                                        ->state(fn ($livewire) => $this->calcClassAvg($livewire, 'grammar')),
                                        
                                    // Rata-rata Total Kelas (Sum of Averages di atas)
                                    TextEntry::make('c_total')->hiddenLabel()->alignCenter()->weight(FontWeight::Black)->color('primary')
                                        ->state(function ($livewire) {
                                            $l = (float)$this->calcClassAvg($livewire, 'listening');
                                            $r = (float)$this->calcClassAvg($livewire, 'reading');
                                            $w = (float)$this->calcClassAvg($livewire, 'writing');
                                            $s = (float)$this->calcClassAvg($livewire, 'speaking');
                                            $g = (float)$this->calcClassAvg($livewire, 'grammar');
                                            return number_format($l + $r + $w + $s + $g, 1);
                                        }),
                                        
                                    // Rata-rata Final Kelas
                                    TextEntry::make('c_f')->hiddenLabel()->alignCenter()->weight(FontWeight::Black)->color('success')
                                        ->state(function ($livewire) {
                                            $total = (float)$this->calcClassAvg($livewire, 'listening') 
                                                   + (float)$this->calcClassAvg($livewire, 'reading')
                                                   + (float)$this->calcClassAvg($livewire, 'writing')
                                                   + (float)$this->calcClassAvg($livewire, 'speaking')
                                                   + (float)$this->calcClassAvg($livewire, 'grammar');
                                            return number_format($total / 5, 1);
                                        }),
                                        
                                    TextEntry::make('c_rank')->default('')->hiddenLabel(),
                                ])
                        ])
                ])
        ];
    }


    
    public function calcClassAvg($livewire, $column)
    {
        $program = \App\Models\Program::find($livewire->program_id);
        if (!$program) return 0;
        
        // Pisahkan Review & Semester
        $assessments = $program->assessments;
        $semesterTest = $assessments->first(fn($a) => \Illuminate\Support\Str::contains(strtolower($a->name), 'semester'));
        $semesterTestId = $semesterTest ? $semesterTest->id : null;
        $reviewIds = $assessments->reject(fn($a) => \Illuminate\Support\Str::contains(strtolower($a->name), 'semester'))->pluck('id');
        

        $avgReviews = Grade::whereIn('student_id', $program->siswas->pluck('id'))
                        ->whereIn('assessment_id', $reviewIds)
                        ->avg($column) ?? 0;
                        

        $avgSemester = 0;
        if ($semesterTestId) {
            $avgSemester = Grade::whereIn('student_id', $program->siswas->pluck('id'))
                            ->where('assessment_id', $semesterTestId)
                            ->avg($column) ?? 0;
        }
        
        return number_format(($avgReviews + $avgSemester) / 2, 1);
    }

    public $activeAssessmentId = null; 

    public function mount()
    {
        $this->program_id = request()->query('program_id');

        if (!$this->program_id) {
            return redirect()->back();
        }

        $firstAssessment = Assessment::where('program_id', $this->program_id)
            ->orderBy('order', 'asc')
            ->first();

        if ($firstAssessment) {
            $this->activeAssessmentId = $firstAssessment->id;
        }
    }

    public function getTitle(): string
    {
        $programName = Program::find($this->program_id)->nama_program ?? '-';
        if ($this->activeAssessmentId === 'summary') {
            return "Summary & Average: " . $programName;
        }
        return "Input Value: " . $programName;
    }

    public function table(Table $table): Table
    {
        return $table 
            ->query(
                Siswa::query()->where('program_id', $this->program_id)
            )
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
            'listening' => 'Listening',
            'reading' => 'Reading',
            'writing' => 'Writing',
            'grammar' => 'Grammar',
            'speaking' => 'Speaking',
        ];

        $columns = [];

        foreach ($skills as $field => $label) {
            
            
            $columns[] = $this->makeInputColumn($field, substr($label, 0, 2))
                ->visible(fn() => $this->activeAssessmentId !== 'summary'); 


            $columns[] = Tables\Columns\TextColumn::make('avg_' . $field)
                ->label('Avg ' . $label)
                ->alignment(Alignment::Center)
                ->state(function (Siswa $record) use ($field) {
                    return $record->grades()
                        ->whereHas('assessment', function($q) {
                            $q->where('program_id', $this->program_id);
                        })
                        ->avg($field);
                })
                ->formatStateUsing(fn ($state) => number_format((float)$state, 1))
                ->color('info')
                ->weight('bold')
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


        $columns[] = Tables\Columns\TextColumn::make('final_score')
            ->label('FINAL SCORE')
            ->alignment(Alignment::Center)
            ->state(function (Siswa $record) {
                return $record->grades()
                        ->whereHas('assessment', function($q) {
                            $q->where('program_id', $this->program_id);
                        })
                        ->avg('average');
            })
            ->badge()
            ->color(fn ($state) => $state < 70 ? 'danger' : 'success')
            ->formatStateUsing(fn ($state) => number_format((float)$state, 1))
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
                    [
                        'student_id' => $record->id, 
                        'assessment_id' => $this->activeAssessmentId
                    ],
                    [
                        $field => $state
                    ]
                );
            });
    }

    protected function getGradeValue($siswa, $field)
    {
        if (!$this->activeAssessmentId || $this->activeAssessmentId === 'summary') return null;
        
        $grade = Grade::where('student_id', $siswa->id)
            ->where('assessment_id', $this->activeAssessmentId)
            ->first();

        return $grade ? $grade->$field : null;
    }

    
}