<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;
use App\Models\Siswa;
use App\Observers\SiswaObserver;
use Illuminate\Support\Facades\Auth;
use Filament\Facades\Filament;
use App\Models\Program;
use Filament\Navigation\NavigationGroup;
use Filament\Navigation\NavigationItem;
use App\Filament\Pages\ProgramSchedule;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Siswa::observe(SiswaObserver::class);
        Filament::serving(function () {
            // Logika ini hanya akan berjalan jika user sudah login
            if (Auth::check() && Auth::user()->hasRole('guru')) {
                $guruId = Auth::user()->guru_id;
                $programs = Program::where('guru_id', $guruId)->get();

                // Bangun grup navigasi baru secara dinamis
                Filament::registerNavigationGroups([
                    NavigationGroup::make('Jadwal Program Saya'),
                ]);

                // Bangun item navigasi baru untuk setiap program guru
                $navItems = $programs->map(function (Program $program) {
                    return NavigationItem::make($program->nama_program)
                        ->group('Jadwal Program Saya')
                        ->icon('heroicon-o-calendar-days')
                        ->url(ProgramSchedule::getUrl(['program' => $program->id]))
                        ->isActiveWhen(fn () => request()->route('program')?->id === $program->id);
                })->all();

                Filament::registerNavigationItems($navItems);
            }
        });
    }
}
