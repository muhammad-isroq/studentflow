<x-filament-panels::page>
    <div class="flex items-center justify-between p-4 bg-white border rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-700">
        
        <div class="flex items-center gap-3">
            <div class="p-2 bg-red-100 rounded-full dark:bg-red-900">
                <x-heroicon-o-banknotes class="w-6 h-6 text-red-600 dark:text-red-400" />
            </div>
            <div>
                <h2 class="text-sm font-medium text-gray-500 dark:text-gray-400">
                    Total Tunggakan (Sesuai Filter)
                </h2>
                <p class="text-2xl font-bold text-red-600 dark:text-red-400">
                    Rp {{ number_format($this->totalTunggakan, 0, ',', '.') }}
                </p>
            </div>
        </div>

        <div class="text-sm text-right text-gray-500">
            *Angka berubah otomatis<br>saat filter diganti.
        </div>
    </div>

    {{ $this->table }}
</x-filament-panels::page>