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


class GradingPage extends Page implements HasTable
{
    use InteractsWithTable;

    protected static BackedEnum | string | null $navigationIcon = 'heroicon-o-document-text';

    protected string $view = 'filament.pages.grading-page';

    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $slug = 'grading';

    public $program_id;
    

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