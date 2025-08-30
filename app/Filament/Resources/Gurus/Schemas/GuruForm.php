<?php

namespace App\Filament\Resources\Gurus\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class GuruForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nama_guru')
                    ->label('Name')
                    ->required(),
                TextInput::make('no_hp')
                    ->label('Phone Number')
                    ->required(),
            ]);
    }
}
