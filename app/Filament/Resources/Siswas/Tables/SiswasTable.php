<?php

namespace App\Filament\Resources\Siswas\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Columns\ImageColumn;
use Filament\Actions\ViewAction;

class SiswasTable
{

    protected static ?string $defaultSort = 'created_at, desc';

    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nama')
                    ->label('Name')
                    ->searchable(),
                ImageColumn::make('foto')
                    ->label('Image')
                    ->imageWidth(100)
                    ->imageHeight(100)
                    ->circular(),
                TextColumn::make('program.nama_program')
                    ->label('Program')
                    ->searchable(),
                TextColumn::make('kelas_disekolah')
                    ->label('Grade')
                    ->searchable(),
                TextColumn::make('no_wali')
                    ->label('Parents number')
                    ->searchable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
                ViewAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
