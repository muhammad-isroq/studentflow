<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use App\Models\User;
use Filament\Notifications\Notification;
use Illuminate\Support\HtmlString; 

class BirthdayNotifierWidget extends Widget
{
    protected string $view = 'filament.widgets.birthday-notifier-widget';
    
    protected int | string | array $columnSpan = 'full';
    protected static ?int $sort = 100;

    public function mount(): void
    {
        $birthdayUsers = User::query()
            ->whereNotNull('tanggal_lahir')
            ->whereMonth('tanggal_lahir', now()->month)
            ->whereDay('tanggal_lahir', now()->day)
            ->get();

        if ($birthdayUsers->isNotEmpty()) {
            foreach ($birthdayUsers as $user) {
                Notification::make()
                    
                    ->title(new HtmlString('<span class="text-lg font-bold">Happy Birthday!</span>'))
                    ->body(new HtmlString("<span class=\"text-base\">Today is <strong>{$user->name}'s</strong> birthday. Don’t forget to wish her</span>"))
                    
                    ->success()
                    ->icon('heroicon-o-cake')
                    ->duration(60000) 
                    
                    // 4. PASTIKAN TOMBOL TUTUP (X) SELALU MUNCUL
                    ->persistent() 
                    
                    ->send();
            }
        }
    }
}

