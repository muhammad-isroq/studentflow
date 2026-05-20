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
use Filament\Tables\Columns\TextInputColumn;
use Filament\Notifications\Notification;
use Filament\Forms\Components\Toggle;

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
            TextInput::make('unit')
                ->label('Unit / Materi')
                ->placeholder('Contoh: Unit 1, Review Unit')
                ->required()
                ->maxLength(255),
            Select::make('guru_id')
                ->relationship('guru', 'nama_guru')
                ->label('Replacement Teacher')
                ->required(),
            Toggle::make('is_ramadhan_session')
                ->label('Sesi Ramadhan?')
                ->helperText('Aktifkan jika sesi ini adalah sesi mutasi Ramadhan yang bisa diakses siswa dari kelas lain.')
                ->default(false)
                ->onColor('warning')
                ->offColor('gray'),
        ];

        
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
            ->modifyQueryUsing(fn ($query) => $query->with(['attendances', 'guru', 'replacementGuru'])->latest())
            ->recordTitleAttribute('session_date')
            ->columns([
                TextColumn::make('session_date')
                    ->label('Session Date')
                    ->date('d M Y')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('is_ramadhan_session')
                ->label('Type')
                ->formatStateUsing(fn ($state) => $state ? 'Ramadhan' : 'Regular')
                ->badge()
                ->color(fn ($state) => $state ? 'warning' : 'gray')
                ->icon(fn ($state) => $state ? 'heroicon-m-moon' : 'heroicon-m-calendar'),
                TextInputColumn::make('unit') 
                ->label('Unit / Materi')
                ->placeholder('Isi Unit...') 
                ->sortable(),
                TextColumn::make('guru.nama_guru')
                    ->label('Teacher')
                    ->badge()
                    ->color(fn ($record) => $record->replacement_guru_id ? 'gray' : 'success'),
                TextColumn::make('is_forced_enabled')
    ->label('Status')
    ->formatStateUsing(fn ($state) => $state ? 'Unlocked' : 'Normal')
    ->badge()
    ->color(fn ($state) => $state ? 'warning' : 'gray')
    ->tooltip('Staff has manually unlocked this session'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make()
                ->icon('heroicon-o-plus'),
                Action::make('Rekap Absen')
                    ->color('success')
                    ->icon('heroicon-o-document-chart-bar')
                    
                    ->url(fn (): string => AttendanceRecap::getUrl(['program' => $this->getOwnerRecord()->id]))
                    ->extraAttributes(['class' => 'mr-7']),
            ])
            ->actions([
                EditAction::make()
                ->modalHeading(function ($record) {
                    if (empty($record?->session_date)) {
                        return 'Edit Sesi';
                    }
                    $date = $record->session_date->format('d-m-Y');
                    return 'Edit Session Date ' . $date;
                })
                ->mutateFormDataUsing(function (array $data): array {
                        if (isset($data['replacement_guru_id']) && $data['replacement_guru_id'] === $data['guru_id']) {
                            $data['replacement_guru_id'] = null;
                        }
                        
                        return $data;
                    }),
                DeleteAction::make(),
                Action::make('view_attendance')
                    ->label('Lihat Absensi')
                    ->icon('heroicon-o-eye')
                    ->color('gray')
                    ->url(fn (ClassSession $record): string => ViewAttendance::getUrl(['record' => $record]))
                    ->badge(fn (ClassSession $record) => $record->attendances->count() > 0 ? '✓ Filled' : '! Empty')
                    ->badgeColor(fn (ClassSession $record) => $record->attendances->count() > 0 ? 'success' : 'warning')
                    ->extraAttributes(['class' => 'mr-7']),
                Action::make('viewLessonPlan')
                    ->label('View Lesson Plan')
                    ->icon('heroicon-o-eye')
                    ->color('info')
                    ->badge(fn (ClassSession $record) => $record->topic ? 'Filled' : 'Empty')
                    ->badgeColor(fn (ClassSession $record) => $record->topic ? 'success' : 'gray')
                    ->visible(fn () => auth()->user()->hasRole(['admin', 'staff', 'super_staff', 'guru'])) 
                    
                    // --- BAGIAN PENTING: Panggil View Custom ---
                    ->modalContent(fn (ClassSession $record) => view('filament.components.lesson-plan-modal', ['record' => $record]))
                    
                    // --- Konfigurasi Modal ---
                    ->modalHeading(fn (ClassSession $record) => new HtmlString(
                        '<div class="flex items-center gap-3">
                            <div class="flex items-center justify-center w-12 h-12 rounded-xl bg-gradient-to-br from-purple-500 to-pink-600 shadow-lg">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                </svg>
                            </div>
                            <div>
                                <div class="text-xl font-bold text-gray-900 dark:text-white">Detail Lesson Plan</div>
                                <div class="text-sm text-gray-500 dark:text-gray-400 font-medium">
                                    📅 ' . ($record->session_date ? $record->session_date->format('l, d F Y') : '-') . '
                                </div>
                            </div>
                        </div>'
                    ))
                    ->modalWidth('5xl')
                    ->slideOver()
                    ->modalSubmitAction(false) // Hilangkan tombol Save
                    ->modalCancelActionLabel('✖️ Tutup'),
                   Action::make('re_enable_access')
                    ->label('Unlock Access')
                    ->icon('heroicon-m-lock-open')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->modalHeading('Unlock Teacher Access')
                    ->modalDescription('This will allow the teacher to fill in the missing data for this session.')
                    
                    ->visible(fn (ClassSession $record) => 
                        $record->isAccessExpired() && 
                        !$record->is_forced_enabled && 
                        (!$record->attendances()->exists() || empty($record->topic))
                    )

                    ->action(function (ClassSession $record) {
                        $record->update([
                            'is_forced_enabled' => true,
                            'manual_open_at' => now(),
                        ]);

                        Notification::make()
                            ->title('Access Unlocked')
                            ->body('Teacher can now complete the missing information.')
                            ->success()
                            ->send();
                    }),
                    Action::make('relock_access')
                    ->label('Relock Access')
                    ->icon('heroicon-m-lock-closed')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Kunci Kembali Akses Guru')
                    ->modalDescription('Ini akan mengembalikan sesi ke status Normal. Jika sesi sudah melewati batas 7 hari, guru tidak akan bisa mengisi data lagi.')
                    
                    // Tombol hanya muncul jika statusnya saat ini sedang 'Unlocked' (is_forced_enabled == true)
                    ->visible(fn (ClassSession $record) => $record->is_forced_enabled)
                    
                    ->action(function (ClassSession $record) {
                        $record->update([
                            'is_forced_enabled' => false,
                            // Opsional: bersihkan manual_open_at jika Anda ingin reset total
                            'manual_open_at' => null, 
                        ]);

                        Notification::make()
                            ->title('Akses Terkunci Kembali')
                            ->body('Sesi telah dikembalikan ke status Normal.')
                            ->danger()
                            ->send();
                    }),
        // Action::make('resetLessonPlan')
        // ->label('Reset Lesson Plan')
        // ->icon('heroicon-m-arrow-path')
        // ->color('danger')
        // ->requiresConfirmation()
        // ->modalHeading('Reset Lesson Plan Data?')
        // ->modalDescription('This action will permanently delete all Lesson Plan content (topic, activity, vocabulary, and journal) for this session.')
        // ->modalSubmitActionLabel('Yes, Reset Everything')
        // ->visible(fn (ClassSession $record) => !empty($record->topic))
        // ->action(function (ClassSession $record) {
        //     $record->update([
        //         'topic' => null,
        //         'activity' => null,
        //         'vocabulary_list' => null,
        //         'class_journal' => null,
        //     ]);

        //     Notification::make()
        //         ->title('Lesson Plan Reset Successfully')
        //         ->danger() 
        //         ->send();
        // }),
                                ])
                                ->toolbarActions([
                                    BulkActionGroup::make([
                                        DeleteBulkAction::make(),
                                    ]),
                                ])
                                ->defaultSort('session_date', 'asc');
    }
}