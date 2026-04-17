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
                            <th class="px-6 py-3 font-bold text-gray-500 uppercase tracking-wider text-right">Files</th>
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
                                    'submitted' => 'bg-sky-100 text-sky-700',
                                    'in_progress' => 'bg-amber-100 text-amber-700',
                                    'missing' => 'bg-amber-100 text-amber-700',
                                    default => 'bg-slate-100 text-slate-700',
                                };

                                $studentName = $task->assignment?->student?->name ?? 'N/A';
                                $companyName = $task->assignment?->company?->name ?? 'N/A';
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
                                    <div class="space-x-3">
                                        @if ($task->attachment_path)
                                            @php
                                                $label = in_array($task->status, ['submitted', 'approved', 'rejected'], true) ? 'Submission' : 'Task File';
                                                $color = in_array($task->status, ['submitted', 'approved', 'rejected'], true) ? 'text-sky-700 hover:text-sky-900' : 'text-indigo-700 hover:text-indigo-900';
                                            @endphp
                                            <a href="{{ Storage::url($task->attachment_path) }}" target="_blank" class="font-bold {{ $color }}">{{ $label }}</a>
                                        @endif

                                        @if ($task->status === 'rejected' && $task->supervisor_attachment_path)
                                            <a href="{{ Storage::url($task->supervisor_attachment_path) }}" target="_blank" class="font-bold text-rose-700 hover:text-rose-900">Feedback</a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-10 text-center text-gray-500 font-medium">
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
