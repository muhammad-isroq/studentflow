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
                    ->label('Summary / Average')
                    ->icon('heroicon-m-chart-bar')
                    ->color('success')
                    ->record($programRecord)
                    // ->record(fn ($livewire) => $livewire->getOwnerRecord())
                    ->modalHeading('Rekapitulasi Nilai & Rata-rata Kelas')
                    ->modalWidth('7xl') // Agar popup lebar
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Tutup')
                    ->infolist([
                        Section::make()
                            ->schema([
                                // Bagian Header Tabel (Label Kolom)
                                Grid::make(7)
                ->schema([
                    TextEntry::make('header_nama')
                        ->hiddenLabel() // Sembunyikan label otomatis
                        ->default('NAMA SISWA')
                        ->weight(FontWeight::Bold),
                    
                    TextEntry::make('header_l')
                        ->hiddenLabel()
                        ->default('AVG LISTENING')
                        ->weight(FontWeight::Bold)
                        ->alignCenter(), // Rata tengah agar sejajar dengan angka
                    
                    TextEntry::make('header_r')
                        ->hiddenLabel()
                        ->default('AVG READING')
                        ->weight(FontWeight::Bold)
                        ->alignCenter(),
                    
                    TextEntry::make('header_w')
                        ->hiddenLabel()
                        ->default('AVG WRITING')
                        ->weight(FontWeight::Bold)
                        ->alignCenter(),
                    
                    TextEntry::make('header_s')
                        ->hiddenLabel()
                        ->default('AVG SPEAKING')
                        ->weight(FontWeight::Bold)
                        ->alignCenter(),
                    
                    TextEntry::make('header_g')
                        ->hiddenLabel()
                        ->default('AVG GRAMMAR')
                        ->weight(FontWeight::Bold)
                        ->alignCenter(),
                    
                    TextEntry::make('header_final')
                        ->hiddenLabel()
                        ->default('FINAL SCORE')
                        ->weight(FontWeight::Bold)
                        ->alignCenter()
                        ->color('success'),
                ]),

                                // Bagian Data (Looping Siswa)
                               RepeatableEntry::make('summary_data')
                                        ->label('') 
                                        ->state(function ($livewire) {

                                            $program = $livewire->getOwnerRecord();
                                            

                                            $assessmentIds = $program->assessments->pluck('id');

                                            $students = $program->siswas; 

                                            if (!$students) {
                                                return collect([]);
                                            }


                                            return $students->map(function ($student) use ($assessmentIds) {
                                            $grades = Grade::where('student_id', $student->id)
                                                ->whereIn('assessment_id', $assessmentIds)
                                                ->get();

                                            return [
                                                'nama' => $student->nama, 
                                                'avg_l' => number_format($grades->avg('listening') ?? 0, 1),
                                                'avg_r' => number_format($grades->avg('reading') ?? 0, 1),
                                                'avg_w' => number_format($grades->avg('writing') ?? 0, 1),
                                                'avg_s' => number_format($grades->avg('speaking') ?? 0, 1),
                                                'avg_g' => number_format($grades->avg('grammar') ?? 0, 1),
                                                'final' => number_format($grades->avg('average') ?? 0, 1),
                                            ];
                                            });
                                        })
                                    ->schema([
                                        Grid::make(7)
                                            ->schema([
                                                TextEntry::make('nama')->hiddenLabel(),
                                                TextEntry::make('avg_l')->hiddenLabel()->alignCenter(),
                                                TextEntry::make('avg_r')->hiddenLabel()->alignCenter(),
                                                TextEntry::make('avg_w')->hiddenLabel()->alignCenter(),
                                                TextEntry::make('avg_s')->hiddenLabel()->alignCenter(),
                                                TextEntry::make('avg_g')->hiddenLabel()->alignCenter(),
                                                TextEntry::make('final')->hiddenLabel()->alignCenter()
                                                    ->badge()
                                                    ->color(fn ($state) => $state >= 80 ? 'success' : ($state >= 60 ? 'warning' : 'danger')),
                                            ]),
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