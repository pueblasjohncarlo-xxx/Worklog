<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\Task;
use App\Notifications\TaskSubmittedNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class TaskController extends Controller
{
    public function index(Request $request): View
    {
        $user = Auth::user();
        $sortOrder = $request->query('sort', 'desc');
        $filter = (string) $request->query('filter', 'all');
        if (! in_array($filter, ['all', 'done', 'pending', 'rejected'], true)) {
            $filter = 'all';
        }

        // DEBUG: Log execution
        \Log::info('========== STUDENT TASKS CALLED ==========');
        \Log::info('Route: student.tasks.index | Controller: Student\TaskController@index | User: ' . $user->id . ' (' . $user->name . ')');

        // Get student's assignment with relationships eager loaded
        $assignment = Assignment::with(['student', 'supervisor', 'company'])
            ->where('student_id', $user->id)
            ->first();

        if (!$assignment) {
            return view('student.tasks.index', [
                'sem1_tasks' => collect(),
                'sem2_tasks' => collect(),
                'sortOrder' => $sortOrder,
                'assignment' => null,
            ]);
        }

        // Get ALL tasks for this assignment
        $allTasks = Task::where('assignment_id', $assignment->id)
            ->orderBy('created_at', $sortOrder)
            ->get()
            ->map(function ($task) {
                // Ensure every task has a semester (fallback to 1st if NULL)
                if (empty($task->semester)) {
                    $task->semester = '1st';
                }
                return $task;
            });

        $totalTasksCount = $allTasks->count();
        $completedTasksCount = $allTasks->whereIn('status', ['approved', 'completed'])->count();
        $pendingTasksCount = $allTasks->whereIn('status', ['pending', 'in_progress', 'submitted'])->count();
        $rejectedTasksCount = $allTasks->where('status', 'rejected')->count();

        $filteredTasks = match ($filter) {
            'done' => $allTasks->whereIn('status', ['approved', 'completed']),
            'pending' => $allTasks->whereIn('status', ['pending', 'in_progress', 'submitted']),
            'rejected' => $allTasks->where('status', 'rejected'),
            default => $allTasks,
        };

        // Separate by semester
        $sem1_tasks = $filteredTasks->where('semester', '1st');
        $sem2_tasks = $filteredTasks->where('semester', '2nd');

        return view('student.tasks.index', [
            'sem1_tasks' => $sem1_tasks->values()->toArray(),
            'sem2_tasks' => $sem2_tasks->values()->toArray(),
            'sortOrder' => $sortOrder,
            'assignment' => $assignment,
            'totalTasks' => $allTasks->count(),
            'activeTaskFilter' => $filter,
            'totalTasksCount' => $totalTasksCount,
            'completedTasksCount' => $completedTasksCount,
            'pendingTasksCount' => $pendingTasksCount,
            'rejectedTasksCount' => $rejectedTasksCount,
        ]);
    }

    public function show(int $taskId): View|RedirectResponse
    {
        $user = Auth::user();

        $assignment = Assignment::with(['supervisor', 'company'])
            ->where('student_id', $user->id)
            ->first();

        if (! $assignment) {
            return redirect()
                ->route('student.tasks.index')
                ->with('error', 'No active assignment found for your account.');
        }

        $task = Task::with(['assignment.supervisor', 'assignment.company'])
            ->where('id', $taskId)
            ->where('assignment_id', $assignment->id)
            ->first();

        if (! $task) {
            return redirect()
                ->route('student.tasks.index')
                ->with('error', 'Task not found or not assigned to your account.');
        }

        if (empty($task->semester)) {
            $task->semester = '1st';
        }

        return view('student.tasks.show', [
            'task' => $task,
            'assignment' => $assignment,
        ]);
    }

    public function submit(Request $request, Task $task): RedirectResponse
    {
        $this->authorizeTask($task);

        $request->validate([
            'attachment' => 'required|file|max:10240', // Max 10MB
        ]);

        $path = null;
        $originalName = null;

        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $originalName = $file->getClientOriginalName();
            // Store file
            $path = $file->store('task_submissions', 'public');
        }

        $update = [
            'status' => 'submitted',
            'submitted_at' => now(),
            'attachment_path' => $path,
            'original_filename' => $originalName,
        ];

        // Back-compat: tasks created before task_attachment_* existed may have the supervisor-provided
        // file stored in attachment_path. Preserve it before overwriting with the student's submission.
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

        $assignment = Assignment::with('supervisor')
            ->where('id', $task->assignment_id)
            ->first();

        if ($assignment?->supervisor) {
            $assignment->supervisor->notify(new TaskSubmittedNotification($task));
        }

        return redirect()->back()->with('status', 'Task submitted successfully.');
    }

    public function unsubmit(Task $task): RedirectResponse
    {
        $this->authorizeTask($task);

        // Optional: Delete file on unsubmit? Or keep it?
        // Usually, keeping it is safer, or we can delete it.
        // Let's keep it for history or overwrite on next submit.
        // If we want to simulate Google Classroom, unsubmit usually keeps the file but allows editing.
        // But for simplicity here, we just change status.

        $task->update([
            'status' => 'pending', // Revert to pending
            'unsubmitted_at' => now(),
            // Note: Attachment path is handled separately if needed
            // 'original_filename' => null,
        ]);

        return redirect()->back()->with('status', 'Task unsubmitted successfully.');
    }

    public function updateStatus(Request $request, Task $task): RedirectResponse
    {
        $this->authorizeTask($task);

        $request->validate([
            'status' => 'required|in:pending,in_progress,completed',
        ]);

        $task->update([
            'status' => $request->status,
        ]);

        return redirect()->back()->with('status', 'Task status updated successfully.');
    }

    private function authorizeTask(Task $task): void
    {
        $user = Auth::user();
        $assignment = Assignment::where('student_id', $user->id)
            ->where('id', $task->assignment_id)
            ->first();

        if (! $assignment) {
            abort(403, 'Unauthorized action.');
        }
    }
}
