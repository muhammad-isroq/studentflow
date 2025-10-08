<?php

namespace App\Filament\Resources\VideoTestimonis\Pages;

use App\Filament\Resources\VideoTestimonis\VideoTestimoniResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListVideoTestimonis extends ListRecords
{
    protected static string $resource = VideoTestimoniResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
