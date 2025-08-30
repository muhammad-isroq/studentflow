<?php

namespace App\Filament\Resources\Programs\Tables;

use App\Filament\Resources\Siswas\SiswaResource;
use App\Models\Program;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Actions\Action;
use Filament\Actions\ViewAction;

class ProgramsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nama_program')
                    ->label('Program name')
                    ->searchable(),
                TextColumn::make('nama_ruangan')
                    ->label('Room name')
                    ->searchable(),
                TextColumn::make('jadwal_program')
                    ->label('Program schedule')
                    ->searchable(),
                TextColumn::make('guru.nama_guru')
                    ->label('Teachers name')
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
                // ViewAction::make(),
                Action::make('view_class')
                    ->label('Lihat Kelas')
                    ->icon('heroicon-o-users')
                    ->color('info')
                    ->url(fn (Program $record): string =>
                        SiswaResource::getUrl('index', ['program_id' => $record->id])
                    ),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
