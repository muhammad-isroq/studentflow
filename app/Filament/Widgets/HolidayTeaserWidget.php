<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use Filament\Notifications\Notification;
use Illuminate\Support\HtmlString;

class HolidayTeaserWidget extends Widget
{
    protected string $view = 'filament.widgets.holiday-teaser-widget';
    protected int | string | array $columnSpan = 'full';
    protected static ?int $sort = -1;

    public function mount(): void
    {
        // Trigger notifikasi pop-up saat dashboard dimuat
        Notification::make()
            ->title(new HtmlString('<span class="text-xl font-black text-amber-600"> Bro, it\'s a holiday.</span>'))
            ->body(new HtmlString('<span class="text-base font-medium text-gray-700">Why are you still here? Close this app and go get some rest! hahaa </span>'))
            ->warning() // Memberikan aksen warna oranye/kuning
            ->icon('heroicon-o-face-smile')
            ->persistent() // Memaksa pop-up tetap muncul sampai tombol 'X' ditekan
            ->send();
    }
}