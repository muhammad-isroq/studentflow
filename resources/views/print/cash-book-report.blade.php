<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Buku Kas Ringkas - {{ $monthName }}</title>
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
        
        .bg-blue { background-color: #e3f2fd; }
        .bg-green { background-color: #e6fffa; }
        .bg-red { background-color: #fff5f5; }
        .bg-yellow { background-color: #fffbe6; font-weight: bold; }

        /* Box Ringkasan */
        .summary-box { border: 2px solid #333; padding: 15px; width: 50%; margin-left: auto; margin-top: 30px; background: #fff; }
        .summary-row { display: flex; justify-content: space-between; margin-bottom: 8px; font-size: 14px; }
        .total-final { font-size: 18px; font-weight: bold; border-top: 2px solid #333; padding-top: 10px; margin-top: 5px; color: #000; }
    </style>
</head>
<body onload="window.print()">

    <div class="header">
        <div class="title">STUDENTFLOW LEARNING CENTER</div>
        <div class="subtitle">Buku Kas (Ringkasan): {{ $monthName }}</div>
    </div>

    <div style="margin-bottom: 20px; padding: 10px; background-color: #e3f2fd; border: 1px solid #ccc;">
        <span style="font-size: 14px; font-weight: bold;">SALDO AWAL (BEGINNING BALANCE):</span>
        <span style="font-size: 14px; float: right;">Rp {{ number_format($beginningBalance, 0, ',', '.') }}</span>
    </div>

    <div class="section-title" style="color: green;">A. PEMASUKAN (DEBIT)</div>
    <table>
        <thead>
            <tr>
                <th width="5%">No</th>
                <th>Sumber Pemasukan</th>
                <th width="20%" class="text-center">Frekuensi</th>
                <th width="25%" class="text-right">Total (IDR)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($groupedIncomes as $index => $inc)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ ucwords(str_replace('_', ' ', $inc->category)) }}</td>
                <td class="text-center">{{ $inc->total_count }}x Transaksi</td>
                <td class="text-right">{{ number_format($inc->total_amount, 0, ',', '.') }}</td>
            </tr>
            @empty
            <tr><td colspan="4" class="text-center">Tidak ada pemasukan periode ini.</td></tr>
            @endforelse
            <tr class="bg-green">
                <td colspan="3" class="text-right"><strong>TOTAL PEMASUKAN</strong></td>
                <td class="text-right"><strong>(+) Rp {{ number_format($totalDebit, 0, ',', '.') }}</strong></td>
            </tr>
        </tbody>
    </table>

    <div class="section-title" style="color: red;">B. PENGELUARAN (KREDIT)</div>
    <table>
        <thead>
            <tr>
                <th width="5%">No</th>
                <th>Jenis Pengeluaran</th>
                <th width="20%" class="text-center">Frekuensi</th>
                <th width="25%" class="text-right">Total (IDR)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($groupedExpenses as $index => $exp)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ ucwords(str_replace('_', ' ', $exp->category)) }}</td>
                <td class="text-center">{{ $exp->total_count }}x Transaksi</td>
                <td class="text-right">{{ number_format($exp->total_amount, 0, ',', '.') }}</td>
            </tr>
            @empty
            <tr><td colspan="4" class="text-center">Tidak ada pengeluaran periode ini.</td></tr>
            @endforelse
            <tr class="bg-red">
                <td colspan="3" class="text-right"><strong>TOTAL PENGELUARAN</strong></td>
                <td class="text-right"><strong>(-) Rp {{ number_format($totalCredit, 0, ',', '.') }}</strong></td>
            </tr>
        </tbody>
    </table>

    <div class="summary-box">
        <div class="summary-row">
            <span><strong>Saldo Awal</strong> (Sisa Bulan Lalu)</span>
            <span>Rp {{ number_format($beginningBalance, 0, ',', '.') }}</span>
        </div>
        <div class="summary-row" style="color: green;">
            <span>Total Pemasukan Bulan Ini (+)</span>
            <span>Rp {{ number_format($totalDebit, 0, ',', '.') }}</span>
        </div>
        <div class="summary-row" style="color: red;">
            <span>Total Pengeluaran Bulan Ini (-)</span>
            <span>(Rp {{ number_format($totalCredit, 0, ',', '.') }})</span>
        </div>
        
        <hr style="border: 1px dashed #ccc; margin: 10px 0;">

        <div class="summary-row total-final">
            <span>SALDO AKHIR (ENDING BALANCE):</span>
            <span style="background-color: yellow; padding: 0 5px;">
                Rp {{ number_format($endingBalance, 0, ',', '.') }}
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