<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Info Guru --}}
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h2 class="text-xl font-semibold mb-4">Informasi Guru</h2>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <span class="text-gray-600 dark:text-gray-400">Nama:</span>
                    <span class="font-medium ml-2">{{ $record->nama_guru }}</span>
                </div>
                <div>
                    <span class="text-gray-600 dark:text-gray-400">No HP:</span>
                    <span class="font-medium ml-2">{{ $record->no_hp }}</span>
                </div>
            </div>
        </div>

        {{-- Rekap Program --}}
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h2 class="text-xl font-semibold mb-4">Rekap Program Mengajar</h2>
            
            @if($programsWithCounts->isEmpty())
                <p class="text-gray-500">Belum ada program yang diajar.</p>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead class="border-b dark:border-gray-700">
                            <tr>
                                <th class="pb-3 font-semibold">Program</th>
                                <th class="pb-3 font-semibold text-right">Jumlah Sesi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($programsWithCounts as $program)
                                <tr class="border-b dark:border-gray-700 last:border-0">
                                    <td class="py-3">{{ $program->nama_program ?? 'N/A' }}</td>
                                    <td class="py-3 text-right">{{ $program->class_sessions_count }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="border-t-2 dark:border-gray-700">
                            <tr>
                                <td class="pt-3 font-semibold">Total</td>
                                <td class="pt-3 text-right font-semibold">
                                    {{ $programsWithCounts->sum('class_sessions_count') }}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            @endif
        </div>
    </div>
</x-filament-panels::page>