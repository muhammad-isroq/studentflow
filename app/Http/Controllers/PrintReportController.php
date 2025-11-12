<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Inventory;
use App\Models\Borrowing;
use App\Models\StockLog;

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
}
