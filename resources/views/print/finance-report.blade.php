<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Keuangan Ringkas - {{ $monthName }}</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #000; padding-bottom: 10px; }
        .title { font-size: 18px; font-weight: bold; }
        .subtitle { font-size: 14px; color: #555; }
        
        .section-title { font-size: 14px; font-weight: bold; margin-top: 20px; margin-bottom: 5px; text-decoration: underline; }
        
        table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        th, td { border: 1px solid #ccc; padding: 6px 10px; text-align: left; }
        th { background-color: #f0f0f0; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .bg-green { background-color: #e6fffa; }
        .bg-red { background-color: #fff5f5; }
        
        /* Box Ringkasan Laba Rugi */
        .summary-box { border: 2px solid #333; padding: 15px; width: 50%; margin-left: auto; margin-top: 30px; background: #fff; }
        .summary-row { display: flex; justify-content: space-between; margin-bottom: 8px; font-size: 14px; }
        .total-profit { font-size: 18px; font-weight: bold; border-top: 2px solid #333; padding-top: 10px; margin-top: 5px; }
    </style>
</head>
<body onload="window.print()">

    <div class="header">
        <div class="title">STUDENTFLOW LEARNING CENTER</div>
        <div class="subtitle">Laporan Rekapitulasi Keuangan: {{ $monthName }}</div>
    </div>

    <div class="section-title" style="color: green;">A. REKAP PEMASUKAN (INCOME)</div>
    <table>
        <thead>
            <tr>
                <th width="5%">No</th>
                <th>Kategori Pemasukan</th>
                <th width="20%" class="text-center">Jumlah Transaksi</th>
                <th width="25%" class="text-right">Total (IDR)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($incomes as $index => $inc)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ ucwords(str_replace('_', ' ', $inc->category)) }}</td>
                <td class="text-center">{{ $inc->total_count }}x</td>
                <td class="text-right">{{ number_format($inc->total_amount, 0, ',', '.') }}</td>
            </tr>
            @empty
            <tr><td colspan="4" class="text-center">Tidak ada data pemasukan.</td></tr>
            @endforelse
            <tr class="bg-green">
                <td colspan="3" class="text-right"><strong>TOTAL PEMASUKAN</strong></td>
                <td class="text-right"><strong>Rp {{ number_format($totalIncome, 0, ',', '.') }}</strong></td>
            </tr>
        </tbody>
    </table>

    <div class="section-title" style="color: red;">B. REKAP PENGELUARAN (EXPENSE)</div>
    <table>
        <thead>
            <tr>
                <th width="5%">No</th>
                <th>Kategori Pengeluaran</th>
                <th width="20%" class="text-center">Jumlah Transaksi</th>
                <th width="25%" class="text-right">Total (IDR)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($expenses as $index => $exp)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ ucwords(str_replace('_', ' ', $exp->category)) }}</td>
                <td class="text-center">{{ $exp->total_count }}x</td>
                <td class="text-right">{{ number_format($exp->total_amount, 0, ',', '.') }}</td>
            </tr>
            @empty
            <tr><td colspan="4" class="text-center">Tidak ada data pengeluaran.</td></tr>
            @endforelse
            <tr class="bg-red">
                <td colspan="3" class="text-right"><strong>TOTAL PENGELUARAN</strong></td>
                <td class="text-right"><strong>Rp {{ number_format($totalExpense, 0, ',', '.') }}</strong></td>
            </tr>
        </tbody>
    </table>

    <div class="summary-box">
        <div class="summary-row">
            <span>Total Pemasukan (+)</span>
            <span>Rp {{ number_format($totalIncome, 0, ',', '.') }}</span>
        </div>
        <div class="summary-row">
            <span>Total Pengeluaran (-)</span>
            <span>(Rp {{ number_format($totalExpense, 0, ',', '.') }})</span>
        </div>
        <div class="summary-row total-profit">
            <span>LABA BERSIH:</span>
            <span style="color: {{ $profit >= 0 ? 'green' : 'red' }};">
                Rp {{ number_format($profit, 0, ',', '.') }}
            </span>
        </div>
    </div>

    <div style="margin-top: 40px; text-align: right; font-size: 11px;">
        <p>Dicetak otomatis pada: {{ now()->format('d F Y H:i') }}</p>
        <br><br><br>
        <p><strong>( Admin Keuangan )</strong></p>
    </div>

</body>
</html>