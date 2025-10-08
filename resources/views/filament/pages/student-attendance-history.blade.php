<x-filament-panels::page>

    {{-- KARTU SKOR KEHADIRAN --}}
    @if ($attendanceScore !== null)
        <div class="p-6 bg-white rounded-lg shadow dark:bg-gray-800">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-lg font-medium text-gray-700 dark:text-gray-300">
                        Skor Kehadiran Keseluruhan
                    </p>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        Berdasarkan semua sesi yang tercatat.
                    </p>
                </div>
                
                @php
                    $scoreColorClass = match (true) {
                        $attendanceScore >= 85 => 'text-green-600 dark:text-green-400',
                        $attendanceScore >= 70 => 'text-yellow-600 dark:text-yellow-400',
                        default => 'text-red-600 dark:text-red-400',
                    };
                @endphp

                <span class="text-5xl font-bold {{ $scoreColorClass }}">
                    {{ $attendanceScore }}%
                </span>
            </div>
        </div>
    @endif

    {{-- TABEL DETAIL KEHADIRAN --}}
    <div class="overflow-x-auto bg-white dark:bg-gray-800 rounded-lg shadow">
        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                <tr>
                    <th scope="col" class="px-6 py-3">Tanggal Pertemuan</th>
                    <th scope="col" class="px-6 py-3">Program</th>
                    <th scope="col" class="px-6 py-3">Topik</th>
                    <th scope="col" class="px-6 py-3 text-center">Status Kehadiran</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($attendances as $attendance)
                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                        <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                            {{ $attendance->classSession->session_date->format('l, d F Y') }}
                        </td>
                        <td class="px-6 py-4">
                            {{ $attendance->classSession->program->nama_program ?? '-' }}
                        </td>
                        <td class="px-6 py-4">
                            {{ $attendance->classSession->topic ?? '-' }}
                        </td>
                        <td class="px-6 py-4 text-center">
                             @php
                                $status = $attendance->status;
                                $bgColorClass = match ($status) {
                                    'Hadir' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
                                    'Izin' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300',
                                    'Sakit' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300',
                                    'Alpa' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300',
                                    default => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
                                };
                            @endphp
                            <span class="px-2 py-1 font-semibold leading-tight rounded-full text-xs {{ $bgColorClass }}">
                                {{ $status }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-4 text-center text-gray-500">
                            Tidak ada data absensi yang ditemukan untuk siswa ini.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</x-filament-panels::page>

