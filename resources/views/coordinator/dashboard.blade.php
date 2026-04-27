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
        @php
            $activityStatusMap = [
                'approved' => ['status' => 'approved', 'label' => 'Approved'],
                'pending' => ['status' => 'pending', 'label' => 'Pending'],
                'submitted' => ['status' => 'submitted', 'label' => 'Submitted'],
                'rejected' => ['status' => 'rejected', 'label' => 'Rejected'],
                'draft' => ['status' => 'draft', 'label' => 'Draft'],
            ];
        @endphp
        @php
            $summaryCards = [
                ['label' => 'OJT Students', 'value' => $totalStudents ?? 0, 'href' => route('coordinator.student-overview'), 'tone' => 'indigo'],
                ['label' => 'Active OJT', 'value' => $activeOJTs ?? 0, 'href' => route('coordinator.deployment.index'), 'tone' => 'sky'],
                ['label' => 'OJT Advisers', 'value' => $advisersCount ?? 0, 'href' => route('coordinator.adviser-overview'), 'tone' => 'emerald'],
                ['label' => 'Supervisors', 'value' => $supervisorsCount ?? 0, 'href' => route('coordinator.supervisor-overview'), 'tone' => 'cyan'],
                ['label' => 'Industry', 'value' => $totalCompanies ?? 0, 'href' => route('coordinator.companies.index'), 'tone' => 'amber'],
                ['label' => 'Pending Approvals', 'value' => $pendingApprovals ?? 0, 'href' => route('coordinator.registrations.pending'), 'tone' => 'fuchsia'],
                ['label' => 'Pending AR', 'value' => $pendingAccomplishmentReports ?? 0, 'href' => route('coordinator.accomplishment-reports'), 'tone' => 'rose'],
                ['label' => 'Needs Attention', 'value' => $studentsNeedingAttention?->count() ?? 0, 'href' => route('coordinator.compliance-overview'), 'tone' => 'orange'],
            ];
        @endphp

        <x-coordinator.summary-cards :cards="$summaryCards" />

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="bg-slate-950/80 border border-indigo-300/35 rounded-xl p-6 shadow-xl">
                <h3 class="text-lg font-bold text-white mb-4 flex items-center gap-2">
                    <svg class="h-5 w-5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                    OJT Students
                </h3>
                <p class="mb-4 text-sm font-medium text-slate-200">Count of OJT students by section. Labels and tooltips identify each value so the chart does not rely on color alone.</p>
                <div class="relative h-64">
                    <canvas id="studentChart"
                        data-labels="{{ json_encode($sectionProgress?->pluck('section')->toArray() ?? []) }}"
                        data-values="{{ json_encode($sectionProgress?->pluck('count')->toArray() ?? []) }}">
                    </canvas>
                </div>
            </div>

            <div class="bg-slate-950/80 border border-emerald-300/35 rounded-xl p-6 shadow-xl">
                <h3 class="text-lg font-bold text-white mb-4 flex items-center gap-2">
                    <svg class="h-5 w-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                    </svg>
                    Daily Attendance Trends
                </h3>
                <div class="mb-4 grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div class="rounded-lg border border-emerald-300/40 bg-emerald-50 px-3 py-2 text-sm font-semibold text-emerald-950">
                        <div class="flex items-center gap-2">
                            <span class="inline-flex h-5 w-5 items-center justify-center rounded-full border border-emerald-700 text-[11px] font-black">A</span>
                            <span>Total Clock-ins</span>
                        </div>
                    </div>
                    <div class="rounded-lg border border-rose-300/40 bg-rose-50 px-3 py-2 text-sm font-semibold text-rose-950">
                        <div class="flex items-center gap-2">
                            <span class="inline-flex h-5 w-5 items-center justify-center rounded-full border border-rose-700 text-[11px] font-black">!</span>
                            <span>Incomplete Records</span>
                        </div>
                    </div>
                </div>
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
            <div class="bg-slate-950/80 border border-indigo-300/35 rounded-xl p-6 shadow-xl xl:col-span-2">
                <div class="flex items-center justify-between gap-4 mb-6">
                    <h3 class="text-lg font-bold text-white">Accomplishment Report Status Overview</h3>
                    <span class="wl-status-badge wl-status-info normal-case tracking-normal">
                        <span class="wl-status-badge-icon" aria-hidden="true">i</span>
                        <span>Click a section to view submitted and missing reports</span>
                    </span>
                </div>
                <div class="mb-4 flex flex-wrap gap-3 text-sm">
                    <span class="wl-status-badge wl-status-approved normal-case tracking-normal">
                        <span class="wl-status-badge-icon" aria-hidden="true">✓</span>
                        <span>Submitted</span>
                    </span>
                    <span class="wl-status-badge wl-status-rejected normal-case tracking-normal">
                        <span class="wl-status-badge-icon" aria-hidden="true">×</span>
                        <span>Not Submitted</span>
                    </span>
                </div>

                @if(($sectionReportOverview?->count() ?? 0) > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($sectionReportOverview as $section)
                            <button
                                type="button"
                                @click="openSection('{{ $section['section'] }}')"
                                class="text-left p-4 rounded-xl border border-slate-300/20 bg-slate-900/70 hover:bg-slate-800 hover:border-indigo-300/45 transition"
                            >
                                <div class="flex items-center justify-between gap-3">
                                    <div class="text-sm font-bold text-white">{{ $section['section'] }}</div>
                                    <div class="text-xs font-semibold text-slate-200">{{ $section['total_students'] }} OJT students</div>
                                </div>
                                <div class="mt-4 space-y-3">
                                    <div class="flex items-center justify-between gap-3 text-xs">
                                        <span class="font-bold uppercase tracking-[0.14em] text-emerald-100">Submitted</span>
                                        <span class="font-black text-white">{{ number_format($section['submitted_percentage'], 1) }}%</span>
                                    </div>
                                    <div class="flex items-center justify-between gap-3 text-xs text-slate-200">
                                        <span>{{ round(($section['total_students'] ?? 0) * (($section['submitted_percentage'] ?? 0) / 100)) }} of {{ $section['total_students'] }} OJT students</span>
                                        <span class="font-semibold">Section details</span>
                                    </div>
                                    <div class="h-3 rounded-full bg-slate-700 overflow-hidden" aria-hidden="true">
                                        <div class="h-full rounded-full border-r border-emerald-950/30 bg-[repeating-linear-gradient(135deg,#16a34a_0,#16a34a_10px,#22c55e_10px,#22c55e_20px)]" style="width: {{ $section['submitted_percentage'] }}%"></div>
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

            <div class="bg-slate-950/80 border border-amber-300/35 rounded-xl p-6 shadow-xl">
                <div class="flex items-center justify-between gap-3">
                    <h3 class="text-lg font-bold text-white">Required Hours</h3>
                    <a href="{{ route('coordinator.settings.hours') }}" class="text-sm font-bold text-amber-100 underline-offset-2 hover:text-white hover:underline">Open settings</a>
                </div>
                <p class="text-sm font-medium text-slate-200 mt-2">Set required OJT hours for active or all student assignments.</p>

                <div class="mt-4 p-4 rounded-xl border border-amber-300/30 bg-amber-50">
                    <div class="text-xs font-black uppercase tracking-[0.16em] text-amber-900">Current default</div>
                    <div class="text-2xl font-black text-slate-950">{{ $currentRequiredHours ?? 1600 }} hrs</div>
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
                            class="w-full rounded-md border border-slate-300 bg-white text-slate-950 px-3 py-2 font-semibold focus:outline-none focus:ring-2 focus:ring-indigo-400"
                            required
                        >
                    </div>
                    <div>
                        <label for="scope" class="block text-sm font-medium text-gray-200 mb-1">Scope</label>
                        <select id="scope" name="scope" class="w-full rounded-md border border-slate-300 bg-white text-slate-950 px-3 py-2 font-semibold focus:outline-none focus:ring-2 focus:ring-indigo-400">
                            <option value="active">Active assignments only</option>
                            <option value="all">All assignments</option>
                        </select>
                    </div>
                    <button type="submit" class="w-full rounded-md bg-indigo-700 px-4 py-2 font-bold text-white transition hover:bg-indigo-800">
                        Update Required Hours
                    </button>
                </form>
            </div>
        </div>

        <div class="bg-slate-950/80 border border-cyan-300/35 rounded-xl p-6 shadow-xl">
            <div class="flex items-center justify-between gap-4 mb-6">
                <h3 class="text-lg font-bold text-white">OJT Adviser Dashboard</h3>
                <span class="wl-status-badge wl-status-info normal-case tracking-normal">
                    <span class="wl-status-badge-icon" aria-hidden="true">i</span>
                    <span>Click an adviser to view assigned OJT students</span>
                </span>
            </div>

            @if($ojtAdvisers && count($ojtAdvisers) > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
                    @foreach($ojtAdvisers as $adviser)
                        <button
                            type="button"
                            @click="openAdviser({{ $adviser['id'] }})"
                            class="text-left border border-slate-300/20 rounded-xl p-4 bg-slate-900/70 hover:bg-slate-800 transition-all"
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
                                        <div class="text-sm font-medium text-slate-200 truncate">{{ $adviser['email'] }}</div>
                                    </div>
                                </div>
                                <svg class="h-5 w-5 text-indigo-300 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </div>
                            <div class="grid grid-cols-3 gap-2 mt-4 text-xs">
                                <div class="rounded-lg border border-indigo-300/40 bg-indigo-50 px-2 py-2 text-center">
                                    <div class="text-[11px] font-black uppercase tracking-[0.14em] text-indigo-950">Assigned</div>
                                    <div class="mt-1 text-slate-950 font-black">{{ $adviser['assigned_students_count'] }}</div>
                                </div>
                                <div class="rounded-lg border border-emerald-300/40 bg-emerald-50 px-2 py-2 text-center">
                                    <div class="text-[11px] font-black uppercase tracking-[0.14em] text-emerald-950">On Track</div>
                                    <div class="mt-1 text-slate-950 font-black">{{ $adviser['on_track_count'] }}</div>
                                </div>
                                <div class="rounded-lg border border-rose-300/40 bg-rose-50 px-2 py-2 text-center">
                                    <div class="text-[11px] font-black uppercase tracking-[0.14em] text-rose-950">Attention</div>
                                    <div class="mt-1 text-slate-950 font-black">{{ $adviser['attention_count'] }}</div>
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
            <div class="bg-slate-950/80 border border-rose-300/35 rounded-xl p-6 shadow-xl">
                <h3 class="text-lg font-bold text-white mb-4">OJT Students Needing Attention</h3>
                @if(($studentsNeedingAttention?->count() ?? 0) > 0)
                    <div class="space-y-3 max-h-80 overflow-auto pr-1">
                        @foreach($studentsNeedingAttention->take(10) as $student)
                            <div class="rounded-xl border border-rose-300/40 bg-rose-50 px-4 py-3">
                                <div class="flex items-center justify-between gap-3">
                                    <div>
                                        <div class="font-bold text-rose-950">{{ $student['name'] }}</div>
                                        <div class="text-xs font-medium text-slate-700">{{ $student['section'] }} | {{ $student['company'] }}</div>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-xs font-black uppercase tracking-[0.14em] text-rose-900">Progress</div>
                                        <div class="text-sm font-black text-rose-950">{{ number_format($student['progress'], 1) }}%</div>
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <x-status-badge status="incomplete" label="Needs Attention" size="sm" />
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-sm text-gray-300">No OJT students currently flagged for attention.</div>
                @endif
            </div>

            <div class="bg-slate-950/80 border border-indigo-300/35 rounded-xl p-6 shadow-xl">
                <h3 class="text-lg font-bold text-white mb-4">Recent Accomplishment Activity</h3>
                @if(($recentActivity?->count() ?? 0) > 0)
                    <div class="space-y-3 max-h-80 overflow-auto pr-1">
                        @foreach($recentActivity as $item)
                            @php
                                $activityMeta = $activityStatusMap[strtolower($item['status'] ?? '')] ?? ['status' => 'info', 'label' => ucfirst($item['status'] ?? 'Status')];
                            @endphp
                            <div class="rounded-xl border border-slate-300/20 bg-slate-900/70 px-4 py-3">
                                <div class="flex justify-between gap-4 text-sm">
                                    <div>
                                        <div class="font-semibold text-white">{{ $item['student'] }}</div>
                                        <div class="text-slate-200">{{ $item['type'] }} | {{ $item['section'] }} | {{ $item['company'] }}</div>
                                    </div>
                                    <div class="text-right text-slate-200">
                                        <div>{{ $item['date'] }}</div>
                                        <div class="mt-2 flex flex-col items-end gap-2">
                                            <x-status-badge :status="$activityMeta['status']" :label="$activityMeta['label']" size="sm" />
                                            <div class="text-xs font-semibold">{{ number_format($item['hours'], 2) }}h</div>
                                        </div>
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
                            <div class="rounded-xl border border-slate-300/20 bg-slate-800/80 p-4">
                                <div class="text-sm font-bold uppercase tracking-wider" x-text="type"></div>
                                <div class="mt-3">
                                    <span class="wl-status-badge wl-status-approved normal-case tracking-normal">
                                        <span class="wl-status-badge-icon" aria-hidden="true">OK</span>
                                        <span>Submitted (<span x-text="selectedSection[type].submitted.length"></span>)</span>
                                    </span>
                                </div>
                                <div class="mt-2 space-y-1 max-h-40 overflow-auto">
                                    <template x-for="student in selectedSection[type].submitted" :key="student.student_id + '-s-' + type">
                                        <div class="text-xs font-medium rounded bg-emerald-50 text-emerald-950 border border-emerald-300 px-2 py-1" x-text="student.name"></div>
                                    </template>
                                </div>

                                <div class="mt-4">
                                    <span class="wl-status-badge wl-status-rejected normal-case tracking-normal">
                                        <span class="wl-status-badge-icon" aria-hidden="true">NO</span>
                                        <span>Not Submitted (<span x-text="selectedSection[type].not_submitted.length"></span>)</span>
                                    </span>
                                </div>
                                <div class="mt-2 space-y-1 max-h-40 overflow-auto">
                                    <template x-for="student in selectedSection[type].not_submitted" :key="student.student_id + '-n-' + type">
                                        <div class="text-xs font-medium rounded bg-rose-50 text-rose-950 border border-rose-300 px-2 py-1" x-text="student.name"></div>
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
                            <div class="rounded-md border border-indigo-300/40 bg-indigo-50 px-3 py-2 text-slate-950">
                                <div class="text-xs font-black uppercase tracking-[0.14em] text-indigo-950">Assigned</div>
                                <div class="text-lg font-bold" x-text="selectedAdviser.assigned_students_count"></div>
                            </div>
                            <div class="rounded-md border border-emerald-300/40 bg-emerald-50 px-3 py-2 text-slate-950">
                                <div class="text-xs font-black uppercase tracking-[0.14em] text-emerald-950">On Track</div>
                                <div class="text-lg font-bold" x-text="selectedAdviser.on_track_count"></div>
                            </div>
                            <div class="rounded-md border border-rose-300/40 bg-rose-50 px-3 py-2 text-slate-950">
                                <div class="text-xs font-black uppercase tracking-[0.14em] text-rose-950">Needs Attention</div>
                                <div class="text-lg font-bold" x-text="selectedAdviser.attention_count"></div>
                            </div>
                            <div class="rounded-md border border-cyan-300/40 bg-cyan-50 px-3 py-2 text-slate-950">
                                <div class="text-xs font-black uppercase tracking-[0.14em] text-cyan-950">Completed</div>
                                <div class="text-lg font-bold" x-text="selectedAdviser.completed_count"></div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="adviser-student-search" class="sr-only">Search OJT students</label>
                            <div class="relative">
                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                    <svg class="h-4 w-4 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M10.5 18a7.5 7.5 0 100-15 7.5 7.5 0 000 15z" />
                                    </svg>
                                </div>
                                <input
                                    id="adviser-student-search"
                                    type="text"
                                    x-model="adviserSearchTerm"
                                    placeholder="Search by name, section, company, or status..."
                                    class="w-full rounded-lg border border-slate-300 bg-white text-slate-950 placeholder-slate-500 pl-9 pr-3 py-2 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-indigo-400"
                                >
                            </div>
                        </div>

                        <div class="overflow-x-auto border border-white/10 rounded-lg">
                            <table class="min-w-full text-sm">
                                <thead class="bg-white/10 text-left">
                                    <tr>
                                        <th class="px-4 py-3">OJT Student</th>
                                        <th class="px-4 py-3">Section</th>
                                        <th class="px-4 py-3">Company</th>
                                        <th class="px-4 py-3">Reports</th>
                                        <th class="px-4 py-3">Progress</th>
                                        <th class="px-4 py-3">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <template x-if="getFilteredAdviserStudents().length === 0">
                                        <tr>
                                            <td colspan="6" class="px-4 py-6 text-center text-slate-300">No OJT students found.</td>
                                        </tr>
                                    </template>
                                    <template x-for="student in getFilteredAdviserStudents()" :key="student.assignment_id">
                                        <tr class="border-t border-white/10">
                                            <td class="px-4 py-3" x-text="student.name"></td>
                                            <td class="px-4 py-3" x-text="student.section"></td>
                                            <td class="px-4 py-3" x-text="student.company"></td>
                                            <td class="px-4 py-3 text-xs">
                                                <div class="flex flex-wrap gap-1">
                                                    <span :class="reportSubmissionClass(student.daily_submitted)" x-text="`D ${reportSubmissionLabel(student.daily_submitted)}`"></span>
                                                    <span :class="reportSubmissionClass(student.weekly_submitted)" x-text="`W ${reportSubmissionLabel(student.weekly_submitted)}`"></span>
                                                    <span :class="reportSubmissionClass(student.monthly_submitted)" x-text="`M ${reportSubmissionLabel(student.monthly_submitted)}`"></span>
                                                </div>
                                            </td>
                                            <td class="px-4 py-3" x-text="student.progress + '%' "></td>
                                            <td class="px-4 py-3">
                                                <span class="inline-flex items-center gap-1.5 rounded-full border px-3 py-1 text-xs font-extrabold uppercase tracking-[0.14em]"
                                                    :class="statusBadgeClass(student.status)">
                                                    <span class="inline-flex h-4 w-4 items-center justify-center rounded-full border border-current text-[10px] font-black" aria-hidden="true" x-text="statusBadgeIcon(student.status)"></span>
                                                    <span x-text="student.status"></span>
                                                </span>
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
                adviserSearchTerm: '',
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
                    this.adviserSearchTerm = '';
                    this.selectedAdviser = this.adviserData.find(item => Number(item.id) === Number(adviserId)) || null;
                },
                closeAdviser() {
                    this.adviserSearchTerm = '';
                    this.selectedAdviser = null;
                },
                getFilteredAdviserStudents() {
                    if (!this.selectedAdviser || !Array.isArray(this.selectedAdviser.students)) {
                        return [];
                    }

                    const term = (this.adviserSearchTerm || '').trim().toLowerCase();
                    if (!term) {
                        return this.selectedAdviser.students;
                    }

                    return this.selectedAdviser.students.filter((student) => {
                        const haystack = [
                            student.name,
                            student.section,
                            student.company,
                            student.status,
                        ]
                            .filter(Boolean)
                            .join(' ')
                            .toLowerCase();

                        return haystack.includes(term);
                    });
                },
                statusBadgeClass(status) {
                    switch ((status || '').toLowerCase()) {
                        case 'completed':
                            return 'bg-cyan-50 text-cyan-950 border-cyan-300';
                        case 'on track':
                            return 'bg-emerald-50 text-emerald-950 border-emerald-300';
                        case 'needs attention':
                            return 'bg-rose-50 text-rose-950 border-rose-300';
                        default:
                            return 'bg-amber-50 text-amber-950 border-amber-300';
                    }
                },
                statusBadgeIcon(status) {
                    switch ((status || '').toLowerCase()) {
                        case 'completed':
                            return 'C';
                        case 'on track':
                            return '✓';
                        case 'needs attention':
                            return '!';
                        default:
                            return 'i';
                    }
                },
                reportSubmissionClass(isSubmitted) {
                    return isSubmitted
                        ? 'inline-flex items-center rounded-full border border-emerald-300 bg-emerald-50 px-2 py-1 font-bold text-emerald-950'
                        : 'inline-flex items-center rounded-full border border-rose-300 bg-rose-50 px-2 py-1 font-bold text-rose-950';
                },
                reportSubmissionLabel(isSubmitted) {
                    return isSubmitted ? 'Yes' : 'No';
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
                                label: 'OJT Students',
                                data: hasData ? studentValues : [0],
                                backgroundColor: 'rgba(37, 99, 235, 0.88)',
                                borderColor: 'rgba(30, 64, 175, 1)',
                                borderWidth: 2,
                                borderRadius: 6,
                                hoverBackgroundColor: 'rgba(30, 64, 175, 0.95)'
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            indexAxis: 'y',
                            plugins: {
                                legend: { display: false },
                                tooltip: {
                                    callbacks: {
                                        label: function(context) {
                                            return `Count: ${context.formattedValue} OJT students`;
                                        }
                                    }
                                }
                            },
                            scales: {
                                x: {
                                    beginAtZero: true,
                                    grid: { color: 'rgba(255, 255, 255, 0.05)' },
                                    ticks: { color: 'rgba(255, 255, 255, 0.92)', font: { weight: '700' } }
                                },
                                y: {
                                    grid: { display: false },
                                    ticks: { color: 'rgba(255, 255, 255, 0.96)', font: { weight: '700' } }
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
                                    label: 'Total Clock-ins (Approved/Recorded)',
                                    data: hasTotal ? totalData : [0],
                                    borderColor: '#15803d',
                                    backgroundColor: 'rgba(21, 128, 61, 0.12)',
                                    borderWidth: 3,
                                    fill: true,
                                    tension: 0.4,
                                    pointBackgroundColor: '#15803d',
                                    pointBorderColor: '#ffffff',
                                    pointBorderWidth: 2,
                                    pointRadius: 4
                                },
                                {
                                    label: 'Incomplete Records',
                                    data: hasLate ? lateData : [0],
                                    borderColor: '#b91c1c',
                                    backgroundColor: 'rgba(185, 28, 28, 0.08)',
                                    borderWidth: 3,
                                    borderDash: [8, 5],
                                    fill: true,
                                    tension: 0.4,
                                    pointBackgroundColor: '#b91c1c',
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
                                    labels: { color: 'rgba(255, 255, 255, 0.95)', font: { weight: '700' } }
                                },
                                tooltip: {
                                    callbacks: {
                                        label: function(context) {
                                            return `${context.dataset.label}: ${context.formattedValue}`;
                                        }
                                    }
                                }
                            },
                            scales: {
                                x: {
                                    grid: { color: 'rgba(255, 255, 255, 0.05)' },
                                    ticks: { color: 'rgba(255, 255, 255, 0.92)', font: { weight: '700' } }
                                },
                                y: {
                                    beginAtZero: true,
                                    grid: { color: 'rgba(255, 255, 255, 0.05)' },
                                    ticks: { color: 'rgba(255, 255, 255, 0.92)', font: { weight: '700' } }
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
