<?php

namespace App\Filament\Resources\Transactions\Widgets; // <--- Namespace disesuaikan

use App\Models\Transaction;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Carbon\Carbon;

class TransactionOverview extends BaseWidget
{
    // Opsional: Cek data real-time
    protected ?string $pollingInterval = null;

    protected function getStats(): array
    {
        // 1. Tentukan Rentang Waktu (Bulan Ini)
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        // 2. Hitung Pemasukan (Income)
        $pemasukan = Transaction::where('type', 'income')
            ->whereBetween('date', [$startOfMonth, $endOfMonth])
            ->sum('amount');

        // 3. Hitung Pengeluaran (Expense)
        $pengeluaran = Transaction::where('type', 'expense')
            ->whereBetween('date', [$startOfMonth, $endOfMonth])
            ->sum('amount');

        // 4. Hitung Profit
        $profit = $pemasukan - $pengeluaran;

        return [
            Stat::make('Pemasukan (Bulan Ini)', 'Rp ' . number_format($pemasukan, 0, ',', '.'))
                ->description('Total uang masuk')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success')
                ->chart([7, 2, 10, 3, 15, 4, 17]), 

            Stat::make('Pengeluaran (Bulan Ini)', 'Rp ' . number_format($pengeluaran, 0, ',', '.'))
                ->description('Total biaya operasional')
                ->descriptionIcon('heroicon-m-arrow-trending-down')
                ->color('danger')
                ->chart([15, 4, 10, 2, 12, 4, 12]),

            Stat::make('Laba Bersih (Profit)', 'Rp ' . number_format($profit, 0, ',', '.'))
                ->description($profit >= 0 ? 'Kondisi Aman' : 'Defisit (Rugi)')
                ->descriptionIcon($profit >= 0 ? 'heroicon-m-check-circle' : 'heroicon-m-exclamation-circle')
                ->color($profit >= 0 ? 'success' : 'danger'), 
        ];
    }
}