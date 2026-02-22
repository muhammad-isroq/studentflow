<x-filament-panels::page>
    <div class="space-y-6">
        @if($this->record->isAccessExpired())
            <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-md shadow-sm animate-pulse">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-bold text-red-800">READ-ONLY MODE</h3>
                        <p class="text-xs text-red-700 mt-1">The submission deadline (over 7 days) has expired 😱. You can only view the data, Please contact staff to reactivate editing access.</p>
                    </div>
                </div>
            </div>
        @endif

        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 px-6 py-4 border-b border-gray-200">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between space-y-3 lg:space-y-0">
                    <div>
                        <h1 class="text-xl font-bold text-gray-900 ">Attendance list</h1>
                        @if(!$this->record->isAccessExpired())
                            <p class="text-sm text-gray-600 mt-1">Click on the status column to change student attendance.</p>
                        @else
                            <p class="text-sm text-amber-600 mt-1 font-medium">Viewing historical attendance data.</p>
                        @endif
                    </div>
                    
                    <div class="flex flex-wrap gap-3">
                        <div class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                            {{ $this->record->program->nama_program }}
                        </div>
                        
                        <div class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a4 4 0 118 0v4m-4 4v6m-4 0H4a1 1 0 01-1-1V10a1 1 0 011-1h2V7a4 4 0 118 0v2h2a1 1 0 011 1v7a1 1 0 01-1 1h-2"></path>
                            </svg>
                            {{ $this->record->session_date->format('d M Y') }}
                        </div>
                        
                        <div class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5 0a4 4 0 11-8-0"></path>
                            </svg>
                            {{ $this->record->program->siswas->count() }} Siswa
                        </div>
                    </div>
                </div>
            </div>
            
            @if(!$this->record->isAccessExpired())
                <div class="bg-gray-50 px-6 py-3 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-4">
                            <span class="text-sm text-gray-600 font-medium">Quick action:</span>
                            <button
                                type="button"
                                onclick="confirmSetAllPresent()"
                                class="inline-flex items-center px-3 py-2 text-sm font-medium text-white bg-success-600 hover:bg-success-700 rounded-lg transition-colors duration-150"
                            >
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                All Present
                            </button>
                            
                            <button
                                type="button"
                                onclick="confirmResetAttendance()"
                                class="inline-flex items-center px-3 py-2 text-sm font-medium text-white bg-danger-600 hover:bg-danger-700 rounded-lg transition-colors duration-150"
                            >
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                </svg>
                                Reset
                            </button>
                        </div>
                        
                        <div class="hidden md:flex items-center space-x-4 text-sm">
                            @php $stats = $this->getAttendanceStats(); @endphp
                            <div class="flex items-center">
                                <div class="w-4 h-4 bg-green-400 rounded-full mr-2"></div>
                                <span class="text-gray-600">Present: <span class="font-semibold text-green-700">{{ $stats['hadir'] }}</span></span>
                            </div>
                            <div class="flex items-center">
                                <div class="w-4 h-4 bg-red-400 rounded-full mr-2"></div>
                                <span class="text-gray-600">Alpha: <span class="font-semibold text-red-700">{{ $stats['absen'] }}</span></span>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
            
            <div class="overflow-hidden">
                {{ $this->table }}
            </div>
        </div>

        <div class="flex flex-col sm:flex-row justify-between items-center space-y-3 sm:space-y-0 bg-white rounded-lg shadow px-6 py-4">
            <div class="flex items-center text-sm text-gray-600">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                @if(!$this->record->isAccessExpired())
                    Status changes are automatically saved
                @else
                    This session is locked for editing.
                @endif
            </div>
            
            <div class="flex space-x-3">
                <button
                    type="button"
                    @if(!$this->record->isAccessExpired())
                        onclick="confirmSaveAll()"
                    @else
                        onclick="window.location.href='{{ App\Filament\Pages\ProgramSchedule::getUrl(['program' => $this->record->program_id]) }}'"
                    @endif
                    class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition-colors duration-150"
                >
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 15l-3-3m0 0l3-3m-3 3h8M3 12a9 9 0 1118 0 9 9 0 01-18 0z"></path>
                    </svg>
                    {{ $this->record->isAccessExpired() ? 'Back to Schedule' : 'Finished' }}
                </button>
            </div>
        </div>
    </div>

    @push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function confirmSetAllPresent() {
        Swal.fire({
            title: 'Are you sure?',
            text: "All students will be marked as Present!",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#10b981',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Yes, mark all present!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                // Gunakan @this atau Livewire
                @this.call('setAllPresent');
            }
        });
    }

    function confirmResetAttendance() {
        Swal.fire({
            title: 'Reset Attendance?',
            text: "All attendance status will be reset to 'Not Recorded'!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Yes, reset!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                @this.call('resetAttendance');
            }
        });
    }

    function confirmSaveAll() {
        Swal.fire({
            title: 'Save Attendance?',
            text: "All attendance data will be saved and finalized.",
            icon: 'info',
            showCancelButton: true,
            confirmButtonColor: '#10b981',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Yes, save!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                @this.call('saveAll');
            }
        });
    }
</script>
@endpush
</x-filament-panels::page>