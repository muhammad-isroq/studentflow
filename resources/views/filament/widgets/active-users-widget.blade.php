@php use Illuminate\Support\Facades\Storage; @endphp
<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            Currently Active Users
            <span class="text-sm font-normal text-gray-500">({{ $activeUsers->count() }})</span>
        </x-slot>

        <ul class="space-y-3">
            @forelse($activeUsers as $user)
                <li class="flex items-center gap-3">
                    {{-- Avatar: foto jika ada, fallback ke inisial huruf --}}
                    @if($user->photo)
                        <img
                            src="{{ Storage::url($user->photo) }}"
                            alt="{{ $user->name }}"
                            class="w-8 h-8 rounded-full object-cover"
                        >
                    @else
                        <div class="w-8 h-8 rounded-full bg-primary-500 flex items-center justify-center text-white text-sm font-bold">
                            {{ substr($user->name, 0, 2) }}
                        </div>
                    @endif

                    <div>
                        <p class="text-sm font-medium">{{ $user->name }}</p>
                        <p class="text-xs text-gray-400">
                            Active {{ $user->last_activity->diffForHumans() }}
                        </p>
                    </div>
                </li>
            @empty
                <li class="text-sm text-gray-500 italic">No other users are currently active.</li>
            @endforelse
        </ul>
    </x-filament::section>
</x-filament-widgets::widget>