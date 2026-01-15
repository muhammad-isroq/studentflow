<?php

namespace App\Filament\Resources\Programs\Pages;

use App\Filament\Resources\Programs\ProgramResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions\Action;

class ListPrograms extends ListRecords
{
    protected static string $resource = ProgramResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
            Action::make('monitoring')
                ->label('Attendance Monitoring')
                ->icon('heroicon-o-exclamation-triangle')
                ->color('danger')
                ->url(ProgramResource::getUrl('monitoring')),
        ];
    }
}
