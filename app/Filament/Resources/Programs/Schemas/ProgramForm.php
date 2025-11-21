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
                TextInput::make('lesson_time')
                    ->label('Lesson Time')
                    ->placeholder('Example: 14.00 - 15.30'),
                Select::make('guru_id')
                    ->relationship('guru', 'nama_guru')
                    ->label('Teachers name')
                    ->searchable()
                    ->preload()
                    ->required(),
                    TextInput::make('jam_pelajaran')
                    ->label('Meeting Duration (minutes)')
                    ->numeric()
                    ->required()
                    ->default(90)
                    ->minValue(1)
                    ->helperText('Masukkan durasi dalam satuan menit. Contoh: 90 untuk 1.5 jam.'),
            ]);
    }
}
