<div class="space-y-6">

<div class="bg-white dark:bg-gray-900 shadow rounded-lg p-6 border-l-4 {{ $this->totalArrears > 0 ? 'border-red-500' : 'border-green-500' }}">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-sm font-medium text-gray-500 dark:text-gray-400">
                    Total Tunggakan (Sejak Juli 2025)
                </h2>
                <div class="mt-1 flex items-baseline">
                    <p class="text-3xl font-bold {{ $this->totalArrears > 0 ? 'text-red-600 dark:text-red-500' : 'text-green-600 dark:text-green-500' }}">
                        Rp {{ number_format($this->totalArrears, 0, ',', '.') }}
                    </p>
                    @if($this->totalArrears > 0)
                        <span class="ml-2 text-sm font-medium text-gray-500">
                            Status: <span class="text-red-600">Belum Lunas</span>
                        </span>
                    @else
                        <span class="ml-2 text-sm font-medium text-gray-500">
                            Status: <span class="text-green-600">Aman / Lunas</span>
                        </span>
                    @endif
                </div>
            </div>
            
            {{-- Ikon Visual --}}
            <div class="p-3 rounded-full {{ $this->totalArrears > 0 ? 'bg-red-100 text-red-600 dark:bg-red-900/30' : 'bg-green-100 text-green-600 dark:bg-green-900/30' }}">
                @if($this->totalArrears > 0)
                    {{-- Icon Exclamation/Warning --}}
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                @else
                    {{-- Icon Check/Aman --}}
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                @endif
            </div>
        </div>
    </div>

<div class="max-w-xs mb-4">
    <label for="selectedYear" class="fi-fo-field-wrp-label text-sm font-medium leading-6 text-gray-950 dark:text-white">
        Tampilkan Tahun
    </label>
    
    <select
        wire:model.live="selectedYear"
        id="selectedYear"
        class="fi-input-wrapper block w-full border-none bg-white py-1.5 pe-3 ps-3 text-base text-gray-950 shadow-sm ring-1 transition duration-75 focus:ring-2 disabled:bg-gray-50 disabled:text-gray-500 disabled:ring-gray-200 dark:bg-white/5 dark:text-white dark:ring-white/10 dark:disabled:bg-gray-900 dark:disabled:text-gray-400 dark:disabled:ring-gray-700 fi-select-input rounded-lg ring-gray-950/10 focus:ring-primary-600 dark:ring-gray-700 dark:focus:ring-primary-500"
    >
        @php
            // Ambil tahun dari tagihan tertua siswa ini
            $firstBillYear = $this->getOwnerRecord()->bills()->min(DB::raw('YEAR(due_date)'));
            $startYear = $firstBillYear ? (int)$firstBillYear : (int)date('Y') - 1;
            $endYear = (int)date('Y') + 1; // Tampilkan satu tahun ke depan
        @endphp
        
        @for ($year = $endYear; $year >= $startYear; $year--)
            <option value="{{ $year }}">{{ $year }}</option>
        @endfor
    </select>
</div>

    {{-- SPP Bulanan --}}
    <div class="bg-white dark:bg-gray-900 shadow rounded-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                SPP Bulanan - {{ $selectedYear }}
            </h3>
            <p class="text-sm text-gray-600 dark:text-gray-400">
                Riwayat pembayaran SPP per bulan
            </p>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-800">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Bulan
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Jumlah
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Jatuh Tempo
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Status
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Tanggal Bayar
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Aksi
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($this->getMonthlySppBills() as $monthData)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                {{ $monthData['month'] }} {{ $monthData['year'] }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white font-semibold">
                                Rp {{ number_format($monthData['amount'], 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                @if($monthData['due_date'])
                                    {{ $monthData['due_date']->format('d M Y') }}
                                @else
                                    <span class="text-gray-400">Belum dijadwalkan</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($monthData['status'] === 'paid')
                                    <x-filament::badge color="success">Lunas</x-filament::badge>
                                @elseif($monthData['status'] === 'unpaid')
                                    @if($monthData['due_date'] && $monthData['due_date']->isPast())
                                        <x-filament::badge color="danger">Terlambat</x-filament::badge>
                                    @else
                                        <x-filament::badge color="warning">Belum Bayar</x-filament::badge>
                                    @endif
                                @else
                                    <x-filament::badge color="gray">Belum Dibuat</x-filament::badge>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                @if($monthData['paid_at'])
                                    {{ $monthData['paid_at']->format('d M Y, H:i') }}
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                @if($monthData['bill'])
                                    @if($monthData['status'] !== 'paid')
                                        <button 
                                            wire:click="markAsPaid({{ $monthData['bill']->id }})"
                                            class="text-green-600 hover:text-green-900 text-xs bg-green-100 hover:bg-green-200 px-2 py-1 rounded"
                                        >
                                            Tandai Lunas
                                        </button>
                                    @endif
                                    <button 
                                        wire:click="editBill({{ $monthData['bill']->id }})"
                                        class="text-blue-600 hover:text-blue-900 text-xs bg-blue-100 hover:bg-blue-200 px-2 py-1 rounded"
                                    >
                                        Edit
                                    </button>
                                    
                                    
                                    <button 
                                        wire:click="deleteBill({{ $monthData['bill']->id }})"
                                        wire:confirm="Anda yakin ingin menghapus tagihan ini? Data tidak dapat dikembalikan."
                                        class="text-red-600 hover:text-red-900 text-xs bg-red-100 hover:bg-red-200 px-2 py-1 rounded"
                                    >
                                        Hapus
                                    </button>

                                @else
                                    <button 
                                        wire:click="generateSppBill({{ $monthData['month_number'] }}, {{ $monthData['year'] }})"
                                        class="text-indigo-600 hover:text-indigo-900 text-xs bg-indigo-100 hover:bg-indigo-200 px-2 py-1 rounded"
                                    >
                                        Buat Tagihan
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- Tagihan Lainnya --}}
    @foreach($this->getNonSppBills() as $paymentTypeName => $bills)
        <div class="bg-white dark:bg-gray-900 shadow rounded-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                    {{ $paymentTypeName }}
                </h3>
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    {{ $bills->count() }} tagihan
                </p>
            </div>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-800">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Jumlah
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Jatuh Tempo
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Status
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Tanggal Bayar
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Dibuat
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($bills as $bill)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white font-semibold">
                                    Rp {{ number_format($bill->amount, 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                    {{ $bill->due_date ? $bill->due_date->format('d M Y') : '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($bill->status === 'paid')
                                        <x-filament::badge color="success">Lunas</x-filament::badge>
                                    @elseif($bill->status === 'unpaid')
                                        @if($bill->due_date && $bill->due_date->isPast())
                                            <x-filament::badge color="danger">Terlambat</x-filament::badge>
                                        @else
                                            <x-filament::badge color="warning">Belum Bayar</x-filament::badge>
                                        @endif
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    {{ $bill->paid_at ? $bill->paid_at->format('d M Y, H:i') : '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    {{ $bill->created_at->format('d M Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                    @if($bill->status !== 'paid')
                                        <button 
                                            wire:click="markAsPaid({{ $bill->id }})"
                                            class="text-green-600 hover:text-green-900 text-xs bg-green-100 hover:bg-green-200 px-2 py-1 rounded"
                                        >
                                            Tandai Lunas
                                        </button>
                                    @endif
                                    <button 
                                        wire:click="editBill({{ $bill->id }})"
                                        class="text-blue-600 hover:text-blue-900 text-xs bg-blue-100 hover:bg-blue-200 px-2 py-1 rounded"
                                    >
                                        Edit
                                    </button>

                                   
                                    <button 
                                        wire:click="deleteBill({{ $bill->id }})"
                                        wire:confirm="Anda yakin ingin menghapus tagihan ini? Data tidak dapat dikembalikan."
                                        class="text-red-600 hover:text-red-900 text-xs bg-red-100 hover:bg-red-200 px-2 py-1 rounded"
                                    >
                                        Hapus
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div class="px-6 py-3 bg-gray-50 dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700">
                <button 
                    wire:click="createBill('{{ $paymentTypeName }}')"
                    class="text-sm bg-blue-600 hover:bg-blue-700 text-white px-3 py-1.5 rounded-md font-medium"
                >
                    Tambah {{ $paymentTypeName }}
                </button>
            </div>
        </div>
    @endforeach

    {{-- Button untuk tambah payment type baru (Tidak diubah) --}}
    {{-- <div class="bg-white dark:bg-gray-900 shadow rounded-lg overflow-hidden p-6">
        <button 
            wire:click="createNewPaymentTypeBill"
            class="w-full border-2 border-dashed border-gray-300 dark:border-gray-700 rounded-lg p-4 text-center hover:border-gray-400 dark:hover:border-gray-600 transition-colors"
        >
            <div class="text-gray-600 dark:text-gray-400">
                <svg class="mx-auto h-8 w-8 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                <span class="text-sm font-medium">Tambah Jenis Tagihan Baru</span>
            </div>
        </button>
    </div> --}}
    <livewire:create-bill-modal />
    <livewire:edit-bill-modal />
</div>
