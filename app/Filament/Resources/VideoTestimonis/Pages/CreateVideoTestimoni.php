<?php

namespace App\Filament\Resources\VideoTestimonis\Pages;

use App\Filament\Resources\VideoTestimonis\VideoTestimoniResource;
use Filament\Resources\Pages\CreateRecord;

class CreateVideoTestimoni extends CreateRecord
{
    protected static string $resource = VideoTestimoniResource::class;
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
