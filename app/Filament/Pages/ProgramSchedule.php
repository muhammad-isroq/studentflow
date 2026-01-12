<?php
namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use App\Models\ClassSession;
use App\Models\Program;
use BackedEnum;
use Filament\Support\Icons\Heroicon;
use App\Filament\Pages\FillAttendance;
use Filament\Actions\Action;
use Illuminate\Support\Facades\Auth;
use Filament\Tables\Columns\TextInputColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\RichEditor;
use Filament\Notifications\Notification;
use Illuminate\Support\HtmlString;
use App\Filament\Pages\GradingPage;


class ProgramSchedule extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserGroup;

    public static function getNavigationGroup(): ?string
    {
        return 'My Schedule';
    }

    protected static ?string $slug = 'program-schedule/{program}';

    protected string $view = 'filament.pages.program-schedule';
    
    protected function getHeaderActions(): array
    {
        return [
            Action::make('Rekap Absen')
            ->label('Absence Recap')
            ->icon('heroicon-o-document-chart-bar')
            ->url(fn (): string => AttendanceRecap::getUrl(['program' => $this->program->id])),

            Action::make('unit_test')
            ->label('Unit Test & Grades')
            ->icon('heroicon-o-academic-cap')
            ->color('success')
            ->url(fn () => GradingPage::getUrl(['program_id' => $this->program->id]))
            ->openUrlInNewTab(),
        ];
    }

    
    protected static bool $shouldRegisterNavigation = false;

    public Program $program;

    
    public function mount(Program $program): void
    {
        $this->program = $program;
    }

    
    public function getTitle(): string
    {
        return 'Schedule for the Program: ' . $this->program->nama_program;
    }

    public function table(Table $table): Table
    {
        
        $user = Auth::user();
        $guruId = $user->guru_id; 

        return $table
            
            ->query(
                ClassSession::query()
                    ->where('program_id', $this->program->id)
                    ->when($guruId, function ($query) use ($guruId) {
                        
                        $query->where('guru_id', $guruId);
                    })
            )
            ->columns([
                TextColumn::make('session_date')->label('Meeting Date')->date('l, d M Y')->sortable(),
                TextColumn::make('guru.nama_guru')->label('Teacher'),
                TextColumn::make('program.nama_ruangan')->label('Room Name'),
                
                TextColumn::make('program.lesson_time')->label('Lesson Time')->badge()->color('success')->icon('heroicon-o-clock'),
               
            ])
            ->actions([
                Action::make('fill_attendance')
                    ->label('Fill Attendance')
                    ->icon('heroicon-o-pencil-square')
                    
                    ->url(fn (ClassSession $record): string => FillAttendance::getUrl(['record' => $record]))
                    ->badge(fn (ClassSession $record) => $record->attendances()->exists() ? '✓ Filled' : '! Empty')
                    ->badgeColor(fn (ClassSession $record) => $record->attendances()->exists() ? 'success' : 'warning')
                    ->extraAttributes(['class' => 'mr-7']),
                Action::make('lessonPlan')
    ->label('Lesson Plan')
    ->icon('heroicon-o-book-open') 
    ->color('info')
    ->badge(fn (ClassSession $record) => $record->topic ? '✓ Filled' : '! Empty')
    ->badgeColor(fn (ClassSession $record) => $record->topic ? 'success' : 'warning')
    
    // Header modal yang menarik
    ->modalHeading(fn (ClassSession $record) => new HtmlString(
        '<div class="flex items-center gap-3">
            <div class="flex items-center justify-center w-12 h-12 rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 shadow-lg">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                </svg>
            </div>
            <div>
                <div class="text-xl font-bold text-gray-900 dark:text-white">Edit Lesson Plan</div>
                <div class="text-sm text-gray-500 dark:text-gray-400 font-medium">
                    📅 ' . $record->session_date->format('l, d F Y') . '
                </div>
            </div>
        </div>'
    ))
    
    ->modalDescription('Complete the lesson plan for this session. Ensure all components are filled out completely for proper documentation.')
    ->modalWidth('5xl')
    ->slideOver() 
    
    ->mountUsing(fn (ClassSession $record, \Filament\Schemas\Schema $form) => $form->fill([
        'topic' => $record->topic,
        'activity' => $record->activity,
        'vocabulary_list' => $record->vocabulary_list,
        'class_journal' => $record->class_journal,
    ]))

    ->form([
        // Info Banner
        Placeholder::make('info_banner')
            ->content(new HtmlString('
                <div class="rounded-xl bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-950/50 dark:to-indigo-950/50 p-4 border-2 border-blue-200 dark:border-blue-800">
                    <div class="flex items-start gap-3">
                        <div class="flex-shrink-0 mt-0.5">
                            <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <h4 class="text-sm font-bold text-blue-900 dark:text-blue-100 mb-1">📚 Filling Guide</h4>
                            <p class="text-xs text-blue-700 dark:text-blue-300">
                                Fill in each section with details to create an effective and easy-to-use lesson plan for future reference.
                            </p>
                        </div>
                    </div>
                </div>
            '))
            ->columnSpanFull(),

        // Divider dengan judul
        Placeholder::make('*')
            ->content(new HtmlString('
                <div class="border-l-4 border-blue-500 pl-4 py-2 mb-2">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">📖 Main Learning Content</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Core topics and activities to be implemented</p>
                </div>
            '))
            ->columnSpanFull(),

        // Tips untuk Topic & Activity
        Placeholder::make('tips_main_learning')
            ->label('')
            ->content(new HtmlString('
                <div class="grid grid-cols-2 gap-4">
                    <div class="text-xs text-gray-600 dark:text-gray-400 space-y-1 bg-gray-50 dark:bg-gray-900 p-3 rounded-lg">
                        <p class="font-semibold text-gray-700 dark:text-gray-300">💡 Tips for Topic:</p>
                        <ul class="list-disc list-inside space-y-0.5 ml-2">
                            <li>Use a clear title</li>
                            <li>Specific and measurable</li>
                            <li>According to student level</li>
                        </ul>
                    </div>
                    <div class="text-xs text-gray-600 dark:text-gray-400 space-y-1 bg-gray-50 dark:bg-gray-900 p-3 rounded-lg">
                        <p class="font-semibold text-gray-700 dark:text-gray-300">💡 Tips for Activity:</p>
                        <ul class="list-disc list-inside space-y-0.5 ml-2">
                            <li>Describe the method</li>
                            <li>Duration per activity</li>
                            <li>Media used</li>
                        </ul>
                    </div>
                </div>
            '))
            ->columnSpanFull(),
        
        RichEditor::make('topic')
            ->label('📖 Topic (Unit and Page)')
            ->helperText('Example = Present Continuous Tense : Daily Activities')
            ->placeholder('The main topics that will be discussed in the lesson')
            ->required()
            ->columnSpanFull()
            ->toolbarButtons([
                'bold',
                'italic',
                'underline',
                'bulletList',
                'orderedList',
                'h2',
                'h3',
            ])
            ->maxLength(1000),

        RichEditor::make('activity')
            ->label('🎯 Activity')
            ->helperText('Example = Warming up : Picture description (10 menit), Main activity : Role play in pairs (20 menit), Practice : Fill in the blanks worksheet (15 menit), Closing : Quick quiz (5 menit)')
            ->placeholder('Description of the learning activities to be carried out')
            ->columnSpanFull()
            ->toolbarButtons([
                'bold',
                'italic',
                'underline',
                'bulletList',
                'orderedList',
            ]),

        // Divider untuk section 2
        Placeholder::make('*')
            ->content(new HtmlString('
                <div class="border-l-4 border-emerald-500 pl-4 py-2 mb-2 mt-6">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">📝 Supporting Materials</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Vocabulary and class progress notes</p>
                </div>
            '))
            ->columnSpanFull(),

        // Tips untuk Vocabulary & Journal
        Placeholder::make('tips_supporting_materials')
            ->label('')
            ->content(new HtmlString('
                <div class="grid grid-cols-2 gap-4">
                    <div class="text-xs text-gray-600 dark:text-gray-400 space-y-1 bg-gray-50 dark:bg-gray-900 p-3 rounded-lg">
                        <p class="font-semibold text-gray-700 dark:text-gray-300">💡 Tips Vocabulary:</p>
                        <ul class="list-disc list-inside space-y-0.5 ml-2">
                            <li>Sertakan arti/definisi</li>
                            <li>Example penggunaan</li>
                            <li>Urutkan berdasarkan tema</li>
                        </ul>
                    </div>
                    <div class="text-xs text-gray-600 dark:text-gray-400 space-y-1 bg-gray-50 dark:bg-gray-900 p-3 rounded-lg">
                        <p class="font-semibold text-gray-700 dark:text-gray-300">💡 Tips Class Journal:</p>
                        <ul class="list-disc list-inside space-y-0.5 ml-2">
                            <li>Record student participation</li>
                            <li>Obstacles faced</li>
                            <li>Notes for improvement</li>
                        </ul>
                    </div>
                </div>
            '))
            ->columnSpanFull(),

        RichEditor::make('vocabulary_list') 
            ->label('📝 Vocabulary List')
            ->helperText('Example = Running : Berlari (verb): The children are running in the park | Swimming : Berenang (verb): She is swimming in the pool')
            ->placeholder('List of new vocabulary learned with their meanings')
            ->columnSpanFull()
            ->toolbarButtons([
                'bold',
                'italic',
                'bulletList',
                'orderedList',
            ]),

        RichEditor::make('class_journal')
            ->label('📔 Class Journal')
            ->helperText('Example = Classroom Atmosphere : Students were very enthusiastic and actively participated. Challenges : Some students still had difficulty with pronunciation. Highlights : The role-play activity was very effective.')
            ->placeholder('Observation notes during learning')
            ->columnSpanFull()
            ->toolbarButtons([
                'bold',
                'italic',
                'underline',
                'bulletList',
                'orderedList',
            ]),

        // Summary/Quick Stats
        Placeholder::make('completion_reminder')
            ->content(new HtmlString('
                <div class="rounded-xl bg-gradient-to-r from-emerald-50 to-teal-50 dark:from-emerald-950/50 dark:to-teal-950/50 p-4 border-2 border-emerald-200 dark:border-emerald-800 mt-4">
                    <div class="flex items-center gap-3">
                        <div class="flex-shrink-0">
                            <svg class="w-6 h-6 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <h4 class="text-sm font-bold text-emerald-900 dark:text-emerald-100 mb-1">✅ Checklist Before Saving</h4>
                            <div class="text-xs text-emerald-700 dark:text-emerald-300 grid grid-cols-2 gap-2">
                                <div>✓ The topic is specific</div>
                                <div>✓ Structured activity</div>
                                <div>✓ Vocabulary with meaning</div>
                                <div>✓ Journal containing observations</div>
                            </div>
                        </div>
                    </div>
                </div>
            '))
            ->columnSpanFull(),
    ])
    
    ->modalSubmitActionLabel('💾 Save Lesson Plan')
    ->modalCancelActionLabel('❌ Cancel')
    ->closeModalByClickingAway(false)
    ->successNotificationTitle('✅ Lesson Plan successfully saved')
    
    ->action(function (ClassSession $record, array $data) {
        $record->update($data);
        
        Notification::make()
            ->success()
            ->title('🎉 Saved Lesson Plan!')
            ->body('Lesson plan data for ' . $record->session_date->format('d F Y') . ' has been successfully updated.')
            ->icon('heroicon-o-check-circle')
            ->iconColor('success')
            ->duration(5000)
            ->send();
    }),


                    
            ])
            ->defaultSort('session_date', 'asc');
    }
}