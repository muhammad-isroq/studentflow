<?php

namespace App\Filament\Resources\Activities\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;

class ActivitiesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('log_name')->label('Log Name')->sortable(),
                TextColumn::make('description')->label('Action')->wrap(),
                TextColumn::make('causer.name')->label('Done By')->sortable()->searchable(),
                TextColumn::make('subject_type')->label('Model'),
                TextColumn::make('subject_id') ->label('Siswa')
                ->label('Record')
                ->formatStateUsing(function ($record) {
                    if (! class_exists($record->subject_type)) {
                        return $record->subject_id; // fallback kalau model tidak ada
                    }

                    $model = $record->subject_type::find($record->subject_id);

                    if (! $model) {
                        return $record->subject_id; // fallback kalau data sudah terhapus
                    }

                    // Tentukan kolom yang mewakili nama
                    if ($model->getAttribute('nama')) {
                        return $model->nama;
                    } elseif ($model->getAttribute('name')) {
                        return $model->name;
                    } elseif ($model->getAttribute('title')) {
                        return $model->title;
                    }

                    return $record->subject_id; // fallback terakhir
                }),
                TextColumn::make('created_at')->label('Time')->dateTime()->sortable(),
                TextColumn::make('changes')
                    ->label('Changes')
                    ->formatStateUsing(function ($record) {
                        $changes = [];
                        $properties = $record->properties ?? [];

                        if (isset($properties['attributes']) && isset($properties['old'])) {
                            foreach ($properties['attributes'] as $field => $newValue) {
                                $oldValue = $properties['old'][$field] ?? null;

                                if ($oldValue != $newValue) {
                                    $changes[] = "{$field}: {$oldValue} → {$newValue}";
                                }
                            }
                        }

                        return implode("\n", $changes) ?: '-';
                    })
                    ->wrap(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
