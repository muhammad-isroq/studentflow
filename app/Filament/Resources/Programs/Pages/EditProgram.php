<?php

namespace App\Filament\Resources\Programs\Pages;

use App\Filament\Resources\Programs\ProgramResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditProgram extends EditRecord
{
    protected static string $resource = ProgramResource::class;

    protected function getRedirectUrl(): string
{
    return $this->getResource()::getUrl('index');
}

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
