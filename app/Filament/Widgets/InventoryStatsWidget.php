<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Inventory;

class InventoryStatsWidget extends StatsOverviewWidget
{
    protected static bool $isDiscovered = false;

    public static function canView(): bool
    {
        return auth()->user()->hasRole(['admin', 'staff']);
    }

    protected function getStats(): array
    {
        $totalBarang = Inventory::count();
        $barangHabis = Inventory::where('jumlah', '<=', 0)->count();
        $barangRusak = Inventory::where('status', 'Rusak')->count();
        return [
            Stat::make('Total Barang', $totalBarang)
                ->description('Jumlah semua item terdaftar')
                ->icon('heroicon-o-archive-box')
                ->color('primary'),

            Stat::make('Barang Habis', $barangHabis)
                ->description('Item dengan stok 0')
                ->icon('heroicon-o-x-circle')
                ->color('warning'), 

            Stat::make('Barang Rusak', $barangRusak)
                ->description('Item dengan kondisi rusak')
                ->icon('heroicon-o-exclamation-triangle')
                ->color('danger'), 
        ];
    }
}
