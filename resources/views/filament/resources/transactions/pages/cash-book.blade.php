<x-filament-panels::page>
    {{ $this->form }}

    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
        <div class="p-4 bg-white rounded-xl shadow border border-gray-200 dark:bg-gray-800 dark:border-gray-700">
            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Pemasukan (Periode Ini)</h3>
            <p class="text-2xl font-bold text-success-600">
                Rp {{ number_format($this->stats['income'], 0, ',', '.') }}
            </p>
        </div>

        <div class="p-4 bg-white rounded-xl shadow border border-gray-200 dark:bg-gray-800 dark:border-gray-700">
            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Pengeluaran (Periode Ini)</h3>
            <p class="text-2xl font-bold text-danger-600">
                Rp {{ number_format($this->stats['expense'], 0, ',', '.') }}
            </p>
        </div>

        <div class="p-4 bg-white rounded-xl shadow border border-gray-200 dark:bg-gray-800 dark:border-gray-700">
            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Selisih (Periode Ini)</h3>
            <p class="text-2xl font-bold {{ $this->stats['balance_period'] >= 0 ? 'text-primary-600' : 'text-danger-600' }}">
                Rp {{ number_format($this->stats['balance_period'], 0, ',', '.') }}
            </p>
        </div>

        <div class="p-4 bg-gray-50 rounded-xl shadow border border-gray-300 dark:bg-gray-900 dark:border-gray-600">
            <h3 class="text-sm font-bold text-gray-800 dark:text-gray-200 uppercase">💰 Total Kas Saat Ini</h3>
            <p class="text-2xl font-bold text-gray-900 dark:text-white">
                Rp {{ number_format($this->stats['current_balance'], 0, ',', '.') }}
            </p>
        </div>
    </div>

    {{ $this->table }}
</x-filament-panels::page>