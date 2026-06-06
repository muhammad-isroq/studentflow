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
        // Cek apakah tagihan sudah lunas dan memiliki waktu pelunasan
        if ($bill->status === 'paid' && $bill->paid_at) {
            
            // Cari semua ID tagihan milik siswa yang sama dengan waktu lunas (paid_at) yang identik
            $siblingIds = Bill::where('siswa_id', $bill->siswa_id)
                ->where('paid_at', $bill->paid_at)
                ->pluck('id')
                ->toArray();

            // Jika ditemukan lebih dari 1 tagihan, otomatis belokkan ke cetak kolektif!
            if (count($siblingIds) > 1) {
                $idsString = implode(',', $siblingIds);
                
                // UBAH BARIS INI: Gunakan nama rute asli Anda
                return redirect()->route('print.receipt.collective', ['ids' => $idsString]);
            }
        }

        // 2. JIKA BUKAN KOLEKTIF (Hanya 1 Tagihan)
        // Lanjutkan merender struk tunggal seperti biasa
        $bill->load(['siswa', 'paymentType']);
        $siswa = $bill->siswa;
        $paymentType = $bill->paymentType;
        
        // Ambil nama staff yang sedang login
        $staffName = Auth::user() ? Auth::user()->name : 'Admin';

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