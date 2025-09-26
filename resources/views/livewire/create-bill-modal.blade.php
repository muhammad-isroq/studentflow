<div>
    @if ($show)
        <div 
            class="fixed inset-0 z-40 flex items-center justify-center bg-gray-900 bg-opacity-50"
            x-data @keydown.escape.window="$wire.closeModal()"
        >
            <div 
                class="w-full max-w-lg bg-white dark:bg-gray-800 rounded-lg shadow-xl p-6"
                @click.outside="$wire.closeModal()"
            >
                <form wire:submit.prevent="save">
                    {{-- Judul Modal Dinamis --}}
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                        {{ $isCreatingNewType ? 'Buat Jenis Tagihan Baru' : 'Tambah Tagihan: ' . $paymentTypeName }}
                    </h3>

                    <div class="space-y-4">
                        {{-- Tampilkan field ini HANYA jika membuat tipe baru --}}
                        @if ($isCreatingNewType)
                            <div>
                                <label for="paymentTypeName" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nama Jenis Tagihan</label>
                                <input type="text" wire:model="paymentTypeName" id="paymentTypeName" placeholder="Contoh: Uang Buku, Pendaftaran Ulang" class="mt-1 block w-full fi-input-field rounded-lg shadow-sm">
                                @error('paymentTypeName') <span class="text-danger-500 text-sm">{{ $message }}</span> @enderror
                            </div>
                        @endif

                        {{-- Amount Input --}}
                        <div>
                            <label for="amount" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Jumlah</label>
                            <input type="number" wire:model="amount" id="amount" class="mt-1 block w-full fi-input-field rounded-lg shadow-sm">
                            @error('amount') <span class="text-danger-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        {{-- Due Date Input --}}
                        <div>
                            <label for="dueDate" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Jatuh Tempo</label>
                            <input type="date" wire:model="dueDate" id="dueDate" class="mt-1 block w-full fi-input-field rounded-lg shadow-sm">
                            @error('dueDate') <span class="text-danger-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end space-x-3">
                        <x-filament::button type="button" color="gray" wire:click="closeModal">
                            Batal
                        </x-filament::button>
                        <x-filament::button type="submit">
                            Simpan Tagihan
                        </x-filament::button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>