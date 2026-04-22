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

            <div class="p-4 overflow-x-auto">
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
                                <td class="px-3 py-2 text-gray-700 dark:text-gray-300">{{ class_basename($log->auditable_type) }} #{{ $log->auditable_id ?? '—' }}</td>
                                <td class="px-3 py-2 text-gray-700 dark:text-gray-300">{{ $log->ip_address ?? '—' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-3 py-6 text-center text-gray-500 dark:text-gray-400">No audit logs found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="mt-4">
                    {{ $auditLogs->links() }}
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
