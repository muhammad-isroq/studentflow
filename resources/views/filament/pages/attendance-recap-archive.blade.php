<x-filament-panels::page>
    
    <div class="mb-2">
        {{ $this->form }}
    </div>

    @if($this->reportData)
        @php
            $program = $this->reportData['program'];
            $reports = $this->reportData['reports'];
            
            // Karena model AttendanceRecap Anda menyimpan data guru & ruangan secara statis, 
            // kita ambil informasinya dari baris pertama (jika ada) untuk headernya.
            $firstReport = $reports->first();
        @endphp

        <div class="bg-white dark:bg-gray-900 rounded-xl shadow-sm border border-gray-200 dark:border-gray-800 p-6 sm:p-8 mt-4 print-container">

            <div class="mb-8 flex justify-between items-start">
                <div>
                    <h2 class="text-2xl font-extrabold text-gray-900 dark:text-white tracking-tight">Arsip Rekap Absensi: {{ $program->nama_program ?? ($firstReport->nama_program ?? '-') }}</h2>
                    <p class="text-gray-500 dark:text-gray-400 mt-1 font-medium">Periode: {{ $this->semester_name }}</p>
                </div>
                
                <a href="{{ route('print.absensi', ['program_id' => $program->id, 'semester_name' => $this->semester_name]) }}" target="_blank" class="print-hide inline-flex items-center justify-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-primary-500 dark:bg-gray-800 dark:text-gray-200 dark:border-gray-700 dark:hover:bg-gray-700">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    Cetak / Simpan PDF
                </a>
            </div>

            <div class="bg-gray-50 dark:bg-gray-800/50 rounded-lg p-6 border border-gray-100 dark:border-gray-700 mb-8">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Detail Arsip Program</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Nama Program</p>
                        <p class="font-medium text-gray-900 dark:text-white mt-1">{{ $program->nama_program ?? ($firstReport->nama_program ?? '-') }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Guru</p>
                        <p class="font-medium text-gray-900 dark:text-white mt-1">{{ $program->guru->nama_guru ?? ($firstReport->guru->nama_guru ?? '-') }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Ruangan</p>
                        <p class="font-medium text-gray-900 dark:text-white mt-1">{{ $program->nama_ruangan ?? ($firstReport->nama_ruangan ?? '-') }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Jadwal</p>
                        <p class="font-medium text-gray-900 dark:text-white mt-1">{{ $program->jadwal_program ?? ($firstReport->jadwal_program ?? '-') }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Jam Pelajaran</p>
                        <p class="font-medium text-gray-900 dark:text-white mt-1">{{ $program->lesson_time ?? ($firstReport->jam_pelajaran ?? '-') }}</p>
                    </div>
                </div>
            </div>

            <div class="border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50 dark:bg-gray-800 text-gray-500 dark:text-gray-400 font-medium border-b border-gray-200 dark:border-gray-700 uppercase text-xs">
                        <tr>
                            <th class="px-6 py-4">NAMA SISWA</th>
                            <th class="px-4 py-4 text-center">TOTAL HADIR</th>
                            <th class="px-4 py-4 text-center">TOTAL SESI</th>
                            <th class="px-4 py-4 text-center text-primary-600 dark:text-primary-400">PERSENTASE</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($reports as $report)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
                            <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">{{ $report->siswa->nama ?? '-' }}</td>
                            <td class="px-4 py-4 text-center">{{ $report->total_hadir }}</td>
                            <td class="px-4 py-4 text-center">{{ $report->total_sesi }}</td>
                            <td class="px-4 py-4 text-center font-bold text-primary-600 dark:text-primary-400">{{ $report->percentage }}%</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-6 py-8 text-center text-gray-500">Belum ada data absensi yang diarsipkan.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <style>
            @media print {
                aside, header, .fi-topbar, .fi-sidebar, .mb-2, .print-hide { display: none !important; }
                .fi-main { padding: 0 !important; margin: 0 !important; max-width: 100% !important; }
                body { background-color: white !important; }
                .print-container { box-shadow: none !important; border: none !important; padding: 0 !important; margin-top: 0 !important; }
            }
        </style>
        
    @else
        <div class="bg-white dark:bg-gray-900 rounded-xl shadow-sm border border-gray-200 dark:border-gray-800 p-12 text-center mt-4">
            <div class="mx-auto w-16 h-16 bg-gray-100 dark:bg-gray-800 rounded-full flex items-center justify-center mb-4 text-primary-500">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" /></svg>
            </div>
            <h3 class="text-lg font-medium text-gray-900 dark:text-white">Pilih Program dan Semester</h3>
            <p class="text-gray-500 mt-1">Silakan pilih Program dan Semester pada menu dropdown di atas untuk memunculkan lembar laporan rekap absensi.</p>
        </div>
    @endif
</x-filament-panels::page>