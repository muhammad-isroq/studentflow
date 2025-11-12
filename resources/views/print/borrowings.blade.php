<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Laporan Peminjaman Barang</title>
    <style>
        /* Anda bisa copy-paste <style>...<style> dari file print/inventory.blade.php */
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

    <h1>Laporan Peminjaman Barang</h1>
    <p>Dicetak pada: {{ $printDate }}</p>

    <button class="print-button" onclick="window.print()">Cetak Halaman</button>

    <table>
        <thead>
            <tr>
                <th>No.</th>
                <th>Nama Barang</th>
                <th>Peminjam</th>
                <th>Jumlah</th>
                <th>Tgl. Pinjam</th>
                <th>Jatuh Tempo</th>
                <th>Tgl. Kembali</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($borrowings as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item->inventory->nama_barang ?? 'Barang Dihapus' }}</td>
                    <td>{{ $item->borrower_name }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>{{ $item->borrow_date->format('d M Y') }}</td>
                    <td>{{ $item->due_date->format('d M Y') }}</td>
                    <td>
                        @if ($item->return_date)
                            {{ $item->return_date->format('d M Y') }}
                        @else
                            <span style="color: red;">Belum Kembali</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align: center;">Tidak ada data peminjaman.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

</body>
</html>