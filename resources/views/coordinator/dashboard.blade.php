<x-coordinator-layout>
    <x-slot name="header">
        Coordinator Dashboard
    </x-slot>

    <div class="space-y-6">
        <!-- Top 4 Stat Cards -->
        <div class="grid gap-4 grid-cols-1 sm:grid-cols-2 lg:grid-cols-4">
            <!-- OJT Students -->
            <div class="bg-indigo-600/20 backdrop-blur-md border border-indigo-500/30 rounded-lg shadow-lg overflow-hidden hover:shadow-indigo-500/20 group transition-all">
                <div class="p-6">
                    <div class="text-sm text-indigo-200 font-bold uppercase tracking-widest">OJT Students</div>
                    <div class="mt-2 text-4xl font-black text-white">{{ $totalStudents ?? 0 }}</div>
                </div>
            </div>

            <!-- OJT Advisory -->
            <div class="bg-emerald-600/20 backdrop-blur-md border border-emerald-500/30 rounded-lg shadow-lg overflow-hidden hover:shadow-emerald-500/20 group transition-all">
                <div class="p-6">
                    <div class="text-sm text-emerald-200 font-bold uppercase tracking-widest">OJT Advisory</div>
                    <div class="mt-2 text-4xl font-black text-white">{{ $advisersCount ?? 0 }}</div>
                </div>
            </div>

            <!-- Industry -->
            <div class="bg-amber-600/20 backdrop-blur-md border border-amber-500/30 rounded-lg shadow-lg overflow-hidden hover:shadow-amber-500/20 group transition-all">
                <div class="p-6">
                    <div class="text-sm text-amber-200 font-bold uppercase tracking-widest">Industry</div>
                    <div class="mt-2 text-4xl font-black text-white">{{ $totalCompanies ?? 0 }}</div>
                </div>
            </div>

            <!-- Status -->
            <div class="bg-rose-600/20 backdrop-blur-md border border-rose-500/30 rounded-lg shadow-lg overflow-hidden hover:shadow-rose-500/20 group transition-all">
                <div class="p-6">
                    <div class="text-sm text-rose-200 font-bold uppercase tracking-widest">Status</div>
                    <div class="mt-2 text-4xl font-black text-white">{{ $activeOJTs ?? 0 }}</div>
                </div>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- OJT Students Chart -->
            <div class="bg-black/40 backdrop-blur-md border border-indigo-500/20 rounded-lg p-6 shadow-xl">
                <h3 class="text-lg font-bold text-white mb-4 flex items-center gap-2">
                    <svg class="h-5 w-5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                    OJT Students
                </h3>
                <div class="relative h-64">
                    <canvas id="studentChart" 
                        data-labels="{{ json_encode($sectionProgress?->pluck('section')->toArray() ?? []) }}"
                        data-values="{{ json_encode($sectionProgress?->pluck('count')->toArray() ?? []) }}">
                    </canvas>
                </div>
            </div>

            <!-- Daily Attendance Trends Chart -->
            <div class="bg-black/40 backdrop-blur-md border border-indigo-500/20 rounded-lg p-6 shadow-xl">
                <h3 class="text-lg font-bold text-white mb-4 flex items-center gap-2">
                    <svg class="h-5 w-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                    </svg>
                    Daily Attendance Trends
                </h3>
                <div class="relative h-64">
                    <canvas id="attendanceChart"
                        data-labels="{{ json_encode($attendanceTrend?->pluck('day')->toArray() ?? []) }}"
                        data-total="{{ json_encode($attendanceTrend?->pluck('total')->toArray() ?? []) }}"
                        data-late="{{ json_encode($attendanceTrend?->pluck('late')->toArray() ?? []) }}">
                    </canvas>
                </div>
            </div>
        </div>

        <!-- OJT Advisory Section -->
        <div class="bg-black/40 backdrop-blur-md border border-indigo-500/20 rounded-lg p-6 shadow-xl">
            <h3 class="text-lg font-bold text-white mb-6">OJT Advisory</h3>

            @if($ojtAdvisers && count($ojtAdvisers) > 0)
                <div class="space-y-4">
                    @foreach($ojtAdvisers as $adviser)
                        <div class="border border-white/10 rounded-lg p-4 bg-white/5 hover:bg-white/10 transition-all group">
                            <div class="flex items-center justify-between gap-4">
                                <div class="flex items-center gap-4 flex-1">
                                    <div class="h-12 w-12 rounded-full overflow-hidden border border-white/10 bg-indigo-600 flex items-center justify-center shrink-0 font-bold text-white">
                                        @if($adviser->photo_url)
                                            <img src="{{ $adviser->photo_url }}" alt="{{ $adviser->name }}" class="h-full w-full object-cover">
                                        @else
                                            {{ substr($adviser->name, 0, 1) }}
                                        @endif
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <div class="text-base font-bold text-white">{{ $adviser->name }}</div>
                                        <div class="text-sm text-gray-400">{{ $adviser->email }}</div>
                                        <div class="mt-1">
                                            <span class="text-xs font-bold text-indigo-300 uppercase tracking-widest bg-indigo-500/10 px-2 py-1 rounded-full border border-indigo-500/20">
                                                {{ $adviser->assigned_students_count }} STUDENTS
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8 text-gray-300">
                    <p class="text-sm">No OJT advisers assigned yet.</p>
                </div>
            @endif
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // OJT Students Bar Chart - with defensive logic
            const studentCtx = document.getElementById('studentChart');
            if (studentCtx) {
                try {
                    const studentLabels = JSON.parse(studentCtx.getAttribute('data-labels') || '[]');
                    const studentValues = JSON.parse(studentCtx.getAttribute('data-values') || '[]');
                    
                    // Ensure we have data, otherwise use placeholder
                    const hasData = studentLabels && studentLabels.length > 0 && studentValues && studentValues.length > 0;
                    
                    new Chart(studentCtx, {
                        type: 'bar',
                        data: {
                            labels: hasData ? studentLabels : ['No Data Available'],
                            datasets: [{
                                label: 'Students',
                                data: hasData ? studentValues : [0],
                                backgroundColor: 'rgba(99, 102, 241, 0.8)',
                                borderColor: 'rgba(99, 102, 241, 1)',
                                borderWidth: 2,
                                borderRadius: 6
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            indexAxis: 'y',
                            plugins: {
                                legend: { display: false }
                            },
                            scales: {
                                x: {
                                    beginAtZero: true,
                                    grid: { color: 'rgba(255, 255, 255, 0.05)' },
                                    ticks: { color: 'rgba(255, 255, 255, 0.6)' }
                                },
                                y: {
                                    grid: { display: false },
                                    ticks: { color: 'rgba(255, 255, 255, 0.8)' }
                                }
                            }
                        }
                    });
                } catch (error) {
                    console.error('Failed to render student chart:', error);
                    studentCtx.style.display = 'none';
                }
            }

            // Daily Attendance Trends Line Chart - with defensive logic
            const attendanceCtx = document.getElementById('attendanceChart');
            if (attendanceCtx) {
                try {
                    const attendanceLabels = JSON.parse(attendanceCtx.getAttribute('data-labels') || '[]');
                    const totalData = JSON.parse(attendanceCtx.getAttribute('data-total') || '[]');
                    const lateData = JSON.parse(attendanceCtx.getAttribute('data-late') || '[]');
                    
                    // Ensure we have valid data arrays
                    const hasLabels = attendanceLabels && attendanceLabels.length > 0;
                    const hasTotal = totalData && totalData.length > 0;
                    const hasLate = lateData && lateData.length > 0;
                    
                    new Chart(attendanceCtx, {
                        type: 'line',
                        data: {
                            labels: hasLabels ? attendanceLabels : ['No Data'],
                            datasets: [
                                {
                                    label: 'Total Clock-ins',
                                    data: hasTotal ? totalData : [0],
                                    borderColor: '#10b981',
                                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                                    borderWidth: 2,
                                    fill: true,
                                    tension: 0.4,
                                    pointBackgroundColor: '#10b981',
                                    pointBorderColor: '#ffffff',
                                    pointBorderWidth: 2,
                                    pointRadius: 4
                                },
                                {
                                    label: 'Incomplete',
                                    data: hasLate ? lateData : [0],
                                    borderColor: '#ef4444',
                                    backgroundColor: 'rgba(239, 68, 68, 0.1)',
                                    borderWidth: 2,
                                    fill: true,
                                    tension: 0.4,
                                    pointBackgroundColor: '#ef4444',
                                    pointBorderColor: '#ffffff',
                                    pointBorderWidth: 2,
                                    pointRadius: 4
                                }
                            ]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    labels: { color: 'rgba(255, 255, 255, 0.8)' }
                                }
                            },
                            scales: {
                                x: {
                                    grid: { color: 'rgba(255, 255, 255, 0.05)' },
                                    ticks: { color: 'rgba(255, 255, 255, 0.6)' }
                                },
                                y: {
                                    beginAtZero: true,
                                    grid: { color: 'rgba(255, 255, 255, 0.05)' },
                                    ticks: { color: 'rgba(255, 255, 255, 0.6)' }
                                }
                            }
                        }
                    });
                } catch (error) {
                    console.error('Failed to render attendance chart:', error);
                    attendanceCtx.style.display = 'none';
                }
            }
        });
    </script>
    @endpush
</x-coordinator-layout>
