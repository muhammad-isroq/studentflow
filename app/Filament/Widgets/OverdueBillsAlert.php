<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use App\Models\Bill;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class OverdueBillsAlert extends Widget
{
    protected string $view = 'filament.widgets.overdue-bills-alert';
    protected int|string|array $columnSpan = 1;
    protected static ?int $sort = 2;

    public $overdueBills;

    // Method ini dijalankan saat widget dimuat
    public function mount(): void
    {
        // $this->overdueBills = Bill::query()

        //     ->whereIn('status', ['unpaid', 'overdue'])
        //     ->where('due_date', '<', Carbon::now()) 
        //     ->whereHas('paymentType', function ($query) {
        //         $query->where('name', 'Monthly spp'); 
        //     })
        //     ->with('siswa') 
        //     ->get();

        //nonaktifkan widget
        $this->overdueBills = collect();
    }

    
    public static function canView(): bool
    {
        // if (!Auth::check() || !Auth::user()->hasAnyRole(['admin', 'staff', 'super_staff'])) {
        //     return false;
        // }

        
        // return Bill::query()
        //     ->whereIn('status', ['unpaid', 'overdue'])
        //     ->where('due_date', '<', Carbon::now())
        //     ->whereHas('paymentType', function ($query) {
        //         $query->where('name', 'Monthly spp');
        //     })
        //     ->exists();

        //nonaktifkan widget
        return false;
    }
}
