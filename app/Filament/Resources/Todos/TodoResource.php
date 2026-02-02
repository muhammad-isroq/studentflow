<?php

namespace App\Filament\Resources\Todos;

use App\Filament\Resources\Todos\Pages\CreateTodo;
use App\Filament\Resources\Todos\Pages\EditTodo;
use App\Filament\Resources\Todos\Pages\ListTodos;
use App\Filament\Resources\Todos\Schemas\TodoForm;
use App\Filament\Resources\Todos\Tables\TodosTable;
use App\Models\Todo;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class TodoResource extends Resource
{
    protected static ?string $model = Todo::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserCircle;
    protected static string | \UnitEnum | null $navigationGroup = 'Work Management';
    protected static ?string $navigationLabel = 'My Todo';

    protected static ?string $recordTitleAttribute = 'task';

    public static function canAccess(): bool
    {
        return auth()->user()->hasRole(['admin', 'super_staff', 'staff']);
    }

    public static function form(Schema $schema): Schema
    {
        return TodoForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TodosTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTodos::route('/'),
            'create' => CreateTodo::route('/create'),
            'edit' => EditTodo::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('user_id', auth()->id());
    }
}
