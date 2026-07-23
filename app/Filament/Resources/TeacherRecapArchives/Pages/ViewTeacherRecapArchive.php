<?php

namespace App\Filament\Resources\TeacherRecapArchives\Pages;

use App\Filament\Resources\TeacherRecapArchives\TeacherRecapArchiveResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewTeacherRecapArchive extends ViewRecord
{
    protected static string $resource = TeacherRecapArchiveResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
