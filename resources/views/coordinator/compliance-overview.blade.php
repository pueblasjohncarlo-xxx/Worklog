<x-coordinator-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="text-3xl font-extrabold text-white drop-shadow-md tracking-tight">Compliance & Analytics</h2>
            <div class="text-right">
                <p class="text-sm text-indigo-100 font-medium">Last Updated: {{ now()->format('M d, Y H:i') }}</p>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6" x-data="coordinatorComplianceOverview('{{ request('focus', 'all') }}')">
        @php
            $summaryCards = [
                ['label' => 'OJT Students', 'value' => $topSummary['totalStudents'] ?? 0, 'href' => route('coordinator.student-overview'), 'tone' => 'indigo'],
                ['label' => 'Active OJT', 'value' => $topSummary['activeOJTs'] ?? 0, 'href' => route('coordinator.deployment.index'), 'tone' => 'sky'],
                ['label' => 'OJT Advisers', 'value' => $topSummary['advisersCount'] ?? 0, 'href' => route('coordinator.adviser-overview'), 'tone' => 'emerald'],
                ['label' => 'Supervisors', 'value' => $topSummary['supervisorsCount'] ?? 0, 'href' => route('coordinator.supervisor-overview'), 'tone' => 'cyan'],
                ['label' => 'Industry', 'value' => $topSummary['totalCompanies'] ?? 0, 'href' => route('coordinator.companies.index'), 'tone' => 'amber'],
                ['label' => 'Pending Approvals', 'value' => $topSummary['pendingApprovals'] ?? 0, 'href' => route('coordinator.registrations.pending'), 'tone' => 'fuchsia'],
                ['label' => 'Pending AR', 'value' => $topSummary['pendingAccomplishmentReports'] ?? 0, 'href' => route('coordinator.accomplishment-reports'), 'tone' => 'rose'],
                ['label' => 'Needs Attention', 'value' => $topSummary['studentsNeedingAttention'] ?? 0, 'href' => route('coordinator.compliance-overview'), 'tone' => 'orange'],
            ];
        @endphp

        <x-coordinator.summary-cards :cards="$summaryCards" />

        <!-- Charts and Progress -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Hours Completion Chart -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-6">Hours Completion</h3>
                <div class="space-y-4">
                    <div>
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-sm font-medium text-gray-600 dark:text-gray-300">Overall Progress</span>
                            <span class="text-sm font-bold text-gray-900 dark:text-white">{{ $overallHoursPercentage }}%</span>
                        </div>
                        <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-3">
                            <div class="bg-gradient-to-r from-purple-500 to-purple-600 h-3 rounded-full" style="width: {{ min($overallHoursPercentage, 100) }}%"></div>
                        </div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">{{ round($totalHoursCompleted, 1) }} / {{ $totalHoursRequired }} hours completed</p>
                    </div>
                </div>
            </div>

            <!-- Tasks Completion Chart -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-6">Tasks Completion</h3>
                <div class="space-y-4">
                    <div>
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-sm font-medium text-gray-600 dark:text-gray-300">Overall Progress</span>
                            <span class="text-sm font-bold text-gray-900 dark:text-white">{{ $overallTasksPercentage }}%</span>
                        </div>
                        <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-3">
                            <div class="bg-gradient-to-r from-indigo-500 to-indigo-600 h-3 rounded-full" style="width: {{ min($overallTasksPercentage, 100) }}%"></div>
                        </div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">{{ $totalTasksSubmitted }} / {{ $totalTasksSubmitted + $totalTasksOutstanding }} tasks submitted</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Student Compliance Table -->
        <div id="student-compliance-details" class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Student Compliance Details</h3>
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-bold uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Filter</span>
                        <span class="wl-status-badge wl-status-info px-2.5 py-1 text-[11px]" x-text="filterLabel()"></span>
                        <button
                            type="button"
                            x-show="selectedCategory !== 'all'"
                            @click="applyCategory('all')"
                            class="rounded-md px-2.5 py-1 text-xs font-bold text-indigo-800 transition-colors hover:bg-indigo-100 focus:bg-indigo-100 focus:outline-none"
                            style="display: none;"
                        >
                            Clear
                        </button>
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-[760px] w-full">
                    <thead class="bg-gray-50 dark:bg-gray-900 border-b border-gray-200 dark:border-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Student</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Program</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Company</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Hours</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Tasks</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-900 dark:text-gray-100 uppercase tracking-[0.14em]">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($studentMetrics as $metric)
                            <tr
                                class="hover:bg-gray-50 dark:hover:bg-gray-700/50"
                                x-bind:class="matchesMetric({ hours: {{ (float) $metric['hoursPercentage'] }}, tasks: {{ (float) $metric['tasksPercentage'] }} }) ? '' : 'hidden'"
                            >
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="h-10 w-10 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-full flex items-center justify-center text-white font-bold text-sm flex-shrink-0">
                                            {{ substr($metric['student']->name ?? '', 0, 1) }}
                                        </div>
                                        <div class="ml-3">
                                            <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $metric['student']->name }}</p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $metric['studentProfile']->student_number ?? 'N/A' }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm text-gray-600 dark:text-gray-300">{{ $metric['studentProfile']->program ?? 'N/A' }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm text-gray-600 dark:text-gray-300">{{ $metric['company']->name ?? 'N/A' }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center space-x-2">
                                        <div class="w-24">
                                            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                                <div class="bg-purple-600 h-2 rounded-full" style="width: {{ min($metric['hoursPercentage'], 100) }}%"></div>
                                            </div>
                                        </div>
                                        <span class="text-xs font-medium text-gray-600 dark:text-gray-300">{{ $metric['hoursPercentage'] }}%</span>
                                    </div>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ round($metric['completedHours'], 1) }}/{{ $metric['requiredHours'] }} hrs</p>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center space-x-2">
                                        <div class="w-24">
                                            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                                <div class="bg-indigo-600 h-2 rounded-full" style="width: {{ min($metric['tasksPercentage'], 100) }}%"></div>
                                            </div>
                                        </div>
                                        <span class="text-xs font-medium text-gray-600 dark:text-gray-300">{{ $metric['tasksPercentage'] }}%</span>
                                    </div>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $metric['submittedTasks'] }}/{{ $metric['totalTasks'] }} tasks</p>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($metric['isOnTrack'])
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-200">
                                            <span class="inline-block h-2 w-2 bg-green-600 dark:bg-green-400 rounded-full mr-2"></span>
                                            On Track
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-200">
                                            <span class="inline-block h-2 w-2 bg-red-600 dark:bg-red-400 rounded-full mr-2"></span>
                                            At Risk
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <a href="#" class="inline-flex items-center rounded-lg border border-indigo-700 bg-indigo-700 px-3 py-2 text-xs font-black uppercase tracking-[0.14em] text-white shadow-sm transition-colors hover:bg-indigo-800 hover:border-indigo-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 active:bg-indigo-900">
                                        View Details
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-8 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <svg class="h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                        </svg>
                                        <p class="text-gray-500 dark:text-gray-400 text-sm">No OJT students assigned yet</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                        @if($studentMetrics->isNotEmpty())
                            <tr x-show="!hasVisibleRows()" style="display: none;">
                                <td colspan="7" class="px-6 py-8 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <svg class="mb-4 h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m-7 5h8a2 2 0 002-2V7l-5-5H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                        </svg>
                                        <p class="text-sm font-semibold text-gray-700 dark:text-gray-200">No students match the selected category.</p>
                                    </div>
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Additional Stats -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Pending Reviews -->
            <button
                type="button"
                @click="applyCategory('pending')"
                class="w-full rounded-lg bg-white p-6 text-left shadow transition hover:-translate-y-0.5 hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-yellow-500 dark:bg-gray-800"
            >
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Pending Reviews</p>
                        <p class="text-3xl font-bold text-yellow-600 dark:text-yellow-400 mt-2">{{ $studentMetrics->filter(fn($m) => $m['hoursPercentage'] < 100)->count() }}</p>
                        <p class="text-xs text-gray-400 mt-1">Incomplete hours</p>
                        <p class="mt-3 text-xs font-bold uppercase tracking-[0.18em] text-yellow-800 dark:text-yellow-300">View related students</p>
                    </div>
                    <div class="p-3 bg-yellow-100 dark:bg-yellow-900/30 rounded-full">
                        <svg class="h-8 w-8 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4v.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </button>

            <!-- Critical Issues -->
            <button
                type="button"
                @click="applyCategory('critical')"
                class="w-full rounded-lg bg-white p-6 text-left shadow transition hover:-translate-y-0.5 hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-red-500 dark:bg-gray-800"
            >
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Critical Issues</p>
                        <p class="text-3xl font-bold text-red-600 dark:text-red-400 mt-2">{{ $studentMetrics->filter(fn($m) => $m['hoursPercentage'] < 50)->count() }}</p>
                        <p class="text-xs text-gray-400 mt-1">&lt;50% hours</p>
                        <p class="mt-3 text-xs font-bold uppercase tracking-[0.18em] text-red-800 dark:text-red-300">View related students</p>
                    </div>
                    <div class="p-3 bg-red-100 dark:bg-red-900/30 rounded-full">
                        <svg class="h-8 w-8 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </div>
                </div>
            </button>

            <!-- Excellent Progress -->
            <button
                type="button"
                @click="applyCategory('excellent')"
                class="w-full rounded-lg bg-white p-6 text-left shadow transition hover:-translate-y-0.5 hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-teal-500 dark:bg-gray-800"
            >
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Excellent Progress</p>
                        <p class="text-3xl font-bold text-teal-600 dark:text-teal-400 mt-2">{{ $studentMetrics->filter(fn($m) => $m['hoursPercentage'] >= 100 && $m['tasksPercentage'] >= 100)->count() }}</p>
                        <p class="text-xs text-gray-400 mt-1">Completed &amp; submitted</p>
                        <p class="mt-3 text-xs font-bold uppercase tracking-[0.18em] text-teal-800 dark:text-teal-300">View related students</p>
                    </div>
                    <div class="p-3 bg-teal-100 dark:bg-teal-900/30 rounded-full">
                        <svg class="h-8 w-8 text-teal-600 dark:text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10h-2m0 0h-2m2 0V8m0 2v2m8-8v12a2 2 0 01-2 2H4a2 2 0 01-2-2V4a2 2 0 012-2h12a2 2 0 012 2z" />
                        </svg>
                    </div>
                </div>
            </button>
        </div>
    </div>

    @push('scripts')
    <script>
        function coordinatorComplianceOverview(initialCategory = 'all') {
            return {
                selectedCategory: initialCategory || 'all',
                matchesMetric(metric) {
                    const hours = Number(metric?.hours || 0);
                    const tasks = Number(metric?.tasks || 0);

                    if (this.selectedCategory === 'pending') {
                        return hours < 100;
                    }

                    if (this.selectedCategory === 'critical') {
                        return hours < 50;
                    }

                    if (this.selectedCategory === 'excellent') {
                        return hours >= 100 && tasks >= 100;
                    }

                    return true;
                },
                hasVisibleRows() {
                    return Array.from(document.querySelectorAll('#student-compliance-details tbody tr'))
                        .some((row) => !row.classList.contains('hidden') && !row.hasAttribute('x-show'));
                },
                filterLabel() {
                    if (this.selectedCategory === 'pending') return 'Pending Reviews';
                    if (this.selectedCategory === 'critical') return 'Critical Issues';
                    if (this.selectedCategory === 'excellent') return 'Excellent Progress';
                    return 'All Students';
                },
                applyCategory(category) {
                    this.selectedCategory = category || 'all';
                    const url = new URL(window.location.href);
                    if (this.selectedCategory === 'all') {
                        url.searchParams.delete('focus');
                    } else {
                        url.searchParams.set('focus', this.selectedCategory);
                    }
                    url.hash = 'student-compliance-details';
                    window.history.replaceState({}, '', url.toString());
                    document.getElementById('student-compliance-details')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
                },
            };
        }
    </script>
    @endpush
</x-coordinator-layout>
