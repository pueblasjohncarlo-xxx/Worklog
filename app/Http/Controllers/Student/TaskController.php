<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\Task;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class TaskController extends Controller
{
    public function index(Request $request): View
    {
        $user = Auth::user();
        $sortOrder = $request->query('sort', 'desc');

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

        // Separate by semester
        $sem1_tasks = $allTasks->where('semester', '1st');
        $sem2_tasks = $allTasks->where('semester', '2nd');

        return view('student.tasks.index', [
            'sem1_tasks' => $sem1_tasks->values()->toArray(),
            'sem2_tasks' => $sem2_tasks->values()->toArray(),
            'sortOrder' => $sortOrder,
            'assignment' => $assignment,
            'totalTasks' => $allTasks->count(),
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

        $task->update([
            'status' => 'submitted',
            'submitted_at' => now(),
            'attachment_path' => $path,
            'original_filename' => $originalName,
        ]);

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
            // 'attachment_path' => null, // Uncomment to remove file reference
            // 'original_filename' => null,
        ]);

        return redirect()->back()->with('status', 'Task unsubmitted successfully.');
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
