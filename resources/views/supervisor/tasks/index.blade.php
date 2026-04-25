<x-supervisor-layout>
    <x-slot name="header">
        Task History & Tracking
    </x-slot>

    <div class="py-6 space-y-6">
        @if (session('status'))
            <div class="rounded-lg border border-emerald-300 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800">
                {{ session('status') }}
            </div>
        @endif

        <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-100">
            <div class="bg-gray-50 px-6 py-4 border-b border-gray-200 flex items-center justify-between gap-3">
                <h3 class="font-bold text-gray-800 text-lg">All Assigned Tasks</h3>
                <a href="{{ route('supervisor.tasks.create') }}" class="inline-flex items-center justify-center px-4 py-2 rounded-lg bg-indigo-600 text-white font-semibold hover:bg-indigo-700">
                    Assign New Task
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-3 font-bold text-gray-500 uppercase tracking-wider">Student</th>
                            <th class="px-6 py-3 font-bold text-gray-500 uppercase tracking-wider">Task</th>
                            <th class="px-6 py-3 font-bold text-gray-500 uppercase tracking-wider">Due</th>
                            <th class="px-6 py-3 font-bold text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 font-bold text-gray-500 uppercase tracking-wider">Submission</th>
                            <th class="px-6 py-3 font-bold text-gray-700 uppercase tracking-wider text-right">Files</th>
                            <th class="px-6 py-3 font-bold text-gray-700 uppercase tracking-wider text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                        @forelse ($tasks as $task)
                            @php
                                $status = strtolower($task->status ?? 'pending');
                                $isOverdue = in_array($status, ['pending', 'in_progress', 'missing'], true) && $task->due_date && $task->due_date->isPast();
                                if ($isOverdue) {
                                    $status = 'missing';
                                }

                                $badge = match ($status) {
                                    'approved' => 'bg-emerald-100 text-emerald-700',
                                    'rejected' => 'bg-rose-100 text-rose-700',
                                    'completed' => 'bg-emerald-100 text-emerald-700',
                                    'submitted' => 'bg-sky-100 text-sky-700',
                                    'in_progress' => 'bg-amber-100 text-amber-700',
                                    'missing' => 'bg-amber-100 text-amber-700',
                                    default => 'bg-slate-100 text-slate-700',
                                };

                                $studentName = $task->assignment?->student?->name ?? 'N/A';
                                $companyName = $task->assignment?->company?->name ?? 'N/A';
                                $canEdit = in_array($task->status, ['pending', 'in_progress'], true) && $task->submitted_at === null;
                                $canDelete = $canEdit;
                                $canComplete = in_array($task->status, ['pending', 'in_progress'], true);

                                $taskFilePath = $task->task_attachment_path ?? null;
                                $taskFileName = $task->task_original_filename ?? null;

                                // Back-compat: older tasks stored the supervisor-provided file in attachment_path
                                if (! $taskFilePath && $task->submitted_at === null && ! in_array($task->status, ['submitted', 'approved', 'rejected'], true)) {
                                    $taskFilePath = $task->attachment_path;
                                    $taskFileName = $task->original_filename;
                                }

                                $hasSubmission = ! empty($task->attachment_path) && ($task->submitted_at !== null || in_array($task->status, ['submitted', 'approved', 'rejected'], true));
                            @endphp

                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="font-bold text-gray-900">{{ $studentName }}</div>
                                    <div class="text-xs text-gray-500">{{ $companyName }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="font-semibold text-gray-900">{{ $task->title }}</div>
                                    <div class="text-xs text-gray-500">Semester: {{ $task->semester ?? 'N/A' }}</div>
                                </td>
                                <td class="px-6 py-4 text-gray-700">
                                    {{ $task->due_date ? $task->due_date->format('M d, Y') : 'No due date' }}
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold {{ $badge }}">
                                        {{ $status === 'missing' ? 'Missing / Overdue' : ucfirst(str_replace('_', ' ', $status)) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-gray-700">
                                    @if ($task->status === 'submitted')
                                        <div class="text-xs text-gray-500">Submitted</div>
                                        <div class="text-sm font-semibold text-gray-900">
                                            {{ $task->submitted_at ? $task->submitted_at->format('M d, Y g:i A') : '—' }}
                                        </div>
                                    @elseif (in_array($task->status, ['approved', 'rejected'], true))
                                        <div class="text-xs text-gray-500">Reviewed</div>
                                        <div class="text-sm font-semibold text-gray-900">{{ $task->updated_at?->format('M d, Y g:i A') ?? '—' }}</div>
                                    @else
                                        <div class="text-sm text-gray-500">Not submitted</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right text-xs">
                                    <div class="flex flex-wrap items-center justify-end gap-2">
                                        @if ($taskFilePath)
                                            <a href="{{ Storage::url($taskFilePath) }}" target="_blank" class="inline-flex items-center rounded-lg bg-indigo-700 px-3 py-2 text-xs font-bold text-white shadow-sm transition hover:bg-indigo-800">
                                                Task File
                                            </a>
                                        @endif

                                        @if ($hasSubmission)
                                            <a href="{{ route('supervisor.tasks.submission.view', $task) }}" target="_blank" class="inline-flex items-center rounded-lg bg-sky-700 px-3 py-2 text-xs font-bold text-white shadow-sm transition hover:bg-sky-800">Submission</a>
                                            <a href="{{ route('supervisor.tasks.submission.download', $task) }}" class="inline-flex items-center rounded-lg bg-cyan-700 px-3 py-2 text-xs font-bold text-white shadow-sm transition hover:bg-cyan-800">Download</a>
                                        @endif

                                        @if ($task->status === 'rejected' && $task->supervisor_attachment_path)
                                            <a href="{{ Storage::url($task->supervisor_attachment_path) }}" target="_blank" class="inline-flex items-center rounded-lg bg-rose-700 px-3 py-2 text-xs font-bold text-white shadow-sm transition hover:bg-rose-800">Feedback</a>
                                        @endif
                                    </div>
                                </td>

                                <td class="px-6 py-4 text-right text-xs">
                                    <div class="flex flex-wrap items-center justify-end gap-2">
                                        @if ($canEdit)
                                            <a href="{{ route('supervisor.tasks.edit', $task) }}" class="inline-flex items-center rounded-lg bg-slate-800 px-3 py-2 text-xs font-bold text-white shadow-sm transition hover:bg-slate-900">Edit</a>
                                        @endif

                                        @if ($canComplete)
                                            <form method="POST" action="{{ route('supervisor.tasks.complete', $task) }}" class="inline">
                                                @csrf
                                                <button type="submit" class="inline-flex items-center rounded-lg bg-emerald-700 px-3 py-2 text-xs font-bold text-white shadow-sm transition hover:bg-emerald-800">Mark Done</button>
                                            </form>
                                        @endif

                                        @if ($canDelete)
                                            <form method="POST" action="{{ route('supervisor.tasks.destroy', $task) }}" class="inline" onsubmit="return confirm('Delete this task? This will hide it from the student.');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="inline-flex items-center rounded-lg bg-rose-700 px-3 py-2 text-xs font-bold text-white shadow-sm transition hover:bg-rose-800">Delete</button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-10 text-center text-gray-500 font-medium">
                                    No tasks assigned yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-supervisor-layout>
