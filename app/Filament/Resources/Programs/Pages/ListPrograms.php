<?php

namespace App\Filament\Resources\Programs\Pages;

use App\Filament\Resources\Programs\ProgramResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions\Action;
use Filament\Notifications\Notification;

class ListPrograms extends ListRecords
{
    protected static string $resource = ProgramResource::class;

    protected function getHeaderActions(): array
    {
        $isDeadlineEnabled = cache()->get('global_deadline_status', true);

        return [
            Action::make('toggleGlobalDeadline')
                ->label($isDeadlineEnabled ? 'Deadline: ACTIVE' : 'Deadline: DISABLED')
                ->icon($isDeadlineEnabled ? 'heroicon-m-clock' : 'heroicon-m-no-symbol')
                ->color($isDeadlineEnabled ? 'success' : 'danger')
                ->requiresConfirmation()
                ->modalHeading($isDeadlineEnabled ? 'Disable Deadline Globally?' : 'Enable Deadline Globally?')
                ->modalDescription('This will affect all teachers and programs in StudentFlow.')
                ->action(function () use ($isDeadlineEnabled) {
                    // Balikkan status dan simpan di cache selamanya (atau durasi lama)
                    cache()->put('global_deadline_status', !$isDeadlineEnabled);

                    Notification::make()
                        ->title('Deadline Setting Updated')
                        ->body('Global deadline is now ' . (!$isDeadlineEnabled ? 'Enabled' : 'Disabled'))
                        ->success()
                        ->send();
                }),
            CreateAction::make(),
            Action::make('monitoring')
                ->label('Attendance Monitoring')
                ->icon('heroicon-o-exclamation-triangle')
                ->color('success')
                ->url(ProgramResource::getUrl('monitoring')),
        ];
    }
}
