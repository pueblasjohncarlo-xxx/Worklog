<x-student-layout>
    <x-slot name="header">
        @if(request('view') === 'reports')
            Reports & Performance
        @else
            Reports & Hours Analytics
        @endif
    </x-slot>

    <div class="space-y-6">
        <!-- Navigation Tabs -->
        <div class="flex gap-4 border-b border-white/20">
            <a href="{{ route('student.reports.index') }}" 
               class="student-tab
                   {{ !request('view') || request('view') !== 'reports' 
                       ? 'student-tab-active' 
                       : 'student-tab-inactive' 
                   }}">
                Hours Log
            </a>
            <a href="{{ route('student.reports.index', ['view' => 'reports']) }}" 
               class="student-tab
                   {{ request('view') === 'reports' 
                       ? 'student-tab-active' 
                       : 'student-tab-inactive' 
                   }}">
                Reports
            </a>
            <a href="{{ route('student.evaluations.index') }}" class="student-tab student-tab-inactive">
                Performance Evaluations
            </a>
        </div>

        @if(request('view') === 'reports')
            <!-- Reports View -->
            @php
                $reports = $workLogs->where('type', '!=', 'attendance')->sortByDesc('created_at')->values();
                $reportPayload = $reports->map(function ($report) use ($assignment) {
                    return [
                        'id' => $report->id,
                        'title' => ucfirst($report->type) . ' Report',
                        'type' => ucfirst($report->type),
                        'date' => $report->work_date?->format('M d, Y') ?? 'No date',
                        'status' => ucfirst((string) $report->status),
                        'company' => $assignment?->company?->name ?? 'No company assigned',
                        'supervisor' => $assignment?->supervisor?->name ?? 'No supervisor assigned',
                        'filename' => $report->attachment_path ? basename($report->attachment_path) : 'Generated report',
                        'view_url' => $report->attachment_path ? route('student.worklogs.attachment', $report) . '?inline=1' : route('student.worklogs.print', $report),
                        'search_blob' => strtolower(implode(' ', array_filter([
                            auth()->user()?->name,
                            ucfirst($report->type) . ' report',
                            $report->work_date?->format('M d, Y'),
                            (string) $report->status,
                            $assignment?->company?->name,
                            $assignment?->supervisor?->name,
                            $report->attachment_path ? basename($report->attachment_path) : 'generated report',
                        ]))),
                    ];
                });
            @endphp
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="student-light-card p-6">
                    <p class="student-card-title mb-1">Submitted Reports</p>
                    <p class="text-3xl font-black text-indigo-600">{{ $workLogs->where('type', '!=', 'attendance')->count() }}</p>
                </div>
                <div class="student-light-card p-6">
                    <p class="student-card-title mb-1">Approved Reports</p>
                    <p class="text-3xl font-black text-emerald-600">{{ $workLogs->where('status', 'approved')->where('type', '!=', 'attendance')->count() }}</p>
                </div>
            </div>

            <!-- Reports List -->
            <div
                class="student-light-card overflow-hidden"
                x-data="studentAccomplishmentReportSearch(@js($reportPayload))"
            >
                <div class="px-6 py-4 border-b border-slate-200">
                    <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                        <div>
                            <h3 class="text-sm font-bold text-slate-900">Your Submitted Reports</h3>
                            <p class="mt-1 text-xs font-medium text-slate-600">Search instantly by report type, date, status, company, supervisor, or filename.</p>
                        </div>
                        <div class="w-full lg:w-[24rem]">
                            <label for="student-report-search" class="sr-only">Search accomplishment reports</label>
                            <div class="relative">
                                <svg class="pointer-events-none absolute left-3 top-3.5 h-5 w-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                                <input
                                    id="student-report-search"
                                    type="search"
                                    x-model.debounce.100ms="searchQuery"
                                    placeholder="Search by type, date, status, company, or filename"
                                    class="w-full rounded-xl border border-slate-300 bg-white py-3 pl-10 pr-4 text-sm font-medium text-slate-900 shadow-sm outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500"
                                >
                            </div>
                        </div>
                    </div>
                </div>
                @if($reports->count() > 0)
                    <div class="flex items-center justify-between px-6 py-3 text-xs font-semibold text-slate-600">
                        <span>Showing <span class="font-black text-slate-900" x-text="filteredReports.length"></span> of <span class="font-black text-slate-900" x-text="reports.length"></span> reports</span>
                        <button
                            x-show="searchQuery"
                            x-cloak
                            @click="searchQuery = ''"
                            type="button"
                            class="rounded-full border border-slate-300 px-3 py-1 text-slate-700 transition hover:border-indigo-400 hover:text-indigo-700"
                        >
                            Clear Search
                        </button>
                    </div>
                    <div class="divide-y divide-gray-100 dark:divide-gray-700">
                        <template x-for="report in filteredReports" :key="report.id">
                            <div class="p-6 hover:bg-slate-50 transition-colors">
                                <div class="flex items-start justify-between gap-4">
                                    <div class="flex-1">
                                        <h4 class="font-bold text-slate-900" x-text="report.title"></h4>
                                        <p class="mt-1 text-sm text-slate-700">
                                            Date: <span x-text="report.date"></span>
                                        </p>
                                        <p class="text-sm text-slate-700">
                                            Type: <span class="font-semibold" x-text="report.type"></span>
                                        </p>
                                        <p class="text-sm text-slate-700">
                                            Status:
                                            <span :class="statusClass(report.status)" class="ml-1 inline-flex rounded-full px-2.5 py-1 text-xs font-bold" x-text="report.status"></span>
                                        </p>
                                        <p class="text-sm text-slate-700">
                                            File: <span x-text="report.filename"></span>
                                        </p>
                                    </div>
                                    <a
                                        :href="report.view_url"
                                        target="_blank"
                                        class="px-4 py-2 bg-indigo-600 text-white font-semibold rounded-lg text-sm hover:bg-indigo-700 transition-colors whitespace-nowrap"
                                    >
                                        View Report
                                    </a>
                                </div>
                            </div>
                        </template>
                    </div>
                    <div x-show="filteredReports.length === 0" class="p-8 text-center text-slate-600" style="display: none;">
                        <p class="font-semibold text-slate-900">No accomplishment reports found.</p>
                        <p class="mt-1 text-sm">Try a different type, date, status, company, supervisor, or filename.</p>
                    </div>
                @else
                    <div class="p-8 text-center text-slate-600">
                        <p>No reports submitted yet. Click on <strong>Accomplishment Report</strong> to submit your first report.</p>
                    </div>
                @endif
            </div>
        @else
            <!-- Hours Log View -->
            <!-- Analytics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div x-data="{ percentage: {{ (float)$progressPercentage }} }" class="student-light-card p-6 hover:shadow-md transition-shadow">
                    <p class="student-card-title mb-1">Total Approved Hours</p>
                    <p class="text-4xl font-black text-emerald-600 mb-3">{{ number_format($totalApprovedHours, 2) }}</p>
                    <div class="mt-4 w-full bg-slate-100 rounded-full h-2">
                        <div class="bg-emerald-500 h-2 rounded-full transition-all duration-500" :style="'width: ' + percentage + '%'"></div>
                    </div>
                    <p class="text-[10px] text-slate-600 mt-3 font-bold uppercase tracking-wider">Goal: {{ $assignment?->required_hours ?? 1600 }} hrs</p>
                </div>

                <div class="student-light-card p-6 hover:shadow-md transition-shadow">
                    <p class="student-card-title mb-1">Approved This Month</p>
                    <p class="text-4xl font-black text-indigo-600 mb-3">{{ number_format($monthlyApprovedHours, 2) }}</p>
                    <p class="text-[10px] text-slate-600 font-bold uppercase tracking-wider">For {{ now()->format('F Y') }}</p>
                </div>

                <div class="student-light-card p-6 hover:shadow-md transition-shadow">
                    <p class="student-card-title mb-1">Remaining Hours</p>
                    <p class="text-4xl font-black text-rose-500 mb-3">{{ number_format($remainingHours, 2) }}</p>
                    <p class="text-[10px] text-slate-600 font-bold uppercase tracking-wider">To complete OJT</p>
                </div>
            </div>

            <!-- Filter Section -->
            <div class="student-light-card p-6">
                <h2 class="text-lg font-semibold text-slate-900 mb-6">Report Options</h2>
                <form action="{{ route('student.reports.index') }}" method="GET" class="space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
                        <div class="space-y-2">
                            <label for="start_date" class="text-xs font-bold text-slate-700 uppercase tracking-widest">Start Date</label>
                            <input type="date" id="start_date" name="start_date" value="{{ request('start_date') }}" class="block w-full rounded-xl border border-slate-300 bg-white text-slate-900 px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all">
                        </div>
                        <div class="space-y-2">
                            <label for="end_date" class="text-xs font-bold text-slate-700 uppercase tracking-widest">End Date</label>
                            <input type="date" id="end_date" name="end_date" value="{{ request('end_date') }}" class="block w-full rounded-xl border border-slate-300 bg-white text-slate-900 px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all">
                        </div>
                        <div class="space-y-2">
                            <label for="status" class="text-xs font-bold text-slate-700 uppercase tracking-widest">Status</label>
                            <select id="status" name="status" class="block w-full rounded-xl border border-slate-300 bg-white text-slate-900 px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all">
                                <option value="">All Status</option>
                                <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                                <option value="submitted" {{ request('status') == 'submitted' ? 'selected' : '' }}>Submitted</option>
                                <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                                <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                            </select>
                        </div>
                        <div class="flex flex-col justify-end">
                            <button type="submit" class="px-6 py-2.5 bg-indigo-600 text-white font-bold rounded-xl text-sm hover:bg-indigo-700 active:bg-indigo-800 transition-all duration-200 shadow-sm hover:shadow-md">
                                Generate Report
                            </button>
                        </div>
                    </div>
                    @if(request()->anyFilled(['start_date', 'end_date', 'status']))
                        <div class="pt-2">
                            <a href="{{ route('student.reports.index') }}" class="inline-block px-4 py-2 text-sm font-semibold text-slate-700 bg-slate-100 rounded-lg hover:bg-slate-200 transition-colors">
                                Clear Filters
                            </a>
                        </div>
                    @endif
                </form>
            </div>

            @if($assignment)
                @if($workLogs->isNotEmpty())
                    <!-- Period Summary Cards -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="student-light-card p-6 hover:shadow-md transition-shadow">
                            <p class="student-card-title mb-1">Total Hours in Period</p>
                            <p class="text-4xl font-black text-indigo-600">{{ number_format($totalHours, 2) }}</p>
                        </div>
                        <div class="student-light-card p-6 hover:shadow-md transition-shadow">
                            <p class="student-card-title mb-1">Approved Hours in Period</p>
                            <p class="text-4xl font-black text-emerald-600">{{ number_format($totalApproved, 2) }}</p>
                        </div>
                    </div>

                    <!-- Export Buttons -->
                    <div class="flex flex-col sm:flex-row gap-4">
                        <a href="{{ route('student.reports.export', array_merge(request()->all(), ['type' => 'print'])) }}" target="_blank" class="flex items-center justify-center gap-2 px-6 py-3 bg-red-600 text-white font-bold rounded-xl text-sm hover:bg-red-700 active:bg-red-800 transition-all duration-200 shadow-sm hover:shadow-md">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                            </svg>
                            Export PDF / Print
                        </a>
                        <a href="{{ route('student.reports.export', array_merge(request()->all(), ['type' => 'csv'])) }}" class="flex items-center justify-center gap-2 px-6 py-3 bg-green-600 text-white font-bold rounded-xl text-sm hover:bg-green-700 active:bg-green-800 transition-all duration-200 shadow-sm hover:shadow-md">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            Export Excel (CSV)
                        </a>
                    </div>

                    <!-- Table View -->
                    <div class="student-light-card overflow-hidden">
                        <div class="px-6 py-4 border-b border-slate-200">
                            <h3 class="text-sm font-bold text-slate-900">Table View</h3>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-sm divide-y divide-slate-200">
                                <thead class="bg-slate-50">
                                    <tr>
                                        <th class="px-6 py-4 font-bold text-slate-700 uppercase tracking-wider text-xs">Date</th>
                                        <th class="px-6 py-4 font-bold text-slate-700 uppercase tracking-wider text-xs">Time In</th>
                                        <th class="px-6 py-4 font-bold text-slate-700 uppercase tracking-wider text-xs">Time Out</th>
                                        <th class="px-6 py-4 font-bold text-slate-700 uppercase tracking-wider text-xs">Hours</th>
                                        <th class="px-6 py-4 font-bold text-slate-700 uppercase tracking-wider text-xs">Status</th>
                                        <th class="px-6 py-4 font-bold text-slate-700 uppercase tracking-wider text-xs">Description</th>
                                        <th class="px-6 py-4 font-bold text-slate-700 uppercase tracking-wider text-xs text-right">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-200">
                                    @foreach($workLogs as $log)
                                        @php
                                            $isAttendanceStyleLog = $log->time_in !== null || $log->time_out !== null;
                                            $canEditHoursLog = $isAttendanceStyleLog;
                                            $editConfirmation = $log->status === 'approved'
                                                ? 'Editing this approved hours log will send it back to your supervisor for approval again. Continue?'
                                                : 'Edit this hours log entry? Saving changes will place it back in the supervisor approval queue.';
                                        @endphp
                                        <tr class="hover:bg-slate-50 transition-colors">
                                            <td class="px-6 py-4 font-semibold text-slate-800">{{ $log->work_date->format('M d, Y') }}</td>
                                            <td class="px-6 py-4 text-slate-700">{{ $log->time_in ? \Carbon\Carbon::parse($log->time_in)->format('h:i A') : '-' }}</td>
                                            <td class="px-6 py-4 text-slate-700">{{ $log->time_out ? \Carbon\Carbon::parse($log->time_out)->format('h:i A') : '-' }}</td>
                                            <td class="px-6 py-4 font-mono font-bold text-indigo-600">{{ number_format($log->hours, 2) }}</td>
                                            <td class="px-6 py-4">
                                                <x-status-badge :status="$log->status" :label="ucfirst($log->status)" size="sm" />
                                            </td>
                                            <td class="px-6 py-4 text-slate-700 max-w-xs truncate" title="{{ $log->description }}">
                                                {{ $log->description ?? '-' }}
                                            </td>
                                            <td class="px-6 py-4 text-right">
                                                @if($canEditHoursLog)
                                                    <a
                                                        href="{{ route('student.worklogs.edit', $log->id) }}"
                                                        onclick="return confirm('{{ $editConfirmation }}')"
                                                        class="inline-flex items-center rounded-lg bg-slate-900 px-3 py-2 text-xs font-black uppercase tracking-[0.14em] text-white transition hover:bg-black focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                                    >
                                                        Edit
                                                    </a>
                                                @else
                                                    <span class="inline-flex rounded-full bg-slate-100 px-2.5 py-1 text-[11px] font-bold uppercase tracking-[0.14em] text-slate-500">
                                                        View in Reports
                                                    </span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @elseif(request()->anyFilled(['start_date', 'end_date', 'status']))
                    <div class="bg-white dark:bg-gray-800 p-12 rounded-2xl text-center shadow-sm border border-gray-100 dark:border-gray-700">
                        <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <p class="text-gray-600 dark:text-gray-400 text-lg font-semibold">No records found</p>
                        <p class="text-gray-500 dark:text-gray-500 text-sm mt-1">No matching work logs for the selected filters.</p>
                    </div>
                @else
                    <div class="bg-white dark:bg-gray-800 p-12 rounded-2xl text-center shadow-sm border border-gray-100 dark:border-gray-700">
                        <svg class="mx-auto h-12 w-12 text-indigo-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        <p class="text-gray-700 dark:text-gray-300 text-lg font-semibold">Ready to generate report</p>
                        <p class="text-gray-500 dark:text-gray-400 text-sm mt-1">Select a date range and click "Generate Report" to view your work logs.</p>
                    </div>
                @endif
            @else
                <div class="bg-white dark:bg-gray-800 p-12 rounded-2xl text-center shadow-sm border border-gray-100 dark:border-gray-700">
                    <svg class="mx-auto h-12 w-12 text-yellow-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4v2m0 4v2M7.08 6.47a7 7 0 1114.84 0"></path>
                    </svg>
                    <p class="text-gray-700 dark:text-gray-300 text-lg font-semibold">No Active Assignment</p>
                    <p class="text-gray-500 dark:text-gray-400 text-sm mt-1">Please contact your OJT Coordinator to activate your assignment.</p>
                </div>
            @endif
        @endif
    </div>

    @if(request('view') === 'reports')
        <script>
            function studentAccomplishmentReportSearch(reports) {
                return {
                    reports: Array.isArray(reports) ? reports : [],
                    searchQuery: '',
                    get filteredReports() {
                        const query = (this.searchQuery || '').trim().toLowerCase();

                        return this.reports.filter((report) => {
                            return query === '' || (report.search_blob || '').includes(query);
                        });
                    },
                    statusClass(status) {
                        if (status === 'Approved') {
                            return 'bg-emerald-100 text-emerald-700';
                        }

                        if (status === 'Submitted') {
                            return 'bg-blue-100 text-blue-700';
                        }

                        if (status === 'Rejected') {
                            return 'bg-rose-100 text-rose-700';
                        }

                        return 'bg-amber-100 text-amber-800';
                    },
                };
            }
        </script>
    @endif
</x-student-layout>
