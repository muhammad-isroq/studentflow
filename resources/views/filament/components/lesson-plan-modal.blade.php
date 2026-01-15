{{-- File: resources/views/filament/components/lesson-plan-modal.blade.php --}}

<div>
    {{-- Tabel Informasi Dasar --}}
    <div class="overflow-hidden bg-white border border-gray-200 rounded-xl shadow-sm dark:bg-gray-900 dark:border-gray-800">
        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
            <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
                <tr class="bg-gray-50 dark:bg-gray-800/50">
                    <th class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white w-1/4">
                        Program Class
                    </th>
                    <td class="px-6 py-4 font-bold text-primary-600">
                        {{ $record->program->nama_program ?? '-' }}
                    </td>
                </tr>
                <tr>
                    <th class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap bg-gray-50 dark:text-white dark:bg-gray-800/50">
                        Session Date
                    </th>
                    <td class="px-6 py-4">
                        {{ $record->session_date->format('l, d F Y') }}
                    </td>
                </tr>
                <tr>
                    <th class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap bg-gray-50 dark:text-white dark:bg-gray-800/50">
                        Unit / Material
                    </th>
                    <td class="px-6 py-4">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                            {{ $record->unit ?? 'No Unit' }}
                        </span>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    {{-- Tabel Isi Lesson Plan --}}
    <div class="mt-6 overflow-hidden bg-white border border-gray-200 rounded-xl shadow-sm dark:bg-gray-900 dark:border-gray-800">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 dark:bg-gray-800/50 dark:border-gray-700">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white">📖 Lesson Content</h3>
        </div>
        
        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
            <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
                
                {{-- TOPIC --}}
                <tr>
                    <th class="px-6 py-6 font-medium text-gray-900 align-top dark:text-white w-1/4 bg-gray-50/50 dark:bg-gray-800/20">
                        Topic
                    </th>
                    <td class="px-6 py-6 prose max-w-none dark:prose-invert">
                        {!! $record->topic ?? '<span class="text-gray-400 italic">Empty</span>' !!}
                    </td>
                </tr>

                {{-- ACTIVITY --}}
                <tr>
                    <th class="px-6 py-6 font-medium text-gray-900 align-top dark:text-white bg-gray-50/50 dark:bg-gray-800/20">
                        Activity
                    </th>
                    <td class="px-6 py-6 prose max-w-none dark:prose-invert">
                        {!! $record->activity ?? '<span class="text-gray-400 italic">Empty</span>' !!}
                    </td>
                </tr>

                {{-- VOCABULARY --}}
                <tr>
                    <th class="px-6 py-6 font-medium text-gray-900 align-top dark:text-white bg-gray-50/50 dark:bg-gray-800/20">
                        Vocabulary List
                    </th>
                    <td class="px-6 py-6 prose max-w-none dark:prose-invert bg-blue-50/30 dark:bg-blue-900/10">
                        {!! $record->vocabulary_list ?? '<span class="text-gray-400 italic">No vocabulary recorded</span>' !!}
                    </td>
                </tr>

                {{-- JOURNAL --}}
                <tr>
                    <th class="px-6 py-6 font-medium text-gray-900 align-top dark:text-white bg-gray-50/50 dark:bg-gray-800/20">
                        Class Journal
                    </th>
                    <td class="px-6 py-6 prose max-w-none dark:prose-invert bg-yellow-50/30 dark:bg-yellow-900/10">
                        {!! $record->class_journal ?? '<span class="text-gray-400 italic">No journal entry</span>' !!}
                    </td>
                </tr>

            </tbody>
        </table>
    </div>
</div>