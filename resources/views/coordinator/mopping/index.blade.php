<x-coordinator-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Mapping') }}
        </h2>
    </x-slot>

    <div class="space-y-6">
        <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg dark:bg-gray-800">
            <div class="space-y-4 p-6 text-gray-900 dark:text-gray-100">
                <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                    <div>
                        <h3 class="text-lg font-black text-gray-950 dark:text-gray-50">Attendance Mapping (AR Validation)</h3>
                        <p class="text-sm font-semibold text-gray-700 dark:text-gray-200">Attendance-based hours mapping to validate submitted accomplishment reports.</p>
                    </div>

                    <form method="GET" class="flex items-center gap-2">
                        <label for="month" class="text-sm font-black text-gray-800 dark:text-gray-200">Month</label>
                        <input id="month" name="month" type="month" value="{{ $monthKey }}" class="rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100" />
                        <button type="submit" class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">Apply</button>
                    </form>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-900/40">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-black uppercase tracking-wider text-gray-700 dark:text-gray-300">Student</th>
                                <th class="px-4 py-3 text-left text-xs font-black uppercase tracking-wider text-gray-700 dark:text-gray-300">Company</th>
                                <th class="px-4 py-3 text-left text-xs font-black uppercase tracking-wider text-gray-700 dark:text-gray-300">Attendance</th>
                                <th class="px-4 py-3 text-left text-xs font-black uppercase tracking-wider text-gray-700 dark:text-gray-300">AR</th>
                                <th class="px-4 py-3 text-left text-xs font-black uppercase tracking-wider text-gray-700 dark:text-gray-300">Status</th>
                                <th class="px-4 py-3"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($rows as $row)
                                @php
                                    $assignment = $row['assignment'];
                                    $summary = $row['summary'];
                                    $status = $summary['status'] ?? 'incomplete';
                                    $statusClasses = $status === 'match'
                                        ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-200'
                                        : ($status === 'inconsistent'
                                            ? 'bg-rose-100 text-rose-800 dark:bg-rose-900/30 dark:text-rose-200'
                                            : 'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-200');
                                @endphp
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-900/20">
                                    <td class="px-4 py-4">
                                        <div class="font-black text-gray-950 dark:text-gray-50">{{ $assignment->student?->name ?? 'N/A' }}</div>
                                        <div class="text-xs font-semibold text-gray-600 dark:text-gray-300">{{ $assignment->student?->normalizedStudentSection() ?? '-' }}</div>
                                    </td>
                                    <td class="px-4 py-4 text-sm font-semibold text-gray-800 dark:text-gray-200">{{ $assignment->company?->name ?? 'N/A' }}</td>
                                    <td class="px-4 py-4 text-sm text-gray-800 dark:text-gray-200">
                                        <div class="font-semibold">{{ $summary['attendance_days'] ?? 0 }} day(s)</div>
                                        <div class="text-xs font-bold text-gray-600 dark:text-gray-300">{{ number_format((float)($summary['attendance_hours'] ?? 0), 2) }} hr</div>
                                    </td>
                                    <td class="px-4 py-4 text-sm text-gray-800 dark:text-gray-200">
                                        <div class="font-semibold">{{ $summary['ar_count'] ?? 0 }} report(s)</div>
                                        <div class="text-xs font-bold text-gray-600 dark:text-gray-300">{{ number_format((float)($summary['ar_hours'] ?? 0), 2) }} hr</div>
                                    </td>
                                    <td class="px-4 py-4">
                                        <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-black uppercase tracking-[0.14em] {{ $statusClasses }}">
                                            {{ ucfirst($status) }}
                                        </span>
                                        @if(($summary['attendance_open_count'] ?? 0) > 0)
                                            <div class="mt-1 text-xs font-bold text-gray-600 dark:text-gray-300">Open time-out: {{ $summary['attendance_open_count'] }}</div>
                                        @endif
                                    </td>
                                    <td class="px-4 py-4 text-right">
                                        <div class="inline-flex items-center gap-2">
                                            <a href="{{ route('coordinator.mapping.show', ['assignment' => $assignment->id, 'from' => $monthKey, 'to' => $monthKey, 'month' => $monthKey]) }}" class="inline-flex items-center rounded-md bg-slate-900 px-3 py-2 text-sm font-bold text-white shadow-sm ring-1 ring-slate-700 transition-colors hover:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-slate-400 focus:ring-offset-2 active:bg-slate-950 disabled:cursor-not-allowed disabled:opacity-60 dark:bg-slate-100 dark:text-slate-950 dark:ring-slate-300 dark:hover:bg-white dark:focus:ring-slate-500 dark:focus:ring-offset-gray-800 dark:active:bg-slate-200">
                                                View
                                            </a>
                                            <a href="{{ route('coordinator.mapping.show', ['assignment' => $assignment->id, 'from' => $monthKey, 'to' => $monthKey, 'month' => $monthKey, 'print' => 1]) }}" target="_blank" class="inline-flex items-center rounded-md bg-indigo-700 px-3 py-2 text-sm font-bold text-white shadow-sm ring-1 ring-indigo-500 transition-colors hover:bg-indigo-600 focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:ring-offset-2 active:bg-indigo-800 disabled:cursor-not-allowed disabled:opacity-60 dark:bg-indigo-300 dark:text-slate-950 dark:ring-indigo-200 dark:hover:bg-indigo-200 dark:focus:ring-indigo-300 dark:focus:ring-offset-gray-800 dark:active:bg-indigo-400">
                                                Print
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-10 text-center text-sm font-semibold text-gray-600 dark:text-gray-300">No active OJT students found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-coordinator-layout>
