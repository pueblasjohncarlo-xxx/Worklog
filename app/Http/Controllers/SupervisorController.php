<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\Task;
use App\Models\WorkLog;
use App\Notifications\WorkLogReviewedNotification;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class SupervisorController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();

        // Assignments under this supervisor
        $assignments = Assignment::query()
            ->where('supervisor_id', $user->id)
            ->active()
            ->whereHas('student', fn ($q) => $q->eligibleStudentForRoster())
            ->with(['student', 'company'])
            ->orderByDesc('updated_at')
            ->get()
            ->unique('student_id')
            ->values();

        $assignmentIds = $assignments->pluck('id');

        // 1. Pending Attendance Approvals (clocked-in, not yet clocked-out)
        $pendingAttendanceLogs = WorkLog::with(['assignment.student'])
            ->whereIn('assignment_id', $assignmentIds)
            ->where('status', 'submitted')
            ->where(function ($q) {
                $q->where('submitted_to', 'supervisor')
                    ->orWhereNull('submitted_to');
            })
            ->whereNotNull('time_in')
            ->whereNull('time_out')
            ->orderBy('work_date')
            ->get();

        // 2. Pending Hours Approvals
        // - Completed attendance entries (clocked out)
        // - and pure journal entries (no time_in/time_out)
        $pendingHoursLogs = WorkLog::with(['assignment.student'])
            ->whereIn('assignment_id', $assignmentIds)
            ->where('status', 'submitted')
            ->where(function ($q) {
                $q->where('submitted_to', 'supervisor')
                    ->orWhere(function ($q2) {
                        // Back-compat: attendance worklogs created before submitted_to existed
                        // (or before clock-out started setting it) should still reach supervisors.
                        $q2->whereNull('submitted_to')->whereNotNull('time_in');
                    });
            })
            ->where(function ($query) {
                $query->where(function ($q) {
                    $q->whereNotNull('time_in')->whereNotNull('time_out');
                })->orWhere(function ($q) {
                    $q->whereNull('time_in')->whereNull('time_out');
                });
            })
            ->orderBy('work_date')
            ->get();

        // 3. Task Reviews (Tasks marked as submitted)
        $pendingTaskReviews = Task::with(['assignment.student'])
            ->whereIn('assignment_id', $assignmentIds)
            ->where('status', 'submitted')
            ->orderByDesc('updated_at')
            ->get();

        // 4. Recent Tasks Assigned
        $recentTasks = Task::with(['assignment.student'])
            ->whereIn('assignment_id', $assignmentIds)
            ->orderByDesc('created_at')
            ->take(5)
            ->get();

        // Task Completion Data for Doughnut Chart
        $allTasks = Task::whereIn('assignment_id', $assignmentIds)->get();
        $taskStats = [
            'Completed' => $allTasks->where('status', 'completed')->count(),
            'In Progress' => $allTasks->where('status', 'in_progress')->count(),
            'Pending' => $allTasks->where('status', 'pending')->count(),
            'Submitted' => $allTasks->where('status', 'submitted')->count(),
        ];

        // Weekly Approved Hours for Line Chart
        $fromDate = Carbon::now()->subDays(6)->toDateString();
        $logsForWeek = WorkLog::whereIn('assignment_id', $assignmentIds)
            ->where('status', 'approved')
            ->where('work_date', '>=', $fromDate)
            ->get();

        $weeklyHours = collect(range(0, 6))->map(function ($offset) use ($logsForWeek) {
            $date = Carbon::now()->subDays(6 - $offset)->toDateString();
            $label = Carbon::parse($date)->format('M d');
            $total = $logsForWeek
                ->filter(fn ($log) => $log->work_date?->toDateString() === $date)
                ->sum('hours');

            return [
                'day' => $label,
                'total_hours' => round((float) $total, 2),
            ];
        });

        return view('dashboards.supervisor', [
            'assignments' => $assignments,
            'pendingAttendanceLogs' => $pendingAttendanceLogs,
            'pendingHoursLogs' => $pendingHoursLogs,
            'pendingTaskReviews' => $pendingTaskReviews,
            'recentTasks' => $recentTasks,
            'taskStats' => $taskStats,
            'weeklyHours' => $weeklyHours,
        ]);
    }

    public function approveWorkLog(Request $request, WorkLog $workLog)
    {
        $this->authorizeSupervisor($workLog);

        $workLog->update([
            'status' => 'approved',
            'reviewer_id' => Auth::id(),
            'reviewed_at' => now(),
        ]);

        $workLog->loadMissing(['assignment.student']);
        if ($workLog->assignment?->student) {
            $workLog->assignment->student->notify(new WorkLogReviewedNotification($workLog));
        }

        return redirect()->back()->with('status', 'Work log approved successfully.');
    }

    public function rejectWorkLog(Request $request, WorkLog $workLog)
    {
        $this->authorizeSupervisor($workLog);

        $workLog->update([
            'status' => 'rejected',
            'reviewer_id' => Auth::id(),
            'reviewed_at' => now(),
        ]);

        $workLog->loadMissing(['assignment.student']);
        if ($workLog->assignment?->student) {
            $workLog->assignment->student->notify(new WorkLogReviewedNotification($workLog));
        }

        return redirect()->back()->with('status', 'Work log rejected.');
    }

    public function reviewWorkLog(Request $request, WorkLog $workLog): RedirectResponse
    {
        $this->authorizeSupervisor($workLog);

        $validated = $request->validate([
            'status' => ['required', 'in:approved,rejected'],
            'grade' => ['nullable', 'string', 'max:10'],
            'reviewer_comment' => ['nullable', 'string'],
        ]);

        $workLog->update([
            'status' => $validated['status'],
            'grade' => $validated['grade'] ?? null,
            'reviewer_comment' => $validated['reviewer_comment'] ?? null,
            'reviewer_id' => Auth::id(),
            'reviewed_at' => now(),
        ]);

        $workLog->loadMissing(['assignment.student']);
        if ($workLog->assignment?->student) {
            $workLog->assignment->student->notify(new WorkLogReviewedNotification($workLog));
        }

        return redirect('/supervisor/dashboard');
    }

    public function accomplishmentReports(Request $request): View
    {
        $assignments = Assignment::query()
            ->where('supervisor_id', Auth::id())
            ->active()
            ->whereHas('student', fn ($q) => $q->eligibleStudentForRoster())
            ->pluck('id');

        $type = $request->query('type');
        $status = $request->query('status');
        $sentDate = $request->query('sent_date');

        $workLogsQuery = WorkLog::with(['assignment.student', 'assignment.company'])
            ->whereIn('assignment_id', $assignments)
            ->whereNull('time_in')
            ->where('submitted_to', 'supervisor')
            ->orderByDesc('work_date');

        if ($type) {
            $workLogsQuery->where('type', $type);
        }

        if ($status) {
            if ($status === 'declined') {
                $workLogsQuery->where('status', 'rejected');
            } else {
                $workLogsQuery->where('status', $status);
            }
        }

        if ($sentDate) {
            $workLogsQuery->whereDate('created_at', Carbon::parse($sentDate));
        }

        $workLogs = $workLogsQuery->paginate(30)->withQueryString();

        return view('supervisor.accomplishment-reports.index', compact('workLogs', 'type', 'status', 'sentDate'));
    }

    private function authorizeSupervisor($model)
    {
        if ($model instanceof WorkLog && ($model->submitted_to ?? null) !== 'supervisor') {
            abort(403, 'Unauthorized action.');
        }

        if ($model->assignment->supervisor_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }
    }
}
