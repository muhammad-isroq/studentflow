<x-filament-panels::page>
    <div class="flex items-center space-x-4 mb-6 p-4 bg-white dark:bg-gray-800 rounded-xl shadow">
        <div class="w-1/2">
            <label for="month" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Bulan</label>
            <select id="month" wire:model.live="selectedMonth" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm rounded-md">
                @foreach($months as $num => $name)
                    <option value="{{ $num }}">{{ $name }}</option>
                @endforeach
            </select>
        </div>
        <div class="w-1/2">
            <label for="year" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Tahun</label>
            <select id="year" wire:model.live="selectedYear" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm rounded-md">
                @foreach($years as $year)
                    <option value="{{ $year }}">{{ $year }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="fi-ta-ctn overflow-hidden bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 sm:rounded-xl">
        <table class="fi-ta-table w-full table-auto divide-y divide-gray-200 text-start dark:divide-white/5">
            <thead class="bg-gray-50 dark:bg-white/5">
                <tr class="text-sm">
                    <th class="fi-ta-header-cell px-3 py-3.5 sm:px-6 text-left">
                        Nama Program
                    </th>
                    <th class="fi-ta-header-cell px-3 py-3.5 sm:px-6 text-left">
                        Jumlah Pertemuan 
                    </th>
                    <th class="fi-ta-header-cell px-3 py-3.5 sm:px-6 text-left">
                        Jumlah Jam Mengajar
                    </th>
                    <th class="fi-ta-header-cell px-3 py-3.5 sm:px-6 text-left">
                        Jumlah Siswa dikelas
                    </th>
                    <th class="fi-ta-header-cell px-3 py-3.5 sm:px-6 text-left">
                        Keterangan
                    </th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 whitespace-nowrap dark:divide-white/5">
                @forelse ($this->programsWithTotals as $program)
                    <tr class="fi-ta-row">
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
                        <td class="fi-ta-cell p-0 sm:first-of-type:ps-3 sm:last-of-type:pe-3">
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
                        <td colspan="3" class="fi-ta-cell p-0 first-of-type:ps-1 last-of-type:pe-1 sm:first-of-type:ps-3 sm:last-of-type:pe-3">
                            <div class="fi-ta-col-wrp px-3 py-4 text-center text-gray-500">
                                Guru ini belum memiliki jadwal mengajar.
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>

             @if ($this->programsWithTotals->isNotEmpty())
                <tfoot class="bg-gray-50 dark:bg-white/5 border-t-2 border-gray-300 dark:border-white/10">
                    <tr class="text-base">
                        <td class="fi-ta-cell p-0 first-of-type:ps-1 last-of-type:pe-1 sm:first-of-type:ps-3 sm:last-of-type:pe-3">
                            <div class="fi-ta-col-wrp px-3 py-4 text-left font-bold text-gray-700 dark:text-gray-300">
                                Total Keseluruhan =
                            </div>
                        </td>
                        <td class="fi-ta-cell p-0 first-of-type:ps-1 last-of-type:pe-1 sm:first-of-type:ps-3 sm:last-of-type:pe-3">
                            <div class="fi-ta-col-wrp px-3 py-4 font-bold text-gray-900 dark:text-white">
                                {{ $this->grandTotalSessions }} Pertemuan
                            </div>
                        </td>
                        <td class="fi-ta-cell p-0 first-of-type:ps-1 last-of-type:pe-1 sm:first-of-type:ps-3 sm:last-of-type:pe-3">
                            {{-- Kolom keterangan dibiarkan kosong untuk total --}}
                        </td>
                    </tr>
                </tfoot>
            @endif
        </table>
    </div>
</x-filament-panels::page>

