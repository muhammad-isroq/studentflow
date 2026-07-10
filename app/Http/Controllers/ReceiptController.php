<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReceiptController extends Controller
{
    public function printSingle(Bill $bill)
    {
        // 1. SMART PRINT LOGIC: Deteksi Pembayaran Kolektif
        if ($bill->status === 'paid' && $bill->paid_at) {
            
            // Menggunakan query yang lebih luas:
            // Cari tagihan yang 'paid_at' nya berdekatan (dalam rentang 1 menit)
            // ATAU gunakan logika group jika Anda menambahkan kolom 'group_id' di masa depan.
            // Saat ini, kita cari berdasarkan paid_at yang sama atau sangat dekat.
            
            $siblingIds = Bill::where('status', 'paid')
                ->where('paid_at', $bill->paid_at) // Jika diproses sekaligus, paid_at biasanya persis sama
                ->where('transaction_type', $bill->transaction_type)
                ->pluck('id')
                ->toArray();

            // Jika ada lebih dari satu tagihan yang dibayar di detik yang sama
            if (count($siblingIds) > 1) {
                return redirect()->route('print.receipt.collective', ['ids' => implode(',', $siblingIds)]);
            }
        }

        // 2. JIKA BUKAN KOLEKTIF (Single Receipt)
        $bill->load(['siswa', 'paymentType']);
        $siswas = $bill->siswa; 
        $paymentType = $bill->paymentType;
        $staffName = Auth::user() ? Auth::user()->name : 'Admin';

        return view('print.receipt', compact('bill', 'siswas', 'paymentType', 'staffName'));
    }

    public function printCollective(Request $request)
    {
        $ids = explode(',', $request->query('ids', ''));
        
        $bills = Bill::whereIn('id', $ids)
            ->with(['siswa', 'paymentType'])
            ->get();

        if ($bills->isEmpty()) {
            return "Data struk tidak ditemukan.";
        }

        // Ambil koleksi unik semua siswa dari semua tagihan tersebut
        $allSiswa = $bills->flatMap->siswa->unique('id');
        
        // Ambil data dari tagihan pertama untuk header struk
        $firstBill = $bills->first();
        $total = $bills->sum('amount');
        $staffName = Auth::user() ? Auth::user()->name : 'Admin';

        return view('print.receipt-collective', compact('bills', 'allSiswa', 'total', 'staffName', 'firstBill'));
    }
}