<x-filament-panels::page>
    
    <div class="mb-2">
        {{ $this->form }}
    </div>

    @if($this->reportData)
        @php
            $program = $this->reportData['program'];
            $reports = $this->reportData['reports'];
        @endphp

        <div class="bg-white dark:bg-gray-900 rounded-xl shadow-sm border border-gray-200 dark:border-gray-800 p-6 sm:p-8 mt-4 print-container">

            <div class="mb-8 flex justify-between items-start">
                <div>
                    <h2 class="text-2xl font-extrabold text-gray-900 dark:text-white tracking-tight">Archived Scoring Sheet: {{ $program->nama_program ?? '-' }}</h2>
                    <p class="text-gray-500 dark:text-gray-400 mt-1 font-medium">Academic Period: {{ strtoupper($this->semester_name) }}</p>
                </div>
                
                <a href="{{ route('print.arsip', ['program_id' => $program->id, 'semester_name' => $this->semester_name]) }}" target="_blank" class="print-hide inline-flex items-center justify-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-primary-500 dark:bg-gray-800 dark:text-gray-200 dark:border-gray-700 dark:hover:bg-gray-700">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    Print / Save PDF
                </a>
            </div>

            <div class="bg-gray-50 dark:bg-gray-800/50 rounded-lg p-6 border border-gray-100 dark:border-gray-700 mb-8">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Program Detail Archive</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Program Name</p>
                        <p class="font-medium text-gray-900 dark:text-white mt-1">{{ $program->nama_program ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Tutor / Teacher</p>
                        <p class="font-medium text-gray-900 dark:text-white mt-1">{{ $program->guru->nama_guru ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Classroom</p>
                        <p class="font-medium text-gray-900 dark:text-white mt-1">{{ $program->nama_ruangan ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Schedule</p>
                        <p class="font-medium text-gray-900 dark:text-white mt-1">{{ $program->jadwal_program ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Lesson Time</p>
                        <p class="font-medium text-gray-900 dark:text-white mt-1">{{ $program->lesson_time ?? '-' }}</p>
                    </div>
                </div>
            </div>

            @if($reports && $reports->count() > 0)
                <div class="mt-8 space-y-8">
                    <div>
                        <h3 class="text-sm font-bold text-primary-700 dark:text-primary-400 uppercase tracking-wider mb-3 border-l-4 border-primary-500 pl-2">
                            TABLE 1: ORIGINAL STUDENT SCORES (Average of Review & Semester Test)
                        </h3>
                        <div class="overflow-x-auto bg-white dark:bg-gray-900 rounded-lg shadow border border-gray-200 dark:border-gray-700">
                            <table class="w-full text-sm text-left">
                                <thead class="bg-gray-50 dark:bg-gray-800 text-gray-500 font-medium border-b uppercase text-xs">
                                    <tr>
                                        <th class="px-4 py-3 text-center">RANK</th>
                                        <th class="px-4 py-3">STUDENT NAME</th>
                                        <th class="px-3 py-3 text-center">LS</th>
                                        <th class="px-3 py-3 text-center">RD</th>
                                        <th class="px-3 py-3 text-center">WR</th>
                                        <th class="px-3 py-3 text-center">SP</th>
                                        <th class="px-3 py-3 text-center">GR</th>
                                        <th class="px-4 py-3 text-center text-primary-600">TOTAL</th>
                                        <th class="px-4 py-3 text-center text-success-600">FINAL AV</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                    @php $sortedRaw = $reports->sortByDesc('final_score')->values(); @endphp
                                    @foreach($sortedRaw as $index => $report)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
                                        <td class="px-4 py-3 text-center font-bold">{{ $index + 1 }}</td>
                                        <td class="px-4 py-3 font-medium">{{ $report->siswa->nama ?? '-' }}</td>
                                        <td class="px-3 py-3 text-center">{{ number_format($report->avg_listening, 1) }}</td>
                                        <td class="px-3 py-3 text-center">{{ number_format($report->avg_reading, 1) }}</td>
                                        <td class="px-3 py-3 text-center">{{ number_format($report->avg_writing, 1) }}</td>
                                        <td class="px-3 py-3 text-center">{{ number_format($report->avg_speaking, 1) }}</td>
                                        <td class="px-3 py-3 text-center">{{ number_format($report->avg_grammar, 1) }}</td>
                                        <td class="px-4 py-3 text-center font-bold text-primary-600">{{ number_format($report->total_score, 1) }}</td>
                                        <td class="px-4 py-3 text-center font-bold text-success-600">{{ number_format($report->final_score, 1) }}</td>
                                    </tr>
                                    @endforeach
                                    <tr class="bg-gray-100 dark:bg-gray-800 border-t-2 border-gray-300 dark:border-gray-600">
                                        <td colspan="2" class="px-4 py-3 font-black text-primary-600 tracking-wide">CLASS AVG (ORIGINAL)</td>
                                        <td class="px-3 py-3 text-center font-bold">{{ number_format($reports->avg('avg_listening'), 1) }}</td>
                                        <td class="px-3 py-3 text-center font-bold">{{ number_format($reports->avg('avg_reading'), 1) }}</td>
                                        <td class="px-3 py-3 text-center font-bold">{{ number_format($reports->avg('avg_writing'), 1) }}</td>
                                        <td class="px-3 py-3 text-center font-bold">{{ number_format($reports->avg('avg_speaking'), 1) }}</td>
                                        <td class="px-3 py-3 text-center font-bold">{{ number_format($reports->avg('avg_grammar'), 1) }}</td>
                                        <td class="px-4 py-3 text-center font-black text-primary-600">{{ number_format($reports->avg('total_score'), 1) }}</td>
                                        <td class="px-4 py-3 text-center font-black text-success-600">{{ number_format($reports->avg('final_score'), 1) }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div>
                        <h3 class="text-sm font-bold text-amber-600 dark:text-amber-400 uppercase tracking-wider mb-3 border-l-4 border-amber-500 pl-2">
                            TABLE 2: REPORT CARD SCORES (Manual Teacher Input)
                        </h3>
                        <div class="overflow-x-auto bg-white dark:bg-gray-900 rounded-lg shadow border border-amber-200 dark:border-amber-700">
                            <table class="w-full text-sm text-left">
                                <thead class="bg-amber-50 dark:bg-amber-900/30 text-amber-800 dark:text-amber-400 font-medium border-b border-amber-200 uppercase text-xs">
                                    <tr>
                                        <th class="px-4 py-3 text-center">RANK</th>
                                        <th class="px-4 py-3">STUDENT NAME</th>
                                        <th class="px-3 py-3 text-center">LS</th>
                                        <th class="px-3 py-3 text-center">RD</th>
                                        <th class="px-3 py-3 text-center">WR</th>
                                        <th class="px-3 py-3 text-center">SP</th>
                                        <th class="px-3 py-3 text-center">GR</th>
                                        <th class="px-4 py-3 text-center">TOTAL</th>
                                        <th class="px-4 py-3 text-center text-success-600">FINAL AV</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-amber-100 dark:divide-amber-800/50">
                                    @php $sortedRapor = $reports->sortBy('rank')->values(); @endphp
                                    @foreach($sortedRapor as $report)
                                    @php
                                        $rapor_total = $report->rapor_listening + $report->rapor_reading + $report->rapor_writing + $report->rapor_speaking + $report->rapor_grammar;
                                        $rapor_final = $rapor_total / 5;
                                    @endphp
                                    <tr class="hover:bg-amber-50/50 dark:hover:bg-amber-900/20 transition-colors">
                                        <td class="px-4 py-3 text-center font-bold">{{ $report->rank }}</td>
                                        <td class="px-4 py-3 font-medium">{{ $report->siswa->nama ?? '-' }}</td>
                                        <td class="px-3 py-3 text-center text-amber-700 dark:text-amber-300">{{ number_format($report->rapor_listening, 1) }}</td>
                                        <td class="px-3 py-3 text-center text-amber-700 dark:text-amber-300">{{ number_format($report->rapor_reading, 1) }}</td>
                                        <td class="px-3 py-3 text-center text-amber-700 dark:text-amber-300">{{ number_format($report->rapor_writing, 1) }}</td>
                                        <td class="px-3 py-3 text-center text-amber-700 dark:text-amber-300">{{ number_format($report->rapor_speaking, 1) }}</td>
                                        <td class="px-3 py-3 text-center text-amber-700 dark:text-amber-300">{{ number_format($report->rapor_grammar, 1) }}</td>
                                        <td class="px-4 py-3 text-center font-bold text-amber-700 dark:text-amber-300">{{ number_format($rapor_total, 1) }}</td>
                                        <td class="px-4 py-3 text-center font-bold text-success-600">{{ number_format($rapor_final, 1) }}</td>
                                    </tr>
                                    @endforeach
                                    <tr class="bg-amber-100 dark:bg-amber-900/50 border-t-2 border-amber-300 dark:border-amber-600">
                                        <td colspan="2" class="px-4 py-3 font-black text-amber-700 dark:text-amber-400 tracking-wide">CLASS AVG (REPORT CARD)</td>
                                        <td class="px-3 py-3 text-center font-bold text-amber-700 dark:text-amber-400">{{ number_format($reports->avg('rapor_listening'), 1) }}</td>
                                        <td class="px-3 py-3 text-center font-bold text-amber-700 dark:text-amber-400">{{ number_format($reports->avg('rapor_reading'), 1) }}</td>
                                        <td class="px-3 py-3 text-center font-bold text-amber-700 dark:text-amber-400">{{ number_format($reports->avg('rapor_writing'), 1) }}</td>
                                        <td class="px-3 py-3 text-center font-bold text-amber-700 dark:text-amber-400">{{ number_format($reports->avg('rapor_speaking'), 1) }}</td>
                                        <td class="px-3 py-3 text-center font-bold text-amber-700 dark:text-amber-400">{{ number_format($reports->avg('rapor_grammar'), 1) }}</td>
                                        <td class="px-4 py-3 text-center font-black text-amber-700 dark:text-amber-400">
                                            {{ number_format($reports->avg('rapor_listening') + $reports->avg('rapor_reading') + $reports->avg('rapor_writing') + $reports->avg('rapor_speaking') + $reports->avg('rapor_grammar'), 1) }}
                                        </td>
                                        <td class="px-4 py-3 text-center font-black text-success-600">
                                            {{ number_format(($reports->avg('rapor_listening') + $reports->avg('rapor_reading') + $reports->avg('rapor_writing') + $reports->avg('rapor_speaking') + $reports->avg('rapor_grammar')) / 5, 1) }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <style>
            @media print {
                aside, header, .fi-topbar, .fi-sidebar, .mb-2, .print-hide {
                    display: none !important;
                }
                .fi-main {
                    padding: 0 !important;
                    margin: 0 !important;
                    max-width: 100% !important;
                }
                body {
                    background-color: white !important;
                }
                .print-container {
                    box-shadow: none !important;
                    border: none !important;
                    padding: 0 !important;
                    margin-top: 0 !important;
                }
            }
        </style>
        
    @else
        <div class="bg-white dark:bg-gray-900 rounded-xl shadow-sm border border-gray-200 dark:border-gray-800 p-12 text-center mt-4">
            <div class="mx-auto w-16 h-16 bg-gray-100 dark:bg-gray-800 rounded-full flex items-center justify-center mb-4 text-primary-500">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" /></svg>
            </div>
            <h3 class="text-lg font-medium text-gray-900 dark:text-white">Select Program and Semester</h3>
            <p class="text-gray-500 mt-1">Please select a Program and Semester from the dropdown filters above to load the archived academic records.</p>
        </div>
    @endif

</x-filament-panels::page>