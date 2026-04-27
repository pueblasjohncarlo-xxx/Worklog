<x-ojt-adviser-layout>
    <x-slot name="header">
        <h1 class="text-3xl font-bold text-white">OJT Adviser Dashboard</h1>
    </x-slot>

    <div class="space-y-8 pb-8">
        <!-- ===== ENHANCED SUMMARY CARDS ===== -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 auto-rows-max">
            <!-- Assigned OJT Students -->
            <a href="{{ route('ojt_adviser.students') }}" class="block bg-gradient-to-br from-indigo-600/20 to-indigo-600/10 border border-indigo-500/30 rounded-xl p-5 shadow-lg cursor-pointer hover:-translate-y-0.5 hover:border-indigo-400/50 hover:shadow-indigo-900/30 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-400 transition-all">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <p class="text-xs text-indigo-300 font-semibold uppercase tracking-widest">Assigned OJT Students</p>
                        <h3 class="text-3xl font-black text-white mt-2">{{ $totalStudents }}</h3>
                        <p class="text-xs text-indigo-400 mt-1">Total under supervision</p>
                    </div>
                    <div class="p-2.5 bg-indigo-500/30 rounded-lg">
                        <svg class="h-6 w-6 text-indigo-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </div>
                </div>
                <div class="mt-3 pt-3 border-t border-indigo-500/20">
                    <div class="text-xs text-indigo-300">
                        <span class="inline-block bg-indigo-500/20 px-2 py-1 rounded">{{ $completedHoursCount }} completed</span>
                    </div>
                </div>
            </a>

            <!-- Incomplete Logs -->
            <a href="{{ route('ojt_adviser.reports') }}" class="block bg-gradient-to-br from-red-600/20 to-red-600/10 border border-red-500/30 rounded-xl p-5 shadow-lg cursor-pointer hover:-translate-y-0.5 hover:border-red-400/50 hover:shadow-red-900/30 focus:outline-none focus-visible:ring-2 focus-visible:ring-red-400 transition-all">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <p class="text-xs text-red-300 font-semibold uppercase tracking-widest">Incomplete Logs</p>
                        <h3 class="text-3xl font-black text-white mt-2">{{ $incompleteLogsCount }}</h3>
                        <p class="text-xs text-red-400 mt-1">Awaiting completion</p>
                    </div>
                    <div class="p-2.5 bg-red-500/30 rounded-lg">
                        <svg class="h-6 w-6 text-red-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
                <div class="mt-3 pt-3 border-t border-red-500/20">
                    <div class="text-xs text-red-300">
                        <span class="font-semibold">{{ $totalStudents > 0 ? round(($incompleteLogsCount / $totalStudents) * 100, 0) : 0 }}%</span> of OJT students
                    </div>
                </div>
            </a>

            <!-- Completed OJT -->
            <a href="{{ route('ojt_adviser.accomplishment-reports') }}" class="block bg-gradient-to-br from-emerald-600/20 to-emerald-600/10 border border-emerald-500/30 rounded-xl p-5 shadow-lg cursor-pointer hover:-translate-y-0.5 hover:border-emerald-400/50 hover:shadow-emerald-900/30 focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-400 transition-all">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <p class="text-xs text-emerald-300 font-semibold uppercase tracking-widest">Completed OJT</p>
                        <h3 class="text-3xl font-black text-white mt-2">{{ $completedHoursCount }}</h3>
                        <p class="text-xs text-emerald-400 mt-1">400+ hours completed</p>
                    </div>
                    <div class="p-2.5 bg-emerald-500/30 rounded-lg">
                        <svg class="h-6 w-6 text-emerald-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
                <div class="mt-3 pt-3 border-t border-emerald-500/20">
                    <div class="text-xs text-emerald-300">
                        <span class="font-semibold">{{ $totalStudents > 0 ? round(($completedHoursCount / $totalStudents) * 100, 0) : 0 }}%</span> completion rate
                    </div>
                </div>
            </a>

            <!-- Evaluation Progress -->
            <a href="{{ route('ojt_adviser.evaluations') }}" class="block bg-gradient-to-br from-amber-600/20 to-amber-600/10 border border-amber-500/30 rounded-xl p-5 shadow-lg cursor-pointer hover:-translate-y-0.5 hover:border-amber-400/50 hover:shadow-amber-900/30 focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-400 transition-all">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <p class="text-xs text-amber-300 font-semibold uppercase tracking-widest">Evaluations</p>
                        <h3 class="text-3xl font-black text-white mt-2">{{ $evaluationProgress }}%</h3>
                        <p class="text-xs text-amber-400 mt-1">OJT students evaluated</p>
                    </div>
                    <div class="p-2.5 bg-amber-500/30 rounded-lg">
                        <svg class="h-6 w-6 text-amber-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                    </div>
                </div>
                <div class="mt-3 pt-3 border-t border-amber-500/20">
                    <div class="w-full bg-amber-900/30 rounded-full h-1.5">
                        <div class="bg-amber-500 h-1.5 rounded-full" style="width: {{ $evaluationProgress }}%"></div>
                    </div>
                </div>
            </a>

            <!-- Pending Evaluations (New) - Wrapped to new row on sm screens -->
            <a href="{{ route('ojt_adviser.evaluations', ['status' => 'pending']) }}" class="block bg-gradient-to-br from-orange-600/20 to-orange-600/10 border border-orange-500/30 rounded-xl p-5 shadow-lg cursor-pointer hover:-translate-y-0.5 hover:border-orange-400/50 hover:shadow-orange-900/30 focus:outline-none focus-visible:ring-2 focus-visible:ring-orange-400 transition-all">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <p class="text-xs text-orange-300 font-semibold uppercase tracking-widest">Pending Reviews</p>
                        <h3 class="text-3xl font-black text-white mt-2">{{ $pendingEvaluationsCount }}</h3>
                        <p class="text-xs text-orange-400 mt-1">Need evaluation</p>
                    </div>
                    <div class="p-2.5 bg-orange-500/30 rounded-lg flex-shrink-0">
                        <svg class="h-6 w-6 text-orange-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6m1 3h2m-2 0a3 3 0 10-6 0 3 3 0 006 0z" />
                        </svg>
                    </div>
                </div>
            </a>
        </div>

        <!-- ===== CHARTS SECTION - IMPROVED SPACING ===== -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 auto-rows-max">
        <!-- ===== CHARTS SECTION - IMPROVED SPACING ===== -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 auto-rows-max">
            <!-- Student Completion Chart -->
            <div class="bg-white/5 border border-white/10 rounded-xl p-6 shadow-lg backdrop-blur-sm overflow-hidden">
                <h3 class="text-lg font-bold text-white mb-6 flex items-center gap-2">
                    <svg class="h-5 w-5 text-indigo-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                    <span>Student Completion Progress</span>
                </h3>
                <div class="relative min-h-[320px] w-full">
                    <canvas id="studentProgressChart"
                        data-labels="{{ json_encode($assignments->pluck('student.name')) }}"
                        data-values="{{ json_encode($assignments->map(fn($a) => $a->progressPercentage())->values()) }}">
                    </canvas>
                </div>
            </div>

            <!-- Performance Metrics Radar -->
            <div class="bg-white/5 border border-white/10 rounded-xl p-6 shadow-lg backdrop-blur-sm overflow-hidden">
                <h3 class="text-lg font-bold text-white mb-6 flex items-center gap-2">
                    <svg class="h-5 w-5 text-emerald-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z" />
                    </svg>
                    <span>Average Performance Metrics</span>
                </h3>
                <div class="relative min-h-[320px] w-full">
                    <canvas id="evaluationRadarChart"
                        data-labels="{{ json_encode(array_keys($evaluationAverages)) }}"
                        data-values="{{ json_encode(array_values($evaluationAverages)) }}">
                    </canvas>
                </div>
            </div>
        </div>

        <!-- ===== CRITICAL SECTIONS - BETTER SPACING ===== -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- OJT Students Needing Attention -->
            <div class="bg-white/5 border border-red-500/30 rounded-xl p-6 shadow-lg backdrop-blur-sm overflow-hidden">
                <h3 class="text-lg font-bold text-white mb-5 flex items-center gap-2">
                    <svg class="h-5 w-5 text-red-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4v2m0 5v1m8.5-15a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>OJT Students Needing Attention</span>
                </h3>
                <div class="space-y-3 max-h-96 overflow-y-auto">
                    @forelse($studentsNeedingAttention as $assignment)
                        <div class="p-4 bg-red-900/20 border border-red-500/30 rounded-lg hover:bg-red-900/30 transition">
                            <div class="flex items-center justify-between gap-3">
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-semibold text-white truncate">{{ $assignment->student->name }}</p>
                                    <p class="text-xs text-gray-400 truncate mt-1">{{ $assignment->company->name }}</p>
                                </div>
                                <a href="{{ route('ojt_adviser.student-logs', $assignment->student) }}" class="px-3 py-1.5 bg-red-600/50 hover:bg-red-600 text-white text-xs font-semibold rounded transition flex-shrink-0">
                                    Review
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="p-8 text-center text-gray-400">
                            <p class="text-sm">✓ All OJT students on track</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Pending Evaluations List -->
            <div class="bg-white/5 border border-amber-500/30 rounded-xl p-6 shadow-lg backdrop-blur-sm overflow-hidden">
                <h3 class="text-lg font-bold text-white mb-5 flex items-center gap-2">
                    <svg class="h-5 w-5 text-amber-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>Due for Evaluation</span>
                </h3>
                <div class="space-y-3 max-h-96 overflow-y-auto">
                    @forelse($pendingEvaluations as $assignment)
                        <div class="p-4 bg-amber-900/20 border border-amber-500/30 rounded-lg hover:bg-amber-900/30 transition">
                            <div class="flex items-center justify-between gap-3">
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-semibold text-white truncate">{{ $assignment->student->name }}</p>
                                    <p class="text-xs text-gray-400 truncate mt-1">{{ $assignment->company->name }}</p>
                                </div>
                                <a href="{{ route('ojt_adviser.evaluations.student', $assignment->student) }}" class="px-3 py-1.5 bg-amber-600/50 hover:bg-amber-600 text-white text-xs font-semibold rounded transition flex-shrink-0">
                                    Evaluate
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="p-6 text-center text-gray-400">
                            <p class="text-sm">✓ All evaluations current</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- ===== ENHANCED STUDENT TABLE ===== -->
        <div class="bg-white/5 border border-white/10 rounded-xl overflow-hidden shadow-lg backdrop-blur-sm">
            <div class="p-6 border-b border-white/10 flex justify-between items-center">
                <h3 class="text-lg font-bold text-white">Assigned OJT Students Overview</h3>
                <a href="{{ route('ojt_adviser.students') }}" class="text-indigo-400 hover:text-indigo-300 text-sm font-bold uppercase tracking-wider transition">View All</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-black/30 border-b border-white/10">
                        <tr>
                            <th class="px-6 py-4 text-xs font-bold text-gray-300 uppercase">Student</th>
                            <th class="px-6 py-4 text-xs font-bold text-gray-300 uppercase">Company</th>
                            <th class="px-6 py-4 text-xs font-bold text-gray-300 uppercase">Progress</th>
                            <th class="px-6 py-4 text-xs font-bold text-gray-300 uppercase text-right">Hours</th>
                            <th class="px-6 py-4 text-xs font-bold text-gray-300 uppercase">Status</th>
                            <th class="px-6 py-4 text-xs font-bold text-gray-300 uppercase text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        @forelse($assignments->take(8) as $assignment)
                            <tr class="hover:bg-white/5 transition">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3 min-w-0">
                                        <div class="h-8 w-8 rounded-full bg-indigo-500/20 flex items-center justify-center text-indigo-300 font-bold text-xs flex-shrink-0">
                                            {{ substr($assignment->student->name, 0, 1) }}
                                        </div>
                                        <span class="font-semibold text-gray-100 truncate">{{ $assignment->student->name }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-gray-300 truncate max-w-xs">{{ $assignment->company->name }}</td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2">
                                        <div class="w-24 h-2 bg-gray-700 rounded-full overflow-hidden flex-shrink-0">
                                            <div class="h-full bg-indigo-500 rounded-full" style="width: {{ $assignment->progressPercentage() }}%"></div>
                                        </div>
                                        <span class="text-xs font-semibold text-gray-300 w-10 flex-shrink-0">{{ $assignment->progressPercentage() }}%</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-right text-gray-300 font-mono text-xs">
                                    {{ $assignment->totalApprovedHours() }}/{{ $assignment->required_hours }}
                                </td>
                                <td class="px-6 py-4">
                                    @if($assignment->progressPercentage() >= 100)
                                        <x-status-badge status="completed" label="Completed" size="sm" />
                                    @else
                                        <x-status-badge status="active" label="Active" size="sm" />
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="#" class="p-1.5 hover:bg-white/10 rounded transition" title="View Profile">
                                            <svg class="h-4 w-4 text-gray-400 hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </a>
                                        <a href="{{ route('ojt_adviser.evaluations.student', $assignment->student) }}" class="p-1.5 hover:bg-white/10 rounded transition" title="Evaluate">
                                            <svg class="h-4 w-4 text-gray-400 hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m7 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        </a>
                                        <a href="{{ route('ojt_adviser.student-logs', $assignment->student) }}" class="p-1.5 hover:bg-white/10 rounded transition" title="View Logs">
                                            <svg class="h-4 w-4 text-gray-400 hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-8 text-center text-gray-400">
                                    No assigned OJT students. Check back later.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- ===== RECENT ACTIVITIES ===== -->
        @if($recentActivities->count() > 0)
        <div class="bg-white/5 border border-white/10 rounded-xl p-6 shadow-lg backdrop-blur-sm">
            <h3 class="text-lg font-bold text-white mb-5 flex items-center gap-2">
                <svg class="h-5 w-5 text-cyan-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                </svg>
                <span>Recent Work Activities</span>
            </h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($recentActivities->take(6) as $activity)
                    <div class="p-4 bg-cyan-900/10 border border-cyan-500/20 rounded-lg hover:bg-cyan-900/20 transition">
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold text-white truncate">{{ $activity->assignment->student->name }}</p>
                                <p class="text-xs text-gray-400 mt-1">{{ $activity->work_date->format('M d, Y') }}</p>
                                <p class="text-xs text-cyan-300 font-medium mt-2">{{ $activity->hours }} hours</p>
                            </div>
                            <span class="text-xs text-cyan-300 font-semibold whitespace-nowrap flex-shrink-0">{{ $activity->work_date->diffForHumans() }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            try {
                // Student Progress Bar Chart
                const progressCanvas = document.getElementById('studentProgressChart');
                if (progressCanvas && progressCanvas.dataset.labels) {
                    const progressLabels = JSON.parse(progressCanvas.dataset.labels);
                    const progressData = JSON.parse(progressCanvas.dataset.values);

                    const progressCtx = progressCanvas.getContext('2d');
                    new Chart(progressCtx, {
                        type: 'bar',
                        data: {
                            labels: progressLabels && progressLabels.length > 0 ? progressLabels : ['No Data'],
                            datasets: [{
                                label: 'Completion %',
                                data: progressData && progressData.length > 0 ? progressData : [0],
                                backgroundColor: window.getWorklogChartColor('submitted', '#6366f1'),
                                borderRadius: 6,
                                barThickness: 12,
                            }]
                        },
                        options: {
                            indexAxis: 'y',
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: { display: false },
                                tooltip: {
                                    callbacks: {
                                        label: function(context) {
                                            return context.parsed.x + '% Complete';
                                        }
                                    }
                                }
                            },
                            scales: {
                                x: {
                                    max: 100,
                                    grid: { color: 'rgba(255, 255, 255, 0.05)' },
                                    ticks: { color: '#9ca3af' }
                                },
                                y: {
                                    grid: { display: false },
                                    ticks: { color: '#9ca3af', font: { size: 11 } }
                                }
                            }
                        }
                    });
                }
            } catch (error) {
                console.error('Error rendering student progress chart:', error);
            }

            try {
                // Evaluation Radar Chart
                const radarCanvas = document.getElementById('evaluationRadarChart');
                if (radarCanvas && radarCanvas.dataset.labels) {
                    const evaluationLabels = JSON.parse(radarCanvas.dataset.labels);
                    const evaluationData = JSON.parse(radarCanvas.dataset.values);

                    const radarCtx = radarCanvas.getContext('2d');
                    new Chart(radarCtx, {
                        type: 'radar',
                        data: {
                            labels: evaluationLabels && evaluationLabels.length > 0 ? evaluationLabels : ['No Data'],
                            datasets: [{
                                label: 'Average Score (0-5)',
                                data: evaluationData && evaluationData.length > 0 ? evaluationData : [0],
                                backgroundColor: 'rgba(16, 185, 129, 0.15)',
                                borderColor: window.getWorklogChartColor('approved', '#10b981'),
                                pointBackgroundColor: '#10b981',
                                pointBorderColor: '#10b981',
                                pointHoverBackgroundColor: '#059669',
                                pointRadius: 4,
                                pointHoverRadius: 6,
                                borderWidth: 2,
                                fill: true,
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: {
                                r: {
                                    min: 0,
                                    max: 5,
                                    grid: { color: 'rgba(255, 255, 255, 0.1)' },
                                    angleLines: { color: 'rgba(255, 255, 255, 0.1)' },
                                    pointLabels: { 
                                        color: '#9ca3af',
                                        font: { size: 11, weight: 'bold' }
                                    },
                                    ticks: {
                                        color: '#6b7280',
                                        font: { size: 10 }
                                    }
                                }
                            },
                            plugins: {
                                legend: {
                                    labels: { color: '#9ca3af', font: { size: 11 } }
                                },
                                tooltip: {
                                    callbacks: {
                                        label: function(context) {
                                            return context.parsed.r.toFixed(1) + ' / 5.0';
                                        }
                                    }
                                }
                            }
                        }
                    });
                }
            } catch (error) {
                console.error('Error rendering evaluation radar chart:', error);
            }
        });
    </script>
</x-ojt-adviser-layout>
