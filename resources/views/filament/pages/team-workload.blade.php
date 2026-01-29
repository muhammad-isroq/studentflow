<x-filament-panels::page>
    <div class="p-6 bg-white rounded-xl shadow-sm border border-gray-200 dark:bg-gray-900 dark:border-gray-800">
        {{ $this->form }}
    </div>

    @if($this->staffId)
        <div class="mt-4">
            <h3 class="text-lg font-bold mb-2 text-gray-700 dark:text-gray-300">
                Tasks for This Week
            </h3>
            {{ $this->table }}
        </div>
    @else
        <div class="mt-6 text-center p-10 bg-gray-50 rounded-xl border border-dashed border-gray-300 text-gray-500">
            <x-heroicon-o-user-group class="w-12 h-12 mx-auto mb-2 text-gray-400"/>
            <p>Please select a staff member above to view their workload for this week.</p>
        </div>
    @endif
</x-filament-panels::page>