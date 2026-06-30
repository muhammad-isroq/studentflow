<!DOCTYPE html>
<html>
<head>
    <title>Daftar Hadir Pengambilan Rapor</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        /* padding atas-bawah 15px, kiri-kanan 8px. text-align: center membuat isi jadi di tengah */
        th, td { 
            border: 1px solid black; 
            padding: 15px 8px; 
            text-align: center; 
        }
        /* Khusus untuk kolom Nama Siswa jika ingin tetap rata kiri */
        td:nth-child(2) { text-align: left; }
        
        .header { text-align: center; }
    </style>
</head>
<body onload="window.print()">
    <div class="header">
        <h3>THE MASTER OF DUMAI</h3>
        <h4>DAFTAR HADIR PENGAMBILAN RAPOR - {{ $program->nama_program }}</h4>
        <h4>PERIODE JANUARY - JUNE 2026</h4>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Siswa</th>
                <th>Orangtua/Wali</th>
                <th>Tanggal</th>
                <th>Tanda Tangan</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($siswas as $index => $siswa)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $siswa->nama }}</td>
                <td></td> 
                <td></td>
                <td></td>
                <td></td>
            </tr>
            @endforeach
        </tbody>
    </table>
    
    <div style="margin-top: 30px; float: right; width: 200px; text-align: center;">
        <p>{{ \Carbon\Carbon::parse($tanggal)->format('l, F j, Y') }}</p>
        <p>Teacher</p>
        <br><br><br>
        <p><strong>{{ $guru }}</strong></p>
    </div>
</body>
</html>