<?php

namespace App\Filament\Resources\TeacherRecapArchives;

use App\Filament\Resources\TeacherRecapArchives\Pages\CreateTeacherRecapArchive;
use App\Filament\Resources\TeacherRecapArchives\Pages\EditTeacherRecapArchive;
use App\Filament\Resources\TeacherRecapArchives\Pages\ListTeacherRecapArchives;
use App\Filament\Resources\TeacherRecapArchives\Pages\ViewTeacherRecapArchive;
use App\Filament\Resources\TeacherRecapArchives\Schemas\TeacherRecapArchiveForm;
use App\Filament\Resources\TeacherRecapArchives\Schemas\TeacherRecapArchiveInfolist;
use App\Filament\Resources\TeacherRecapArchives\Tables\TeacherRecapArchivesTable;
use App\Models\TeacherRecapArchive;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Auth;
use Filament\Tables\Table;

class TeacherRecapArchiveResource extends Resource
{
    protected static ?string $model = TeacherRecapArchive::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedArchiveBox;

    public static function form(Schema $schema): Schema
    {
        return TeacherRecapArchiveForm::configure($schema);
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canViewAny(): bool
    {
        // Hanya Admin dan Super Staff yang boleh lihat menu ini
        return Auth::user()->hasAnyRole(['admin', 'super_staff']);
    }

    public static function infolist(Schema $schema): Schema
    {
        return TeacherRecapArchiveInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TeacherRecapArchivesTable::configure($table);
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
            'index' => ListTeacherRecapArchives::route('/'),
            'create' => CreateTeacherRecapArchive::route('/create'),
            'view' => ViewTeacherRecapArchive::route('/{record}'),
            'edit' => EditTeacherRecapArchive::route('/{record}/edit'),
        ];
    }
}
