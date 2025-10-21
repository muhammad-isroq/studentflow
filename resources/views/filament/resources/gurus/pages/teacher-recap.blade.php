<x-filament-panels::page>
    {{-- Filter Bulan dan Tahun - Responsive --}}
    <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-4 mb-6 p-4 bg-white dark:bg-gray-800 rounded-xl shadow">
        <div class="w-full sm:w-1/2">
            <label for="month" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Bulan</label>
            <select id="month" wire:model.live="selectedMonth" class="block w-full pl-3 pr-10 py-2 text-base border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm rounded-md">
                @foreach($months as $num => $name)
                    <option value="{{ $num }}">{{ $name }}</option>
                @endforeach
            </select>
        </div>
        <div class="w-full sm:w-1/2">
            <label for="year" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Tahun</label>
            <select id="year" wire:model.live="selectedYear" class="block w-full pl-3 pr-10 py-2 text-base border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm rounded-md">
                @foreach($years as $year)
                    <option value="{{ $year }}">{{ $year }}</option>
                @endforeach
            </select>
        </div>
    </div>

    {{-- Tabel Desktop View (Hidden on Mobile) --}}
    <div class="hidden lg:block fi-ta-ctn overflow-hidden bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 sm:rounded-xl">
        <div class="overflow-x-auto">
            <table class="fi-ta-table w-full table-auto divide-y divide-gray-200 text-start dark:divide-white/5">
                <thead class="bg-gray-50 dark:bg-white/5">
                    <tr class="text-sm">
                        <th class="fi-ta-header-cell px-3 py-3.5 sm:px-6 text-center">
                            Nama Program
                        </th>
                        <th class="fi-ta-header-cell px-3 py-3.5 sm:px-6 text-center">
                            Jumlah Pertemuan 
                        </th>
                        <th class="fi-ta-header-cell px-3 py-3.5 sm:px-6 text-center">
                            Jumlah Jam Mengajar
                        </th>
                        <th class="fi-ta-header-cell px-3 py-3.5 sm:px-6 text-center">
                            Jumlah Siswa dikelas
                        </th>
                        <th class="fi-ta-header-cell px-3 py-3.5 sm:px-6 text-center">
                            Keterangan
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 whitespace-nowrap dark:divide-white/5">
                    @forelse ($this->programsWithTotals as $program)
                        <tr class="fi-ta-row text-center">
                            <td class="fi-ta-cell p-0 first-of-type:ps-1 last-of-type:pe-1 sm:first-of-type:ps-3 sm:last-of-type:pe-3">
                                <div class="fi-ta-col-wrp px-3 py-4">
                                    {{ $program->nama_program }}
                                </div>
                            </td>
                            <td class="fi-ta-cell p-0 first-of-type:ps-1 last-of-type:pe-1 sm:first-of-type:ps-3 sm:last-of-type:pe-3">
                                <div class="fi-ta-col-wrp px-3 py-4">
                                    {{ $program->total_sessions }} Pertemuan
                                </div>
                            </td>
                            <td class="fi-ta-cell p-0 first-of-type:ps-1 last-of-type:pe-1 sm:first-of-type:ps-3 sm:last-of-type:pe-3">
                                <div class="fi-ta-col-wrp px-3 py-4">
                                    @php
                                        $hours = floor($program->total_teaching_minutes / 60);
                                        $minutes = $program->total_teaching_minutes % 60;
                                    @endphp
                                    {{ $hours > 0 ? $hours . ' jam' : '' }} {{ $minutes > 0 ? $minutes . ' menit' : '' }}
                                </div>
                            </td>
                            <td class="fi-ta-cell p-0 first-of-type:ps-1 last-of-type:pe-1 sm:first-of-type:ps-3 sm:last-of-type:pe-3">
                                <div class="fi-ta-col-wrp px-3 py-4">
                                    {{ $program->siswas_count }} Siswa
                                </div>
                            </td>
                            <td class="fi-ta-cell p-0 first-of-type:ps-1 last-of-type:pe-1 sm:first-of-type:ps-3 sm:last-of-type:pe-3">
                                <div class="fi-ta-col-wrp px-3 py-4">
                                    @if ($program->replacement_sessions_count > 0)
                                        <span class="text-xs text-gray-500 dark:text-gray-400">
                                            Termasuk {{ $program->replacement_sessions_count }} sesi pengganti
                                        </span>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr class="fi-ta-row">
                            <td colspan="5" class="fi-ta-cell p-0 first-of-type:ps-1 last-of-type:pe-1 sm:first-of-type:ps-3 sm:last-of-type:pe-3">
                                <div class="fi-ta-col-wrp px-3 py-4 text-center text-gray-500">
                                    Guru ini belum memiliki jadwal mengajar.
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>

                @if ($this->programsWithTotals->isNotEmpty())
                    <tfoot class="bg-gray-50 dark:bg-white/5 border-t-2 border-gray-300 dark:border-white/10 ">
                        <tr class="text-base">
                            <td class="fi-ta-cell p-0" colspan="2">
                                <div class="fi-ta-col-wrp px-3 py-4 text-left font-bold text-gray-700 dark:text-gray-300">
                                    Total Keseluruhan : 
                                </div>
                            </td>
                            <td class="fi-ta-cell p-0" colspan="2">
                                <div class="fi-ta-col-wrp px-3 py-4 font-bold text-gray-900 dark:text-white">
                                    @php
                                        $totalHours = floor($this->grandTotalTeachingMinutes / 60);
                                        $totalMinutes = $this->grandTotalTeachingMinutes % 60;
                                        $hoursText = $totalHours > 0 ? "{$totalHours} jam" : '';
                                        $minutesText = $totalMinutes > 0 ? "{$totalMinutes} menit" : '';
                                        $totalTimeText = trim("{$hoursText} {$minutesText}");
                                    @endphp
                                    
                                    {{ $this->grandTotalSessions }} Pertemuan
                                    
                                    @if (!empty($totalTimeText))
                                        <span class="font-normal text-gray-500 dark:text-gray-400">/</span> {{ $totalTimeText }}
                                    @endif
                                </div>
                            </td>
                            <td class="fi-ta-cell p-0">
                                {{-- Kolom keterangan dibiarkan kosong --}}
                            </td>
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>
    </div>

    {{-- Card View untuk Mobile & Tablet (Hidden on Desktop) --}}
    <div class="lg:hidden space-y-4">
        @forelse ($this->programsWithTotals as $program)
            <div class="bg-white dark:bg-gray-900 rounded-xl shadow-sm ring-1 ring-gray-950/5 dark:ring-white/10 overflow-hidden">
                <div class="bg-gray-50 dark:bg-white/5 px-4 py-3 border-b border-gray-200 dark:border-white/5">
                    <h3 class="font-semibold text-gray-900 dark:text-white">
                        {{ $program->nama_program }}
                    </h3>
                </div>
                <div class="p-4 space-y-3">
                    <div class="flex justify-between items-center py-2 border-b border-gray-100 dark:border-white/5">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Jumlah Pertemuan</span>
                        <span class="font-medium text-gray-900 dark:text-white">{{ $program->total_sessions }} Pertemuan</span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b border-gray-100 dark:border-white/5">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Jam Mengajar</span>
                        <span class="font-medium text-gray-900 dark:text-white">
                            @php
                                $hours = floor($program->total_teaching_minutes / 60);
                                $minutes = $program->total_teaching_minutes % 60;
                            @endphp
                            {{ $hours > 0 ? $hours . ' jam' : '' }} {{ $minutes > 0 ? $minutes . ' menit' : '' }}
                        </span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b border-gray-100 dark:border-white/5">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Jumlah Siswa</span>
                        <span class="font-medium text-gray-900 dark:text-white">{{ $program->siswas_count }} Siswa</span>
                    </div>
                    @if ($program->replacement_sessions_count > 0)
                        <div class="pt-2">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-medium bg-blue-50 text-blue-700 dark:bg-blue-900/20 dark:text-blue-400">
                                Termasuk {{ $program->replacement_sessions_count }} sesi pengganti
                            </span>
                        </div>
                    @endif
                </div>
            </div>
        @empty
            <div class="bg-white dark:bg-gray-900 rounded-xl shadow-sm ring-1 ring-gray-950/5 dark:ring-white/10 p-8">
                <p class="text-center text-gray-500 dark:text-gray-400">
                    Guru ini belum memiliki jadwal mengajar.
                </p>
            </div>
        @endforelse

        {{-- Total Card untuk Mobile dengan Total Jam Mengajar --}}
        @if ($this->programsWithTotals->isNotEmpty())
            <div class="bg-primary-50 dark:bg-primary-900/20 rounded-xl shadow-sm ring-1 ring-primary-200 dark:ring-primary-800 overflow-hidden mt-6">
                <div class="bg-primary-100 dark:bg-primary-900/40 px-4 py-2 border-b border-primary-200 dark:border-primary-800">
                    <h4 class="font-bold text-gray-900 dark:text-white text-sm uppercase tracking-wide">
                        Total Keseluruhan
                    </h4>
                </div>
                <div class="p-4 space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Total Pertemuan</span>
                        <span class="font-bold text-primary-700 dark:text-primary-400 text-lg">
                            {{ $this->grandTotalSessions }} Pertemuan
                        </span>
                    </div>
                    @php
                        $totalHours = floor($this->grandTotalTeachingMinutes / 60);
                        $totalMinutes = $this->grandTotalTeachingMinutes % 60;
                        $hoursText = $totalHours > 0 ? "{$totalHours} jam" : '';
                        $minutesText = $totalMinutes > 0 ? "{$totalMinutes} menit" : '';
                        $totalTimeText = trim("{$hoursText} {$minutesText}");
                    @endphp
                    @if (!empty($totalTimeText))
                        <div class="flex justify-between items-center pt-2 border-t border-primary-200 dark:border-primary-800">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Total Jam Mengajar</span>
                            <span class="font-bold text-primary-700 dark:text-primary-400 text-lg">
                                {{ $totalTimeText }}
                            </span>
                        </div>
                    @endif
                </div>
            </div>
        @endif
    </div>
</x-filament-panels::page>