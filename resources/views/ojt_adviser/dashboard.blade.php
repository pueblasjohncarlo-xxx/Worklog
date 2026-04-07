<x-ojt-adviser-layout>
    <x-slot name="header">
        OJT Adviser Dashboard
    </x-slot>

    <div class="space-y-6">
        <!-- Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Total Students -->
            <div class="bg-indigo-900/40 border border-indigo-500/30 rounded-2xl p-6 shadow-xl backdrop-blur-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-indigo-300 text-sm font-bold uppercase tracking-wider">Assigned OJT Students</p>
                        <h3 class="text-4xl font-black text-white mt-1">{{ $totalStudents }}</h3>
                    </div>
                    <div class="p-3 bg-indigo-500/20 rounded-xl">
                        <svg class="h-8 w-8 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Incomplete Logs -->
            <div class="bg-red-900/40 border border-red-500/30 rounded-2xl p-6 shadow-xl backdrop-blur-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-red-300 text-sm font-bold uppercase tracking-wider">Incomplete Logs</p>
                        <h3 class="text-4xl font-black text-white mt-1">{{ $incompleteLogsCount }}</h3>
                    </div>
                    <div class="p-3 bg-red-500/20 rounded-xl">
                        <svg class="h-8 w-8 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Completed Hours -->
            <div class="bg-emerald-900/40 border border-emerald-500/30 rounded-2xl p-6 shadow-xl backdrop-blur-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-emerald-300 text-sm font-bold uppercase tracking-wider">Completed OJT</p>
                        <h3 class="text-4xl font-black text-white mt-1">{{ $completedHoursCount }}</h3>
                    </div>
                    <div class="p-3 bg-emerald-500/20 rounded-xl">
                        <svg class="h-8 w-8 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Evaluation Progress -->
            <div class="bg-amber-900/40 border border-amber-500/30 rounded-2xl p-6 shadow-xl backdrop-blur-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-amber-300 text-sm font-bold uppercase tracking-wider">Evaluation Progress</p>
                        <h3 class="text-4xl font-black text-white mt-1">{{ $evaluationProgress }}%</h3>
                    </div>
                    <div class="p-3 bg-amber-500/20 rounded-xl">
                        <svg class="h-8 w-8 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Progress Overview Bar Chart -->
            <div class="bg-white/5 border border-white/10 rounded-2xl p-6 shadow-xl backdrop-blur-md">
                <h3 class="text-lg font-bold text-white mb-4 flex items-center gap-2">
                    <svg class="h-5 w-5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                    Student Completion Progress
                </h3>
                <div class="relative h-[300px]">
                    <canvas id="studentProgressChart"
                        data-labels="{{ json_encode($assignments->pluck('student.name')) }}"
                        data-values="{{ json_encode($assignments->map(fn($a) => $a->progressPercentage())->values()) }}">
                    </canvas>
                </div>
            </div>

            <!-- Evaluation Radar Chart -->
            <div class="bg-white/5 border border-white/10 rounded-2xl p-6 shadow-xl backdrop-blur-md">
                <h3 class="text-lg font-bold text-white mb-4 flex items-center gap-2">
                    <svg class="h-5 w-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.001 0 0120.488 9z" />
                    </svg>
                    Average Performance Metrics
                </h3>
                <div class="relative h-[300px]">
                    <canvas id="evaluationRadarChart"
                        data-labels="{{ json_encode(array_keys($evaluationAverages)) }}"
                        data-values="{{ json_encode(array_values($evaluationAverages)) }}">
                    </canvas>
                </div>
            </div>
        </div>

        <!-- Student List -->
        <div class="bg-white/5 border border-white/10 rounded-2xl overflow-hidden shadow-xl backdrop-blur-md">
            <div class="p-6 border-b border-white/10 flex justify-between items-center">
                <h3 class="text-lg font-bold text-white">Assigned OJT Student Overview</h3>
                <a href="{{ route('ojt_adviser.students') }}" class="text-indigo-400 hover:text-indigo-300 text-sm font-bold uppercase tracking-wider transition-colors">View All OJT Students</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-black/30">
                        <tr>
                            <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Student</th>
                            <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Company</th>
                            <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Supervisor</th>
                            <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider text-right">Hours</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        @foreach($assignments->take(5) as $assignment)
                            <tr class="hover:bg-white/5 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="h-8 w-8 rounded-full bg-indigo-500/20 flex items-center justify-center text-indigo-400 font-bold text-xs">
                                            {{ substr($assignment->student->name, 0, 1) }}
                                        </div>
                                        <span class="text-sm font-semibold text-gray-200">{{ $assignment->student->name }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-sm text-gray-400">{{ $assignment->company->name }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-sm text-gray-400">{{ $assignment->supervisor->name ?? 'N/A' }}</span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <span class="text-sm font-mono text-indigo-400">{{ $assignment->totalApprovedHours() }} / {{ $assignment->required_hours }}</span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Student Progress Bar Chart
            const progressCanvas = document.getElementById('studentProgressChart');
            const progressLabels = JSON.parse(progressCanvas.dataset.labels);
            const progressData = JSON.parse(progressCanvas.dataset.values);

            const progressCtx = progressCanvas.getContext('2d');
            new Chart(progressCtx, {
                type: 'bar',
                data: {
                    labels: progressLabels,
                    datasets: [{
                        label: 'Completion %',
                        data: progressData,
                        backgroundColor: '#6366f1',
                        borderRadius: 8,
                        barThickness: 15,
                    }]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        x: {
                            max: 100,
                            grid: { color: 'rgba(255, 255, 255, 0.05)' },
                            ticks: { color: '#9ca3af' }
                        },
                        y: {
                            grid: { display: false },
                            ticks: { color: '#9ca3af' }
                        }
                    }
                }
            });

            // Evaluation Radar Chart
            const radarCanvas = document.getElementById('evaluationRadarChart');
            const evaluationLabels = JSON.parse(radarCanvas.dataset.labels);
            const evaluationData = JSON.parse(radarCanvas.dataset.values);

            const radarCtx = radarCanvas.getContext('2d');
            new Chart(radarCtx, {
                type: 'radar',
                data: {
                    labels: evaluationLabels,
                    datasets: [{
                        label: 'Average Score',
                        data: evaluationData,
                        backgroundColor: 'rgba(16, 185, 129, 0.2)',
                        borderColor: '#10b981',
                        pointBackgroundColor: '#10b981',
                        borderWidth: 2
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
                            pointLabels: { color: '#9ca3af', font: { size: 12 } },
                            ticks: { display: false }
                        }
                    },
                    plugins: {
                        legend: { display: false }
                    }
                }
            });
        });
    </script>
</x-ojt-adviser-layout>
