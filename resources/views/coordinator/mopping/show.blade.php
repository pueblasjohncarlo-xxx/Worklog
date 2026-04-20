<x-coordinator-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Mopping Details') }}
        </h2>
    </x-slot>

    <div class="space-y-6">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900 dark:text-gray-100 space-y-4">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div>
                        <h3 class="text-lg font-semibold">{{ $assignment->student?->name ?? 'Student' }}</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400">{{ $assignment->company?->name ?? 'Company' }} • {{ $monthStart->format('F Y') }}</p>
                    </div>

                    <form method="GET" class="flex items-center gap-2">
                        <label for="month" class="text-sm text-gray-600 dark:text-gray-400">Month</label>
                        <input id="month" name="month" type="month" value="{{ $monthKey }}" class="rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100" />
                        <button type="submit" class="px-4 py-2 rounded-md bg-indigo-600 text-white text-sm font-semibold hover:bg-indigo-700">Apply</button>
                        <a href="{{ route('coordinator.mopping.index', ['month' => $monthKey]) }}" class="px-4 py-2 rounded-md bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-100 text-sm font-semibold">Back</a>
                    </form>
                </div>

                @php
                    $status = $summary['status'] ?? 'incomplete';
                    $statusClasses = $status === 'match'
                        ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300'
                        : ($status === 'inconsistent'
                            ? 'bg-rose-100 text-rose-700 dark:bg-rose-900/30 dark:text-rose-300'
                            : 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300');
                @endphp

                <div class="flex flex-wrap items-center gap-3">
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold {{ $statusClasses }}">{{ ucfirst($status) }}</span>
                    <span class="text-sm text-gray-700 dark:text-gray-300">Attendance: <span class="font-semibold">{{ $summary['attendance_days'] ?? 0 }}</span> day(s), <span class="font-semibold">{{ number_format((float)($summary['attendance_hours'] ?? 0), 2) }}</span> hr</span>
                    <span class="text-sm text-gray-700 dark:text-gray-300">AR: <span class="font-semibold">{{ $summary['ar_count'] ?? 0 }}</span> report(s), <span class="font-semibold">{{ number_format((float)($summary['ar_hours'] ?? 0), 2) }}</span> hr</span>
                    <span class="text-sm text-gray-700 dark:text-gray-300">Delta: <span class="font-semibold">{{ number_format((float)($summary['hours_delta'] ?? 0), 2) }}</span> hr</span>
                </div>

                @if(!empty($summary['uncovered_attendance_dates']))
                    <div class="rounded-md border border-amber-200 dark:border-amber-900/40 bg-amber-50 dark:bg-amber-900/10 p-4">
                        <div class="font-semibold text-amber-800 dark:text-amber-200">Uncovered attendance dates</div>
                        <div class="text-sm text-amber-800 dark:text-amber-200">{{ implode(', ', $summary['uncovered_attendance_dates']) }}</div>
                    </div>
                @endif

                @if(!empty($summary['extra_daily_ar_dates']))
                    <div class="rounded-md border border-rose-200 dark:border-rose-900/40 bg-rose-50 dark:bg-rose-900/10 p-4">
                        <div class="font-semibold text-rose-800 dark:text-rose-200">Daily AR dates without attendance</div>
                        <div class="text-sm text-rose-800 dark:text-rose-200">{{ implode(', ', $summary['extra_daily_ar_dates']) }}</div>
                    </div>
                @endif

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div class="rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
                        <div class="px-4 py-3 bg-gray-50 dark:bg-gray-900/40 border-b border-gray-200 dark:border-gray-700">
                            <h4 class="font-semibold">Attendance Logs</h4>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-white dark:bg-gray-800">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600 dark:text-gray-400">Date</th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600 dark:text-gray-400">Time In</th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600 dark:text-gray-400">Time Out</th>
                                        <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-600 dark:text-gray-400">Hours</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                    @forelse($attendanceLogs as $log)
                                        <tr>
                                            <td class="px-4 py-3 text-sm">{{ $log->work_date?->format('M d, Y') ?? '—' }}</td>
                                            <td class="px-4 py-3 text-sm">{{ $log->time_in ? $log->time_in->format('H:i') : '—' }}</td>
                                            <td class="px-4 py-3 text-sm">{{ $log->time_out ? $log->time_out->format('H:i') : '—' }}</td>
                                            <td class="px-4 py-3 text-sm text-right">{{ number_format((float)$log->hours, 2) }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="px-4 py-8 text-center text-sm text-gray-500 dark:text-gray-400">No attendance logs in this month.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
                        <div class="px-4 py-3 bg-gray-50 dark:bg-gray-900/40 border-b border-gray-200 dark:border-gray-700">
                            <h4 class="font-semibold">Accomplishment Reports</h4>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-white dark:bg-gray-800">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600 dark:text-gray-400">Type</th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600 dark:text-gray-400">Date</th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600 dark:text-gray-400">Status</th>
                                        <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-600 dark:text-gray-400">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                    @forelse($arLogs as $log)
                                        <tr>
                                            <td class="px-4 py-3 text-sm">{{ ucfirst($log->type ?? 'AR') }}</td>
                                            <td class="px-4 py-3 text-sm">{{ $log->work_date?->format('M d, Y') ?? '—' }}</td>
                                            <td class="px-4 py-3 text-sm">{{ ucfirst($log->status === 'rejected' ? 'declined' : ($log->status ?? 'unknown')) }}</td>
                                            <td class="px-4 py-3 text-sm text-right space-x-2">
                                                <a href="{{ route('coordinator.worklogs.print', $log->id) }}" class="text-indigo-700 dark:text-indigo-300 font-semibold hover:underline">View</a>
                                                @if($log->attachment_path)
                                                    <a href="{{ route('coordinator.worklogs.attachment', $log->id) }}" class="text-gray-700 dark:text-gray-300 font-semibold hover:underline">Download</a>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="px-4 py-8 text-center text-sm text-gray-500 dark:text-gray-400">No AR logs in this month.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-coordinator-layout>
