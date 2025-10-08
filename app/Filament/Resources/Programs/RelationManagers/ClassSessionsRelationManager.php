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
                ->label('Teacher in Charge')
                ->required(),
        ];

        // Tambahkan field replacement untuk SEMUA user (bisa disesuaikan nanti)
        $components[] = Select::make('replacement_guru_id')
            ->relationship('replacementGuru', 'nama_guru')
            ->label('Replacement Teacher (if any)')
            ->placeholder('Pilih guru pengganti jika ada')
            ->searchable()
            ->helperText('Pilih guru pengganti jika guru utama tidak bisa hadir');

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
                TextColumn::make('replacementGuru.nama_guru')
                    ->label('Replacement')
                    ->badge()
                    ->color('warning')
                    ->placeholder('-'),
                TextColumn::make('topic')
                    ->placeholder('-'),
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
                EditAction::make(),
                Action::make('view_attendance')
                    ->label('Lihat Absensi')
                    ->icon('heroicon-o-eye')
                    ->color('gray')
                    ->url(fn (ClassSession $record): string => ViewAttendance::getUrl(['record' => $record])),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('session_date', 'asc');
    }
}