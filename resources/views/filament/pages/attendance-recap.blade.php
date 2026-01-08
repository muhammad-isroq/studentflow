<x-filament-panels::page>
    <div class="overflow-x-auto rounded-lg border border-gray-200 shadow-sm dark:border-gray-700">
        <table class="min-w-full divide-y divide-gray-200 bg-white dark:divide-gray-700 dark:bg-gray-800">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th scope="col" class="sticky left-0 z-10 w-48 bg-gray-50 px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:bg-gray-700 dark:text-gray-300">
                        Student Name
                    </th>

                    @foreach ($sessions as $session)
                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-300">
                            {{ $session->session_date->format('d M') }}
                        </th>
                    @endforeach

                    <th scope="col" class="sticky right-0 z-10 bg-gray-50 px-6 py-3 text-center text-xs font-medium uppercase tracking-wider text-gray-500 dark:bg-gray-700 dark:text-gray-300">
                        Percentage
                    </th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                @forelse ($siswas as $siswa)
                    <tr>
                        <td class="sticky left-0 z-10 whitespace-nowrap bg-white px-6 py-4 text-sm font-medium text-gray-900 dark:bg-gray-800 dark:text-white">
                            {{ $siswa->nama }}
                        </td>

                        @foreach ($sessions as $session)
                            @php
                                // Ambil status asli dari database (Hadir, Absen, Izin, Sakit)
                                $status = $attendanceData[$siswa->id][$session->id] ?? '-';

                                // 1. Tentukan Warna (Tetap pakai status Indonesia untuk logika)
                                $colorClass = match($status) {
                                    'Hadir' => 'text-green-500',
                                    'Absen', 'Alpha' => 'text-red-500', // Menangani Absen/Alpha
                                    'Izin', 'Sakit' => 'text-yellow-500',
                                    default => 'text-gray-400',
                                };

                                // 2. Translate ke Bahasa Inggris untuk Tampilan
                                $englishStatus = match($status) {
                                    'Hadir' => 'Present',
                                    'Absen' => 'Absent',
                                    'Alpha' => 'Absent',
                                    'Izin'  => 'Permit', // Atau 'Excused'
                                    'Sakit' => 'Sick',
                                    '-'     => '-',
                                    default => $status, // Jika ada status lain, tampilkan aslinya
                                };
                            @endphp
                            
                            <td class="whitespace-nowrap px-6 py-4 text-center text-sm font-medium {{ $colorClass }}">
                                {{ $englishStatus }}
                            </td>
                        @endforeach

                        <td class="sticky right-0 z-10 whitespace-nowrap bg-white px-6 py-4 text-center text-sm font-medium text-gray-900 dark:bg-gray-800 dark:text-white">
                            {{ $attendanceScores[$siswa->id] ?? 0 }}%
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ $sessions->count() + 2 }}" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                            There are no students in this program.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-filament-panels::page>