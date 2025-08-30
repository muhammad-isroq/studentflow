<?php

namespace App\Filament\Resources\Programs\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;


class ProgramForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nama_program')
                    ->label('Program name')
                    ->required(),
                TextInput::make('nama_ruangan')
                    ->label('Room name')
                    ->required(),
                TextInput::make('jadwal_program')
                    ->label('Program schedule')
                    ->required(),
                Select::make('guru_id')
                    ->relationship('guru', 'nama_guru')
                    ->label('Teachers name')
                    ->searchable()
                    ->preload()
                    ->required(),
            ]);
    }
}
