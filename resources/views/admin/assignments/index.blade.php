<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-bold text-white">Assigned Overview</h2>
                <p class="text-gray-400 text-xs">Current deployment and assignment summary</p>
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
                    <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100">Assignments</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ $assignments->total() }} total assignments</p>
                </div>
            </div>

            <div class="p-4 overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50">
                            <th class="text-left px-3 py-2 font-bold text-gray-700 dark:text-gray-200">Student</th>
                            <th class="text-left px-3 py-2 font-bold text-gray-700 dark:text-gray-200">Company</th>
                            <th class="text-left px-3 py-2 font-bold text-gray-700 dark:text-gray-200">Supervisor</th>
                            <th class="text-left px-3 py-2 font-bold text-gray-700 dark:text-gray-200">OJT Adviser</th>
                            <th class="text-left px-3 py-2 font-bold text-gray-700 dark:text-gray-200">Progress</th>
                            <th class="text-left px-3 py-2 font-bold text-gray-700 dark:text-gray-200">Hours</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse ($assignments as $assignment)
                            @php
                                $completedHours = $assignment->totalApprovedHours();
                                $progress = $assignment->progressPercentage();
                            @endphp
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-900/30 transition-colors">
                                <td class="px-3 py-2 font-semibold text-gray-900 dark:text-gray-100">{{ $assignment->student?->name ?? 'N/A' }}</td>
                                <td class="px-3 py-2 text-gray-700 dark:text-gray-300">{{ $assignment->company?->name ?? '—' }}</td>
                                <td class="px-3 py-2 text-gray-700 dark:text-gray-300">{{ $assignment->supervisor?->name ?? '—' }}</td>
                                <td class="px-3 py-2 text-gray-700 dark:text-gray-300">{{ $assignment->ojtAdviser?->name ?? '—' }}</td>
                                <td class="px-3 py-2">
                                    <div class="flex items-center gap-2">
                                        <div class="w-32 bg-gray-200 dark:bg-gray-700 rounded-full h-2 overflow-hidden">
                                            <div class="h-2 rounded-full bg-indigo-600" style="width: {{ $progress }}%"></div>
                                        </div>
                                        <span class="text-xs font-bold text-gray-700 dark:text-gray-200">{{ $progress }}%</span>
                                    </div>
                                </td>
                                <td class="px-3 py-2 text-gray-700 dark:text-gray-300">
                                    <span class="font-bold text-gray-900 dark:text-gray-100">{{ (int) $completedHours }}</span> / {{ $assignment->required_hours }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-3 py-6 text-center text-gray-500 dark:text-gray-400">No assignments found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="mt-4">
                    {{ $assignments->links() }}
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
