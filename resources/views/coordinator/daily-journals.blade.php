<x-coordinator-layout>
    <x-slot name="header">
        Journals
    </x-slot>

    <div class="space-y-6" x-data="coordinatorJournalSearch()">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900 dark:text-gray-100">
                <div class="mb-6 flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                    <div>
                        <h3 class="text-lg font-semibold">Centralized Journal Monitoring</h3>
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-300">Search journals by student, section, date, status, reviewer, company, or keyword.</p>
                    </div>
                    <div class="w-full max-w-xl">
                        <label for="journal-search" class="sr-only">Search journals</label>
                        <div class="relative">
                            <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M10.5 18a7.5 7.5 0 100-15 7.5 7.5 0 000 15z" />
                                </svg>
                            </span>
                            <input
                                id="journal-search"
                                x-model.debounce.150ms="searchTerm"
                                type="text"
                                placeholder="Search by student, section, date, status, or keyword..."
                                class="block w-full rounded-xl border border-gray-300 bg-white py-3 pl-10 pr-4 text-sm font-medium text-gray-900 shadow-sm transition focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100"
                            >
                        </div>
                    </div>
                </div>

                @forelse($groupedJournals as $section => $students)
                    @php
                        $sectionSearch = strtolower($section.' '.collect($students)->flatMap(function ($logs, $studentName) {
                            return $logs->map(function ($log) use ($studentName) {
                                return implode(' ', array_filter([
                                    $studentName,
                                    $log->work_date?->format('M d, Y'),
                                    $log->assignment->company->name ?? '',
                                    $log->status,
                                    $log->description,
                                    $log->skills_applied,
                                    $log->reflection,
                                    $log->reviewer->name ?? '',
                                ]));
                            });
                        })->implode(' '));
                    @endphp
                    <div data-journal-section x-data="{ openSection: true }" x-effect="if (searchTerm) openSection = true" x-bind:class="matches(@js($sectionSearch)) ? '' : 'hidden'" class="mb-4 border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden">
                        <!-- Section Header -->
                        <button @click="openSection = !openSection" class="w-full px-6 py-4 bg-gray-100 dark:bg-gray-900 flex items-center justify-between hover:bg-gray-200 dark:hover:bg-gray-800 transition-colors">
                            <div class="flex items-center gap-3">
                                <span class="px-3 py-1 rounded text-sm font-bold bg-indigo-600 text-white shadow-sm">
                                    {{ $section }}
                                </span>
                                <span class="text-sm text-gray-600 dark:text-gray-400 font-medium">
                                    {{ $students->count() }} OJT Students with Logs
                                </span>
                            </div>
                            <svg class="h-5 w-5 text-gray-500 transform transition-transform duration-200" :class="{ 'rotate-180': openSection }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <!-- Student List (Accordion) -->
                        <div x-show="openSection" class="bg-white dark:bg-gray-800 p-4 space-y-3">
                            @foreach($students as $studentName => $logs)
                                @php
                                    $studentSearch = strtolower($section.' '.$studentName.' '.collect($logs)->map(function ($log) {
                                        return implode(' ', array_filter([
                                            $log->work_date?->format('M d, Y'),
                                            $log->assignment->company->name ?? '',
                                            $log->status,
                                            $log->description,
                                            $log->skills_applied,
                                            $log->reflection,
                                            $log->reviewer->name ?? '',
                                        ]));
                                    })->implode(' '));
                                @endphp
                                <div x-data="{ openStudent: false }" x-effect="if (searchTerm && matches(@js($studentSearch))) openStudent = true" x-bind:class="matches(@js($studentSearch)) ? '' : 'hidden'" class="border border-gray-100 dark:border-gray-700 rounded-md">
                                    <button @click="openStudent = !openStudent" class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-800/50 flex items-center justify-between hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                                        <div class="flex items-center gap-3">
                                            <div class="h-8 w-8 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 font-bold text-xs">
                                                {{ substr($studentName, 0, 1) }}
                                            </div>
                                            <span class="font-medium text-gray-800 dark:text-gray-200">{{ $studentName }}</span>
                                            <span class="text-xs text-gray-500">({{ $logs->count() }} Logs)</span>
                                        </div>
                                        <svg class="h-4 w-4 text-gray-400 transform transition-transform duration-200" :class="{ 'rotate-180': openStudent }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </button>

                                    <!-- Logs Table -->
                                    <div x-show="openStudent" class="overflow-x-auto">
                                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                            <thead class="bg-gray-50 dark:bg-gray-900/50">
                                                <tr>
                                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Date</th>
                                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Company</th>
                                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Hours</th>
                                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Reviewer</th>
                                                </tr>
                                            </thead>
                                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                                @foreach($logs as $log)
                                                    @php
                                                        $logSearch = strtolower(implode(' ', array_filter([
                                                            $section,
                                                            $studentName,
                                                            $log->work_date?->format('M d, Y'),
                                                            $log->assignment->company->name ?? '',
                                                            $log->status,
                                                            $log->description,
                                                            $log->skills_applied,
                                                            $log->reflection,
                                                            $log->reviewer->name ?? '',
                                                        ])));
                                                    @endphp
                                                    <tr x-bind:class="matches(@js($logSearch)) ? '' : 'hidden'" class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                                            {{ $log->work_date->format('M d, Y') }}
                                                        </td>
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                                            {{ $log->assignment->company->name ?? 'N/A' }}
                                                        </td>
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-mono text-gray-900 dark:text-gray-100">
                                                            {{ number_format($log->hours, 2) }}
                                                        </td>
                                                        <td class="px-6 py-4 whitespace-nowrap">
                                                            <x-status-badge :status="$log->status" :label="ucfirst($log->status)" size="sm" />
                                                        </td>
                                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-gray-500 dark:text-gray-400">
                                                            {{ $log->reviewer->name ?? 'N/A' }}
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @empty
                    <p class="text-center text-gray-500 italic py-8">
                        Walang nahanap na work logs.
                    </p>
                @endforelse

                @if($groupedJournals->isNotEmpty())
                    <div x-show="hasNoMatches()" class="py-10 text-center" style="display: none;">
                        <p class="text-sm font-semibold text-gray-700 dark:text-gray-200">No journals matched your search.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function coordinatorJournalSearch() {
            return {
                searchTerm: '',
                normalize(value) {
                    return String(value || '').trim().toLowerCase();
                },
                matches(haystack) {
                    const query = this.normalize(this.searchTerm);
                    if (query === '') {
                        return true;
                    }

                    return this.normalize(haystack).includes(query);
                },
                hasNoMatches() {
                    if (this.normalize(this.searchTerm) === '') {
                        return false;
                    }

                    return Array.from(document.querySelectorAll('[data-journal-section]'))
                        .every((section) => section.classList.contains('hidden'));
                },
            };
        }
    </script>
    @endpush
</x-coordinator-layout>
