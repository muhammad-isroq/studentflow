<?php

namespace App\Providers\Filament;

use Filament\Navigation\MenuItem;
use App\Filament\Pages\ChangePassword;
use App\Filament\Widgets\OverdueBillsAlert;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets\AccountWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use App\Filament\Widgets\StudentRegistrationChart;
use App\Http\Middleware\CheckPasswordChanged;
use App\Filament\Widgets\BirthdayWidget;
use App\Filament\Widgets\BirthdayNotifierWidget;
use Filament\Notifications\Livewire\Notifications;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\VerticalAlignment;
use App\Filament\Widgets\UpcomingMeetingsWidget; 
use App\Http\Controllers\PrintReportController;
use Illuminate\Support\Facades\Route;
use Filament\Support\Facades\FilamentView;
use Illuminate\Support\Facades\Blade;
use App\Filament\Widgets\ActiveUsersWidget;
use App\Http\Middleware\LogUserActivity;


class AdminPanelProvider extends PanelProvider
{
    public function boot(): void
    {
        Notifications::alignment(Alignment::Center);
        Notifications::verticalAlignment(VerticalAlignment::Start);
        FilamentView::registerRenderHook(
            'panels::topbar.start',
            fn (): string => Blade::render('
                @if(app(\'Lab404\Impersonate\Services\ImpersonateManager\')->isImpersonating())
                    <div class="flex items-center gap-3 px-4 py-1 text-sm font-bold text-white bg-orange-600 rounded-full shadow-lg ml-4 transition-all">
                        <span>🔴 Memantau Akun: {{ auth()->user()->name }}</span>
                        <a href="{{ route(\'impersonate.leave.custom\') }}" class="px-2 py-0.5 bg-white text-orange-600 rounded-md hover:bg-gray-100 font-extrabold shadow-sm">
                            KEMBALI KE ADMIN
                        </a>
                    </div>
                @endif
            '),
        );
    }

    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('studentflow')
            ->authGuard('web')
            ->login()
            
            ->colors([
                'primary' => Color::Amber,
            ])
            ->userMenuItems([ 
                MenuItem::make()
                    ->label('Change Password')
                    ->url(fn () => ChangePassword::getUrl())
                    ->icon('heroicon-o-key'),
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->routes(function () { 
                \Illuminate\Support\Facades\Route::get('/print/inventory', [PrintReportController::class, 'printInventory'])
                    ->name('print.inventory');
                \Illuminate\Support\Facades\Route::get('/print/borrowings', [PrintReportController::class, 'printBorrowings'])
                    ->name('print.borrowings');
                
                \Illuminate\Support\Facades\Route::get('/print/stock-report', [PrintReportController::class, 'printStockReport'])
                    ->name('print.stock_report');
                \Illuminate\Support\Facades\Route::get('/print/finance', [PrintReportController::class, 'printFinance'])
                     ->name('print.finance');
            })
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                AccountWidget::class,
                StudentRegistrationChart::class,
                OverdueBillsAlert::class,
                BirthdayWidget::class,
                BirthdayNotifierWidget::class,
                UpcomingMeetingsWidget::class,
                ActiveUsersWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
                LogUserActivity::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->globalSearch(false)
            ->viteTheme('resources/css/filament/admin/theme.css');
    }
}