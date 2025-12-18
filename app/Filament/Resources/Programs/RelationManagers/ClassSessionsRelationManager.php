<?php

namespace App\Filament\Resources\Programs\RelationManagers;

use App\Models\ClassSession;
use App\Models\Guru;
use Filament\Actions\AssociateAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DissociateAction;
use Filament\Actions\DissociateBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use App\Filament\Pages\ViewAttendance;
use Filament\Actions\Action;
use App\Filament\Pages\AttendanceRecap;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Placeholder;
use Illuminate\Support\HtmlString;

class ClassSessionsRelationManager extends RelationManager
{
    protected static string $relationship = 'classSessions';
    protected static ?string $title = 'Class Session';

    public function form(Schema $schema): Schema
    {
        $components = [
            DatePicker::make('session_date')
                ->label('Session Date')
                ->required(),
            Select::make('guru_id')
                ->relationship('guru', 'nama_guru')
                ->label('Replacement Teacher')
                ->required(),
        ];

        // Tambahkan field replacement untuk SEMUA user (bisa disesuaikan nanti)
        $components[] = Select::make('replacement_guru_id')
            ->relationship('replacementGuru', 'nama_guru')
            ->label('Replacement User')
            ->placeholder('Pilih user')
            ->searchable();

        $components[] = TextInput::make('topic')
            ->label('Topic')
            ->maxLength(255);

        return $schema->components($components);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('session_date')
            ->columns([
                TextColumn::make('session_date')
                    ->label('Session Date')
                    ->date('d M Y')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('guru.nama_guru')
                    ->label('Teacher')
                    ->badge()
                    ->color(fn ($record) => $record->replacement_guru_id ? 'gray' : 'success'),
                // TextColumn::make('replacementGuru.nama_guru')
                //     ->label('Replacement')
                //     ->badge()
                //     ->color('warning')
                //     ->placeholder('-'),
                // TextColumn::make('topic')
                //     ->placeholder('-'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make(),
                Action::make('Rekap Absen')
                    ->color('success')
                    ->icon('heroicon-o-document-chart-bar')
                    // Arahkan ke halaman rekap dengan membawa ID Program saat ini
                    ->url(fn (): string => AttendanceRecap::getUrl(['program' => $this->getOwnerRecord()->id])),
            ])
            ->actions([
                EditAction::make()
                ->modalHeading(function ($record) {
                    if (empty($record?->session_date)) {
                        return 'Edit Sesi';
                    }
                    $date = $record->session_date->format('d-m-Y');
                    return 'Edit Session Date ' . $date;
                }),
                DeleteAction::make(),
                Action::make('view_attendance')
                    ->label('Lihat Absensi')
                    ->icon('heroicon-o-eye')
                    ->color('gray')
                    ->url(fn (ClassSession $record): string => ViewAttendance::getUrl(['record' => $record]))
                    ->badge(fn (ClassSession $record) => $record->attendances()->exists() ? '✓ Filled' : '! Empty')
                    ->badgeColor(fn (ClassSession $record) => $record->attendances()->exists() ? 'success' : 'warning')
                    ->extraAttributes(['class' => 'mr-7']),
                Action::make('viewLessonPlan')
    ->label('View Lesson Plan')
    ->icon('heroicon-o-eye')
    ->color('info')
    ->badge(fn (ClassSession $record) => $record->topic ? 'Filled' : 'Empty')
    ->badgeColor(fn (ClassSession $record) => $record->topic ? 'success' : 'gray')
    ->visible(fn () => auth()->user()->hasRole(['admin', 'staff', 'super_staff'])) 
    
    // Header modal yang menarik
    ->modalHeading(fn (ClassSession $record) => new HtmlString(
        '<div class="flex items-center gap-3">
            <div class="flex items-center justify-center w-12 h-12 rounded-xl bg-gradient-to-br from-purple-500 to-pink-600 shadow-lg">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                </svg>
            </div>
            <div>
                <div class="text-xl font-bold text-gray-900 dark:text-white">Detail Lesson Plan</div>
                <div class="text-sm text-gray-500 dark:text-gray-400 font-medium">
                    📅 ' . $record->session_date->format('l, d F Y') . '
                </div>
            </div>
        </div>'
    ))
    
    ->modalDescription('Menampilkan rencana pembelajaran yang telah dibuat untuk sesi ini.')
    ->modalWidth('5xl')
    ->slideOver()
    
    ->mountUsing(fn (ClassSession $record, $form) => $form->fill([
        'topic' => $record->topic,
        'activity' => $record->activity,
        'vocabulary_list' => $record->vocabulary_list,
        'class_journal' => $record->class_journal,
    ]))
    
    ->form([
        // Info Banner untuk Read-Only Mode
        Placeholder::make('readonly_info')
            ->content(new HtmlString('
                <div class="rounded-xl bg-gradient-to-r from-purple-50 to-pink-50 dark:from-purple-950/50 dark:to-pink-950/50 p-4 border-2 border-purple-200 dark:border-purple-800">
                    <div class="flex items-start gap-3">
                        <div class="flex-shrink-0 mt-0.5">
                            <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"></path>
                                <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <h4 class="text-sm font-bold text-purple-900 dark:text-purple-100 mb-1">👁️ Mode Lihat Saja</h4>
                            <p class="text-xs text-purple-700 dark:text-purple-300">
                                Anda sedang melihat lesson plan yang telah dibuat. Untuk mengedit, gunakan tombol "Lesson Plan" (Edit).
                            </p>
                        </div>
                    </div>
                </div>
            '))
            ->columnSpanFull(),

        // Section 1: Core Content
        Placeholder::make('section1_title')
            ->content(new HtmlString('
                <div class="border-l-4 border-purple-500 pl-4 py-2 mb-2 mt-2">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">📖 Konten Pembelajaran</h3>
                </div>
            '))
            ->columnSpanFull(),

        RichEditor::make('topic')
            ->label('📖 Topic')
            ->helperText('Topik utama yang dibahas dalam pembelajaran')
            ->disabled()
            ->columnSpanFull()
            ->toolbarButtons([]), // Hilangkan toolbar untuk read-only

        RichEditor::make('activity')
            ->label('🎯 Activity')
            ->helperText('Aktivitas pembelajaran yang dilakukan')
            ->disabled()
            ->columnSpanFull()
            ->toolbarButtons([]),

        // Section 2: Supporting Materials
        Placeholder::make('section2_title')
            ->content(new HtmlString('
                <div class="border-l-4 border-emerald-500 pl-4 py-2 mb-2 mt-6">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">📝 Materi Pendukung</h3>
                </div>
            '))
            ->columnSpanFull(),

        RichEditor::make('vocabulary_list') 
            ->label('📝 Vocabulary List')
            ->helperText('Daftar kosakata yang dipelajari')
            ->disabled()
            ->columnSpanFull()
            ->toolbarButtons([]),

        RichEditor::make('class_journal')
            ->label('📔 Class Journal')
            ->helperText('Catatan dan observasi selama pembelajaran')
            ->disabled()
            ->columnSpanFull()
            ->toolbarButtons([]),

        // Summary Info
        Placeholder::make('summary_info')
            ->content(new HtmlString('
                <div class="rounded-xl bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-950/50 dark:to-indigo-950/50 p-4 border-2 border-blue-200 dark:border-blue-800 mt-4">
                    <div class="flex items-start gap-3">
                        <div class="flex-shrink-0">
                            <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <h4 class="text-sm font-bold text-blue-900 dark:text-blue-100 mb-1">💡 Informasi</h4>
                            <p class="text-xs text-blue-700 dark:text-blue-300">
                                Lesson plan ini dapat disimpan sebagai referensi untuk evaluasi pembelajaran.
                            </p>
                        </div>
                    </div>
                </div>
            '))
            ->columnSpanFull(),
    ])
    
    // Hilangkan tombol "Save/Submit" karena ini hanya untuk melihat
    ->modalSubmitAction(false) 
    ->modalCancelActionLabel('✖️ Tutup'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('session_date', 'asc');
    }
}