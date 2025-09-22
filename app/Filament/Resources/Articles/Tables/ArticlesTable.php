<?php

namespace App\Filament\Resources\Articles\Tables;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Table;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;

class ArticlesTable
{
    public static function configure(Table $table): Table
    {
        return $table
             ->columns([
            ImageColumn::make('image')
                ->label('Image')
                ->getStateUsing(fn ($record) => asset('storage/' . $record->image))
                ->width(100)
                ->height(100)
                ->circular(),
            TextColumn::make('title')
                ->label('Judul')
                ->searchable() 
                ->sortable(),

            TextColumn::make('slug')
                ->label('Slug'),

            TextColumn::make('published_at')
                ->label('Waktu Publikasi')
                ->dateTime('d M Y H:i') 
                ->sortable(),
        ])
        ->filters([
        ])
        ->actions([
           EditAction::make(),
           ViewAction::make(),
        ])
        ->bulkActions([
           BulkActionGroup::make([
               DeleteBulkAction::make(),
            ]),
        ]);
    }
}
