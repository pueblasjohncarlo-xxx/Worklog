<x-app-layout>
    <x-slot name="header">
        @if(request('view') === 'reports')
            Reports & Performance
        @else
            Hours Log & Analytics
        @endif
    </x-slot>

    <div class="space-y-6">
        <!-- Navigation Tabs -->
        <div class="flex gap-4 border-b border-gray-200 dark:border-gray-700">
            <a href="{{ route('student.reports.index') }}" 
               class="px-4 py-3 font-bold text-sm uppercase tracking-wider
                   {{ !request('view') || request('view') !== 'reports' 
                       ? 'text-indigo-600 dark:text-indigo-400 border-b-2 border-indigo-600' 
                       : 'text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200' 
                   }}">
                📊 Hours Log
            </a>
            <a href="{{ route('student.reports.index', ['view' => 'reports']) }}" 
               class="px-4 py-3 font-bold text-sm uppercase tracking-wider
                   {{ request('view') === 'reports' 
                       ? 'text-indigo-600 dark:text-indigo-400 border-b-2 border-indigo-600' 
                       : 'text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200' 
                   }}">
                📋 Reports
            </a>
        </div>

        @if(request('view') === 'reports')
            <!-- Reports View -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Submitted Reports</p>
                    <p class="text-3xl font-black text-indigo-600">{{ $workLogs->where('type', '!=', 'attendance')->count() }}</p>
                </div>
                <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Approved Reports</p>
                    <p class="text-3xl font-black text-emerald-600">{{ $workLogs->where('status', 'approved')->where('type', '!=', 'attendance')->count() }}</p>
                </div>
            </div>

            <!-- Reports List -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700">
                    <h3 class="text-sm font-bold text-gray-800 dark:text-gray-200">Your Submitted Reports</h3>
                </div>
                @php
                    $reports = $workLogs->where('type', '!=', 'attendance')->sortByDesc('created_at');
                @endphp
                @if($reports->count() > 0)
                    <div class="divide-y divide-gray-100 dark:divide-gray-700">
                        @foreach($reports as $report)
                            <div class="p-6 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                <div class="flex items-start justify-between gap-4">
                                    <div class="flex-1">
                                        <h4 class="font-bold text-gray-900 dark:text-white capitalize">{{ ucfirst($report->type) }} Report</h4>
                                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                            Date: {{ $report->work_date->format('M d, Y') }}
                                        </p>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">
                                            Status: 
                                            <span class="px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wider
                                                {{ $report->status === 'approved' ? 'bg-emerald-100 text-emerald-700' : '' }}
                                                {{ $report->status === 'submitted' ? 'bg-blue-100 text-blue-700' : '' }}
                                                {{ $report->status === 'draft' ? 'bg-gray-100 text-gray-700' : '' }}
                                                {{ $report->status === 'rejected' ? 'bg-rose-100 text-rose-700' : '' }}
                                            ">
                                                {{ $report->status }}
                                            </span>
                                        </p>
                                    </div>
                                    <a href="{{ route('student.worklogs.print', $report) }}" target="_blank" 
                                       class="px-4 py-2 bg-indigo-600 text-white font-semibold rounded-lg text-sm hover:bg-indigo-700 transition-colors whitespace-nowrap">
                                        View Report
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="p-8 text-center text-gray-500 dark:text-gray-400">
                        <p>No reports submitted yet. Click on <strong>Accomplishment Report</strong> to submit your first report.</p>
                    </div>
                @endif
            </div>
        @else
            <!-- Hours Log View (Original) -->
            @php
                $target = $assignment->required_hours ?? 1600;
                $percentage = ($target) > 0 
                    ? min(100, ($totalApprovedHours / $target) * 100) 
                    : 0;
            @endphp
            <!-- Analytics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div x-data="{ percentage: {{ (float)$percentage }} }" class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Total Approved Hours</p>
                <p class="text-3xl font-black text-emerald-600">{{ number_format($totalApprovedHours, 2) }}</p>
                <div class="mt-2 w-full bg-gray-100 dark:bg-gray-700 rounded-full h-1.5">
                    <div class="bg-emerald-500 h-1.5 rounded-full" :style="'width: ' + percentage + '%'"></div>
                </div>
                <p class="text-[10px] text-gray-500 mt-2 font-bold uppercase tracking-wider">Goal: {{ $assignment->required_hours ?? 1600 }} hrs</p>
            </div>

            <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Approved This Month</p>
                <p class="text-3xl font-black text-indigo-600">{{ number_format($monthlyApprovedHours, 2) }}</p>
                <p class="text-[10px] text-gray-500 mt-2 font-bold uppercase tracking-wider">For {{ now()->format('F Y') }}</p>
            </div>

            <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Remaining Hours</p>
                <p class="text-3xl font-black text-rose-500">{{ number_format(max(0, ($assignment->required_hours ?? 1600) - $totalApprovedHours), 2) }}</p>
                <p class="text-[10px] text-gray-500 mt-2 font-bold uppercase tracking-wider">To complete OJT</p>
            </div>
        </div>

        <!-- Filter Section -->
        <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
            <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">Report Options</h2>
            <form action="{{ route('student.reports.index') }}" method="GET" class="flex flex-wrap gap-4 items-end">
                <div class="space-y-1">
                    <label class="text-xs font-bold text-gray-400 uppercase tracking-widest">Start Date</label>
                    <input type="date" name="start_date" value="{{ request('start_date') }}" class="block w-full rounded-xl border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 text-sm focus:ring-indigo-500">
                </div>
                <div class="space-y-1">
                    <label class="text-xs font-bold text-gray-400 uppercase tracking-widest">End Date</label>
                    <input type="date" name="end_date" value="{{ request('end_date') }}" class="block w-full rounded-xl border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 text-sm focus:ring-indigo-500">
                </div>
                <div class="space-y-1">
                    <label class="text-xs font-bold text-gray-400 uppercase tracking-widest">Status</label>
                    <select name="status" class="block w-full rounded-xl border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 text-sm focus:ring-indigo-500">
                        <option value="">All Status</option>
                        <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="submitted" {{ request('status') == 'submitted' ? 'selected' : '' }}>Submitted</option>
                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                    </select>
                </div>
                <button type="submit" class="px-6 py-2.5 bg-indigo-600 text-white font-bold rounded-xl text-sm hover:bg-indigo-700 transition-colors">
                    Generate Summary Report
                </button>
                @if(request()->anyFilled(['start_date', 'end_date', 'status']))
                    <a href="{{ route('student.reports.index') }}" class="px-6 py-2.5 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 font-bold rounded-xl text-sm hover:bg-gray-200 transition-colors">
                        Clear
                    </a>
                @endif
            </form>
        </div>

        @if($workLogs->isNotEmpty())
            <!-- Report Summary -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Total Hours in Period</p>
                    <p class="text-3xl font-black text-indigo-600">{{ number_format($totalHours, 2) }}</p>
                </div>
                <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Approved Hours in Period</p>
                    <p class="text-3xl font-black text-emerald-600">{{ number_format($totalApproved, 2) }}</p>
                </div>
            </div>

            <!-- Export Buttons -->
            <div class="flex gap-4">
                <a href="{{ route('student.reports.export', array_merge(request()->all(), ['type' => 'print'])) }}" target="_blank" class="flex items-center gap-2 px-6 py-3 bg-red-600 text-white font-bold rounded-xl text-sm hover:bg-red-700 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                    </svg>
                    Export PDF / Print
                </a>
                <a href="{{ route('student.reports.export', array_merge(request()->all(), ['type' => 'csv'])) }}" class="flex items-center gap-2 px-6 py-3 bg-green-600 text-white font-bold rounded-xl text-sm hover:bg-green-700 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Export Excel (CSV)
                </a>
            </div>

            <!-- Logs Table -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700">
                    <h3 class="text-sm font-bold text-gray-800 dark:text-gray-200">Table View</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm divide-y divide-gray-100 dark:divide-gray-800">
                        <thead class="bg-gray-50 dark:bg-gray-900/50">
                            <tr>
                                <th class="px-6 py-4 font-bold text-gray-400 uppercase tracking-wider">Date</th>
                                <th class="px-6 py-4 font-bold text-gray-400 uppercase tracking-wider">Time In</th>
                                <th class="px-6 py-4 font-bold text-gray-400 uppercase tracking-wider">Time Out</th>
                                <th class="px-6 py-4 font-bold text-gray-400 uppercase tracking-wider">Hours</th>
                                <th class="px-6 py-4 font-bold text-gray-400 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-4 font-bold text-gray-400 uppercase tracking-wider">Description</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50 dark:divide-gray-900">
                            @foreach($workLogs as $log)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                    <td class="px-6 py-4 font-bold text-gray-800 dark:text-gray-200">{{ $log->work_date->format('M d, Y') }}</td>
                                    <td class="px-6 py-4 text-gray-500">{{ $log->time_in ? \Carbon\Carbon::parse($log->time_in)->format('h:i A') : '-' }}</td>
                                    <td class="px-6 py-4 text-gray-500">{{ $log->time_out ? \Carbon\Carbon::parse($log->time_out)->format('h:i A') : '-' }}</td>
                                    <td class="px-6 py-4 font-mono font-bold text-indigo-600">{{ number_format($log->hours, 2) }}</td>
                                    <td class="px-6 py-4">
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wider
                                            {{ $log->status === 'approved' ? 'bg-emerald-100 text-emerald-700' : '' }}
                                            {{ $log->status === 'submitted' ? 'bg-blue-100 text-blue-700' : '' }}
                                            {{ $log->status === 'draft' ? 'bg-gray-100 text-gray-700' : '' }}
                                            {{ $log->status === 'rejected' ? 'bg-rose-100 text-rose-700' : '' }}
                                        ">
                                            {{ $log->status }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-gray-600 dark:text-gray-400 max-w-xs truncate">
                                        {{ $log->description }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @elseif(request()->anyFilled(['start_date', 'end_date', 'status']))
            <div class="bg-white dark:bg-gray-800 p-8 rounded-2xl text-center shadow-sm border border-gray-100 dark:border-gray-700">
                <p class="text-gray-500 dark:text-gray-400">No records found matching your filters.</p>
            </div>
        @else
            <div class="bg-white dark:bg-gray-800 p-8 rounded-2xl text-center shadow-sm border border-gray-100 dark:border-gray-700">
                <p class="text-gray-500 dark:text-gray-400">Select a date range and click "Generate Summary Report" to view data.</p>
            </div>
        @endif
        @endif
    </div>
</x-app-layout>
