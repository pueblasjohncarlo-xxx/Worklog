<x-student-layout>
    <x-slot name="header">
        <h2 class="text-white">Task Details</h2>
    </x-slot>

    <div class="px-4 md:px-6 lg:px-8 py-6">
        <div class="max-w-5xl mx-auto space-y-6">
            @if (session('status'))
                <div class="rounded-lg border border-emerald-300 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800">
                    {{ session('status') }}
                </div>
            @endif

            @if (session('error'))
                <div class="rounded-lg border border-rose-300 bg-rose-50 px-4 py-3 text-sm font-medium text-rose-800">
                    {{ session('error') }}
                </div>
            @endif

            @php
                $status = strtolower($task->status ?? 'pending');
                if ($status === 'pending' && $task->due_date && $task->due_date->isPast()) {
                    $status = 'missing';
                }

                $statusLabel = $status === 'missing' ? 'Missing / Overdue' : ucfirst($status);
                $isSubmittable = in_array($status, ['pending', 'in_progress', 'missing', 'rejected'], true);
            @endphp

            <div class="rounded-xl bg-white border border-gray-200 shadow-sm p-5 md:p-6">
                <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4">
                    <div>
                        <h3 class="text-2xl font-black text-gray-900">{{ $task->title }}</h3>
                        <p class="text-sm text-gray-600 mt-1">Assigned by {{ $assignment->supervisor->name ?? 'Supervisor' }}</p>
                        <p class="text-xs text-gray-500 mt-1">Semester: {{ $task->semester ?? '1st' }}</p>
                    </div>
                    <x-status-badge :status="$status" :label="$statusLabel" />
                </div>

                <div class="mt-5 grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="rounded-lg border border-gray-200 bg-gray-50 p-3">
                        <div class="text-xs uppercase font-bold text-gray-500">Company</div>
                        <div class="text-sm font-semibold text-gray-900 mt-1">{{ $assignment->company->name ?? 'N/A' }}</div>
                    </div>
                    <div class="rounded-lg border border-gray-200 bg-gray-50 p-3">
                        <div class="text-xs uppercase font-bold text-gray-500">Due Date</div>
                        <div class="text-sm font-semibold text-gray-900 mt-1">
                            {{ $task->due_date ? $task->due_date->format('M d, Y') : 'No due date' }}
                        </div>
                    </div>
                    <div class="rounded-lg border border-gray-200 bg-gray-50 p-3">
                        <div class="text-xs uppercase font-bold text-gray-500">Grade</div>
                        <div class="text-sm font-semibold text-gray-900 mt-1">{{ $task->grade ?? 'Not graded yet' }}</div>
                    </div>
                </div>

                @if (!empty($task->description))
                    <div class="mt-5">
                        <h4 class="text-sm font-bold text-gray-900 mb-2">Task Description</h4>
                        <p class="text-sm text-gray-700 leading-relaxed whitespace-pre-line">{{ $task->description }}</p>
                    </div>
                @endif

                @php
                    $taskFilePath = $task->task_attachment_path ?? null;
                    $taskFileName = $task->task_original_filename ?? null;

                    // Back-compat: older tasks stored supervisor file in attachment_path before submissions existed.
                    if (! $taskFilePath && $task->submitted_at === null && ! in_array($task->status, ['submitted', 'approved', 'rejected'], true)) {
                        $taskFilePath = $task->attachment_path;
                        $taskFileName = $task->original_filename;
                    }

                    $submissionPath = $task->attachment_path;
                    $submissionName = $task->original_filename;
                    $hasSubmission = ! empty($submissionPath) && ($task->submitted_at !== null || in_array($task->status, ['submitted', 'approved', 'rejected'], true));
                @endphp

                @if ($taskFilePath)
                    <div class="mt-5 rounded-lg border border-indigo-200 bg-indigo-50 p-3">
                        <div class="text-xs uppercase font-bold text-indigo-700">Supervisor Attachment</div>
                        <a href="{{ Storage::url($taskFilePath) }}" target="_blank" download class="mt-1 inline-flex text-sm font-semibold text-indigo-700 hover:text-indigo-900">
                            {{ $taskFileName ?? 'Download attachment' }}
                        </a>
                    </div>
                @endif

                @if ($hasSubmission)
                    <div class="mt-5 rounded-lg border border-sky-200 bg-sky-50 p-3">
                        <div class="text-xs uppercase font-bold text-sky-700">Your Submission</div>
                        <a href="{{ Storage::url($submissionPath) }}" target="_blank" download class="mt-1 inline-flex text-sm font-semibold text-sky-700 hover:text-sky-900">
                            {{ $submissionName ?? 'Download submission' }}
                        </a>
                    </div>
                @endif

                @if ($task->status === 'rejected' && ($task->supervisor_note || $task->supervisor_attachment_path))
                    <div class="mt-5 rounded-lg border border-rose-200 bg-rose-50 p-3">
                        <div class="text-xs uppercase font-bold text-rose-700">Supervisor Feedback</div>
                        @if ($task->supervisor_note)
                            <p class="mt-1 text-sm text-rose-900 whitespace-pre-line">{{ $task->supervisor_note }}</p>
                        @endif
                        @if ($task->supervisor_attachment_path)
                            <a href="{{ Storage::url($task->supervisor_attachment_path) }}" target="_blank" class="mt-2 inline-flex text-sm font-semibold text-rose-700 hover:text-rose-900">
                                {{ $task->supervisor_original_filename ?? 'View feedback file' }}
                            </a>
                        @endif
                    </div>
                @endif

                <div class="mt-6 pt-4 border-t border-gray-200 flex flex-col sm:flex-row gap-3 sm:items-center sm:justify-between">
                    <a href="{{ route('student.tasks.index') }}" class="wl-button-neutral">
                        Back to My Tasks
                    </a>

                    <div class="flex flex-wrap items-center gap-2">
                        @if ($isSubmittable)
                            <form action="{{ route('student.tasks.submit', $task) }}" method="POST" enctype="multipart/form-data" class="flex flex-wrap items-center gap-2">
                                @csrf
                                <input type="file" name="attachment" required class="text-xs text-gray-700">
                                <button type="submit" class="inline-flex items-center justify-center rounded-lg bg-indigo-700 px-4 py-2 font-bold text-white hover:bg-indigo-800">
                                    Submit Task
                                </button>
                            </form>
                        @endif

                        @if ($status === 'submitted')
                            <form action="{{ route('student.tasks.unsubmit', $task) }}" method="POST">
                                @csrf
                                <button type="submit" class="inline-flex items-center justify-center rounded-lg bg-slate-700 px-4 py-2 font-bold text-white hover:bg-slate-900">
                                    Unsubmit
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-student-layout>
