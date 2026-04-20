<?php

namespace App\Http\Controllers\Supervisor;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\Task;
use App\Notifications\NewTaskAssignedNotification;
use App\Notifications\TaskReviewedNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class SupervisorTaskController extends Controller
{
    public function index(): View
    {
        $supervisorId = Auth::id();

        $assignmentIds = Assignment::query()
            ->where('supervisor_id', $supervisorId)
            ->active()
            ->whereHas('student', fn ($q) => $q->eligibleStudentForRoster())
            ->pluck('id');

        $tasks = Task::with(['assignment.student', 'assignment.company'])
            ->whereIn('assignment_id', $assignmentIds)
            ->orderByDesc('created_at')
            ->get();

        return view('supervisor.tasks.index', compact('tasks'));
    }

    public function create(): View
    {
        $supervisorId = Auth::id();
        $assignments = Assignment::query()
            ->where('supervisor_id', $supervisorId)
            ->active()
            ->whereHas('student', fn ($q) => $q->eligibleStudentForRoster())
            ->with('student')
            ->orderByDesc('updated_at')
            ->get()
            ->unique('student_id')
            ->values()
            ->sortBy(fn ($assignment) => $assignment->student->name);

        return view('supervisor.tasks.create', compact('assignments'));
    }

    public function edit(Task $task): View
    {
        $this->authorizeTask($task);
        $this->ensureTaskCanBeEdited($task);

        return view('supervisor.tasks.edit', compact('task'));
    }

    public function update(Request $request, Task $task): RedirectResponse
    {
        $this->authorizeTask($task);
        $this->ensureTaskCanBeEdited($task);

        $validated = $request->validate([
            'semester' => ['required', 'in:1st,2nd'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'due_date' => ['nullable', 'date'],
            'attachment' => ['nullable', 'file', 'mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,jpg,jpeg,png,txt,zip', 'max:10240'],
        ]);

        $update = [
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'due_date' => $validated['due_date'] ?? null,
            'semester' => $validated['semester'],
        ];

        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $originalFilename = $file->getClientOriginalName();
            $path = $file->store('task_attachments', 'public');

            if (Schema::hasColumn('tasks', 'task_attachment_path') && Schema::hasColumn('tasks', 'task_original_filename')) {
                $update['task_attachment_path'] = $path;
                $update['task_original_filename'] = $originalFilename;
            } else {
                // Back-compat: fall back to legacy columns (may be overwritten when student submits)
                $update['attachment_path'] = $path;
                $update['original_filename'] = $originalFilename;
            }
        }

        // Back-compat: if this task was created before task_attachment_* existed and has a legacy
        // task file in attachment_path while not submitted, copy pointers into new columns.
        if (
            Schema::hasColumn('tasks', 'task_attachment_path') &&
            Schema::hasColumn('tasks', 'task_original_filename') &&
            empty($task->task_attachment_path) &&
            ! empty($task->attachment_path) &&
            empty($task->submitted_at) &&
            ! in_array($task->status, ['submitted', 'approved', 'rejected'], true)
        ) {
            $update['task_attachment_path'] = $task->attachment_path;
            $update['task_original_filename'] = $task->original_filename;
        }

        $task->update($update);

        return redirect()->route('supervisor.tasks.index')->with('status', 'Task updated successfully.');
    }

    public function destroy(Task $task): RedirectResponse
    {
        $this->authorizeTask($task);
        $this->ensureTaskCanBeDeleted($task);

        if (! Schema::hasColumn('tasks', 'deleted_at')) {
            return back()->withErrors(['task' => 'Task deletion is not available until the latest migrations are applied.']);
        }

        $task->delete();

        return redirect()->route('supervisor.tasks.index')->with('status', 'Task deleted successfully.');
    }

    public function complete(Task $task): RedirectResponse
    {
        $this->authorizeTask($task);
        $this->ensureTaskCanBeManuallyCompleted($task);

        $task->update([
            'status' => 'completed',
        ]);

        return redirect()->route('supervisor.tasks.index')->with('status', 'Task marked as completed.');
    }

    public function viewSubmission(Task $task)
    {
        $this->authorizeTask($task);
        $this->ensureTaskHasSubmission($task);

        $path = $task->attachment_path;

        if (! Storage::disk('public')->exists($path)) {
            return response()->view('supervisor.tasks.submission-missing', ['task' => $task, 'reason' => 'missing-on-disk'], 404);
        }

        $fullPath = Storage::disk('public')->path($path);
        $name = $task->original_filename ?: basename($path);

        return response()->file($fullPath, [
            'Content-Disposition' => 'inline; filename="'.$name.'"',
        ]);
    }

    public function downloadSubmission(Task $task)
    {
        $this->authorizeTask($task);
        $this->ensureTaskHasSubmission($task);

        $path = $task->attachment_path;

        if (! Storage::disk('public')->exists($path)) {
            return response()->view('supervisor.tasks.submission-missing', ['task' => $task, 'reason' => 'missing-on-disk'], 404);
        }

        $name = $task->original_filename ?: basename($path);

        return Storage::disk('public')->download($path, $name);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'assignment_id' => 'required|exists:assignments,id',
            'semester' => 'required|in:1st,2nd',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date',
            'attachment' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,jpg,jpeg,png,txt,zip|max:10240',
        ]);

        // Verify assignment belongs to supervisor
        $assignment = Assignment::findOrFail($validated['assignment_id']);
        if ($assignment->supervisor_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $attachmentPath = null;
        $originalFilename = null;

        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $originalFilename = $file->getClientOriginalName();
            $attachmentPath = $file->store('task_attachments', 'public');
        }

        $payload = [
            'assignment_id' => $validated['assignment_id'],
            'title' => $validated['title'],
            'description' => $validated['description'],
            'due_date' => $validated['due_date'],
            'semester' => $validated['semester'],
            'status' => 'pending',
        ];

        // Store supervisor-provided task file in dedicated columns when available.
        if ($attachmentPath) {
            if (Schema::hasColumn('tasks', 'task_attachment_path') && Schema::hasColumn('tasks', 'task_original_filename')) {
                $payload['task_attachment_path'] = $attachmentPath;
                $payload['task_original_filename'] = $originalFilename;
            } else {
                // Back-compat: legacy columns (may be overwritten by student submission)
                $payload['attachment_path'] = $attachmentPath;
                $payload['original_filename'] = $originalFilename;
            }
        }

        $task = Task::create($payload);

        // Notify the student through the assignment relationship
        if ($assignment->student) {
            $assignment->student->notify(new NewTaskAssignedNotification($task));
        }

        return redirect()->route('supervisor.dashboard')
            ->with('status', "✓ Task '{$task->title}' assigned successfully to {$assignment->student->name} for {$validated['semester']} semester.");
    }

    public function approve(Request $request, Task $task): RedirectResponse
    {
        $this->authorizeTask($task);

        $validated = $request->validate([
            'grade' => 'required|string|max:10', // Assuming a short grade string
        ]);

        $task->update([
            'status' => 'approved',
            'grade' => $validated['grade'],
        ]);

        $task->loadMissing(['assignment.student']);
        if ($task->assignment?->student) {
            $task->assignment->student->notify(new TaskReviewedNotification($task));
        }

        return redirect()->back()->with('status', 'Task approved with grade.');
    }

    public function reject(Request $request, Task $task): RedirectResponse
    {
        $this->authorizeTask($task);

        $request->validate([
            'note' => 'nullable|string|max:1000',
            'attachment' => 'nullable|file|max:10240', // Max 10MB
        ]);

        $path = null;
        $originalName = null;

        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $originalName = $file->getClientOriginalName();
            $path = $file->store('supervisor_feedback', 'public');
        }

        $task->update([
            'status' => 'rejected',
            'supervisor_note' => $request->note,
            'supervisor_attachment_path' => $path,
            'supervisor_original_filename' => $originalName,
        ]);

        $task->loadMissing(['assignment.student']);
        if ($task->assignment?->student) {
            $task->assignment->student->notify(new TaskReviewedNotification($task));
        }

        return redirect()->back()->with('status', 'Task rejected with feedback.');
    }

    public function unapprove(Task $task): RedirectResponse
    {
        $this->authorizeTask($task);

        $task->update([
            'status' => 'submitted', // Revert to submitted so it can be graded/reviewed again
            'grade' => null, // Optional: clear grade or keep it as previous draft? Clearing it makes sense if unapproving.
        ]);

        return redirect()->back()->with('status', 'Approval cancelled. Task reverted to submitted status.');
    }

    private function authorizeTask(Task $task): void
    {
        $assignment = Assignment::findOrFail($task->assignment_id);
        if ($assignment->supervisor_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }
    }

    private function ensureTaskCanBeEdited(Task $task): void
    {
        if (! in_array($task->status, ['pending', 'in_progress'], true) || $task->submitted_at !== null) {
            abort(403, 'This task can no longer be edited.');
        }
    }

    private function ensureTaskCanBeDeleted(Task $task): void
    {
        if (! in_array($task->status, ['pending', 'in_progress'], true) || $task->submitted_at !== null) {
            abort(403, 'This task can no longer be deleted.');
        }
    }

    private function ensureTaskCanBeManuallyCompleted(Task $task): void
    {
        if ($task->submitted_at !== null) {
            abort(403, 'This task has already been submitted by the student.');
        }

        if (in_array($task->status, ['completed', 'approved'], true)) {
            abort(403, 'This task is already completed.');
        }

        if (! in_array($task->status, ['pending', 'in_progress'], true)) {
            abort(403, 'Only pending or in-progress tasks can be manually completed.');
        }
    }

    private function ensureTaskHasSubmission(Task $task): void
    {
        $hasSubmission = ! empty($task->attachment_path)
            && ($task->submitted_at !== null || in_array($task->status, ['submitted', 'approved', 'rejected'], true));

        if (! $hasSubmission) {
            abort(404);
        }
    }
}
