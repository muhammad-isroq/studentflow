<div>
    @if($isOpen)
        <div class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto bg-black/50">
            <div class="bg-white dark:bg-gray-900 rounded-xl shadow-xl max-w-md w-full p-6 border dark:border-gray-700">
                <h3 class="text-lg font-bold mb-4 dark:text-white">Bayar Banyak Bulan Sekaligus</h3>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium mb-1 dark:text-gray-300">Jumlah Bulan</label>
                        <input type="number" wire:model="monthCount" min="1" 
                            class="w-full rounded-lg border-gray-300 dark:bg-gray-800 dark:border-gray-600 dark:text-white focus:ring-primary-500">
                    </div>
                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <button wire:click="closeModal" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200">
                        Batal
                    </button>
                    <button wire:click="processPayment" class="px-4 py-2 text-sm font-medium text-white bg-success-600 rounded-lg hover:bg-success-700">
                        Proses Pembayaran
                    </button>
                </div>
            </div>
        </div>
    @endif

    @script
<script>
    
    $wire.on('print-collective-receipt', (event) => {
        // Livewire 3 mengirimkan data di dalam properti pertama dari array
        const data = Array.isArray(event) ? event[0] : event;
        const ids = data.billIds;

        console.log("Mencoba mencetak Bill IDs:", ids);

        if (ids && ids.length > 0) {
            const idString = ids.join(',');
            const url = `/print-receipt-collective?ids=${idString}`;
            
            // Buka tab baru
            window.open(url, '_blank');
        } else {
            console.error("Tidak ada ID tagihan yang diterima untuk dicetak.");
        }
    });
</script>
@endscript
</div>