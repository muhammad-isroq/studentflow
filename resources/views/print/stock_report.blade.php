<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Laporan Riwayat Stok</title>
    <style>
        /* Copy-paste <style> yang sama dari file sebelumnya */
        body { font-family: Arial, sans-serif; margin: 20px; }
        h1 { font-size: 20px; text-align: center; border-bottom: 1px solid #000; padding-bottom: 10px; }
        p { font-size: 12px; text-align: right; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; font-size: 12px; }
        th, td { border: 1px solid #000; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .print-button { display: block; width: 100px; margin: 20px auto; padding: 10px; background-color: #007bff; color: white; text-align: center; border: none; border-radius: 5px; cursor: pointer; }
        @media print { .print-button { display: none; } body { margin: 0; } }
    </style>
</head>
<body>

    <h1>Laporan Riwayat Stok</h1>
    <p>Dicetak pada: {{ $printDate }}</p>

    <button class="print-button" onclick="window.print()">Cetak Halaman</button>

    <table>
        <thead>
            <tr>
                <th>No.</th>
                <th>Tanggal</th>
                <th>Nama Barang</th>
                <th>Perubahan</th>
                <th>Stok Akhir</th>
                <th>Alasan</th>
                <th>Dicatat Oleh</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($stockLogs as $index => $log)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $log->created_at->format('d M Y, H:i') }}</td>
                    <td>{{ $log->inventory->nama_barang ?? 'Barang Dihapus' }}</td>
                    <td style="color: {{ $log->change_amount < 0 ? 'red' : 'green' }};">
                        {{ $log->change_amount > 0 ? '+' : '' }}{{ $log->change_amount }}
                    </td>
                    <td>{{ $log->stock_after_change }}</td>
                    <td>{{ $log->reason }}</td>
                    <td>{{ $log->user->name ?? 'Sistem' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align: center;">Tidak ada riwayat stok.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

</body>
</html>