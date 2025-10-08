<?php

namespace App\Filament\Resources\VideoTestimonis\Pages;

use App\Filament\Resources\VideoTestimonis\VideoTestimoniResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditVideoTestimoni extends EditRecord
{
    protected static string $resource = VideoTestimoniResource::class;
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
