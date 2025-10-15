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
                    
                    ->title(new HtmlString('<span class="text-lg font-bold">Selamat Ulang Tahun!</span>'))
                    ->body(new HtmlString("<span class=\"text-base\">Hari ini <strong>{$user->name}</strong> sedang berulang tahun. Jangan lupa ucapkan selamat!</span>"))
                    
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

