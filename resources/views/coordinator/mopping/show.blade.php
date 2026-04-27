<x-coordinator-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Mapping Details') }}
        </h2>
    </x-slot>

    <style>
        @media print {
            .app-sidebar, header, nav, .no-print { display: none !important; }
            main { padding: 0 !important; }
            body { background: #fff !important; color: #0f172a !important; }
        }
    </style>

    @if(request()->boolean('print'))
        <script>
            window.addEventListener('load', function () {
                window.print();
            });
        </script>
    @endif

    <div class="space-y-6">
        <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg dark:bg-gray-800">
            <div class="space-y-4 p-6 text-gray-900 dark:text-gray-100">
                <div class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
                    <div>
                        <h3 class="text-lg font-black text-gray-950 dark:text-gray-50">Mapping of OJT Hours</h3>
                        <p class="text-sm font-semibold text-gray-700 dark:text-gray-200">Calendar-style breakdown with weekly and monthly totals (attendance-based).</p>
                    </div>

                    <div class="no-print flex flex-col items-stretch gap-3 sm:flex-row sm:items-end">
                        <form method="GET" class="flex flex-col items-stretch gap-3 sm:flex-row sm:items-end">
                            <div>
                                <label for="from" class="block text-xs font-black uppercase tracking-wider text-gray-700 dark:text-gray-200">From</label>
                                <input id="from" name="from" type="month" value="{{ $fromKey ?? '' }}" class="mt-1 rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100" />
                            </div>
                            <div>
                                <label for="to" class="block text-xs font-black uppercase tracking-wider text-gray-700 dark:text-gray-200">To</label>
                                <input id="to" name="to" type="month" value="{{ $toKey ?? '' }}" class="mt-1 rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100" />
                            </div>
                            <button type="submit" class="h-[42px] rounded-md bg-indigo-600 px-4 text-sm font-semibold text-white hover:bg-indigo-700">Apply</button>
                        </form>
                        <button type="button" onclick="window.print()" class="h-[42px] rounded-md bg-gray-900 px-4 text-sm font-semibold text-white hover:bg-black">Print</button>
                    </div>
                </div>

                @if(!empty($mapping))
                    @include('partials.mapping.calendar-range', ['mapping' => $mapping])
                @endif
            </div>
        </div>

        <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg dark:bg-gray-800">
            <div class="space-y-4 p-6 text-gray-900 dark:text-gray-100">
                <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                    <div>
                        <h3 class="text-lg font-black text-gray-950 dark:text-gray-50">{{ $assignment->student?->name ?? 'Student' }}</h3>
                        <p class="text-sm font-semibold text-gray-700 dark:text-gray-200">{{ $assignment->company?->name ?? 'Company' }} | {{ $monthStart->format('F Y') }}</p>
                    </div>

                    <form method="GET" class="flex items-center gap-2">
                        <label for="month" class="text-sm font-black text-gray-800 dark:text-gray-200">Month</label>
                        <input id="month" name="month" type="month" value="{{ $monthKey }}" class="rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100" />
                        <input type="hidden" name="from" value="{{ $fromKey ?? '' }}" />
                        <input type="hidden" name="to" value="{{ $toKey ?? '' }}" />
                        <button type="submit" class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">Apply</button>
                        <a href="{{ route('coordinator.mapping.index', ['month' => $monthKey]) }}" class="rounded-md bg-gray-200 px-4 py-2 text-sm font-semibold text-gray-900 dark:bg-gray-700 dark:text-gray-100">Back</a>
                    </form>
                </div>

                @php
                    $status = $summary['status'] ?? 'incomplete';
                    $statusClasses = $status === 'match'
                        ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-200'
                        : ($status === 'inconsistent'
                            ? 'bg-rose-100 text-rose-800 dark:bg-rose-900/30 dark:text-rose-200'
                            : 'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-200');
                @endphp

                <div class="flex flex-wrap items-center gap-3">
                    <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-black uppercase tracking-[0.14em] {{ $statusClasses }}">{{ ucfirst($status) }}</span>
                    <span class="text-sm font-semibold text-gray-800 dark:text-gray-200">Attendance: <span class="font-black">{{ $summary['attendance_days'] ?? 0 }}</span> day(s), <span class="font-black">{{ number_format((float)($summary['attendance_hours'] ?? 0), 2) }}</span> hr</span>
                    <span class="text-sm font-semibold text-gray-800 dark:text-gray-200">AR: <span class="font-black">{{ $summary['ar_count'] ?? 0 }}</span> report(s), <span class="font-black">{{ number_format((float)($summary['ar_hours'] ?? 0), 2) }}</span> hr</span>
                    <span class="text-sm font-semibold text-gray-800 dark:text-gray-200">Delta: <span class="font-black">{{ number_format((float)($summary['hours_delta'] ?? 0), 2) }}</span> hr</span>
                </div>

                @if(!empty($summary['uncovered_attendance_dates']))
                    <div class="rounded-md border border-amber-200 bg-amber-50 p-4 dark:border-amber-900/40 dark:bg-amber-900/10">
                        <div class="font-black text-amber-900 dark:text-amber-200">Uncovered attendance dates</div>
                        <div class="text-sm font-semibold text-amber-900 dark:text-amber-200">{{ implode(', ', $summary['uncovered_attendance_dates']) }}</div>
                    </div>
                @endif

                @if(!empty($summary['extra_daily_ar_dates']))
                    <div class="rounded-md border border-rose-200 bg-rose-50 p-4 dark:border-rose-900/40 dark:bg-rose-900/10">
                        <div class="font-black text-rose-900 dark:text-rose-200">Daily AR dates without attendance</div>
                        <div class="text-sm font-semibold text-rose-900 dark:text-rose-200">{{ implode(', ', $summary['extra_daily_ar_dates']) }}</div>
                    </div>
                @endif

                <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                    <div class="overflow-hidden rounded-lg border border-gray-200 dark:border-gray-700">
                        <div class="border-b border-gray-200 bg-gray-50 px-4 py-3 dark:border-gray-700 dark:bg-gray-900/40">
                            <h4 class="font-black text-gray-900 dark:text-gray-100">Attendance Logs</h4>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-white dark:bg-gray-800">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-black uppercase tracking-wider text-gray-700 dark:text-gray-300">Date</th>
                                        <th class="px-4 py-3 text-left text-xs font-black uppercase tracking-wider text-gray-700 dark:text-gray-300">Time In</th>
                                        <th class="px-4 py-3 text-left text-xs font-black uppercase tracking-wider text-gray-700 dark:text-gray-300">Time Out</th>
                                        <th class="px-4 py-3 text-right text-xs font-black uppercase tracking-wider text-gray-700 dark:text-gray-300">Hours</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                    @forelse($attendanceLogs as $log)
                                        <tr>
                                            <td class="px-4 py-3 text-sm font-semibold text-gray-800 dark:text-gray-200">{{ $log->work_date?->format('M d, Y') ?? '-' }}</td>
                                            <td class="px-4 py-3 text-sm font-semibold text-gray-800 dark:text-gray-200">{{ $log->time_in ? $log->time_in->format('H:i') : '-' }}</td>
                                            <td class="px-4 py-3 text-sm font-semibold text-gray-800 dark:text-gray-200">{{ $log->time_out ? $log->time_out->format('H:i') : '-' }}</td>
                                            <td class="px-4 py-3 text-right text-sm font-black text-indigo-700 dark:text-indigo-300">{{ number_format((float)$log->hours, 2) }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="px-4 py-8 text-center text-sm font-semibold text-gray-600 dark:text-gray-300">No attendance logs in this month.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="overflow-hidden rounded-lg border border-gray-200 dark:border-gray-700">
                        <div class="border-b border-gray-200 bg-gray-50 px-4 py-3 dark:border-gray-700 dark:bg-gray-900/40">
                            <h4 class="font-black text-gray-900 dark:text-gray-100">Accomplishment Reports</h4>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-white dark:bg-gray-800">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-black uppercase tracking-wider text-gray-700 dark:text-gray-300">Type</th>
                                        <th class="px-4 py-3 text-left text-xs font-black uppercase tracking-wider text-gray-700 dark:text-gray-300">Date</th>
                                        <th class="px-4 py-3 text-left text-xs font-black uppercase tracking-wider text-gray-700 dark:text-gray-300">Status</th>
                                        <th class="px-4 py-3 text-right text-xs font-black uppercase tracking-wider text-gray-700 dark:text-gray-300">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                    @forelse($arLogs as $log)
                                        <tr>
                                            <td class="px-4 py-3 text-sm font-semibold text-gray-800 dark:text-gray-200">{{ ucfirst($log->type ?? 'AR') }}</td>
                                            <td class="px-4 py-3 text-sm font-semibold text-gray-800 dark:text-gray-200">{{ $log->work_date?->format('M d, Y') ?? '-' }}</td>
                                            <td class="px-4 py-3 text-sm font-black text-gray-900 dark:text-gray-100">{{ ucfirst($log->status === 'rejected' ? 'declined' : ($log->status ?? 'unknown')) }}</td>
                                            <td class="space-x-2 px-4 py-3 text-right text-sm">
                                                @if($log->attachment_path)
                                                    <a href="{{ route('coordinator.worklogs.attachment', ['workLog' => $log->id, 'inline' => 1, 'v' => optional($log->updated_at)->timestamp ?? $log->id]) }}" target="_blank" class="font-black text-indigo-700 hover:underline dark:text-indigo-300">View</a>
                                                @else
                                                    <a href="{{ route('coordinator.worklogs.print', $log->id) }}" target="_blank" class="font-black text-indigo-700 hover:underline dark:text-indigo-300">View</a>
                                                @endif
                                                @if($log->attachment_path)
                                                    <a href="{{ route('coordinator.worklogs.attachment', ['workLog' => $log->id, 'v' => optional($log->updated_at)->timestamp ?? $log->id]) }}" class="font-black text-gray-800 hover:underline dark:text-gray-200">Download</a>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="px-4 py-8 text-center text-sm font-semibold text-gray-600 dark:text-gray-300">No AR logs in this month.</td>
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
