<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\Leave;
use App\Models\PerformanceEvaluation;
use App\Models\User;
use App\Models\WorkLog;
use App\Notifications\LeaveStatusUpdatedNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class OjtAdviserController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();
        $assignments = Assignment::where('ojt_adviser_id', $user->id)
            ->with(['student', 'company', 'supervisor'])
            ->get();

        $totalStudents = $assignments->count();

        // ===== BASIC METRICS =====
        $incompleteLogsCount = 0;
        $completedHoursCount = 0;
        $pendingEvaluationsCount = 0;
        $lowPerformanceCount = 0;

        foreach ($assignments as $assignment) {
            $lastLog = WorkLog::where('assignment_id', $assignment->id)->latest('work_date')->first();
            if (! $lastLog || ($lastLog->time_in && ! $lastLog->time_out)) {
                $incompleteLogsCount++;
            }

            if ($assignment->progressPercentage() >= 100) {
                $completedHoursCount++;
            }

            // Check if student has pending evaluation
            $evaluation = PerformanceEvaluation::where('student_id', $assignment->student_id)->latest()->first();
            if (!$evaluation || $evaluation->created_at->diffInDays(now()) > 30) {
                $pendingEvaluationsCount++;
            }
        }

        // ===== EVALUATION METRICS =====
        $evaluationProgress = 0;
        $evaluations = collect();
        if ($totalStudents > 0) {
            $evaluations = PerformanceEvaluation::whereIn('student_id', $assignments->pluck('student_id'))->get();
            $evaluatedCount = $evaluations->unique('student_id')->count();
            $evaluationProgress = round(($evaluatedCount / $totalStudents) * 100);
        }

        // Evaluation Averages for Radar Chart
        $evaluationAverages = [
            'Attendance' => round($evaluations->avg('attendance_punctuality') ?? 0, 1),
            'Quality of Work' => round($evaluations->avg('quality_of_work') ?? 0, 1),
            'Initiative' => round($evaluations->avg('initiative') ?? 0, 1),
            'Cooperation' => round($evaluations->avg('cooperation') ?? 0, 1),
            'Dependability' => round($evaluations->avg('dependability') ?? 0, 1),
            'Communication' => round($evaluations->avg('communication_skills') ?? 0, 1),
        ];

        // ===== STUDENTS NEEDING ATTENTION =====
        $studentsNeedingAttention = $assignments->filter(function ($assignment) {
            $lastLog = WorkLog::where('assignment_id', $assignment->id)->latest('work_date')->first();
            $isIncomplete = !$lastLog || ($lastLog->time_in && ! $lastLog->time_out);
            
            $evaluation = PerformanceEvaluation::where('student_id', $assignment->student_id)->latest()->first();
            $isLowPerformance = $evaluation && $evaluation->average_score <= 2.5;
            
            return $isIncomplete || $isLowPerformance;
        })->take(5);

        // ===== PENDING EVALUATIONS =====
        $pendingEvaluations = $assignments->filter(function ($assignment) {
            $evaluation = PerformanceEvaluation::where('student_id', $assignment->student_id)->latest()->first();
            return !$evaluation || $evaluation->created_at->diffInDays(now()) > 30;
        })->take(5);

        // ===== RECENT ACTIVITIES =====
        $recentActivities = WorkLog::whereIn('assignment_id', $assignments->pluck('id'))
            ->latest('work_date')
            ->take(8)
            ->get()
            ->load('assignment.student');

        // ===== COMPLETION TRENDS (Last 6 months) =====
        $completionTrends = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $completed = $assignments->filter(function ($a) use ($month) {
                $logs = WorkLog::where('assignment_id', $a->id)
                    ->whereMonth('work_date', $month->month)
                    ->whereYear('work_date', $month->year)
                    ->sum('hours');
                return $logs >= $a->required_hours;
            })->count();
            
            $completionTrends[] = [
                'month' => $month->format('M Y'),
                'completed' => $completed
            ];
        }

        // ===== CHART DATA =====
        $studentProgressData = $assignments->map(function($a) {
            return [
                'name' => $a->student->name,
                'percentage' => $a->progressPercentage(),
                'hours' => $a->totalApprovedHours(),
                'required' => $a->required_hours
            ];
        });

        return view('ojt_adviser.dashboard', [
            'totalStudents' => $totalStudents,
            'incompleteLogsCount' => $incompleteLogsCount,
            'completedHoursCount' => $completedHoursCount,
            'evaluationProgress' => $evaluationProgress,
            'pendingEvaluationsCount' => $pendingEvaluationsCount,
            'assignments' => $assignments,
            'evaluationAverages' => $evaluationAverages,
            'studentsNeedingAttention' => $studentsNeedingAttention,
            'pendingEvaluations' => $pendingEvaluations,
            'recentActivities' => $recentActivities,
            'completionTrends' => $completionTrends,
            'studentProgressData' => $studentProgressData,
        ]);
    }

    public function students(): View
    {
        $user = Auth::user();
        $assignments = Assignment::where('ojt_adviser_id', $user->id)
            ->with(['student.studentProfile', 'company', 'supervisor'])
            ->get();

        return view('ojt_adviser.students.index', compact('assignments'));
    }

    public function studentLogs(User $student): View
    {
        $assignment = Assignment::where('ojt_adviser_id', Auth::id())
            ->where('student_id', $student->id)
            ->firstOrFail();

        $logs = WorkLog::where('assignment_id', $assignment->id)
            ->orderBy('work_date', 'desc')
            ->paginate(15);

        return view('ojt_adviser.students.logs', compact('student', 'logs'));
    }

    public function studentJournals(User $student): View
    {
        $assignment = Assignment::where('ojt_adviser_id', Auth::id())
            ->where('student_id', $student->id)
            ->firstOrFail();

        $workLogs = WorkLog::where('assignment_id', $assignment->id)
            ->whereNotNull('description')
            ->orderBy('work_date', 'desc')
            ->paginate(15);

        return view('ojt_adviser.students.journals', compact('student', 'workLogs'));
    }

    public function commentJournal(Request $request, WorkLog $workLog)
    {
        $request->validate([
            'comment' => 'required|string|max:1000',
        ]);

        $workLog->update(['adviser_comment' => $request->comment]);

        return back()->with('status', 'Comment added successfully.');
    }

    public function accomplishmentReports(): View
    {
        $assignmentIds = Assignment::where('ojt_adviser_id', Auth::id())->pluck('id');

        $workLogs = WorkLog::with(['assignment.student', 'assignment.company'])
            ->whereIn('assignment_id', $assignmentIds)
            ->whereNull('time_in')
            ->orderByDesc('work_date')
            ->paginate(30);

        return view('ojt_adviser.accomplishment-reports.index', compact('workLogs'));
    }

    public function evaluations(): View
    {
        $user = Auth::user();
        $assignments = Assignment::where('ojt_adviser_id', $user->id)
            ->with(['student', 'company', 'supervisor'])
            ->get();

        return view('ojt_adviser.evaluations.index', compact('assignments'));
    }

    public function reports(): View
    {
        $user = Auth::user();
        $assignments = Assignment::where('ojt_adviser_id', $user->id)
            ->with(['student', 'company'])
            ->get();

        return view('ojt_adviser.reports.index', compact('assignments'));
    }

    public function leavesIndex(Request $request): View
    {
        $assignments = Assignment::where('ojt_adviser_id', Auth::id())
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

        return view('ojt_adviser.leaves.index', compact('leaves'));
    }

    public function approveLeave(Request $request, Leave $leave)
    {
        $this->authorizeAdviser($leave);

        $validated = $request->validate([
            'reviewer_remarks' => ['nullable', 'string', 'max:1000'],
        ]);

        if (! in_array($leave->status, [Leave::STATUS_SUBMITTED, Leave::STATUS_PENDING], true)) {
            return back()->withErrors(['leave' => 'Only submitted/pending leave requests can be approved.']);
        }

        $leave->update([
            'status' => Leave::STATUS_APPROVED,
            'reviewer_remarks' => $validated['reviewer_remarks'] ?? null,
            'reviewer_id' => Auth::id(),
            'reviewed_at' => now(),
        ]);

        if ($leave->assignment?->student) {
            $leave->assignment->student->notify(new LeaveStatusUpdatedNotification($leave->fresh()));
        }

        Log::info('OJT adviser approved leave', [
            'leave_id' => $leave->id,
            'ojt_adviser_id' => Auth::id(),
            'status' => $leave->status,
        ]);

        return redirect()->back()->with('status', 'Leave request approved.');
    }

    public function rejectLeave(Request $request, Leave $leave)
    {
        $this->authorizeAdviser($leave);

        $validated = $request->validate([
            'reviewer_remarks' => ['required', 'string', 'max:1000'],
        ]);

        if (! in_array($leave->status, [Leave::STATUS_SUBMITTED, Leave::STATUS_PENDING], true)) {
            return back()->withErrors(['leave' => 'Only submitted/pending leave requests can be rejected.']);
        }

        $leave->update([
            'status' => Leave::STATUS_REJECTED,
            'reviewer_remarks' => $validated['reviewer_remarks'],
            'reviewer_id' => Auth::id(),
            'reviewed_at' => now(),
        ]);

        if ($leave->assignment?->student) {
            $leave->assignment->student->notify(new LeaveStatusUpdatedNotification($leave->fresh()));
        }

        Log::info('OJT adviser rejected leave', [
            'leave_id' => $leave->id,
            'ojt_adviser_id' => Auth::id(),
            'status' => $leave->status,
        ]);

        return redirect()->back()->with('status', 'Leave request rejected.');
    }

    private function authorizeAdviser(Leave $model): void
    {
        if ($model->assignment->ojt_adviser_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }
    }
}
