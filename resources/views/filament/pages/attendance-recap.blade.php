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
                            <div class="flex flex-col items-center">
                                <span>{{ $session->session_date->format('d M') }}</span>
                                {{-- Tambahkan Label Ramadhan --}}
                                @if($session->is_ramadhan_session)
                                    <span class="text-[9px] text-orange-600 font-black bg-orange-100 dark:bg-orange-900/30 px-1.5 py-0.5 rounded mt-0.5 tracking-tighter">RAMADHAN</span>
                                @endif
                            </div>
                        </th>
                    @endforeach

                    <th scope="col" class="sticky right-0 z-10 bg-gray-50 px-6 py-3 text-center text-xs font-medium uppercase tracking-wider text-gray-500 dark:bg-gray-700 dark:text-gray-300">
                        Attendance Rate
                    </th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                @forelse ($siswas as $siswa)
                    @php
                        $scoreDetail = $attendanceScores[$siswa->id] ?? ['score' => 0, 'total' => 0, 'hadir' => 0, 'is_moved' => false];
                    @endphp
                    <tr>
                        <td class="sticky left-0 z-10 whitespace-nowrap bg-white px-6 py-4 text-sm font-medium text-gray-900 dark:bg-gray-800 dark:text-white border-r dark:border-gray-700">
                            <div class="flex flex-col">
                                <span>{{ $siswa->nama }}</span>
                                @if($scoreDetail['is_moved'])
                                    <span class="text-[10px] font-bold text-orange-500 uppercase tracking-tighter italic">Moved/Mutated</span>
                                @endif
                            </div>
                        </td>

                        @foreach ($sessions as $session)
                            @php
                                $status = $attendanceData[$siswa->id][$session->id] ?? '-';
                                
                                // LOGIKA BARU: Jika sesi Ramadhan tapi siswa tidak ikut (tidak ada record absen)
                                $isSkippedRamadhan = $session->is_ramadhan_session && !isset($attendanceData[$siswa->id][$session->id]);

                                // Penentuan warna teks
                                $colorClass = match($status) {
                                    'Hadir' => 'text-green-600 dark:text-green-400 font-bold',
                                    'Absen', 'Alpha' => 'text-red-600 dark:text-red-400 font-bold',
                                    'Izin', 'Sakit' => 'text-yellow-600 dark:text-yellow-500',
                                    '-' => 'text-gray-300 dark:text-gray-600',
                                    default => 'text-gray-500',
                                };

                                // Jika dilewati (mutasi ke tempat lain), buat jadi sangat samar (abu-abu muda)
                                if ($isSkippedRamadhan) {
                                    $colorClass = 'text-gray-200 dark:text-gray-700';
                                }

                                $englishStatus = match($status) {
                                    'Hadir' => 'Present',
                                    'Absen', 'Alpha' => 'Alpha',
                                    'Izin' => 'Permit',
                                    'Sakit' => 'Sick',
                                    '-' => '•',
                                    default => $status,
                                };
                            @endphp
                            
                            <td class="whitespace-nowrap px-6 py-4 text-center text-sm {{ $colorClass }}">
                                @if($isSkippedRamadhan)
                                    <span title="Siswa tidak mengikuti sesi mutasi Ramadhan ini">•</span>
                                @else
                                    <span title="{{ $status }}">{{ $englishStatus }}</span>
                                @endif
                            </td>
                        @endforeach

                        <td class="sticky right-0 z-10 whitespace-nowrap bg-white px-6 py-4 text-center dark:bg-gray-800 border-l dark:border-gray-700">
                            <div class="flex flex-col items-center">
                                <span class="text-sm font-bold text-gray-900 dark:text-white">
                                    {{ $scoreDetail['score'] }}%
                                </span>
                                <span class="text-[10px] text-gray-500 dark:text-gray-400">
                                    ({{ $scoreDetail['hadir'] }}/{{ $scoreDetail['total'] }} sessions)
                                </span>
                            </div>
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