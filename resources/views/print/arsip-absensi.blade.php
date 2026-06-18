<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Arsip Absensi - {{ $program->nama_program }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Pengaturan Kertas A4 */
        @page { size: A4; margin: 20mm; }
        body { font-family: 'Arial', sans-serif; background-color: white; color: black; }
        
        /* Sembunyikan tombol saat di-print ke kertas */
        @media print {
            .no-print { display: none !important; }
            body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        }
    </style>
</head>
<body onload="window.print()" class="p-8">

    <div class="text-center border-b-2 border-black pb-4 mb-6">
        <h1 class="text-2xl font-bold uppercase tracking-wider">ARSIP REKAPITULASI ABSENSI SEMESTER</h1>
        <h2 class="text-xl font-bold mt-1">THE MASTER OF DUMAI</h2>
        <p class="text-sm mt-2 text-gray-700">Periode Akademik: <span class="font-bold">{{ $semester_name }}</span></p>
    </div>

    <div class="grid grid-cols-2 gap-4 mb-6 text-sm">
        <div>
            <table class="w-full">
                <tr><td class="w-32 font-bold py-1">Program Kelas</td><td>: {{ $program->nama_program ?? '-' }}</td></tr>
                <tr><td class="font-bold py-1">Guru Pengajar</td><td>: {{ $program->guru->nama_guru ?? '-' }}</td></tr>
            </table>
        </div>
        <div>
            <table class="w-full">
                <tr><td class="w-32 font-bold py-1">Ruangan</td><td>: {{ $program->nama_ruangan ?? '-' }}</td></tr>
                <tr><td class="font-bold py-1">Jadwal</td><td>: {{ $program->jadwal_program ?? '-' }} ({{ $program->lesson_time ?? '-' }})</td></tr>
            </table>
        </div>
    </div>

    <table class="w-full text-sm border-collapse border border-gray-400">
        <thead class="bg-gray-100">
            <tr>
                <th class="border border-gray-400 px-4 py-2 text-left w-12">NO</th>
                <th class="border border-gray-400 px-4 py-2 text-left">NAMA SISWA</th>
                <th class="border border-gray-400 px-4 py-2 text-center">TOTAL HADIR</th>
                <th class="border border-gray-400 px-4 py-2 text-center">TOTAL SESI</th>
                <th class="border border-gray-400 px-4 py-2 text-center bg-gray-200">PERSENTASE</th>
            </tr>
        </thead>
        <tbody>
            @forelse($reports as $index => $report)
            <tr>
                <td class="border border-gray-400 px-4 py-2 text-center">{{ $index + 1 }}</td>
                <td class="border border-gray-400 px-4 py-2 font-semibold">{{ $report->siswa->nama ?? '-' }}</td>
                <td class="border border-gray-400 px-4 py-2 text-center">{{ $report->total_hadir }}</td>
                <td class="border border-gray-400 px-4 py-2 text-center">{{ $report->total_sesi }}</td>
                <td class="border border-gray-400 px-4 py-2 text-center font-bold bg-gray-50">{{ $report->percentage }}%</td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="border border-gray-400 px-4 py-6 text-center italic text-gray-500">Belum ada data rekap absensi untuk kelas ini.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="mt-12 w-full flex justify-end">
        <div class="text-center w-64">
            <p class="text-sm mb-16">Dumai, {{ date('d F Y') }}</p>
            <p class="font-bold underline">{{ $program->guru->nama_guru ?? '_______________________' }}</p>
            <p class="text-sm">Teacher</p>
        </div>
    </div>

    <div class="mt-8 text-center no-print">
        <button onclick="window.print()" class="bg-blue-600 text-white px-6 py-2 rounded shadow hover:bg-blue-700">Print Ulang</button>
        <button onclick="window.close()" class="bg-gray-500 text-white px-6 py-2 rounded shadow hover:bg-gray-600 ml-2">Tutup Tab</button>
    </div>

</body>
</html>