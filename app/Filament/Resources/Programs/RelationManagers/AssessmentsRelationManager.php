<?php

namespace App\Filament\Resources\Programs\RelationManagers;

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
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('order', 'asc');
    }
}
