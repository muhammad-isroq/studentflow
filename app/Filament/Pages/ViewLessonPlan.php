<?php

namespace App\Filament\Pages;

use App\Models\ClassSession;
use Filament\Pages\Page;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Placeholder;
use Filament\Support\Enums\FontWeight;
use BackedEnum;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\HtmlString;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;


class ViewLessonPlan extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentText;
    protected static bool $shouldRegisterNavigation = false;
    protected static ?string $slug = 'lesson-plan/{record}';
    protected string $view = 'filament.pages.view-lesson-plan';

    public ClassSession $record;
    
    
    public ?array $data = [];

    public function mount($record): void
    {
        
        if ($record instanceof ClassSession) {
            $this->record = $record;
        } else {
            $this->record = ClassSession::findOrFail($record);
        }


        $this->form->fill($this->record->toArray());
    }

    public function getTitle(): string
    {
        return 'Lesson Plan: ' . $this->record->session_date->format('d F Y');
    }

    
    public function table(Table $table): Table
    {
        return $table
            ->query(
                
                ClassSession::query()->where('class_session_id', $this->record->id)
            )
            ->columns([
                TextColumn::make('program.nama_program')
                    ->label('Program Class')
                    ->searchable(),
                TextColumn::make('unit')
                    ->label('Unit'),
                TextColumn::make('updated_at')
                    ->label('Diperbarui pada')
                    ->dateTime('d M Y, H:i')
                    ->sortable(),
            ])
            ->paginated(false);
    }

    public static function configure(Schema $schema): Schema
    {
        return $schema
                ->schema([
                Section::make()
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('program.nama_program')
                                    ->label('Program Class')
                                    ->prefixIcon('heroicon-m-academic-cap'),
                                    
                                TextInput::make('unit')
                                    ->label('Unit'),
                            ]),
                    ]),

                Section::make('Main Content')
                    ->description('Core learning materials and activities')
                    ->icon('heroicon-m-book-open')
                    ->schema([
                        RichEditor::make('topic')
                            ->label('Topic')
                            ->columnSpanFull(),
                        
                        RichEditor::make('activity')
                            ->label('Activity')
                            ->columnSpanFull(),
                    ])->collapsible(),


                Section::make('Supporting Materials')
                    ->description('Vocabulary notes and journal')
                    ->icon('heroicon-m-clipboard-document-list')
                    ->schema([
                        RichEditor::make('vocabulary_list')
                            ->label('Vocabulary List')
                            ->columnSpanFull(),

                        RichEditor::make('class_journal')
                            ->label('Class Journal')
                            ->columnSpanFull(),
                    ])->collapsible(),
            ])
            ->statePath('data') 
            ->disabled(); 
    }
}