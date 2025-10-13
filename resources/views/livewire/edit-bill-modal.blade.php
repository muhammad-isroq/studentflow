<div>
    <x-filament::modal
        id="edit-bill-modal"
        wire:model="isOpen"
        width="2xl"
        sticky-header
    >
        <x-slot name="heading">
            Edit Tagihan
        </x-slot>

        <x-slot name="description">
            Perbarui informasi tagihan siswa
        </x-slot>

        @if($bill)
            <form wire:submit="save" class="space-y-6">
                {{ $this->form }}

                <div class="flex justify-end gap-3 mt-6">
                    <x-filament::button
                        color="gray"
                        wire:click="closeModal"
                        type="button"
                    >
                        Batal
                    </x-filament::button>

                    <x-filament::button
                        type="submit"
                        wire:loading.attr="disabled"
                    >
                        <span wire:loading.remove wire:target="save">Simpan</span>
                        <span wire:loading wire:target="save">Menyimpan...</span>
                    </x-filament::button>
                </div>
            </form>
        @endif
    </x-filament::modal>
</div>