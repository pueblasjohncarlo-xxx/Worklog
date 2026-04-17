<x-supervisor-layout>
    <x-slot name="header">
        Student Profile
    </x-slot>

    <div class="max-w-5xl mx-auto py-6 space-y-6">
        <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-100">
            <div class="bg-indigo-600 px-6 py-4">
                <h3 class="font-bold text-white text-lg">
                    {{ $assignment->student->name ?? 'Student' }}
                </h3>
                <p class="text-indigo-100 text-sm">
                    {{ $assignment->company->name ?? 'N/A' }}
                </p>
            </div>

            <div class="p-6 space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="rounded-lg border border-gray-200 bg-gray-50 p-4">
                        <div class="text-xs uppercase font-bold text-gray-500">Email</div>
                        <div class="text-sm font-semibold text-gray-900 mt-1">{{ $assignment->student->email ?? 'N/A' }}</div>
                    </div>
                    <div class="rounded-lg border border-gray-200 bg-gray-50 p-4">
                        <div class="text-xs uppercase font-bold text-gray-500">Section / Dept</div>
                        <div class="text-sm font-semibold text-gray-900 mt-1">
                            {{ $assignment->student->section ?? $assignment->student->department ?? 'N/A' }}
                        </div>
                    </div>
                    <div class="rounded-lg border border-gray-200 bg-gray-50 p-4">
                        <div class="text-xs uppercase font-bold text-gray-500">Required Hours</div>
                        <div class="text-sm font-semibold text-gray-900 mt-1">{{ $requiredHours > 0 ? number_format($requiredHours, 0) : 'N/A' }}</div>
                    </div>
                </div>

                <div class="rounded-xl border border-gray-200 bg-white p-5">
                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <div class="text-sm font-bold text-gray-900">Approved Hours Progress</div>
                            <div class="text-xs text-gray-500 mt-1">{{ number_format($approvedHours, 2) }} / {{ $requiredHours > 0 ? number_format($requiredHours, 0) : 'N/A' }} hrs</div>
                        </div>
                        <div class="text-right">
                            <div class="text-2xl font-black text-gray-900">{{ number_format($percentage, 0) }}%</div>
                        </div>
                    </div>
                    <div class="mt-3">
                        <x-progress-bar :value="$percentage" />
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="rounded-xl border border-gray-200 bg-white overflow-hidden">
                        <div class="bg-gray-50 px-5 py-3 border-b border-gray-200">
                            <h4 class="font-bold text-gray-800">Recent Logs</h4>
                        </div>
                        <div class="p-5">
                            @if (($recentLogs->count() ?? 0) === 0)
                                <div class="text-sm text-gray-500">No logs yet.</div>
                            @else
                                <div class="space-y-3">
                                    @foreach ($recentLogs as $log)
                                        <div class="flex items-start justify-between gap-3">
                                            <div>
                                                <div class="text-sm font-semibold text-gray-900">
                                                    {{ $log->work_date ? $log->work_date->format('M d, Y') : 'N/A' }}
                                                    <span class="text-xs text-gray-500">({{ ucfirst($log->type ?? 'log') }})</span>
                                                </div>
                                                <div class="text-xs text-gray-500">Status: {{ ucfirst($log->status ?? 'draft') }}</div>
                                            </div>
                                            <div class="text-sm font-bold text-gray-700">{{ number_format((float) ($log->hours ?? 0), 2) }}h</div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="rounded-xl border border-gray-200 bg-white overflow-hidden">
                        <div class="bg-gray-50 px-5 py-3 border-b border-gray-200">
                            <h4 class="font-bold text-gray-800">Recent Tasks</h4>
                        </div>
                        <div class="p-5">
                            @if (($activeTasks->count() ?? 0) === 0)
                                <div class="text-sm text-gray-500">No tasks found.</div>
                            @else
                                <div class="space-y-3">
                                    @foreach ($activeTasks as $task)
                                        <div class="flex items-start justify-between gap-3">
                                            <div>
                                                <div class="text-sm font-semibold text-gray-900">{{ $task->title }}</div>
                                                <div class="text-xs text-gray-500">
                                                    Status: {{ ucfirst($task->status ?? 'pending') }}
                                                    @if ($task->due_date)
                                                        · Due: {{ $task->due_date->format('M d, Y') }}
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="text-right">
                                                @if ($task->attachment_path)
                                                    <a href="{{ Storage::url($task->attachment_path) }}" target="_blank" class="text-xs font-bold text-indigo-700 hover:text-indigo-900">Attachment</a>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="flex justify-end">
                    <a href="{{ route('supervisor.team.index') }}" class="inline-flex items-center justify-center px-4 py-2 rounded-lg border border-gray-300 text-gray-700 font-semibold hover:bg-gray-50">
                        Back to Team Overview
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-supervisor-layout>
