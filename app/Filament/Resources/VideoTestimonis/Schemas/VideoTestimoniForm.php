<?php

namespace App\Filament\Resources\VideoTestimonis\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class VideoTestimoniForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('link_video')
                    ->required(),
                TextInput::make('notes1')
                    ->required(),
                Textarea::make('notes2')
                    ->required()
                    ->columnSpanFull(),
                TextInput::make('nama_ortu')
                    ->required(),
                TextInput::make('nama_anak')
                    ->required(),
            ]);
    }
}
