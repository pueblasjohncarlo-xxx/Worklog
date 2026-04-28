<x-ojt-adviser-layout>
    <x-slot name="header">
        Attendance Logs: {{ $student->name }}
    </x-slot>

    @php
        $logPayload = $logs->map(function ($log) use ($student) {
            $rawStatus = (string) ($log->status ?? 'draft');
            $normalizedStatus = $rawStatus === 'submitted' ? 'pending' : $rawStatus;

            return [
                'id' => $log->id,
                'student_name' => $student->name,
                'date_label' => optional($log->work_date)->format('M d, Y') ?? '---',
                'time_in_label' => $log->time_in ? $log->time_in->format('h:i A') : '---',
                'time_out_label' => $log->time_out ? $log->time_out->format('h:i A') : '---',
                'hours_label' => rtrim(rtrim(number_format((float) $log->hours, 2, '.', ''), '0'), '.') . 'h',
                'status_key' => $normalizedStatus,
                'status_label' => match ($normalizedStatus) {
                    'approved' => 'Approved',
                    'rejected' => 'Rejected',
                    'pending' => 'Pending',
                    default => 'Draft',
                },
                'search_blob' => strtolower(implode(' ', array_filter([
                    $student->name,
                    optional($log->work_date)->format('M d, Y'),
                    $log->time_in ? $log->time_in->format('h:i A') : null,
                    $log->time_out ? $log->time_out->format('h:i A') : null,
                    rtrim(rtrim(number_format((float) $log->hours, 2, '.', ''), '0'), '.'),
                    $normalizedStatus,
                    match ($normalizedStatus) {
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                        'pending' => 'Pending',
                        default => 'Draft',
                    },
                ]))),
            ];
        })->values();
    @endphp

    <div
        x-data="attendanceLogsPage(@js($logPayload))"
        class="space-y-6"
    >
        <div class="flex justify-between items-center">
            <a href="{{ route('ojt_adviser.students') }}" class="text-indigo-400 hover:text-indigo-300 text-sm font-bold flex items-center gap-2 transition-colors">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Back to OJT Students
            </a>
        </div>

        <div class="bg-white/5 border border-white/10 rounded-2xl overflow-hidden shadow-xl backdrop-blur-md">
            <div class="border-b border-white/10 bg-black/20 px-6 py-5">
                <div class="flex flex-col gap-4 xl:flex-row xl:items-end xl:justify-between">
                    <div class="space-y-2">
                        <h2 class="text-lg font-bold text-white">Search and Filter Attendance Logs</h2>
                        <p class="text-sm text-gray-300 max-w-3xl">
                            Search instantly by student name, date, time in, time out, hours, or status. Click a status badge to focus on logs that are
                            <span class="font-semibold text-white">Pending</span>,
                            <span class="font-semibold text-white">Approved</span>,
                            <span class="font-semibold text-white">Rejected</span>, or
                            <span class="font-semibold text-white">Draft</span>.
                        </p>
                    </div>

                    <div class="w-full xl:max-w-md">
                        <label for="attendance-log-search" class="block text-xs font-bold uppercase tracking-[0.18em] text-indigo-200 mb-2">
                            Search Attendance Records
                        </label>
                        <div class="relative">
                            <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m21 21-4.35-4.35M10.5 18a7.5 7.5 0 1 1 0-15 7.5 7.5 0 0 1 0 15Z" />
                                </svg>
                            </span>
                            <input
                                id="attendance-log-search"
                                type="search"
                                x-model="search"
                                placeholder="Type a name, date, time, hours, or status"
                                class="w-full rounded-xl border border-white/15 bg-white/10 pl-10 pr-4 py-3 text-sm font-medium text-white placeholder:text-gray-400 focus:border-indigo-400 focus:outline-none focus:ring-2 focus:ring-indigo-400/50"
                            >
                        </div>
                    </div>
                </div>

                <div class="mt-4 flex flex-wrap items-center gap-2">
                    <button
                        type="button"
                        @click="setStatus('all')"
                        :class="selectedStatus === 'all' ? 'bg-white text-slate-950 border-white shadow-lg' : 'bg-white/5 text-gray-200 border-white/10 hover:bg-white/10'"
                        class="inline-flex items-center rounded-full border px-3 py-1.5 text-xs font-bold uppercase tracking-[0.14em] transition-all"
                    >
                        All Statuses
                        <span class="ml-2 rounded-full bg-black/10 px-2 py-0.5 text-[10px]" x-text="statusCounts.all"></span>
                    </button>
                    <template x-for="badge in statusBadges" :key="badge.key">
                        <button
                            type="button"
                            @click="setStatus(badge.key)"
                            :class="selectedStatus === badge.key ? badge.activeClass : badge.inactiveClass"
                            class="inline-flex items-center rounded-full border px-3 py-1.5 text-xs font-bold uppercase tracking-[0.14em] transition-all"
                        >
                            <span x-text="badge.label"></span>
                            <span class="ml-2 rounded-full bg-black/10 px-2 py-0.5 text-[10px]" x-text="statusCounts[badge.key] ?? 0"></span>
                        </button>
                    </template>
                    <button
                        type="button"
                        x-show="search || selectedStatus !== 'all'"
                        x-cloak
                        @click="clearFilters()"
                        class="inline-flex items-center rounded-full border border-indigo-300/30 bg-indigo-500/10 px-3 py-1.5 text-xs font-bold uppercase tracking-[0.14em] text-indigo-100 hover:bg-indigo-500/20 transition-all"
                    >
                        Clear Filters
                    </button>
                </div>

                <div class="mt-4 flex flex-wrap items-center justify-between gap-3 text-xs text-gray-300">
                    <div>
                        Showing <span class="font-bold text-white" x-text="filteredLogs.length"></span>
                        of <span class="font-bold text-white" x-text="logs.length"></span> attendance logs
                    </div>
                    <div class="text-right">
                        Active filter:
                        <span class="font-bold text-white" x-text="selectedStatusLabel"></span>
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-black/30">
                        <tr>
                            <th class="px-6 py-4 text-xs font-bold text-gray-300 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-4 text-xs font-bold text-gray-300 uppercase tracking-wider">Time In</th>
                            <th class="px-6 py-4 text-xs font-bold text-gray-300 uppercase tracking-wider">Time Out</th>
                            <th class="px-6 py-4 text-xs font-bold text-gray-300 uppercase tracking-wider">Hours</th>
                            <th class="px-6 py-4 text-xs font-bold text-gray-300 uppercase tracking-wider text-right">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        <template x-if="filteredLogs.length === 0">
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center">
                                    <div class="text-base font-bold text-white">No attendance logs found</div>
                                    <div class="mt-1 text-sm text-gray-400">Try adjusting the search text or choosing a different status filter.</div>
                                </td>
                            </tr>
                        </template>

                        <template x-for="log in filteredLogs" :key="log.id">
                            <tr class="hover:bg-white/5 transition-colors">
                                <td class="px-6 py-4 font-semibold text-gray-100" x-text="log.date_label"></td>
                                <td class="px-6 py-4 text-gray-300" x-text="log.time_in_label"></td>
                                <td class="px-6 py-4 text-gray-300" x-text="log.time_out_label"></td>
                                <td class="px-6 py-4 text-indigo-300 font-black" x-text="log.hours_label"></td>
                                <td class="px-6 py-4 text-right">
                                    <button
                                        type="button"
                                        @click="setStatus(log.status_key)"
                                        :class="statusBadgeClass(log.status_key, selectedStatus === log.status_key)"
                                        class="inline-flex items-center rounded-full border px-3 py-1.5 text-[11px] font-black uppercase tracking-[0.16em] transition-all"
                                        :title="`Filter by ${log.status_label}`"
                                    >
                                        <span class="mr-1.5 text-xs" x-text="statusIcon(log.status_key)"></span>
                                        <span x-text="log.status_label"></span>
                                    </button>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        function attendanceLogsPage(logs) {
            return {
                logs: Array.isArray(logs) ? logs : [],
                search: '',
                selectedStatus: 'all',
                statusBadges: [
                    {
                        key: 'pending',
                        label: 'Pending',
                        activeClass: 'bg-amber-400 text-slate-950 border-amber-300 shadow-lg shadow-amber-500/20',
                        inactiveClass: 'bg-amber-500/10 text-amber-100 border-amber-400/30 hover:bg-amber-500/20',
                    },
                    {
                        key: 'approved',
                        label: 'Approved',
                        activeClass: 'bg-emerald-400 text-slate-950 border-emerald-300 shadow-lg shadow-emerald-500/20',
                        inactiveClass: 'bg-emerald-500/10 text-emerald-100 border-emerald-400/30 hover:bg-emerald-500/20',
                    },
                    {
                        key: 'rejected',
                        label: 'Rejected',
                        activeClass: 'bg-rose-400 text-slate-950 border-rose-300 shadow-lg shadow-rose-500/20',
                        inactiveClass: 'bg-rose-500/10 text-rose-100 border-rose-400/30 hover:bg-rose-500/20',
                    },
                    {
                        key: 'draft',
                        label: 'Draft',
                        activeClass: 'bg-slate-200 text-slate-950 border-slate-100 shadow-lg shadow-slate-500/20',
                        inactiveClass: 'bg-slate-500/10 text-slate-100 border-slate-400/30 hover:bg-slate-500/20',
                    },
                ],
                get filteredLogs() {
                    const term = (this.search || '').trim().toLowerCase();

                    return this.logs.filter((log) => {
                        const matchesStatus = this.selectedStatus === 'all' || log.status_key === this.selectedStatus;
                        const matchesSearch = term === '' || (log.search_blob || '').includes(term);

                        return matchesStatus && matchesSearch;
                    });
                },
                get statusCounts() {
                    return this.logs.reduce((counts, log) => {
                        counts.all += 1;
                        counts[log.status_key] = (counts[log.status_key] || 0) + 1;
                        return counts;
                    }, { all: 0, pending: 0, approved: 0, rejected: 0, draft: 0 });
                },
                get selectedStatusLabel() {
                    if (this.selectedStatus === 'all') {
                        return 'All Statuses';
                    }

                    const match = this.statusBadges.find((badge) => badge.key === this.selectedStatus);
                    return match ? match.label : 'All Statuses';
                },
                setStatus(status) {
                    this.selectedStatus = status;
                },
                clearFilters() {
                    this.search = '';
                    this.selectedStatus = 'all';
                },
                statusIcon(status) {
                    switch (status) {
                        case 'approved':
                            return 'OK';
                        case 'pending':
                            return '!';
                        case 'rejected':
                            return 'X';
                        default:
                            return '-';
                    }
                },
                statusBadgeClass(status, isActive) {
                    const palette = {
                        approved: isActive
                            ? 'bg-emerald-400 text-slate-950 border-emerald-300 shadow-lg shadow-emerald-500/20'
                            : 'bg-emerald-500/10 text-emerald-100 border-emerald-400/30 hover:bg-emerald-500/20',
                        pending: isActive
                            ? 'bg-amber-400 text-slate-950 border-amber-300 shadow-lg shadow-amber-500/20'
                            : 'bg-amber-500/10 text-amber-100 border-amber-400/30 hover:bg-amber-500/20',
                        rejected: isActive
                            ? 'bg-rose-400 text-slate-950 border-rose-300 shadow-lg shadow-rose-500/20'
                            : 'bg-rose-500/10 text-rose-100 border-rose-400/30 hover:bg-rose-500/20',
                        draft: isActive
                            ? 'bg-slate-200 text-slate-950 border-slate-100 shadow-lg shadow-slate-500/20'
                            : 'bg-slate-500/10 text-slate-100 border-slate-400/30 hover:bg-slate-500/20',
                    };

                    return palette[status] || palette.draft;
                },
            };
        }
    </script>
</x-ojt-adviser-layout>
