<?php

namespace App\Filament\Resources\Programs\Pages;


use App\Filament\Resources\Programs\ProgramResource;
use Filament\Actions\DeleteAction;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\CheckboxList;
use App\Models\ClassSession;
use Carbon\CarbonPeriod;
use Filament\Notifications\Notification;

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
            Actions\DeleteAction::make(),
            
            Actions\Action::make('generateSessions')
                ->label('Generate Sesion')
                ->icon('heroicon-o-calendar-days')
                ->form([
                    DatePicker::make('start_date')
                        ->label('Start Date')
                        ->required(),
                    DatePicker::make('end_date')
                        ->label('End Date')
                        ->required()
                        ->after('start_date'),
                    CheckboxList::make('days_of_week')
                        ->label('Days of week')
                        ->options([
                            '1' => 'Monday',
                            '2' => 'Tuesday',
                            '3' => 'Wednesday',
                            '4' => 'Thursday',
                            '5' => 'Friday',
                            '6' => 'Saturday',
                            '0' => 'Sunday',
                        ])
                        ->columns(3)
                        ->required(),
                ])
                ->action(function (array $data) {
                     $period = CarbonPeriod::create($data['start_date'], $data['end_date']);
                    $program = $this->getRecord();
                    $count = 0;

                    foreach ($period as $date) {
                        if (in_array($date->dayOfWeek, $data['days_of_week'])) {
                            ClassSession::firstOrCreate(
                                [
                                    'program_id' => $program->id,
                                    'session_date' => $date,
                                ],
                                [
                                    'guru_id' => $program->guru_id,
                                    'unit' => null,
                                ]
                            );
                            $count++;
                        }
                    }
                    
                    Notification::make()
                        ->title('Berhasil!')
                        ->body("Sebanyak {$count} sesi kelas baru telah dibuat.")
                        ->success()
                        ->send();
                        
                    $program = $this->getRecord();
                    return redirect(ProgramResource::getUrl('edit', ['record' => $program]));
                }),
        ];
    }
}
