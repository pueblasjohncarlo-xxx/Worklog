<x-coordinator-layout>
    <x-slot name="header">
        Daily Journals
    </x-slot>

    <div class="space-y-6">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900 dark:text-gray-100">
                <h3 class="text-lg font-semibold mb-6">Centralized Work Log Monitoring</h3>

                @forelse($groupedJournals as $section => $students)
                    <div x-data="{ openSection: true }" class="mb-4 border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden">
                        <!-- Section Header -->
                        <button @click="openSection = !openSection" class="w-full px-6 py-4 bg-gray-100 dark:bg-gray-900 flex items-center justify-between hover:bg-gray-200 dark:hover:bg-gray-800 transition-colors">
                            <div class="flex items-center gap-3">
                                <span class="px-3 py-1 rounded text-sm font-bold bg-indigo-600 text-white shadow-sm">
                                    {{ $section }}
                                </span>
                                <span class="text-sm text-gray-600 dark:text-gray-400 font-medium">
                                    {{ $students->count() }} Students with Logs
                                </span>
                            </div>
                            <svg class="h-5 w-5 text-gray-500 transform transition-transform duration-200" :class="{ 'rotate-180': openSection }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <!-- Student List (Accordion) -->
                        <div x-show="openSection" class="bg-white dark:bg-gray-800 p-4 space-y-3">
                            @foreach($students as $studentName => $logs)
                                <div x-data="{ openStudent: false }" class="border border-gray-100 dark:border-gray-700 rounded-md">
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
                                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                                            {{ $log->work_date->format('M d, Y') }}
                                                        </td>
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                                            {{ $log->assignment->company->name }}
                                                        </td>
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-mono text-gray-900 dark:text-gray-100">
                                                            {{ number_format($log->hours, 2) }}
                                                        </td>
                                                        <td class="px-6 py-4 whitespace-nowrap">
                                                            <span class="px-2 py-1 text-xs rounded-full
                                                                {{ $log->status === 'approved' ? 'bg-green-100 text-green-800' : '' }}
                                                                {{ $log->status === 'rejected' ? 'bg-red-100 text-red-800' : '' }}
                                                                {{ $log->status === 'submitted' ? 'bg-blue-100 text-blue-800' : '' }}
                                                                {{ $log->status === 'draft' ? 'bg-gray-100 text-gray-800' : '' }}
                                                            ">
                                                                {{ ucfirst($log->status) }}
                                                            </span>
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
            </div>
        </div>
    </div>
</x-coordinator-layout>
