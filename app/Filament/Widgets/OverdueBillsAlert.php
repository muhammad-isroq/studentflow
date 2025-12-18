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
        $this->overdueBills = Bill::query()
            // PERBAIKAN 1: Ambil status 'unpaid' ATAU 'overdue'
            ->whereIn('status', ['unpaid', 'overdue'])
            ->where('due_date', '<', Carbon::now()) // Cari yang jatuh temponya sudah lewat
            ->whereHas('paymentType', function ($query) {
                $query->where('name', 'Monthly spp'); // Khusus untuk Monthly spp
            })
            ->with('siswa') // Ambil data siswa yang terkait
            ->get();
    }

    // Method ini untuk menyembunyikan widget jika tidak ada tagihan telat
    public static function canView(): bool
    {
        if (!Auth::check() || !Auth::user()->hasAnyRole(['admin', 'staff', 'super_staff'])) {
            return false;
        }

        // Hanya tampilkan widget jika ada tagihan SPP yang telat
        return Bill::query()
            ->whereIn('status', ['unpaid', 'overdue'])
            ->where('due_date', '<', Carbon::now())
            ->whereHas('paymentType', function ($query) {
                $query->where('name', 'Monthly spp');
            })
            ->exists();
    }
}
