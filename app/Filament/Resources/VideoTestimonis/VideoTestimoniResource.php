<?php

namespace App\Filament\Resources\VideoTestimonis;

use App\Filament\Resources\VideoTestimonis\Pages\CreateVideoTestimoni;
use App\Filament\Resources\VideoTestimonis\Pages\EditVideoTestimoni;
use App\Filament\Resources\VideoTestimonis\Pages\ListVideoTestimonis;
use App\Filament\Resources\VideoTestimonis\Schemas\VideoTestimoniForm;
use App\Filament\Resources\VideoTestimonis\Tables\VideoTestimonisTable;
use App\Models\VideoTestimoni;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class VideoTestimoniResource extends Resource
{
    protected static ?string $model = VideoTestimoni::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'VideoTestimoni';

     public static function canViewAny(): bool
    {
        // Hanya user dengan role 'admin' atau 'editor' yang bisa melihat menu ini
        return auth()->user()->hasAnyRole(['admin', 'editor']);
    }

    public static function form(Schema $schema): Schema
    {
        return VideoTestimoniForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return VideoTestimonisTable::configure($table);
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
            'index' => ListVideoTestimonis::route('/'),
            'create' => CreateVideoTestimoni::route('/create'),
            'edit' => EditVideoTestimoni::route('/{record}/edit'),
        ];
    }
}
