<?php

namespace App\Filament\Pages;

use App\Models\Todo;
use Filament\Pages\Page;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Illuminate\Database\Eloquent\Builder;
use BackedEnum;
use UnitEnum;
use Filament\Support\Icons\Heroicon;
use App\Filament\Resources\Todos\Widgets\TopUrgentTodos;
use Illuminate\Support\Str;

class TeamTodos extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserGroup;
    protected static string | \UnitEnum | null $navigationGroup = 'Work Management';
    protected static ?string $navigationLabel = 'Team Todo';
    protected static ?string $title = 'Team Public Tasks';
    protected static ?int $navigationSort = 2;

    protected string $view = 'filament.pages.team-todos';

    public static function canAccess(): bool
    {
        return auth()->user()->hasRole(['admin', 'super_staff', 'staff']);
    }

    protected function getHeaderWidgets(): array
    {
        return [
            TopUrgentTodos::class, 
        ];
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Todo::query()
                    ->where('is_public', true) 
                    ->whereBetween('due_date', [
                            now()->startOfWeek(), 
                            now()->endOfWeek()
                        ])
                    ->orWhere('category', 'urgent')
                    ->orderBy('created_at', 'desc')
            )
            ->columns([
                TextColumn::make('user.name')
                    ->label('Owner')
                    ->icon('heroicon-o-user-circle')
                    ->weight('bold')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('task')
                    ->searchable()
                    ->description(fn (Todo $record) => Str::limit(strip_tags($record->description), 50)),

                TextColumn::make('category')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'urgent' => 'danger',
                        'important' => 'warning',
                        'general' => 'gray',
                        default => 'info',
                    }),

                TextColumn::make('due_date')
                    ->date('d M')
                    ->sortable(),


                IconColumn::make('is_completed')
                    ->label('Done')
                    ->boolean(),
            ])

            ->filters([
                \Filament\Tables\Filters\SelectFilter::make('user')
                    ->relationship('user', 'name')
                    ->label('Filter by User'),
                \Filament\Tables\Filters\SelectFilter::make('category')
                    ->options([
                        'important' => 'Important',
                        'urgent' => 'Urgent',
                        'general' => 'General',
                    ]),
            ]);
    }
}