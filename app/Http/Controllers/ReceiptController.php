<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReceiptController extends Controller
{
    public function printSingle(Bill $bill)
    {
        $bill->load(['siswa', 'paymentType']);
        $siswa = $bill->siswa;
        $paymentType = $bill->paymentType;
        
        // Ambil nama staff yang sedang login
        $staffName = Auth::user() ? Auth::user()->name : 'Admin';

        // DI SINI PERUBAHANNYA: Sesuaikan dengan nama file receipt.blade.php Anda
        return view('print.receipt', compact('bill', 'siswa', 'paymentType', 'staffName'));
    }

    public function printCollective(Request $request)
    {
        $ids = explode(',', $request->query('ids'));
        $bills = Bill::whereIn('id', $ids)->with(['siswa', 'paymentType'])->get();

        if ($bills->isEmpty()) {
            return "Data struk tidak ditemukan.";
        }

        $siswa = $bills->first()->siswa;
        $total = $bills->sum('amount');
        
        $staffName = Auth::user() ? Auth::user()->name : 'Admin';

        return view('print.receipt-collective', compact('bills', 'siswa', 'total', 'staffName'));
    }
}