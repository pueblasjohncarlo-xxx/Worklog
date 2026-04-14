<x-coordinator-layout>
    <x-slot name="header">
        Coordinator Dashboard
    </x-slot>

    <div
        x-data="dashboardInteractions()"
        x-init="init({
            sectionReportOverview: @js($sectionReportOverview ?? []),
            adviserData: @js($ojtAdvisers ?? [])
        })"
        class="space-y-6"
    >
        <div class="grid gap-4 grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 xl:grid-cols-8">
            <a href="{{ route('coordinator.student-overview') }}" class="block bg-indigo-600/20 backdrop-blur-md border border-indigo-500/30 rounded-lg shadow-lg overflow-hidden cursor-pointer hover:-translate-y-0.5 hover:shadow-indigo-500/20 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-400 transition-all">
                <div class="p-4">
                    <div class="text-xs text-indigo-200 font-bold uppercase tracking-widest">Students</div>
                    <div class="mt-2 text-3xl font-black text-white">{{ $totalStudents ?? 0 }}</div>
                </div>
            </a>

            <a href="{{ route('coordinator.deployment.index') }}" class="block bg-sky-600/20 backdrop-blur-md border border-sky-500/30 rounded-lg shadow-lg overflow-hidden cursor-pointer hover:-translate-y-0.5 hover:shadow-sky-500/20 focus:outline-none focus-visible:ring-2 focus-visible:ring-sky-400 transition-all">
                <div class="p-4">
                    <div class="text-xs text-sky-200 font-bold uppercase tracking-widest">Active OJT</div>
                    <div class="mt-2 text-3xl font-black text-white">{{ $activeOJTs ?? 0 }}</div>
                </div>
            </a>

            <a href="{{ route('coordinator.adviser-overview') }}" class="block bg-emerald-600/20 backdrop-blur-md border border-emerald-500/30 rounded-lg shadow-lg overflow-hidden cursor-pointer hover:-translate-y-0.5 hover:shadow-emerald-500/20 focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-400 transition-all">
                <div class="p-4">
                    <div class="text-xs text-emerald-200 font-bold uppercase tracking-widest">OJT Advisers</div>
                    <div class="mt-2 text-3xl font-black text-white">{{ $advisersCount ?? 0 }}</div>
                </div>
            </a>

            <a href="{{ route('coordinator.supervisor-overview') }}" class="block bg-cyan-600/20 backdrop-blur-md border border-cyan-500/30 rounded-lg shadow-lg overflow-hidden cursor-pointer hover:-translate-y-0.5 hover:shadow-cyan-500/20 focus:outline-none focus-visible:ring-2 focus-visible:ring-cyan-400 transition-all">
                <div class="p-4">
                    <div class="text-xs text-cyan-200 font-bold uppercase tracking-widest">Supervisors</div>
                    <div class="mt-2 text-3xl font-black text-white">{{ $supervisorsCount ?? 0 }}</div>
                </div>
            </a>

            <a href="{{ route('coordinator.companies.index') }}" class="block bg-amber-600/20 backdrop-blur-md border border-amber-500/30 rounded-lg shadow-lg overflow-hidden cursor-pointer hover:-translate-y-0.5 hover:shadow-amber-500/20 focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-400 transition-all">
                <div class="p-4">
                    <div class="text-xs text-amber-200 font-bold uppercase tracking-widest">Industry</div>
                    <div class="mt-2 text-3xl font-black text-white">{{ $totalCompanies ?? 0 }}</div>
                </div>
            </a>

            <a href="{{ route('coordinator.registrations.pending') }}" class="block bg-fuchsia-600/20 backdrop-blur-md border border-fuchsia-500/30 rounded-lg shadow-lg overflow-hidden cursor-pointer hover:-translate-y-0.5 hover:shadow-fuchsia-500/20 focus:outline-none focus-visible:ring-2 focus-visible:ring-fuchsia-400 transition-all">
                <div class="p-4">
                    <div class="text-xs text-fuchsia-200 font-bold uppercase tracking-widest">Pending Approvals</div>
                    <div class="mt-2 text-3xl font-black text-white">{{ $pendingApprovals ?? 0 }}</div>
                </div>
            </a>

            <a href="{{ route('coordinator.accomplishment-reports') }}" class="block bg-rose-600/20 backdrop-blur-md border border-rose-500/30 rounded-lg shadow-lg overflow-hidden cursor-pointer hover:-translate-y-0.5 hover:shadow-rose-500/20 focus:outline-none focus-visible:ring-2 focus-visible:ring-rose-400 transition-all">
                <div class="p-4">
                    <div class="text-xs text-rose-200 font-bold uppercase tracking-widest">Pending AR</div>
                    <div class="mt-2 text-3xl font-black text-white">{{ $pendingAccomplishmentReports ?? 0 }}</div>
                </div>
            </a>

            <a href="{{ route('coordinator.compliance-overview') }}" class="block bg-orange-600/20 backdrop-blur-md border border-orange-500/30 rounded-lg shadow-lg overflow-hidden cursor-pointer hover:-translate-y-0.5 hover:shadow-orange-500/20 focus:outline-none focus-visible:ring-2 focus-visible:ring-orange-400 transition-all">
                <div class="p-4">
                    <div class="text-xs text-orange-200 font-bold uppercase tracking-widest">Needs Attention</div>
                    <div class="mt-2 text-3xl font-black text-white">{{ $studentsNeedingAttention?->count() ?? 0 }}</div>
                </div>
            </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
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

        <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
            <div class="bg-black/40 backdrop-blur-md border border-indigo-500/20 rounded-lg p-6 shadow-xl xl:col-span-2">
                <div class="flex items-center justify-between gap-4 mb-6">
                    <h3 class="text-lg font-bold text-white">Accomplishment Report Status Overview</h3>
                    <span class="text-xs text-indigo-200 bg-indigo-500/20 border border-indigo-400/30 rounded-full px-3 py-1">
                        Click a section to view details
                    </span>
                </div>

                @if(($sectionReportOverview?->count() ?? 0) > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($sectionReportOverview as $section)
                            <button
                                type="button"
                                @click="openSection('{{ $section['section'] }}')"
                                class="text-left p-4 rounded-lg border border-white/10 bg-white/5 hover:bg-indigo-500/15 hover:border-indigo-400/40 transition"
                            >
                                <div class="flex items-center justify-between gap-3">
                                    <div class="text-sm font-bold text-white">{{ $section['section'] }}</div>
                                    <div class="text-xs text-gray-300">{{ $section['total_students'] }} students</div>
                                </div>
                                <div class="mt-3">
                                    <div class="flex justify-between text-xs text-gray-300 mb-1">
                                        <span>Submitted</span>
                                        <span>{{ number_format($section['submitted_percentage'], 1) }}%</span>
                                    </div>
                                    <div class="h-2.5 rounded-full bg-white/10 overflow-hidden">
                                        <div class="h-full bg-emerald-400" style="width: {{ $section['submitted_percentage'] }}%"></div>
                                    </div>
                                </div>
                            </button>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-10 text-gray-300">
                        No active accomplishment report data yet.
                    </div>
                @endif
            </div>

            <div class="bg-black/40 backdrop-blur-md border border-indigo-500/20 rounded-lg p-6 shadow-xl">
                <div class="flex items-center justify-between gap-3">
                    <h3 class="text-lg font-bold text-white">Required Hours</h3>
                    <a href="{{ route('coordinator.settings.hours') }}" class="text-xs text-indigo-200 hover:text-white">Open settings</a>
                </div>
                <p class="text-sm text-gray-300 mt-2">Set required OJT hours for active or all student assignments.</p>

                <div class="mt-4 p-3 rounded-lg border border-white/10 bg-white/5">
                    <div class="text-xs uppercase tracking-wider text-gray-400">Current default</div>
                    <div class="text-2xl font-black text-white">{{ $currentRequiredHours ?? 1600 }} hrs</div>
                </div>

                <form action="{{ route('coordinator.settings.hours.update') }}" method="POST" class="mt-4 space-y-3">
                    @csrf
                    <div>
                        <label for="required_hours" class="block text-sm font-medium text-gray-200 mb-1">Required Hours</label>
                        <input
                            id="required_hours"
                            name="required_hours"
                            type="number"
                            min="1"
                            max="5000"
                            value="{{ old('required_hours', $currentRequiredHours ?? 1600) }}"
                            class="w-full rounded-md border border-white/20 bg-white/10 text-white px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-400"
                            required
                        >
                    </div>
                    <div>
                        <label for="scope" class="block text-sm font-medium text-gray-200 mb-1">Scope</label>
                        <select id="scope" name="scope" class="w-full rounded-md border border-white/20 bg-white/10 text-white px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-400">
                            <option value="active">Active assignments only</option>
                            <option value="all">All assignments</option>
                        </select>
                    </div>
                    <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-500 text-white font-semibold px-4 py-2 rounded-md transition">
                        Update Required Hours
                    </button>
                </form>
            </div>
        </div>

        <div class="bg-black/40 backdrop-blur-md border border-indigo-500/20 rounded-lg p-6 shadow-xl">
            <div class="flex items-center justify-between gap-4 mb-6">
                <h3 class="text-lg font-bold text-white">OJT Adviser Dashboard</h3>
                <span class="text-xs text-indigo-200 bg-indigo-500/20 border border-indigo-400/30 rounded-full px-3 py-1">
                    Click an adviser to view assigned students
                </span>
            </div>

            @if($ojtAdvisers && count($ojtAdvisers) > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
                    @foreach($ojtAdvisers as $adviser)
                        <button
                            type="button"
                            @click="openAdviser({{ $adviser['id'] }})"
                            class="text-left border border-white/10 rounded-lg p-4 bg-white/5 hover:bg-white/10 transition-all"
                        >
                            <div class="flex items-start justify-between gap-3">
                                <div class="flex items-center gap-4 flex-1 min-w-0">
                                    <div class="h-12 w-12 rounded-full overflow-hidden border border-white/10 bg-indigo-600 flex items-center justify-center shrink-0 font-bold text-white">
                                        @if($adviser['photo_url'])
                                            <img src="{{ $adviser['photo_url'] }}" data-avatar-user-id="{{ $adviser['id'] }}" alt="{{ $adviser['name'] }}" class="h-full w-full object-cover">
                                        @else
                                            {{ strtoupper(substr($adviser['name'], 0, 1)) }}
                                        @endif
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <div class="text-base font-bold text-white truncate">{{ $adviser['name'] }}</div>
                                        <div class="text-sm text-gray-400 truncate">{{ $adviser['email'] }}</div>
                                    </div>
                                </div>
                                <svg class="h-5 w-5 text-indigo-300 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </div>
                            <div class="grid grid-cols-3 gap-2 mt-4 text-xs">
                                <div class="rounded-md border border-indigo-400/20 bg-indigo-500/10 px-2 py-2 text-center">
                                    <div class="text-indigo-200">Assigned</div>
                                    <div class="text-white font-bold">{{ $adviser['assigned_students_count'] }}</div>
                                </div>
                                <div class="rounded-md border border-emerald-400/20 bg-emerald-500/10 px-2 py-2 text-center">
                                    <div class="text-emerald-200">On Track</div>
                                    <div class="text-white font-bold">{{ $adviser['on_track_count'] }}</div>
                                </div>
                                <div class="rounded-md border border-rose-400/20 bg-rose-500/10 px-2 py-2 text-center">
                                    <div class="text-rose-200">Attention</div>
                                    <div class="text-white font-bold">{{ $adviser['attention_count'] }}</div>
                                </div>
                            </div>
                        </button>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8 text-gray-300">
                    <p class="text-sm">No OJT advisers assigned yet.</p>
                </div>
            @endif
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="bg-black/40 backdrop-blur-md border border-indigo-500/20 rounded-lg p-6 shadow-xl">
                <h3 class="text-lg font-bold text-white mb-4">Students Needing Attention</h3>
                @if(($studentsNeedingAttention?->count() ?? 0) > 0)
                    <div class="space-y-3 max-h-80 overflow-auto pr-1">
                        @foreach($studentsNeedingAttention->take(10) as $student)
                            <div class="rounded-lg border border-rose-400/20 bg-rose-500/10 px-4 py-3">
                                <div class="flex items-center justify-between gap-3">
                                    <div>
                                        <div class="font-semibold text-white">{{ $student['name'] }}</div>
                                        <div class="text-xs text-rose-100">{{ $student['section'] }} | {{ $student['company'] }}</div>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-xs text-rose-100">Progress</div>
                                        <div class="text-sm font-bold text-white">{{ number_format($student['progress'], 1) }}%</div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-sm text-gray-300">No students currently flagged for attention.</div>
                @endif
            </div>

            <div class="bg-black/40 backdrop-blur-md border border-indigo-500/20 rounded-lg p-6 shadow-xl">
                <h3 class="text-lg font-bold text-white mb-4">Recent Accomplishment Activity</h3>
                @if(($recentActivity?->count() ?? 0) > 0)
                    <div class="space-y-3 max-h-80 overflow-auto pr-1">
                        @foreach($recentActivity as $item)
                            <div class="rounded-lg border border-white/10 bg-white/5 px-4 py-3">
                                <div class="flex justify-between gap-4 text-sm">
                                    <div>
                                        <div class="font-semibold text-white">{{ $item['student'] }}</div>
                                        <div class="text-gray-300">{{ $item['type'] }} | {{ $item['section'] }} | {{ $item['company'] }}</div>
                                    </div>
                                    <div class="text-right text-gray-300">
                                        <div>{{ $item['date'] }}</div>
                                        <div class="text-xs">{{ $item['status'] }} | {{ number_format($item['hours'], 2) }}h</div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-sm text-gray-300">No recent accomplishment reports submitted yet.</div>
                @endif
            </div>
        </div>

        <template x-if="selectedSection">
            <div class="fixed inset-0 z-50" x-cloak>
                <div class="absolute inset-0 bg-black/70" @click="closeSection()"></div>
                <div class="absolute inset-x-0 top-10 mx-auto w-[95%] max-w-5xl rounded-xl border border-white/20 bg-slate-900 text-white shadow-2xl max-h-[85vh] overflow-auto">
                    <div class="sticky top-0 z-10 bg-slate-900/95 backdrop-blur border-b border-white/10 px-6 py-4 flex items-center justify-between">
                        <div>
                            <h3 class="text-xl font-bold" x-text="selectedSection.section"></h3>
                            <p class="text-sm text-slate-300">Accomplishment report submission breakdown</p>
                        </div>
                        <button class="text-slate-300 hover:text-white" @click="closeSection()">Close</button>
                    </div>

                    <div class="p-6 grid grid-cols-1 lg:grid-cols-3 gap-4">
                        <template x-for="type in ['daily', 'weekly', 'monthly']" :key="type">
                            <div class="rounded-lg border border-white/10 bg-white/5 p-4">
                                <div class="text-sm font-bold uppercase tracking-wider" x-text="type"></div>
                                <div class="mt-3 text-xs text-emerald-200">Submitted (<span x-text="selectedSection[type].submitted.length"></span>)</div>
                                <div class="mt-2 space-y-1 max-h-40 overflow-auto">
                                    <template x-for="student in selectedSection[type].submitted" :key="student.student_id + '-s-' + type">
                                        <div class="text-xs rounded bg-emerald-500/10 border border-emerald-400/20 px-2 py-1" x-text="student.name"></div>
                                    </template>
                                </div>

                                <div class="mt-4 text-xs text-rose-200">Not Submitted (<span x-text="selectedSection[type].not_submitted.length"></span>)</div>
                                <div class="mt-2 space-y-1 max-h-40 overflow-auto">
                                    <template x-for="student in selectedSection[type].not_submitted" :key="student.student_id + '-n-' + type">
                                        <div class="text-xs rounded bg-rose-500/10 border border-rose-400/20 px-2 py-1" x-text="student.name"></div>
                                    </template>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </template>

        <template x-if="selectedAdviser">
            <div class="fixed inset-0 z-50" x-cloak>
                <div class="absolute inset-0 bg-black/70" @click="closeAdviser()"></div>
                <div class="absolute inset-x-0 top-10 mx-auto w-[95%] max-w-6xl rounded-xl border border-white/20 bg-slate-900 text-white shadow-2xl max-h-[85vh] overflow-auto">
                    <div class="sticky top-0 z-10 bg-slate-900/95 backdrop-blur border-b border-white/10 px-6 py-4 flex items-center justify-between">
                        <div>
                            <h3 class="text-xl font-bold" x-text="selectedAdviser.name"></h3>
                            <p class="text-sm text-slate-300">Assigned student details and statuses</p>
                        </div>
                        <button class="text-slate-300 hover:text-white" @click="closeAdviser()">Close</button>
                    </div>

                    <div class="p-6">
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-4">
                            <div class="rounded-md border border-indigo-400/20 bg-indigo-500/10 px-3 py-2">
                                <div class="text-xs text-indigo-100">Assigned</div>
                                <div class="text-lg font-bold" x-text="selectedAdviser.assigned_students_count"></div>
                            </div>
                            <div class="rounded-md border border-emerald-400/20 bg-emerald-500/10 px-3 py-2">
                                <div class="text-xs text-emerald-100">On Track</div>
                                <div class="text-lg font-bold" x-text="selectedAdviser.on_track_count"></div>
                            </div>
                            <div class="rounded-md border border-rose-400/20 bg-rose-500/10 px-3 py-2">
                                <div class="text-xs text-rose-100">Needs Attention</div>
                                <div class="text-lg font-bold" x-text="selectedAdviser.attention_count"></div>
                            </div>
                            <div class="rounded-md border border-cyan-400/20 bg-cyan-500/10 px-3 py-2">
                                <div class="text-xs text-cyan-100">Completed</div>
                                <div class="text-lg font-bold" x-text="selectedAdviser.completed_count"></div>
                            </div>
                        </div>

                        <div class="overflow-x-auto border border-white/10 rounded-lg">
                            <table class="min-w-full text-sm">
                                <thead class="bg-white/10 text-left">
                                    <tr>
                                        <th class="px-4 py-3">Student</th>
                                        <th class="px-4 py-3">Section</th>
                                        <th class="px-4 py-3">Company</th>
                                        <th class="px-4 py-3">Reports</th>
                                        <th class="px-4 py-3">Progress</th>
                                        <th class="px-4 py-3">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <template x-if="selectedAdviser.students.length === 0">
                                        <tr>
                                            <td colspan="6" class="px-4 py-6 text-center text-slate-300">No assigned students found for this adviser.</td>
                                        </tr>
                                    </template>
                                    <template x-for="student in selectedAdviser.students" :key="student.assignment_id">
                                        <tr class="border-t border-white/10">
                                            <td class="px-4 py-3" x-text="student.name"></td>
                                            <td class="px-4 py-3" x-text="student.section"></td>
                                            <td class="px-4 py-3" x-text="student.company"></td>
                                            <td class="px-4 py-3 text-xs">
                                                <span :class="student.daily_submitted ? 'text-emerald-300' : 'text-rose-300'">D</span>
                                                <span :class="student.weekly_submitted ? 'text-emerald-300' : 'text-rose-300'"> / W</span>
                                                <span :class="student.monthly_submitted ? 'text-emerald-300' : 'text-rose-300'"> / M</span>
                                            </td>
                                            <td class="px-4 py-3" x-text="student.progress + '%' "></td>
                                            <td class="px-4 py-3">
                                                <span class="px-2 py-1 rounded-full text-xs border"
                                                    :class="
                                                        student.status === 'Completed' ? 'bg-cyan-500/15 border-cyan-300/30 text-cyan-200' :
                                                        (student.status === 'On Track' ? 'bg-emerald-500/15 border-emerald-300/30 text-emerald-200' :
                                                        (student.status === 'Needs Attention' ? 'bg-rose-500/15 border-rose-300/30 text-rose-200' : 'bg-amber-500/15 border-amber-300/30 text-amber-200'))
                                                    "
                                                    x-text="student.status"
                                                ></span>
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        function dashboardInteractions() {
            return {
                sectionData: [],
                adviserData: [],
                selectedSection: null,
                selectedAdviser: null,
                init(payload) {
                    this.sectionData = Array.isArray(payload.sectionReportOverview) ? payload.sectionReportOverview : [];
                    this.adviserData = Array.isArray(payload.adviserData) ? payload.adviserData : [];
                },
                openSection(sectionName) {
                    this.selectedSection = this.sectionData.find(item => item.section === sectionName) || null;
                },
                closeSection() {
                    this.selectedSection = null;
                },
                openAdviser(adviserId) {
                    this.selectedAdviser = this.adviserData.find(item => Number(item.id) === Number(adviserId)) || null;
                },
                closeAdviser() {
                    this.selectedAdviser = null;
                }
            };
        }

        document.addEventListener('DOMContentLoaded', function() {
            const studentCtx = document.getElementById('studentChart');
            if (studentCtx) {
                try {
                    const studentLabels = JSON.parse(studentCtx.getAttribute('data-labels') || '[]');
                    const studentValues = JSON.parse(studentCtx.getAttribute('data-values') || '[]');
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

            const attendanceCtx = document.getElementById('attendanceChart');
            if (attendanceCtx) {
                try {
                    const attendanceLabels = JSON.parse(attendanceCtx.getAttribute('data-labels') || '[]');
                    const totalData = JSON.parse(attendanceCtx.getAttribute('data-total') || '[]');
                    const lateData = JSON.parse(attendanceCtx.getAttribute('data-late') || '[]');
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
