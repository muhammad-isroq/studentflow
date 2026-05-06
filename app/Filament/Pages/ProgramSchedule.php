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
use Filament\Infolists\Components\Section as InfoSection;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Grid as InfoGrid;
use Filament\Infolists\Infolist;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Actions\Action as TableAction;


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

    public function getSubheading(): string | HtmlString | null
    {
        // 1. Cek apakah ada sesi yang terkunci
        $hasLockedSessions = ClassSession::where('program_id', $this->program->id)
            ->where('guru_id', Auth::user()->guru_id)
            ->whereDate('session_date', '<=', now()->subDays(7)->startOfDay()) 
            ->where('is_forced_enabled', false)
            ->exists();

        // 2. Siapkan banner merah hanya jika ada sesi terkunci
        $warningBanner = '';
        if ($hasLockedSessions) {
            $warningBanner = '
                <div class="mt-4 flex items-center gap-3 p-3 bg-red-50 dark:bg-red-950/30 border border-red-200 dark:border-red-800 rounded-lg animate-pulse">
                    <div class="flex-shrink-0">
                        <svg class="w-5 h-5 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                    </div>
                    <p class="text-[11px] font-bold text-red-700 dark:text-red-300 leading-tight uppercase italic">
                        ATTENTION: Some sessions are locked (over 7 days). Please contact Ms. Ulfa to re-enable them.
                    </p>
                </div>';
        }

        // 3. Gabungkan: Kotak Biru selalu tampil, Kotak Merah tampil di dalamnya jika ada data
        return new HtmlString('
            <div class="mt-4 p-4 border-l-4 border-blue-500 bg-blue-50 dark:bg-blue-900/20 dark:border-blue-600 rounded-r-lg shadow-sm">
                <div class="flex items-start gap-3">
                    <div class="flex-shrink-0">
                        <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="w-full">
                        <h3 class="font-bold text-blue-900 dark:text-blue-100 text-sm">Important Attendance Info</h3>
                        <p class="text-sm text-blue-700 dark:text-blue-300 mt-1 italic">
                            "If you are unable to attend a meeting, please inform the staff who the substitute teacher will be at that meeting."
                        </p>
                        
                        ' . $warningBanner . '
                    </div>
                </div>
            </div>
        ');
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
                    ->with(['guru', 'program', 'attendances'])
                    ->where('program_id', $this->program->id)
                    ->when($guruId, function ($query) use ($guruId) {
                        
                        $query->where('guru_id', $guruId);
                    })
            )
            ->columns([
                TextColumn::make('session_date')->label('Meeting Date')->date('l, d M Y')->sortable(),
                TextColumn::make('unit')
                    ->label('Unit')
                    ->badge()
                    ->color('info') // Warna biru muda
                    ->placeholder('-'),
                TextColumn::make('guru.nama_guru')->label('Teacher'),
                TextColumn::make('program.nama_ruangan')->label('Room Name'),
                
                TextColumn::make('program.lesson_time')->label('Lesson Time')->badge()->color('success')->icon('heroicon-o-clock'),
               
            ])
            ->actions([
                Action::make('fill_attendance')
                    ->label('Fill Attendance')
                    ->icon('heroicon-o-pencil-square')
                    
                    ->url(fn (ClassSession $record): string => FillAttendance::getUrl(['record' => $record]))
                    // ->disabled(fn (ClassSession $record) => $record->isAccessExpired())
                    ->badge(fn (ClassSession $record) => $record->attendances()->where('status', '!=', 'Belum Diisi')->exists() ? '✓ Filled' : '! Empty')
                    ->badgeColor(fn (ClassSession $record) => $record->attendances()->where('status', '!=', 'Belum Diisi')->exists() ? 'success' : 'warning')
                    ->extraAttributes(['class' => 'mr-7']),
                Action::make('lessonPlan')
                    ->label(fn (ClassSession $record) => $record->isAccessExpired() ? 'View Lesson Plan' : 'Lesson Plan Form')
                    ->icon(fn (ClassSession $record) => $record->isAccessExpired() ? 'heroicon-o-eye' : 'heroicon-o-book-open') 
                    ->color(fn (ClassSession $record) => $record->isAccessExpired() ? 'gray' : 'info')
                    ->extraAttributes(['class' => 'mr-7'])
                    ->badge(fn (ClassSession $record) => $record->topic ? '✓ Filled' : '! Empty')
                    ->badgeColor(fn (ClassSession $record) => $record->topic ? 'success' : 'warning')
                    
                    ->modalHeading(fn (ClassSession $record) => new HtmlString('
                        <div class="flex flex-col gap-4">
                            <div class="flex items-center gap-3">
                                <div class="flex items-center justify-center w-12 h-12 rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 shadow-lg">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                    </svg>
                                </div>
                                <div>
                                    <div class="text-xl font-bold text-gray-900 dark:text-white">' . ($record->isAccessExpired() ? 'View Lesson Plan' : 'Edit Lesson Plan') . '</div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400 font-medium">
                                        📅 ' . $record->session_date->format('l, d F Y') . '
                                    </div>
                                </div>
                            </div>

                            ' . ($record->isAccessExpired() ? '
                            <div class="flex items-center gap-2 p-3 bg-amber-50 border border-amber-200 rounded-lg shadow-sm">
                                <span class="flex-shrink-0 text-amber-500">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                </span>
                                <div class="text-xs text-amber-700 leading-tight">
                                    <p class="font-bold uppercase">Read Only Mode</p>
                                    <p>The deadline has passed (over 7 days) 😱. Please contact Ms. Ulfa to reactivate editing access.</p>
                                </div>
                            </div>' : '') . '
                        </div>
                    '))
                    
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
                            ->maxLength(1000)
                            ->disabled(fn (ClassSession $record) => $record->isAccessExpired()),

                        RichEditor::make('activity')
                            ->label('🎯 Activity')
                            ->helperText('Example = Warming up : Picture description (10 menit), Main activity : Role play in pairs (20 menit), Practice : Fill in the blanks worksheet (15 menit), Closing : Quick quiz (5 menit)')
                            ->placeholder('Description of the learning activities to be carried out')
                            ->required()
                            ->columnSpanFull()
                            ->toolbarButtons([
                                'bold',
                                'italic',
                                'underline',
                                'bulletList',
                                'orderedList',
                            ])
                            ->disabled(fn (ClassSession $record) => $record->isAccessExpired()),

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
                            ->required()
                            ->toolbarButtons([
                                'bold',
                                'italic',
                                'bulletList',
                                'orderedList',
                            ])
                            ->disabled(fn (ClassSession $record) => $record->isAccessExpired()),

                        RichEditor::make('class_journal')
                            ->label('📔 Class Journal')
                            ->helperText('Example = Classroom Atmosphere : Students were very enthusiastic and actively participated. Challenges : Some students still had difficulty with pronunciation. Highlights : The role-play activity was very effective.')
                            ->placeholder('Observation notes during learning')
                            ->columnSpanFull()
                            ->required()
                            ->toolbarButtons([
                                'bold',
                                'italic',
                                'underline',
                                'bulletList',
                                'orderedList',
                            ])
                            ->disabled(fn (ClassSession $record) => $record->isAccessExpired()),

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
                    
                    ->extraModalActions([
                        Action::make('resetData')
                            ->label('Reset Content')
                            ->color('danger')
                            ->icon('heroicon-m-trash')
                            ->tooltip('Delete all contents of this Lesson Plan')
                            ->requiresConfirmation()
                            ->modalHeading('Empty Lesson Plan?')
                            ->modalDescription('Are you sure you want to delete all the text you"ve entered? This action cannot be undone.')
                            ->modalSubmitActionLabel('Yes, Delete All')
                            // Tombol reset hanya muncul jika akses belum expired dan sudah ada isinya
                            ->visible(fn (ClassSession $record) => !$record->isAccessExpired() && !empty($record->topic))
                            ->action(function (ClassSession $record) {
                                $record->update([
                                    'topic' => null,
                                    'activity' => null,
                                    'vocabulary_list' => null,
                                    'class_journal' => null,
                                ]);

                                Notification::make()
                                    ->title('Lesson Plan Successfully Reset')
                                    ->danger()
                                    ->send();
                            }),
                        ])

                    ->modalSubmitAction(fn ($action, ClassSession $record) => 
        $record->isAccessExpired() ? false : $action
    )
                    
                    ->modalSubmitActionLabel('💾 Save Lesson Plan')
                    ->modalCancelActionLabel('❌ Cancel')
                    ->closeModalByClickingAway(false)
                    ->successNotificationTitle('✅ Lesson Plan successfully saved')
                    
                    ->action(function (ClassSession $record, array $data, $livewire, $action) {
                        if (empty(strip_tags($data['topic'])) || 
                            empty(strip_tags($data['activity'])) || 
                            empty(strip_tags($data['vocabulary_list'])) || 
                            empty(strip_tags($data['class_journal']))) {
                            
                            Notification::make()
                                ->title('Form Incomplete')
                                ->body('Please fill all fields before saving.')
                                ->danger()
                                ->send();

                            $action->halt();
                            return;
                        }
                        $livewire->validate();
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
                    Action::make('viewLessonPlan')
                    ->label('View')
                    ->icon('heroicon-o-eye')
                    ->color('gray')
                    ->visible(fn (ClassSession $record) => !empty($record->topic))
                    ->url(fn (ClassSession $record) => ViewLessonPlan::getUrl(['record' => $record->id]))
                    ->openUrlInNewTab(),


                    
            ])
            ->defaultSort('session_date', 'asc');
    }
}