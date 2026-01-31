<?php

namespace App\Filament\Resources\Todos\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Hidden;


class TodoForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('task')
                    ->required()
                    ->maxLength(255),
                RichEditor::make('description')
                    ->columnSpanFull(),
                DatePicker::make('due_date')
                    ->native(false),
                Select::make('category')
                    ->options([
                        'important' => 'Important',
                        'general' => 'General',
                        'urgent' => 'Urgent',
                    ])
                    ->default('important')
                    ->required(),
                Toggle::make('is_public')
                    ->label('Share to Team?')
                    ->default(true),
                Hidden::make('user_id')
                    ->default(auth()->id()),
            ]);
    }
}
