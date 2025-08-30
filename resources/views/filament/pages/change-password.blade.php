<x-filament-panels::page>
    <div class="text-sm text-gray-500 mb-4">
        Karena ini adalah login pertama Anda, harap ubah password sementara yang diberikan oleh admin untuk keamanan akun Anda.
    </div>
    <form wire:submit.prevent="submit">
        {{ $this->form }}
        <br><br>
        <div class="mt-10">
            <x-filament::button type="submit">
                Simpan Perubahan
            </x-filament::button>
        </div>
    </form>
</x-filament-panels::page>