<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\PerformanceEvaluation;
use App\Models\User;
use App\Models\WorkLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

        $incompleteLogsCount = 0;
        $completedHoursCount = 0;
        foreach ($assignments as $assignment) {
            $lastLog = WorkLog::where('assignment_id', $assignment->id)->latest('work_date')->first();
            if (! $lastLog || ($lastLog->time_in && ! $lastLog->time_out)) {
                $incompleteLogsCount++;
            }

            if ($assignment->progressPercentage() >= 100) {
                $completedHoursCount++;
            }
        }

        $evaluationProgress = 0;
        $evaluations = collect();
        if ($totalStudents > 0) {
            $evaluations = PerformanceEvaluation::whereIn('student_id', $assignments->pluck('student_id'))->get();
            $evaluatedCount = $evaluations->unique('student_id')->count();
            $evaluationProgress = round(($evaluatedCount / $totalStudents) * 100);
        }

        // Evaluation Averages for Radar Chart
        $evaluationAverages = [
            'Attendance' => $evaluations->avg('attendance_punctuality') ?? 0,
            'Quality of Work' => $evaluations->avg('quality_of_work') ?? 0,
            'Initiative' => $evaluations->avg('initiative') ?? 0,
            'Cooperation' => $evaluations->avg('cooperation') ?? 0,
            'Dependability' => $evaluations->avg('dependability') ?? 0,
            'Communication Skills' => $evaluations->avg('communication_skills') ?? 0,
        ];

        return view('ojt_adviser.dashboard', [
            'totalStudents' => $totalStudents,
            'incompleteLogsCount' => $incompleteLogsCount,
            'completedHoursCount' => $completedHoursCount,
            'evaluationProgress' => $evaluationProgress,
            'assignments' => $assignments,
            'evaluationAverages' => $evaluationAverages,
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
            ->whereNotNull('description')
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
}
