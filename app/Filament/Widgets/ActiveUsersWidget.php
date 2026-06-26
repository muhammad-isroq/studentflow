<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use App\Models\User;

class ActiveUsersWidget extends Widget
{
    protected string $view = 'filament.widgets.active-users-widget';

    protected int | string | array $columnSpan = 1;
    protected static ?int $sort = 2;

    protected static ?string $pollingInterval = '15s';

    protected function getViewData(): array
    {
        return [
            'activeUsers' => User::where('last_activity', '>=', now()->subMinutes(25))
                ->where('id', '!=', auth()->id())
                ->orderBy('last_activity', 'desc')
                ->get(),
        ];
    }
}