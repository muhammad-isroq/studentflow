<?php

namespace App\Filament\Resources\TeacherRecapArchives\Pages;

use App\Filament\Resources\TeacherRecapArchives\TeacherRecapArchiveResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditTeacherRecapArchive extends EditRecord
{
    protected static string $resource = TeacherRecapArchiveResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
