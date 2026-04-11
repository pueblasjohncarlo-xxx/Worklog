<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\Leave;
use App\Models\Task;
use App\Models\WorkLog;
use App\Notifications\LeaveStatusUpdatedNotification;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class SupervisorController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();

        // Assignments under this supervisor
        $assignments = Assignment::with(['student', 'company'])
            ->where('supervisor_id', $user->id)
            ->where('status', 'active')
            ->get();

        $assignmentIds = $assignments->pluck('id');

        // 1. Pending Attendance Approvals (clocked-in, not yet clocked-out)
        $pendingAttendanceLogs = WorkLog::with(['assignment.student'])
            ->whereIn('assignment_id', $assignmentIds)
            ->where('status', 'submitted')
            ->where('submitted_to', 'supervisor')
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
            ->where('submitted_to', 'supervisor')
            ->where(function ($query) {
                $query->where(function ($q) {
                    $q->whereNotNull('time_in')->whereNotNull('time_out');
                })->orWhere(function ($q) {
                    $q->whereNull('time_in')->whereNull('time_out');
                });
            })
            ->orderBy('work_date')
            ->get();

        // 3. Pending Leave Requests
        $pendingLeaves = Leave::with(['assignment.student'])
            ->whereIn('assignment_id', $assignmentIds)
            ->where('status', 'pending')
            ->orderBy('start_date')
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
            'pendingLeaves' => $pendingLeaves,
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

        return redirect('/supervisor/dashboard');
    }

    public function accomplishmentReports(Request $request): View
    {
        $assignments = Assignment::where('supervisor_id', Auth::id())
            ->where('status', 'active')
            ->pluck('id');

        $type = $request->query('type');
        $status = $request->query('status');
        $sentDate = $request->query('sent_date');

        $workLogsQuery = WorkLog::with(['assignment.student', 'assignment.company'])
            ->whereIn('assignment_id', $assignments)
            ->whereNotNull('description')
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

    public function leavesIndex(Request $request): View
    {
        $assignments = Assignment::where('supervisor_id', Auth::id())
            ->where('status', 'active')
            ->pluck('id');

        Leave::whereIn('assignment_id', $assignments)
            ->where('status', Leave::STATUS_SUBMITTED)
            ->update(['status' => Leave::STATUS_PENDING]);

        $query = Leave::with(['assignment.student', 'assignment.company', 'reviewer'])
            ->whereIn('assignment_id', $assignments)
            ->orderByDesc('created_at');

        if ($request->filled('status')) {
            $query->where('status', $request->string('status')->toString());
        }

        if ($request->filled('date_from')) {
            $query->whereDate('start_date', '>=', $request->date('date_from'));
        }

        if ($request->filled('date_to')) {
            $query->whereDate('end_date', '<=', $request->date('date_to'));
        }

        if ($request->filled('q')) {
            $search = trim((string) $request->string('q'));
            $query->where(function ($q) use ($search) {
                $q->where('type', 'like', '%'.$search.'%')
                    ->orWhere('reason', 'like', '%'.$search.'%')
                    ->orWhere('student_name', 'like', '%'.$search.'%');
            });
        }

        $leaves = $query->paginate(30)->withQueryString();

        return view('supervisor.leaves.index', compact('leaves'));
    }

    public function approveLeave(Request $request, Leave $leave): RedirectResponse
    {
        $this->authorizeSupervisor($leave);

        $validated = $request->validate([
            'reviewer_remarks' => ['nullable', 'string', 'max:1000'],
        ]);

        if (! in_array($leave->status, [Leave::STATUS_SUBMITTED, Leave::STATUS_PENDING], true)) {
            return back()->withErrors(['leave' => 'Only submitted/pending leave requests can be approved.']);
        }

        $leave->update([
            'status' => 'approved',
            'reviewer_remarks' => $validated['reviewer_remarks'] ?? null,
            'reviewer_id' => Auth::id(),
            'reviewed_at' => now(),
        ]);

        if ($leave->assignment?->student) {
            $leave->assignment->student->notify(new LeaveStatusUpdatedNotification($leave->fresh()));
        }

        Log::info('Supervisor approved leave', [
            'leave_id' => $leave->id,
            'supervisor_id' => Auth::id(),
            'status' => $leave->status,
        ]);

        return redirect()->back()->with('status', 'Leave request approved.');
    }

    public function rejectLeave(Request $request, Leave $leave): RedirectResponse
    {
        $this->authorizeSupervisor($leave);

        $validated = $request->validate([
            'reviewer_remarks' => ['required', 'string', 'max:1000'],
        ]);

        if (! in_array($leave->status, [Leave::STATUS_SUBMITTED, Leave::STATUS_PENDING], true)) {
            return back()->withErrors(['leave' => 'Only submitted/pending leave requests can be rejected.']);
        }

        $leave->update([
            'status' => 'rejected',
            'reviewer_remarks' => $validated['reviewer_remarks'],
            'reviewer_id' => Auth::id(),
            'reviewed_at' => now(),
        ]);

        if ($leave->assignment?->student) {
            $leave->assignment->student->notify(new LeaveStatusUpdatedNotification($leave->fresh()));
        }

        Log::info('Supervisor rejected leave', [
            'leave_id' => $leave->id,
            'supervisor_id' => Auth::id(),
            'status' => $leave->status,
        ]);

        return redirect()->back()->with('status', 'Leave request rejected.');
    }

    private function authorizeSupervisor($model)
    {
        if ($model->assignment->supervisor_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }
    }
}
