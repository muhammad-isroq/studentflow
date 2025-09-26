<?php

namespace App\Filament\Resources\Programs\RelationManagers;

use App\Models\ClassSession;
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

class ClassSessionsRelationManager extends RelationManager
{
    protected static string $relationship = 'classSessions';
    protected static ?string $title = 'Class Session';

    

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                DatePicker::make('session_date')
                    ->label('Session Date')
                    ->required(),
                Select::make('guru_id')
                    ->relationship('guru', 'nama_guru')
                    ->label('teacher in charge')
                    ->required(),
                TextInput::make('topic')
                    ->maxLength(255),
            ]);
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
                    ->label('Teacher'),
                TextColumn::make('topic'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make(),
                // AssociateAction::make(),
            ])
            ->actions([
                EditAction::make(),
                // DissociateAction::make(),
                Action::make('view_attendance')
                    ->label('Lihat Absensi')
                    ->icon('heroicon-o-eye')
                    ->color('gray')
                    ->url(fn (ClassSession $record): string => ViewAttendance::getUrl(['record' => $record])),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    // DissociateBulkAction::make(),
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('session_date', 'asc');
            
    }
}
