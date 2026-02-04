<x-filament::widget>
    <x-filament::section>

        <div class="mb-4">
            <h2 class="text-lg font-bold text-gray-800 dark:text-white">
                👋 Halo, {{ auth()->user()->name }}!
            </h2>
            <p class="text-sm text-gray-500">
                Ayo selesaikan target harimu!
            </p>
        </div>


        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">


            <div class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-lg border border-blue-100 dark:border-blue-800">
                <div class="flex justify-between items-end mb-2">
                    <div>
                        <span class="text-xs font-bold uppercase tracking-wider text-blue-600 dark:text-blue-400">Target Hari Ini</span>
                        <span class="text-xs font-bold uppercase tracking-wider text-red-600 dark:text-red-400">(tenggat waktu hari ini)</span>
                        <div class="text-2xl font-bold text-gray-800 dark:text-gray-200">
                            {{ $percentageToday }}%
                        </div>
                    </div>
                    <span class="text-xs text-gray-500">
                        {{ $completedToday }} / {{ $totalToday }} Selesai
                    </span>
                </div>

                <progress 
                    class="progress progress-warning w-full h-3" 
                    value="{{ $percentageToday }}" 
                    max="100">
                </progress>
            </div>


            <div class="p-4 border-l-0 md:border-l border-gray-100 dark:border-gray-700">
                <div class="flex justify-between items-end mb-2">
                    <div>
                        <span class="text-xs font-bold uppercase tracking-wider text-gray-500">Total Progress</span>
                        <div class="text-xl font-semibold text-gray-700 dark:text-gray-300">
                            {{ $percentage }}%
                        </div>
                    </div>
                    <span class="text-xs text-gray-400">
                        {{ $completed }} / {{ $total }} Tasks
                    </span>
                </div>

                <progress 
                    class="progress progress-primary w-full h-2" 
                    value="{{ $percentage }}" 
                    max="100">
                </progress>
            </div>

        </div>
    </x-filament::section>
</x-filament::widget>