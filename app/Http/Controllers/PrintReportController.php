<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Inventory;
use App\Models\Borrowing;
use App\Models\StockLog;
use App\Models\Transaction;
use Carbon\Carbon;


class PrintReportController extends Controller
{
    public function printInventory()
    {
        
        $inventories = Inventory::all();

        
        return view('print.inventory', [
            'inventories' => $inventories,
            'printDate' => now()->format('d M Y, H:i')
        ]);
    }

    public function printBorrowings()
    {
        
        $borrowings = Borrowing::with('inventory')
                        ->orderBy('borrow_date', 'desc')
                        ->get();

        return view('print.borrowings', [
            'borrowings' => $borrowings,
            'printDate' => now()->format('d M Y, H:i')
        ]);
    }

    public function printStockReport()
    {
        
        $stockLogs = StockLog::with(['inventory', 'user'])
                        ->orderBy('created_at', 'desc')
                        ->get();

        return view('print.stock_report', [
            'stockLogs' => $stockLogs,
            'printDate' => now()->format('d M Y, H:i')
        ]);
    }

    public function printFinance(Request $request)
    {
        $month = $request->input('month', date('m'));
        $year = $request->input('year', date('Y'));

        $dateObj = Carbon::createFromDate($year, $month, 1);
        $monthName = $dateObj->translatedFormat('F Y');

        // 1. QUERY PEMASUKAN (Dikelompokkan per Kategori)
        $incomes = Transaction::where('type', 'income')
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            // Ambil nama kategori, hitung total uangnya, dan hitung jumlah transaksinya
            ->selectRaw('category, sum(amount) as total_amount, count(*) as total_count')
            ->groupBy('category')
            ->get();
        
        // 2. QUERY PENGELUARAN (Dikelompokkan per Kategori)
        $expenses = Transaction::where('type', 'expense')
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->selectRaw('category, sum(amount) as total_amount, count(*) as total_count')
            ->groupBy('category')
            ->get();

        // 3. HITUNG GRAND TOTAL (Dari hasil grouping tadi)
        $totalIncome = $incomes->sum('total_amount');
        $totalExpense = $expenses->sum('total_amount');
        $profit = $totalIncome - $totalExpense;

        return view('print.finance-report', compact(
            'monthName', 
            'incomes', 
            'expenses', 
            'totalIncome', 
            'totalExpense', 
            'profit'
        ));
    }

    public function printCashBook(Request $request)
{
    $month = $request->input('month', date('m'));
    $year = $request->input('year', date('Y'));

    // Tentukan Range Tanggal
    if ($month === 'all') {
        $monthName = "TAHUN " . $year;
        $startDate = Carbon::createFromDate($year, 1, 1)->startOfYear()->format('Y-m-d');
        $endDate   = Carbon::createFromDate($year, 12, 31)->endOfYear()->format('Y-m-d');
    } else {
        $dateObj = Carbon::createFromDate($year, $month, 1);
        $monthName = $dateObj->translatedFormat('F Y');
        $startDate = $dateObj->startOfMonth()->format('Y-m-d');
        $endDate   = $dateObj->endOfMonth()->format('Y-m-d');
    }

    // 1. HITUNG SALDO AWAL (Penting untuk Buku Kas)
    $prevIncome = Transaction::where('type', 'income')->where('date', '<', $startDate)->sum('amount');
    $prevExpense = Transaction::where('type', 'expense')->where('date', '<', $startDate)->sum('amount');
    $beginningBalance = $prevIncome - $prevExpense;

    // 2. GROUPING PEMASUKAN (INCOME)
    // Contoh: Monthly SPP digabung jadi satu baris
    $groupedIncomes = Transaction::where('type', 'income')
        ->whereBetween('date', [$startDate, $endDate])
        ->selectRaw('category, sum(amount) as total_amount, count(*) as total_count')
        ->groupBy('category')
        ->get();

    // 3. GROUPING PENGELUARAN (EXPENSE)
    $groupedExpenses = Transaction::where('type', 'expense')
        ->whereBetween('date', [$startDate, $endDate])
        ->selectRaw('category, sum(amount) as total_amount, count(*) as total_count')
        ->groupBy('category')
        ->get();

    // 4. HITUNG TOTAL & SALDO AKHIR
    $totalDebit = $groupedIncomes->sum('total_amount');
    $totalCredit = $groupedExpenses->sum('total_amount');
    
    // Rumus Buku Kas: Saldo Awal + Masuk - Keluar
    $endingBalance = $beginningBalance + $totalDebit - $totalCredit;

    return view('print.cash-book-report', compact(
        'monthName',
        'groupedIncomes',   // Data Pemasukan (Grouped)
        'groupedExpenses',  // Data Pengeluaran (Grouped)
        'beginningBalance', // Saldo Awal
        'totalDebit',       // Total Masuk Periode Ini
        'totalCredit',      // Total Keluar Periode Ini
        'endingBalance'     // Saldo Akhir
    ));
}

}
