<x-filament-panels::page>

    <div class="overflow-x-auto bg-white dark:bg-gray-800 rounded-lg shadow">
        <table class="min-w-full text-sm text-left text-gray-500 dark:text-gray-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                <tr>
                    <th scope="col" class="px-6 py-3 sticky left-0 bg-gray-50 dark:bg-gray-700 z-10">
                        Nama Siswa
                    </th>
                    @foreach ($sessions as $session)
                        <th scope="col" class="px-6 py-3 text-center">
                            {{ $session->session_date->format('d M') }}
                        </th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @forelse ($siswas as $siswa)
                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                        <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white sticky left-0 bg-white dark:bg-gray-800 z-10">
                            {{ $siswa->nama }}
                        </td>
                        @foreach ($sessions as $session)
                            @php
                                // Ambil status dari array yang sudah kita siapkan di file Page
                                $status = $attendanceData[$siswa->id][$session->id] ?? null;
                                $bgColorClass = match ($status) {
                                    'Hadir' => 'bg-green-100 dark:bg-green-900',
                                    'Izin' => 'bg-yellow-100 dark:bg-yellow-900',
                                    'Sakit' => 'bg-blue-100 dark:bg-blue-900',
                                    'Alpa' => 'bg-red-100 dark:bg-red-900',
                                    default => '',
                                };
                                $textColorClass = match ($status) {
                                    'Hadir' => 'text-green-800 dark:text-green-300',
                                    'Izin' => 'text-yellow-800 dark:text-yellow-300',
                                    'Sakit' => 'text-blue-800 dark:text-blue-300',
                                    'Alpa' => 'text-red-800 dark:text-red-300',
                                    default => 'text-gray-400',
                                };
                            @endphp
                            <td class="px-6 py-4 text-center {{ $bgColorClass }} {{ $textColorClass }}">
                                {{ $status ?? '-' }}
                            </td>
                        @endforeach
                         @php
                            $score = $attendanceScores[$siswa->id] ?? 0;
                            $scoreColorClass = match (true) {
                                $score >= 85 => 'text-green-600 dark:text-green-400',
                                $score >= 70 => 'text-yellow-600 dark:text-yellow-400',
                                default => 'text-red-600 dark:text-red-400',
                            };
                        @endphp
                        <td class="px-6 py-4 font-bold text-center sticky right-0 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-600 z-10 {{ $scoreColorClass }}">
                            {{ $score }}%
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ count($sessions) + 1 }}" class="px-6 py-4 text-center text-gray-500">
                            Tidak ada siswa yang terdaftar di program ini.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</x-filament-panels::page>

