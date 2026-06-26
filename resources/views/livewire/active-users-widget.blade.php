<div wire:poll.15s class="card bg-base-100 shadow-xl border border-base-200">
    <div class="card-body p-5">
        <h3 class="card-title text-base font-bold mb-4">
            User Aktif Saat Ini
            <span class="badge badge-sm">{{ $activeUsers->count() }}</span>
        </h3>

        <ul class="space-y-3">
            @forelse($activeUsers as $user)
                <li class="flex items-center gap-3">
                    <div class="avatar placeholder">
                        <div class="bg-neutral text-neutral-content rounded-full w-8 h-8 text-sm">
                            {{ substr($user->name, 0, 2) }}
                        </div>
                    </div>
                    <span class="text-sm font-medium">{{ $user->name }}</span>
                </li>
            @empty
                <li class="text-sm text-gray-500 italic">Tidak ada user lain.</li>
            @endforelse
        </ul>
    </div>
</div>