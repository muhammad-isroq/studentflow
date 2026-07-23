<?php

namespace App\Filament\Resources\TeacherRecapArchives\Pages;

use App\Filament\Resources\TeacherRecapArchives\TeacherRecapArchiveResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListTeacherRecapArchives extends ListRecords
{
    protected static string $resource = TeacherRecapArchiveResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // CreateAction::make(),
        ];
    }
}
