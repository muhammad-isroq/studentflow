<x-filament-panels::page>
    <div class="p-6 bg-white rounded-xl shadow border border-gray-200">
        {{ $this->form }}
    </div>

    @if($this->staffId)
        <div class="mt-6">
            {{ $this->table }}
        </div>
    @endif
</x-filament-panels::page>