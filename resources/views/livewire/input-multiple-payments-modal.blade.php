<div>
    <x-filament::modal id="input-multiple-payments-modal" wire:model="show" width="4xl">
        <x-slot name="heading">
            Input Pembayaran Serentak (Buku, SPP, Registrasi, dll)
        </x-slot>

        <form wire:submit="save">
            {{ $this->form }}

            <div class="mt-6 flex justify-end gap-3">
                <x-filament::button color="gray" wire:click="closeModal">
                    Batal
                </x-filament::button>
                <x-filament::button type="submit" color="primary">
                    Simpan & Lunasi
                </x-filament::button>
            </div>
        </form>
    </x-filament::modal>
</div>