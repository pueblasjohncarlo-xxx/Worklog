<x-student-layout>
    <!-- DEBUG: resources/views/dashboards/student.blade.php (StudentController@index) -->
    <x-slot name="header">
        Student Dashboard
    </x-slot>

    <div class="space-y-8">
        <!-- Quick Action Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <a href="{{ route('student.tasks.index') }}" class="glass-panel p-8 flex flex-col items-center justify-center group hover:scale-[1.02] transition-transform">
                <div class="p-4 bg-indigo-500/20 rounded-2xl mb-4 group-hover:scale-110 transition-transform shadow-[0_0_15px_rgba(255,255,255,0.2)]">
                    <svg class="h-12 w-12 text-white drop-shadow-md" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7" />
                    </svg>
                </div>
                <span class="text-2xl font-black text-white drop-shadow-sm uppercase tracking-tight">My Tasks</span>
            </a>

            <a href="{{ route('student.journal.index') }}" class="bg-emerald-600/90 backdrop-blur-md border border-emerald-400/30 p-8 rounded-xl shadow-[0_8px_32px_0_rgba(0,0,0,0.37)] flex flex-col items-center justify-center group hover:scale-[1.02] transition-transform">
                <div class="p-4 bg-black/20 rounded-2xl mb-4 group-hover:scale-110 transition-transform text-white shadow-[0_0_15px_rgba(255,255,255,0.3)]">
                    <svg class="h-12 w-12 drop-shadow-md" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <span class="text-2xl font-black text-white drop-shadow-sm uppercase tracking-tight">Daily Journal</span>
            </a>

            <a href="{{ route('student.reports.index') }}" class="bg-indigo-900/90 backdrop-blur-md border border-indigo-400/30 p-8 rounded-xl shadow-[0_8px_32px_0_rgba(0,0,0,0.37)] flex flex-col items-center justify-center group hover:scale-[1.02] transition-transform">
                <div class="p-4 bg-black/20 rounded-2xl mb-4 group-hover:scale-110 transition-transform text-white shadow-[0_0_15px_rgba(255,255,255,0.2)]">
                    <svg class="h-12 w-12 drop-shadow-md" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <span class="text-2xl font-black text-white uppercase tracking-tight drop-shadow-sm">HOURS LOG</span>
            </a>
        </div>

        <!-- Attendance Calendar + Completion Status -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Attendance Calendar -->
            <div class="glass-panel p-6 shadow-xl">
                <div class="flex items-center justify-between mb-2 gap-2">
                    <h3 class="text-lg font-black text-white flex items-center gap-2 uppercase tracking-widest">
                        <svg class="h-6 w-6 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V5a2 2 0 012-2h4a2 2 0 012 2v2M3 19v-7a2 2 0 012-2h14a2 2 0 012 2v7a2 2 0 01-2 2H5a2 2 0 01-2-2z" />
                        </svg>
                        Attendance Calendar
                    </h3>
                    <div class="flex items-center gap-2">
                        <a href="{{ request()->fullUrlWithQuery(['attendance_month' => $calendarCurrentDate->copy()->subMonth()->format('Y-m')]) }}" class="px-2 py-1 bg-gray-800 hover:bg-gray-700 text-white rounded">&larr;</a>
                        <a href="{{ request()->fullUrlWithQuery(['attendance_month' => $calendarCurrentDate->copy()->addMonth()->format('Y-m')]) }}" class="px-2 py-1 bg-gray-800 hover:bg-gray-700 text-white rounded">&rarr;</a>
                    </div>
                </div>
                <div class="flex justify-between items-center text-sm font-bold text-gray-300 mb-3">
                    <span>{{ $calendarCurrentDate->format('F Y') }}</span>
                    <span>Total this month: {{ number_format($monthlyTotalHours ?? 0, 2) }}h</span>
                </div>
                <div class="grid grid-cols-7 gap-px bg-gray-900 rounded-lg text-xs text-center">
                    @foreach(['Sun','Mon','Tue','Wed','Thu','Fri','Sat'] as $d)
                        <div class="py-1 bg-gray-900 text-white">{{ $d }}</div>
                    @endforeach
                    @foreach($attendanceCalendar as $day)
                        @php
                            $statusClass = 'bg-gray-800 text-gray-300';
                            if($day['status'] === 'approved') $statusClass = 'bg-emerald-600 text-white';
                            elseif($day['status'] === 'submitted') $statusClass = 'bg-blue-500 text-white';
                            elseif($day['status'] === 'rejected') $statusClass = 'bg-rose-500 text-white';
                            elseif($day['status'] === 'draft') $statusClass = 'bg-yellow-500 text-gray-900';
                            if(!$day['is_current_month']) $statusClass = 'bg-gray-900 text-gray-500 opacity-40';
                        @endphp
                        <div class="h-16 p-1 border border-gray-700 {{ $statusClass }}" title="Status: {{ ucfirst($day['status'] ?? 'N/A') }}\nIn: {{ $day['time_in'] ? \Carbon\Carbon::parse($day['time_in'])->format('h:i A') : '-' }}\nOut: {{ $day['time_out'] ? \Carbon\Carbon::parse($day['time_out'])->format('h:i A') : '-' }}\nHours: {{ $day['hours'] !== null ? number_format($day['hours'], 2).'h' : '-' }}">
                            <div class="text-xs font-black">{{ $day['date']->day }}</div>
                            @if($day['time_in'] || $day['time_out'])
                                <div class="text-[8px] uppercase">{{ $day['time_in'] ? \Carbon\Carbon::parse($day['time_in'])->format('h:i A') : '-' }}</div>
                            @endif
                            @if($day['hours'] !== null)
                                <div class="text-[9px] uppercase font-bold">{{ number_format($day['hours'], 2) }}h</div>
                            @endif
                        </div>
                    @endforeach
                </div>
                <div class="mt-4 grid grid-cols-4 gap-3 text-xs">
                    <div class="bg-emerald-500/20 p-2 rounded border border-emerald-500/30">
                        <div class="text-emerald-300 font-bold">{{ number_format($monthlyApprovedHours ?? 0, 2) }}h</div>
                        <div class="text-gray-400 text-[10px]">Approved</div>
                    </div>
                    <div class="bg-blue-500/20 p-2 rounded border border-blue-500/30">
                        <div class="text-blue-300 font-bold">{{ number_format($monthlyPendingHours ?? 0, 2) }}h</div>
                        <div class="text-gray-400 text-[10px]">Pending</div>
                    </div>
                    <div class="bg-rose-500/20 p-2 rounded border border-rose-500/30">
                        <div class="text-rose-300 font-bold">{{ number_format($monthlyRejectedHours ?? 0, 2) }}h</div>
                        <div class="text-gray-400 text-[10px]">Rejected</div>
                    </div>
                    <div class="bg-slate-500/20 p-2 rounded border border-slate-500/30">
                        <div class="text-slate-300 font-bold">{{ number_format($monthlyRemainingHours ?? 0, 2) }}h</div>
                        <div class="text-gray-400 text-[10px]">Remaining</div>
                    </div>
                </div>
            </div>

            <!-- OJT Completion Bars -->
            <div class="glass-panel p-6 shadow-xl">
                <h3 class="text-lg font-black text-white mb-6 flex items-center gap-2 uppercase tracking-widest">
                    <svg class="h-6 w-6 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    OJT Hours Status
                </h3>
                <div class="relative h-[250px]">
                    <canvas id="completionProgressChart" data-labels="{{ json_encode(array_keys($completionStats)) }}" data-values="{{ json_encode(array_values($completionStats)) }}"></canvas>
                </div>
                <div class="mt-3 text-xs text-gray-300">
                    <div class="flex justify-between"><span>Target:</span><span>{{ $assignment->required_hours ?? 0 }} hrs</span></div>
                    <div class="flex justify-between"><span>Approved:</span><span>{{ number_format($completionStats['Completed'] ?? 0, 2) }} hrs</span></div>
                    <div class="flex justify-between"><span>Pending:</span><span>{{ number_format($completionStats['Pending'] ?? 0, 2) }} hrs</span></div>
                </div>
            </div>
        </div>

        <!-- Daily Attendance Section -->
        <div class="glass-panel overflow-hidden">
            <div class="bg-emerald-600 px-6 py-4 backdrop-blur-sm border-b border-emerald-500/30">
                <h3 class="text-xl font-black text-white drop-shadow-md uppercase tracking-wider">Daily Attendance</h3>
            </div>
            <div class="p-8">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 items-center">
                    <div>
                        <div class="mb-8">
                            <h4 class="text-gray-400 text-xs font-black uppercase tracking-[0.2em] mb-2">Today:</h4>
                            <p class="text-4xl font-black text-white drop-shadow-sm tracking-tight">{{ now()->format('l, F j, Y') }}</p>
                        </div>

                        <div class="flex flex-wrap gap-4">
                            <!-- Pending Past Session Alert -->
                            @if(isset($pastPendingLog) && $pastPendingLog)
                                <div class="w-full bg-orange-100 dark:bg-orange-900/30 border border-orange-400 text-orange-700 dark:text-orange-300 px-4 py-3 rounded-lg relative" role="alert">
                                    <strong class="font-bold">Pending Log!</strong>
                                    <span class="block sm:inline">You forgot to clock out on {{ $pastPendingLog->work_date->format('M d, Y') }}.</span>
                                    <div class="mt-2">
                                        <form action="{{ route('student.worklogs.manual-clock-out', $pastPendingLog) }}" method="POST" class="flex items-end gap-2">
                                            @csrf
                                            @method('PATCH')
                                            <div>
                                                <label for="time_out" class="block text-xs font-bold uppercase text-orange-600 dark:text-orange-400 mb-1">Time Out:</label>
                                                <input type="time" name="time_out" id="time_out" required class="text-sm rounded-md border-orange-300 dark:border-orange-600 dark:bg-orange-900/50 focus:ring-orange-500 focus:border-orange-500">
                                            </div>
                                            <button type="submit" class="px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white text-sm font-bold rounded-md shadow-sm transition-colors">
                                                Submit Manual Clock-Out
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @endif

                            @if((!$todayLog || !$todayLog->time_in) && (!isset($pastPendingLog) || !$pastPendingLog))
                                <form action="{{ route('student.clock-in') }}" method="POST" class="flex flex-wrap items-end gap-3">
                                    @csrf

                                    <div class="flex items-center gap-2">
                                        <label for="time_in" class="text-xs font-bold uppercase tracking-wider text-white">Clock In Time</label>
                                        <input
                                            type="time"
                                            name="time_in"
                                            id="time_in"
                                            required
                                            value="{{ old('time_in', now()->format('H:i')) }}"
                                            class="rounded-md border border-white/20 bg-black/20 text-white px-2 py-2"
                                        >
                                    </div>

                                    <button type="submit" class="inline-flex items-center gap-2 px-8 py-4 bg-emerald-600 hover:bg-emerald-500 text-white font-bold rounded-full transition-all shadow-[0_0_20px_rgba(16,185,129,0.4)] hover:shadow-[0_0_30px_rgba(16,185,129,0.6)] hover:scale-105">
                                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                        </svg>
                                        Clock In
                                    </button>
                                </form>
                            @endif

                            @if($todayLog && $todayLog->time_in && !$todayLog->time_out)
                                @if(isset($earlyClockOut) && $earlyClockOut)
                                    <button type="button" onclick="openEarlyClockOutModal()" class="inline-flex items-center gap-2 px-8 py-4 bg-yellow-500 hover:bg-yellow-400 text-white font-bold rounded-full transition-all shadow-[0_0_20px_rgba(234,179,8,0.4)] hover:shadow-[0_0_30px_rgba(234,179,8,0.6)] hover:scale-105">
                                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 0l3-3m-3 3l-3-3m0 0v4m8 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        Early Clock Out
                                    </button>
                                @else
                                    <form action="{{ route('student.clock-out') }}" method="POST" class="flex flex-col gap-2">
                                        @csrf
                                        <button type="submit" class="inline-flex items-center gap-2 px-8 py-4 bg-rose-600 hover:bg-rose-500 text-white font-bold rounded-full transition-all shadow-[0_0_20px_rgba(225,29,72,0.4)] hover:shadow-[0_0_30px_rgba(225,29,72,0.6)] hover:scale-105">
                                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                            </svg>
                                            Clock Out
                                        </button>
                                    </form>
                                @endif
                            @endif

                            <div id="earlyClockOutModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/60 px-4">
                                <div class="w-full max-w-lg rounded-2xl bg-gray-900 border border-white/10 shadow-2xl">
                                    <div class="p-6">
                                        <h3 class="text-lg font-bold text-white mb-3">Early Clock Out Confirmation</h3>
                                        <p class="text-xs text-gray-300 mb-4">You are clocking out before 8 hours. Please confirm reason for early clock out.</p>
                                        <form action="{{ route('student.clock-out') }}" method="POST" class="space-y-4">
                                            @csrf
                                            <div>
                                                <label for="early_reason" class="block text-sm text-gray-300 mb-1">Reason</label>
                                                <textarea name="early_reason" id="early_reason" rows="3" required class="w-full rounded-md border border-gray-600 bg-gray-800 text-white px-3 py-2"></textarea>
                                            </div>
                                            <div class="flex justify-end items-center gap-2">
                                                <button type="button" onclick="closeEarlyClockOutModal()" class="px-4 py-2 bg-gray-700 text-gray-100 rounded-md">Cancel</button>
                                                <button type="submit" class="px-4 py-2 bg-rose-600 hover:bg-rose-500 text-white rounded-md">Confirm Clock Out</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <script>
                                function openEarlyClockOutModal() {
                                    const modal = document.getElementById('earlyClockOutModal');
                                    modal.classList.remove('hidden');
                                    modal.classList.add('flex');
                                }

                                function closeEarlyClockOutModal() {
                                    const modal = document.getElementById('earlyClockOutModal');
                                    modal.classList.remove('flex');
                                    modal.classList.add('hidden');
                                }
                            </script>

                            @if($todayLog && $todayLog->time_out)
                                <div class="px-6 py-3 bg-white/10 text-gray-300 rounded-full font-bold flex items-center gap-2 border border-white/20">
                                    <svg class="h-5 w-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    Done for today
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="border-l border-white/10 pl-0 lg:pl-10">
                        <div class="flex justify-between items-center mb-6">
                            <h4 class="text-xs font-black text-gray-400 uppercase tracking-[0.2em]">Recent Logs (Click to edit)</h4>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-base">
                                <thead class="border-b border-white/10">
                                    <tr>
                                        <th class="py-3 font-black text-gray-500 uppercase tracking-widest text-xs">Date</th>
                                        <th class="py-3 font-black text-gray-500 uppercase tracking-widest text-xs">In</th>
                                        <th class="py-3 font-black text-gray-500 uppercase tracking-widest text-xs">Out</th>
                                        <th class="py-3 font-black text-gray-500 uppercase tracking-widest text-xs">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-white/5">
                                    @forelse($workLogs->take(3) as $log)
                                        <tr class="hover:bg-white/5 transition-colors cursor-pointer row-link" data-href="{{ route('student.worklogs.edit', $log) }}">
                                            <td class="py-4 font-bold text-white">{{ $log->work_date->format('M d, Y') }}</td>
                                            <td class="py-4 text-gray-300">{{ $log->time_in ? \Carbon\Carbon::parse($log->time_in)->format('h:i A') : '-' }}</td>
                                            <td class="py-4 text-gray-300">{{ $log->time_out ? \Carbon\Carbon::parse($log->time_out)->format('h:i A') : '-' }}</td>
                                            <td class="py-4">
                                                <span class="px-3 py-1 rounded text-[11px] font-black uppercase tracking-wider
                                                    {{ $log->status === 'approved' ? 'bg-emerald-500 text-white shadow-[0_0_10px_rgba(16,185,129,0.4)]' : '' }}
                                                    {{ $log->status === 'submitted' ? 'bg-blue-500 text-white shadow-[0_0_10px_rgba(59,130,246,0.4)]' : '' }}
                                                    {{ $log->status === 'draft' ? 'bg-gray-500 text-white' : '' }}
                                                    {{ $log->status === 'rejected' ? 'bg-rose-500 text-white shadow-[0_0_10px_rgba(244,63,94,0.4)]' : '' }}
                                                ">
                                                    {{ $log->status }}
                                                </span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="py-4 text-center text-gray-400 italic">No logs found</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Active Tasks Section (REMOVED - Now on dedicated page) -->
        {{-- <div class="glass-panel overflow-hidden" id="my-tasks"> ... </div> --}}
    </div>

    <div id="submitConfirmModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/60 px-4">
        <div class="w-full max-w-md rounded-2xl bg-gray-900 border border-white/10 shadow-2xl">
            <div class="p-6">
                <div class="text-white font-extrabold text-lg">Confirm Submit</div>
                <div class="mt-2 text-sm text-gray-300">
                    Before submitting, please preview the report to check if there are mistakes. After submit, you cannot edit until reviewed.
                </div>

                <div class="mt-5 flex items-center justify-between gap-3">
                    <a id="submitConfirmPreviewLink" href="#" target="_blank" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-bold">
                        Preview
                    </a>
                    <div class="flex items-center gap-2">
                        <button type="button" onclick="closeSubmitConfirm()" class="px-4 py-2 bg-white/10 hover:bg-white/15 text-white rounded-lg font-bold">
                            Cancel
                        </button>
                        <form id="submitConfirmForm" method="POST" action="#">
                            @csrf
                            <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg font-bold">
                                Confirm Submit
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function openSubmitConfirmFromButton(button) {
            openSubmitConfirm(button.dataset.action, button.dataset.preview);
        }

        function openSubmitConfirm(actionUrl, previewUrl) {
            const modal = document.getElementById('submitConfirmModal');
            const form = document.getElementById('submitConfirmForm');
            const preview = document.getElementById('submitConfirmPreviewLink');

            form.action = actionUrl;
            preview.href = previewUrl;
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeSubmitConfirm() {
            const modal = document.getElementById('submitConfirmModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Weekly Hours Bar Chart (legacy, only if exists)
            const hoursCanvas = document.getElementById('weeklyHoursChart');
            if (hoursCanvas) {
                const weeklyHoursLabels = JSON.parse(hoursCanvas.dataset.labels);
                const weeklyHoursData = JSON.parse(hoursCanvas.dataset.values);

                const hoursCtx = hoursCanvas.getContext('2d');
                new Chart(hoursCtx, {
                    type: 'bar',
                    data: {
                        labels: weeklyHoursLabels,
                        datasets: [{
                            label: 'Hours',
                            data: weeklyHoursData,
                            backgroundColor: '#818cf8',
                            borderRadius: 6,
                            barThickness: 30,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: { color: 'rgba(255, 255, 255, 0.05)' },
                                ticks: { color: '#9ca3af' }
                            },
                            x: {
                                grid: { display: false },
                                ticks: { color: '#9ca3af' }
                            }
                        }
                    }
                });
            }

            // OJT Completion Status Bar Chart
            const progressCanvas = document.getElementById('completionProgressChart');
            if (progressCanvas) {
                const completionLabels = JSON.parse(progressCanvas.dataset.labels);
                const completionData = JSON.parse(progressCanvas.dataset.values);

                const progressCtx = progressCanvas.getContext('2d');
                new Chart(progressCtx, {
                    type: 'bar',
                    data: {
                        labels: completionLabels,
                        datasets: [{
                            label: 'Hours',
                            data: completionData,
                            backgroundColor: completionLabels.map(label => {
                                if (label.toLowerCase() === 'completed') return '#10b981';
                                if (label.toLowerCase() === 'pending') return '#3b82f6';
                                if (label.toLowerCase() === 'rejected') return '#ef4444';
                                if (label.toLowerCase() === 'remaining') return '#64748b';
                                return '#9ca3af';
                            }),
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return context.formattedValue + ' hrs';
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                title: {
                                    display: true,
                                    text: 'Hours'
                                },
                                grid: { color: 'rgba(255, 255, 255, 0.12)' },
                                ticks: { color: '#9ca3af' }
                            },
                            x: {
                                grid: { display: false },
                                ticks: { color: '#9ca3af' }
                            }
                        }
                    }
                });
            }
        });
    </script>
</x-student-layout>
