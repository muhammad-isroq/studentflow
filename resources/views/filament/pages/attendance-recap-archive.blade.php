<x-filament-panels::page>

    <div class="mb-4 max-w-xs">
        <x-filament::input.wrapper>
            <x-filament::input.select wire:model.live="selectedSemester">
                <option value="">Pilih Semester...</option>
                @foreach($this->getSemesterOptions() as $semester)
                    <option value="{{ $semester }}">{{ $semester }}</option>
                @endforeach
            </x-filament::input.select>
        </x-filament::input.wrapper>
    </div>

    @if($recapData->isNotEmpty())
        @php
            // Ambil data dari rekaman pertama (semua data programnya sama)
            $arsip = $recapData->first(); 
        @endphp
        <div class="mb-6 rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white">Detail Arsip Program</h3>
            <dl class="mt-4 grid grid-cols-1 gap-x-4 gap-y-4 sm:grid-cols-2 md:grid-cols-3">
                <div class="sm:col-span-1">
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Nama Program</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $arsip->nama_program }}</dd>
                </div>
                <div class="sm:col-span-1">
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Guru</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $arsip->guru->nama_guru ?? 'N/A' }}</dd>
                </div>
                <div class="sm:col-span-1">
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Ruangan</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $arsip->nama_ruangan ?? 'N/A' }}</dd>
                </div>
                <div class="sm:col-span-1">
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Jadwal</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $arsip->jadwal_program ?? 'N/A' }}</dd>
                </div>
                <div class="sm:col-span-1">
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Jam Pelajaran</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $arsip->jam_pelajaran ?? 'N/A' }}</dd>
                </div>
            </dl>
        </div>
    @endif

    <div class="overflow-x-auto rounded-lg border border-gray-200 shadow-sm dark:border-gray-700">
        <table class="min-w-full divide-y divide-gray-200 bg-white dark:divide-gray-700 dark:bg-gray-800">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-300">
                        Nama Siswa
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-300">
                        Total Hadir
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-300">
                        Total Sesi
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-300">
                        Persentase
                    </th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                @forelse ($recapData as $recap)
                    <tr>
                        <td class="whitespace-nowrap px-6 py-4 text-sm font-medium text-gray-900 dark:text-white">
                            {{ $recap->siswa->nama ?? 'Siswa Dihapus' }}
                        </td>
                        <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                            {{ $recap->total_hadir }}
                        </td>
                        <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                            {{ $recap->total_sesi }}
                        </td>
                        <td class="whitespace-nowrap px-6 py-4 text-sm font-medium dark:text-white">
                            {{ $recap->percentage }}%
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                            Tidak ada data rekap yang diarsip untuk semester ini.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</x-filament-panels::page>