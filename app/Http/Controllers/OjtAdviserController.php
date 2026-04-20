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
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class OjtAdviserController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();
        $assignments = Assignment::query()
            ->where('ojt_adviser_id', $user->id)
            ->active()
            ->whereHas('student', fn ($q) => $q->eligibleStudentForRoster())
            ->with(['student', 'company', 'supervisor'])
            ->orderByDesc('updated_at')
            ->get()
            ->unique('student_id')
            ->values();

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
        $assignments = Assignment::query()
            ->where('ojt_adviser_id', $user->id)
            ->active()
            ->whereHas('student', fn ($q) => $q->eligibleStudentForRoster())
            ->with(['student.studentProfile', 'company', 'supervisor'])
            ->orderByDesc('updated_at')
            ->get()
            ->unique('student_id')
            ->values();

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
        $assignmentIds = Assignment::query()
            ->where('ojt_adviser_id', Auth::id())
            ->active()
            ->whereHas('student', fn ($q) => $q->eligibleStudentForRoster())
            ->pluck('id');

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
        $assignments = Assignment::query()
            ->where('ojt_adviser_id', $user->id)
            ->active()
            ->whereHas('student', fn ($q) => $q->eligibleStudentForRoster())
            ->with(['student', 'company', 'supervisor'])
            ->orderByDesc('updated_at')
            ->get()
            ->unique('student_id')
            ->values();

        return view('ojt_adviser.evaluations.index', compact('assignments'));
    }

    public function reports(): View
    {
        $user = Auth::user();
        $assignments = Assignment::query()
            ->where('ojt_adviser_id', $user->id)
            ->active()
            ->whereHas('student', fn ($q) => $q->eligibleStudentForRoster())
            ->with(['student', 'company'])
            ->orderByDesc('updated_at')
            ->get()
            ->unique('student_id')
            ->values();

        return view('ojt_adviser.reports.index', compact('assignments'));
    }

    public function exportAttendance(Request $request): StreamedResponse
    {
        $adviserId = Auth::id();

        $assignmentIds = Assignment::query()
            ->where('ojt_adviser_id', $adviserId)
            ->active()
            ->whereHas('student', fn ($q) => $q->eligibleStudentForRoster())
            ->pluck('id');

        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');

        $query = WorkLog::with(['assignment.student', 'assignment.company'])
            ->whereIn('assignment_id', $assignmentIds)
            ->whereNotNull('time_in')
            ->orderBy('work_date')
            ->orderBy('id');

        if ($dateFrom) {
            $query->whereDate('work_date', '>=', $dateFrom);
        }
        if ($dateTo) {
            $query->whereDate('work_date', '<=', $dateTo);
        }

        $fileName = 'attendance_summary_'.now()->format('Y-m-d_His').'.csv';

        return response()->streamDownload(function () use ($query) {
            $out = fopen('php://output', 'w');
            if (! $out) {
                return;
            }

            // UTF-8 BOM for Excel compatibility
            fwrite($out, "\xEF\xBB\xBF");

            fputcsv($out, [
                'WorkLog ID',
                'Student',
                'Email',
                'Company',
                'Work Date',
                'Time In',
                'Time Out',
                'Hours',
                'Status',
            ]);

            $query->chunk(500, function ($logs) use ($out) {
                foreach ($logs as $log) {
                    $student = $log->assignment?->student;
                    $company = $log->assignment?->company;

                    $timeIn = $log->time_in instanceof \Carbon\Carbon ? $log->time_in->format('H:i:s') : (string) ($log->time_in ?? '');
                    $timeOut = $log->time_out instanceof \Carbon\Carbon ? $log->time_out->format('H:i:s') : (string) ($log->time_out ?? '');

                    fputcsv($out, [
                        $log->id,
                        $student?->name ?? 'N/A',
                        $student?->email ?? 'N/A',
                        $company?->name ?? 'N/A',
                        $log->work_date?->format('Y-m-d') ?? '',
                        $timeIn,
                        $timeOut,
                        $log->hours,
                        $log->status,
                    ]);
                }
            });

            fclose($out);
        }, $fileName, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    public function exportEvaluation(PerformanceEvaluation $evaluation)
    {
        $user = Auth::user();
        abort_unless($user && $user->role === User::ROLE_OJT_ADVISER, 403);

        // Only allow exporting evaluations for students assigned to this adviser
        $assigned = Assignment::where('ojt_adviser_id', $user->id)
            ->where('student_id', $evaluation->student_id)
            ->exists();
        abort_unless($assigned, 403);

        // Follow the same safety rule as coordinator: only export submitted evaluations
        abort_unless($evaluation->submitted_at !== null, 403);

        if ($evaluation->document_path && Storage::disk('public')->exists($evaluation->document_path)) {
            $path = $evaluation->document_path;
            $name = basename($path);
        } else {
            $student = $evaluation->student;
            $supervisor = $evaluation->supervisor;
            $date = $evaluation->evaluation_date->format('F d, Y');
            $assignment = Assignment::where('student_id', $evaluation->student_id)
                ->where('supervisor_id', $evaluation->supervisor_id)
                ->with('company')->latest('start_date')->first();
            $company = $assignment && $assignment->company ? $assignment->company->name : 'N/A';

            $html = "<html><head><meta charset='utf-8'><style>
                body{font-family:Arial,Helvetica,sans-serif;font-size:12pt;color:#111}
                table{border-collapse:collapse;width:100%}
                th,td{border:1px solid #ccc;padding:8px;text-align:left}
            </style></head><body>
                <h1>Student Performance Evaluation</h1>
                <p><strong>Student:</strong> {$student->name}</p>
                <p><strong>Supervisor:</strong> {$supervisor->name}</p>
                <p><strong>Date:</strong> {$date}</p>
                <p><strong>Company:</strong> {$company}</p>
                <p><strong>Type / Semester:</strong> ".e($evaluation->semester ?? 'N/A')."</p>
                <table>
                    <tr><th>Criteria</th><th>Rating</th></tr>
                    <tr><td>Attendance & Punctuality</td><td>{$evaluation->attendance_punctuality} / 5</td></tr>
                    <tr><td>Quality of Work</td><td>{$evaluation->quality_of_work} / 5</td></tr>
                    <tr><td>Initiative</td><td>{$evaluation->initiative} / 5</td></tr>
                    <tr><td>Cooperation</td><td>{$evaluation->cooperation} / 5</td></tr>
                    <tr><td>Dependability</td><td>{$evaluation->dependability} / 5</td></tr>
                    <tr><td>Communication Skills</td><td>{$evaluation->communication_skills} / 5</td></tr>
                    <tr><th>Final Rating</th><th>{$evaluation->final_rating} / 5</th></tr>
                </table>
                <p><strong>Remarks</strong></p>
                <p>".nl2br(e($evaluation->remarks ?? 'N/A'))."</p>
            </body></html>";

            $name = 'evaluation-'.Str::slug($student->name.'-'.$date).'.doc';
            $path = "evaluations/{$name}";
            Storage::disk('public')->put($path, $html);
        }

        $full = Storage::disk('public')->path($path);

        return response()->download($full, $name, ['Content-Type' => 'application/msword']);
    }

    public function evaluationStudent(User $student): View
    {
        $assignment = Assignment::where('ojt_adviser_id', Auth::id())
            ->where('student_id', $student->id)
            ->with(['student', 'company', 'supervisor'])
            ->firstOrFail();

        $evaluations = PerformanceEvaluation::where('student_id', $student->id)
            ->orderByDesc('evaluation_date')
            ->get();

        $latestEval = $evaluations->first();

        return view('ojt_adviser.evaluations.show', [
            'student' => $student,
            'assignment' => $assignment,
            'evaluations' => $evaluations,
            'latestEval' => $latestEval,
        ]);
    }

    public function leavesIndex(Request $request): View
    {
        $assignments = Assignment::where('ojt_adviser_id', Auth::id())
            ->active()
            ->whereHas('student', fn ($q) => $q->eligibleStudentForRoster())
            ->pluck('id');

        $usesStagedLeaveApproval = Schema::hasColumn('leaves', 'supervisor_decision');

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

        return view('ojt_adviser.leaves.index', compact('leaves', 'usesStagedLeaveApproval'));
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

        if (Schema::hasColumn('leaves', 'supervisor_decision')) {
            if (($leave->supervisor_decision ?? null) !== 'approved') {
                return back()->withErrors(['leave' => 'Supervisor approval is required before adviser review.']);
            }
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

        if (Schema::hasColumn('leaves', 'supervisor_decision')) {
            if (($leave->supervisor_decision ?? null) !== 'approved') {
                return back()->withErrors(['leave' => 'Supervisor approval is required before adviser review.']);
            }
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
