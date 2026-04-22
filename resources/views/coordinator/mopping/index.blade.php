<x-coordinator-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Mapping') }}
        </h2>
    </x-slot>

    <div class="space-y-6">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900 dark:text-gray-100 space-y-4">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Attendance Mapping (AR Validation)</h3>
                        <p class="text-sm text-gray-700 dark:text-gray-300">Attendance-based hours mapping to validate submitted accomplishment reports.</p>
                    </div>

                    <form method="GET" class="flex items-center gap-2">
                        <label for="month" class="text-sm font-medium text-gray-700 dark:text-gray-300">Month</label>
                        <input id="month" name="month" type="month" value="{{ $monthKey }}" class="rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100" />
                        <button type="submit" class="px-4 py-2 rounded-md bg-indigo-600 text-white text-sm font-semibold hover:bg-indigo-700">Apply</button>
                    </form>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-900/40">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600 dark:text-gray-400">Student</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600 dark:text-gray-400">Company</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600 dark:text-gray-400">Attendance</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600 dark:text-gray-400">AR</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600 dark:text-gray-400">Status</th>
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
                                        ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300'
                                        : ($status === 'inconsistent'
                                            ? 'bg-rose-100 text-rose-700 dark:bg-rose-900/30 dark:text-rose-300'
                                            : 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300');
                                @endphp
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-900/20">
                                    <td class="px-4 py-4">
                                        <div class="font-semibold text-gray-900 dark:text-gray-100">{{ $assignment->student?->name ?? 'N/A' }}</div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">{{ $assignment->student?->normalizedStudentSection() ?? '—' }}</div>
                                    </td>
                                    <td class="px-4 py-4 text-sm text-gray-700 dark:text-gray-300">{{ $assignment->company?->name ?? 'N/A' }}</td>
                                    <td class="px-4 py-4 text-sm text-gray-700 dark:text-gray-300">
                                        <div>{{ $summary['attendance_days'] ?? 0 }} day(s)</div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">{{ number_format((float)($summary['attendance_hours'] ?? 0), 2) }} hr</div>
                                    </td>
                                    <td class="px-4 py-4 text-sm text-gray-700 dark:text-gray-300">
                                        <div>{{ $summary['ar_count'] ?? 0 }} report(s)</div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">{{ number_format((float)($summary['ar_hours'] ?? 0), 2) }} hr</div>
                                    </td>
                                    <td class="px-4 py-4">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold {{ $statusClasses }}">
                                            {{ ucfirst($status) }}
                                        </span>
                                        @if(($summary['attendance_open_count'] ?? 0) > 0)
                                            <div class="mt-1 text-xs text-gray-500 dark:text-gray-400">Open time-out: {{ $summary['attendance_open_count'] }}</div>
                                        @endif
                                    </td>
                                    <td class="px-4 py-4 text-right">
                                        <div class="inline-flex items-center gap-2">
                                            <a href="{{ route('coordinator.mapping.show', ['assignment' => $assignment->id, 'from' => $monthKey, 'to' => $monthKey, 'month' => $monthKey]) }}" class="inline-flex items-center px-3 py-2 rounded-md bg-gray-900 text-white text-sm font-semibold hover:bg-black shadow-sm">
                                                View
                                            </a>
                                            <a href="{{ route('coordinator.mapping.show', ['assignment' => $assignment->id, 'from' => $monthKey, 'to' => $monthKey, 'month' => $monthKey, 'print' => 1]) }}" target="_blank" class="inline-flex items-center px-3 py-2 rounded-md bg-indigo-600 text-white text-sm font-semibold hover:bg-indigo-700 shadow-sm">
                                                Print
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-10 text-center text-sm text-gray-500 dark:text-gray-400">No active OJT students found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-coordinator-layout>
