<x-filament-panels::page>
    <div class="space-y-6">
        <!-- Attendance Table with Integrated Header -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <!-- Header dengan info session -->
            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 px-6 py-4 border-b border-gray-200">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between space-y-3 lg:space-y-0">
                    <div>
                        <h1 class="text-xl font-bold text-gray-900 ">Attendance list</h1>
                        <p class="text-sm text-gray-600 mt-1">Click on the status column to change student attendance.</p>
                    </div>
                    
                    <!-- Session Info dalam bentuk badges -->
                    <div class="flex flex-wrap gap-3">
                        <div class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                            {{ $this->record->program->nama_program }}
                        </div>
                        
                        <div class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a4 4 0 118 0v4m-4 4v6m-4 0H4a1 1 0 01-1-1V10a1 1 0 011-1h2V7a4 4 0 118 0v2h2a1 1 0 011 1v7a1 1 0 01-1 1h-2"></path>
                            </svg>
                            {{ $this->record->session_date->format('d M Y') }}
                        </div>
                        
                        <div class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5 0a4 4 0 11-8-0"></path>
                            </svg>
                            {{ $this->record->program->siswas->count() }} Siswa
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Bulk Actions Bar -->
            <div class="bg-gray-50 px-6 py-3 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <span class="text-sm text-gray-600 font-medium">Quick action:</span>
                        <x-filament::button
                            color="success"
                            size="sm"
                            wire:click="setAllPresent"
                            wire:confirm="Yakin ingin mengubah semua status menjadi 'Hadir'?"
                        >
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            All Present
                        </x-filament::button>
                        
                        {{-- <x-filament::button
                            color="gray"
                            size="sm"
                            wire:click="resetAttendance"
                            wire:confirm="Yakin ingin reset semua status?"
                        >
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                            </svg>
                            Reset
                        </x-filament::button> --}}
                    </div>
                    
                    <!-- Status Summary -->
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
                        <div class="flex items-center">
                            <div class="w-4 h-4 bg-yellow-400 rounded-full mr-2"></div>
                            <span class="text-gray-600">Permission/Sick Leave: <span class="font-semibold text-yellow-700">{{ $stats['izin'] + $stats['sakit'] }}</span></span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Table -->
            <div class="overflow-hidden">
                {{ $this->table }}
            </div>
        </div>

        <!-- Action Buttons dengan style yang lebih baik -->
        <div class="flex flex-col sm:flex-row justify-between items-center space-y-3 sm:space-y-0 bg-white rounded-lg shadow px-6 py-4">
            <div class="flex items-center text-sm text-gray-600">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Status changes are automatically saved
            </div>
            
            <div class="flex space-x-3">
                {{-- <x-filament::button
                    color="gray"
                    wire:click="backToList"
                >
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Return
                </x-filament::button> --}}
                
                <x-filament::button 
                    wire:click="saveAll"
                    color="success"
                >
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Finished
                </x-filament::button>
            </div>
        </div>
    </div>
</x-filament-panels::page>