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
                Hidden::make('user_id')
                ->default(auth()->id()),
                Group::make()
                    ->schema([
                        Section::make()
                            ->schema([
                                TextInput::make('task')
                                    ->required()
                                    ->label('Task Title')
                                    ->placeholder('e.g., Fix login bug')
                                    ->columnSpanFull(),

                                RichEditor::make('description')
                                    ->columnSpanFull(),
                            ]),
                    ])->columnSpan(['lg' => 3]),

                Group::make()
                    ->schema([
                        Section::make('Priority & Settings')
                            ->schema([
                                // Pilihan Kategori: Urgent/Important
                                Select::make('category')
                                    ->options([
                                        'urgent' => '🔥 Urgent',
                                        'important' => '⭐ Important',
                                        'not_urgent' => '☕ Not Urgent',
                                        'not_important' => '📝 Not Important',
                                    ])
                                    ->required()
                                    ->default('important')
                                    ->native(false),

                                DatePicker::make('due_date')
                                    ->native(false)
                                    ->default(now()),

                                Toggle::make('is_public')
                                    ->label('Visible to Team')
                                    ->default(true) // Default publik agar masuk board tim
                                    ->onIcon('heroicon-m-users')
                                    ->offIcon('heroicon-m-lock-closed')
                                    ->onColor('success')
                                    ->helperText('If disabled, only you can see this task.'),
                                
                                Toggle::make('is_completed')
                                    ->label('Mark as Completed')
                                    ->onColor('success'),
                            ]),
                    ])->columnSpan(['lg' => 3]),
            ])->columns(3);
    }
}
