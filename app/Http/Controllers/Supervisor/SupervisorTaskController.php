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
use Illuminate\View\View;

class SupervisorTaskController extends Controller
{
    public function index(): View
    {
        $supervisorId = Auth::id();

        $assignmentIds = Assignment::where('supervisor_id', $supervisorId)
            ->where('status', 'active')
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
        $assignments = Assignment::with('student')
            ->where('supervisor_id', $supervisorId)
            ->where('status', 'active')
            ->get()
            ->sortBy(function ($assignment) {
                return $assignment->student->name;
            });

        return view('supervisor.tasks.create', compact('assignments'));
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

        $task = Task::create([
            'assignment_id' => $validated['assignment_id'],
            'title' => $validated['title'],
            'description' => $validated['description'],
            'due_date' => $validated['due_date'],
            'attachment_path' => $attachmentPath,
            'original_filename' => $originalFilename,
            'semester' => $validated['semester'],
            'status' => 'pending',
        ]);

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
}
