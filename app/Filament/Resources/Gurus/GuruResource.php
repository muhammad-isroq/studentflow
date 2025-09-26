<?php

namespace App\Filament\Resources\Gurus;

use App\Filament\Resources\Gurus\Pages\CreateGuru;
use App\Filament\Resources\Gurus\Pages\EditGuru;
use App\Filament\Resources\Gurus\Pages\ListGurus;
use App\Filament\Resources\Gurus\Schemas\GuruForm;
use App\Filament\Resources\Gurus\Tables\GurusTable;
use App\Models\Guru;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class GuruResource extends Resource
{
    protected static ?string $model = Guru::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserGroup;

    protected static ?string $recordTitleAttribute = 'nama_guru';

    protected static ?string $navigationLabel = 'Teachers';
    protected static ?string $modelLabel = 'Teacher';
    protected static ?string $pluralModelLabel = 'Teachers';
    protected static ?string $slug = 'teachers';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->latest();
    }

        public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'The number of teachers';
    }

    public static function form(Schema $schema): Schema
    {
        return GuruForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return GurusTable::configure($table);
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
            'index' => ListGurus::route('/'),
            'create' => CreateGuru::route('/create'),
            'edit' => EditGuru::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()->hasAnyRole(['admin', 'staff']);
    }
}
