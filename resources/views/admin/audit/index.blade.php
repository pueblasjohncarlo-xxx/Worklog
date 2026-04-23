<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-bold text-white">Audit Logs</h2>
                <p class="text-gray-400 text-xs">Recent administrative activity</p>
            </div>
            <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center px-3 py-2 rounded-md bg-indigo-600 text-white text-sm font-semibold hover:bg-indigo-700 shadow-sm transition-colors">
                Back to Dashboard
            </a>
        </div>
    </x-slot>

    <div class="space-y-4">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100">Recent Audit Trail</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ $auditLogs->total() }} records</p>
                </div>
            </div>

            <div class="p-4">
                <form method="GET" action="{{ route('admin.audit.index') }}" x-data="{ searchText: @js($search ?? '') }" class="mb-4">
                    <div class="relative max-w-md">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <input
                            type="text"
                            name="search"
                            x-model="searchText"
                            @input.debounce.300ms="$el.form.requestSubmit()"
                            class="block w-full rounded-lg border border-gray-300 bg-white py-2.5 pl-10 pr-4 text-sm text-gray-900 shadow-sm transition focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100"
                            placeholder="Search user, action, subject, or IP..."
                            autocomplete="off"
                        >
                    </div>
                </form>

                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50">
                                <th class="text-left px-3 py-2 font-bold text-gray-700 dark:text-gray-200">Time</th>
                                <th class="text-left px-3 py-2 font-bold text-gray-700 dark:text-gray-200">User</th>
                                <th class="text-left px-3 py-2 font-bold text-gray-700 dark:text-gray-200">Action</th>
                                <th class="text-left px-3 py-2 font-bold text-gray-700 dark:text-gray-200">Subject</th>
                                <th class="text-left px-3 py-2 font-bold text-gray-700 dark:text-gray-200">IP</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse ($auditLogs as $log)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-900/30 transition-colors">
                                    <td class="px-3 py-2 whitespace-nowrap text-gray-700 dark:text-gray-300">{{ $log->created_at?->format('M d, Y h:i A') }}</td>
                                    <td class="px-3 py-2 text-gray-900 dark:text-gray-100">{{ $log->user?->name ?? 'System' }}</td>
                                    <td class="px-3 py-2 text-gray-700 dark:text-gray-300 font-semibold">{{ $log->action }}</td>
                                    <td class="px-3 py-2 text-gray-700 dark:text-gray-300">{{ class_basename($log->auditable_type) }} #{{ $log->auditable_id ?? '-' }}</td>
                                    <td class="px-3 py-2 text-gray-700 dark:text-gray-300">{{ $log->ip_address ?? '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-3 py-6 text-center text-gray-500 dark:text-gray-400">No audit logs found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $auditLogs->links() }}
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
