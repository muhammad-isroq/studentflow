<x-filament::widget>
    <x-filament::section>
        {{-- Layout DaisyUI --}}
        <div class="flex items-center gap-6">
            
            {{-- Bagian Kiri: Teks Informasi --}}
            <div class="flex-1">
                <h2 class="text-lg font-bold text-gray-800 dark:text-white">
                    👋 Halo, {{ auth()->user()->name }}!
                </h2>
                <p class="text-sm text-gray-500 mt-1">
                    Kamu telah menyelesaikan <span class="font-bold text-primary-600">{{ $completed }}</span> 
                    dari <span class="font-bold">{{ $total }}</span> tugasmu.
                </p>
            </div>

            {{-- Bagian Kanan: Progress Bar DaisyUI --}}
            <div class="flex-1 max-w-md">
                <div class="flex justify-between text-xs mb-1 font-semibold">
                    <span>Progress</span>
                    <span>{{ $percentage }}%</span>
                </div>
                
                {{-- KOMPONEN DAISY UI: Progress Bar --}}
                <progress 
                    class="progress progress-primary w-full h-3" 
                    value="{{ $percentage }}" 
                    max="100">
                </progress>
            </div>

        </div>
    </x-filament::section>
</x-filament::widget>