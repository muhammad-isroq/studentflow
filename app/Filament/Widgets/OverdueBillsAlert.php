<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use App\Models\Bill;
use Carbon\Carbon;

class OverdueBillsAlert extends Widget
{
    protected string $view = 'filament.widgets.overdue-bills-alert';
    protected int|string|array $columnSpan = 1;
    protected static ?int $sort = 2;

    public $overdueBills;

    // Method ini dijalankan saat widget dimuat
    public function mount(): void
    {
        $this->overdueBills = Bill::where('status', 'unpaid')
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
        // Hanya tampilkan widget jika ada tagihan SPP yang telat
        return Bill::where('status', 'unpaid')
            ->where('due_date', '<', Carbon::now())
            ->whereHas('paymentType', function ($query) {
                $query->where('name', 'Monthly spp');
            })
            ->exists();
    }
}
