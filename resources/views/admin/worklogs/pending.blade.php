<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-bold text-white">Pending WorkLogs</h2>
                <p class="text-gray-400 text-xs">Submitted entries awaiting review</p>
            </div>
            <div class="text-[10px] text-gray-500">
                Updated: {{ now()->format('H:i') }}
            </div>
        </div>
    </x-slot>

    <div class="space-y-4">
        @if (session('status'))
            <div class="p-3 bg-green-100 text-green-800 rounded">{{ session('status') }}</div>
        @endif

        @if (isset($errors) && $errors->any())
            <div class="p-3 bg-red-100 text-red-800 rounded">{{ $errors->first() }}</div>
        @endif

        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between gap-3">
                <div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100">Queue</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ $workLogs->total() }} pending</p>
                </div>
                <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center px-3 py-2 rounded-md bg-indigo-600 text-white text-sm font-semibold hover:bg-indigo-700 shadow-sm transition-colors">
                    Back to Dashboard
                </a>
            </div>

            <div class="p-4 overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50">
                            <th class="text-left px-3 py-2 font-bold text-gray-700 dark:text-gray-200">Date</th>
                            <th class="text-left px-3 py-2 font-bold text-gray-700 dark:text-gray-200">Student</th>
                            <th class="text-left px-3 py-2 font-bold text-gray-700 dark:text-gray-200">Type</th>
                            <th class="text-left px-3 py-2 font-bold text-gray-700 dark:text-gray-200">Hours</th>
                            <th class="text-left px-3 py-2 font-bold text-gray-700 dark:text-gray-200">Submitted To</th>
                            <th class="text-left px-3 py-2 font-bold text-gray-700 dark:text-gray-200">Company</th>
                            <th class="text-left px-3 py-2 font-bold text-gray-700 dark:text-gray-200">Attachment</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse ($workLogs as $log)
                            <tr class="align-top hover:bg-gray-50 dark:hover:bg-gray-900/30 transition-colors">
                                <td class="px-3 py-2 whitespace-nowrap text-gray-900 dark:text-gray-100">
                                    {{ optional($log->work_date)->format('M d, Y') ?? '-' }}
                                </td>
                                <td class="px-3 py-2 text-gray-900 dark:text-gray-100">
                                    <div class="font-semibold">{{ $log->assignment?->student?->name ?? 'N/A' }}</div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">#{{ $log->id }}</div>
                                </td>
                                <td class="px-3 py-2 text-gray-900 dark:text-gray-100">
                                    {{ ucfirst((string) ($log->type ?? '')) ?: '-' }}
                                </td>
                                <td class="px-3 py-2 text-gray-900 dark:text-gray-100">
                                    {{ is_numeric($log->hours) ? number_format((float) $log->hours, 2) : '-' }}
                                </td>
                                <td class="px-3 py-2 text-gray-900 dark:text-gray-100">
                                    <span class="px-2 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-200">
                                        {{ ucfirst((string) ($log->submitted_to ?? '')) ?: '—' }}
                                    </span>
                                </td>
                                <td class="px-3 py-2 text-gray-900 dark:text-gray-100">
                                    {{ $log->assignment?->company?->name ?? '—' }}
                                </td>
                                <td class="px-3 py-2">
                                    @if ($log->attachment_path)
                                        <div class="flex flex-col gap-1 min-w-[160px]">
                                            <a
                                                href="{{ route('admin.worklogs.attachment', $log) }}?inline=1"
                                                target="_blank"
                                                class="text-indigo-700 dark:text-indigo-300 hover:underline font-semibold"
                                            >
                                                Preview
                                            </a>
                                            <a
                                                href="{{ route('admin.worklogs.attachment', $log) }}"
                                                class="text-emerald-700 dark:text-emerald-300 hover:underline font-semibold"
                                            >
                                                Download
                                            </a>
                                        </div>
                                    @else
                                        <span class="text-gray-500 dark:text-gray-400">—</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-3 py-6 text-center text-gray-500 dark:text-gray-400">
                                    No submitted WorkLogs to review.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="mt-4">
                    {{ $workLogs->links() }}
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
