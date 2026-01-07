<x-filament-panels::page>

    {{-- Pilihan Tabs --}}
    <div class="mb-6">
        <h3 class="text-sm font-medium text-gray-500 mb-2">Select Unit:</h3>
        
        <div class="flex flex-wrap gap-2 items-center">
            @php
                $assessments = \App\Models\Assessment::where('program_id', $this->program_id)
                                ->orderBy('order', 'asc')
                                ->get();
            @endphp

            {{-- 1. Loop Tombol Unit Test --}}
            @foreach($assessments as $test)
                <button 
                    wire:click="$set('activeAssessmentId', {{ $test->id }})"
                    type="button"
                    class="px-4 py-2 rounded-lg text-sm font-semibold transition-all duration-200 border
                    {{ $activeAssessmentId == $test->id 
                       ? 'bg-primary-600 text-white border-primary-600 shadow-md transform scale-105' 
                       : 'bg-white text-gray-600 border-gray-200 hover:bg-gray-50' 
                    }}"
                >
                    {{ $test->name }}
                </button>
            @endforeach

            {{-- Separator --}}
            <div class="h-6 w-px bg-gray-300 mx-2"></div>

            {{-- 2. Tombol SUMMARY --}}
            <button 
                wire:click="$set('activeAssessmentId', 'summary')"
                type="button"
                class="px-4 py-2 rounded-lg text-sm font-bold transition-all duration-200 border flex items-center gap-2
                {{ $activeAssessmentId === 'summary' 
                   ? 'bg-emerald-600 text-white border-emerald-600 shadow-md transform scale-105' 
                   : 'bg-white text-emerald-700 border-emerald-200 hover:bg-emerald-50' 
                }}"
            >
                <x-heroicon-m-chart-bar class="w-4 h-4"/>
                SUMMARY / AVERAGE
            </button>
        </div>
    </div>


    <div wire:loading.class="opacity-50 pointer-events-none">
  
        <div wire:key="grading-table-{{ $activeAssessmentId }}">
            {{ $this->table }}
        </div>
    </div>
    
    <div wire:loading wire:target="activeAssessmentId" class="fixed bottom-4 right-4 bg-gray-900 text-white px-4 py-2 rounded-lg shadow-lg text-xs z-50">
        Memuat data...
    </div>

</x-filament-panels::page>