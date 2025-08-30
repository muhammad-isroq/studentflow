<x-filament-widgets::widget>
    <x-filament::section>
        <div class="flex items-center gap-x-2">
            <div>
                <h2 class="text-lg font-bold tracking-tight">
                    <strong>Late Bill Warning!</strong>
                </h2>
                <p class="text-sm text-gray-500">
                    There are <strong>{{ $this->overdueBills->count() }} students</strong> with overdue tuition fee payments.
                </p>
            </div>
        </div>

        <div class="mt-4 border-t border-gray-200 pt-4">
            <h3 class="font-semibold">List of Students:</h3>
            <ul class="list-disc list-inside mt-2 text-sm space-y-1">
                @foreach($this->overdueBills->take(30) as $bill)
                    <li>
                        <strong>{{ $bill->siswa->nama ?? 'Siswa tidak ditemukan' }}</strong>
                        (Due Date: {{ \Carbon\Carbon::parse($bill->due_date)->format('d M Y') }})
                    </li>
                @endforeach
                @if($this->overdueBills->count() > 30)
                    <li>Dan {{ $this->overdueBills->count() - 5 }} other students...</li>
                @endif
            </ul>
        </div>
        
    </x-filament::section>
</x-filament-widgets::widget>