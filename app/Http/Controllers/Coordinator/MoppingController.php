<?php

namespace App\Http\Controllers\Coordinator;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\WorkLog;
use App\Services\MoppingAnalyzer;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MoppingController extends Controller
{
    public function index(Request $request, MoppingAnalyzer $analyzer): View
    {
        $monthStart = $analyzer->monthRangeFromKey($request->query('month'));
        $monthKey = $analyzer->monthKey($monthStart);
        $monthEnd = $analyzer->monthEnd($monthStart);

        $assignments = Assignment::query()
            ->active()
            ->whereHas('student', fn ($q) => $q->eligibleStudentForRoster())
            ->with(['student', 'company'])
            ->orderByDesc('updated_at')
            ->get()
            ->unique('student_id')
            ->values();

        $assignmentIds = $assignments->pluck('id');

        $monthLogsByAssignment = WorkLog::query()
            ->whereIn('assignment_id', $assignmentIds)
            ->whereBetween('work_date', [$monthStart->toDateString(), $monthEnd->toDateString()])
            ->orderBy('work_date')
            ->get()
            ->groupBy('assignment_id');

        $rows = $assignments->map(function (Assignment $assignment) use ($analyzer, $monthStart, $monthLogsByAssignment) {
            $logs = $monthLogsByAssignment->get($assignment->id, collect());
            $summary = $analyzer->analyzeMonth($logs, $monthStart);

            return [
                'assignment' => $assignment,
                'summary' => $summary,
            ];
        });

        return view('coordinator.mopping.index', [
            'monthKey' => $monthKey,
            'monthStart' => $monthStart,
            'rows' => $rows,
        ]);
    }

    public function show(Request $request, Assignment $assignment, MoppingAnalyzer $analyzer): View
    {
        $monthStart = $analyzer->monthRangeFromKey($request->query('month'));
        $monthKey = $analyzer->monthKey($monthStart);
        $monthEnd = $analyzer->monthEnd($monthStart);

        $assignment->loadMissing(['student', 'company']);

        $monthLogs = WorkLog::query()
            ->where('assignment_id', $assignment->id)
            ->whereBetween('work_date', [$monthStart->toDateString(), $monthEnd->toDateString()])
            ->orderBy('work_date')
            ->get();

        $summary = $analyzer->analyzeMonth($monthLogs, $monthStart);

        $attendanceLogs = $monthLogs
            ->filter(fn (WorkLog $log) => ! is_null($log->time_in))
            ->values();

        $arLogs = $monthLogs
            ->filter(fn (WorkLog $log) => is_null($log->time_in) && in_array($log->type, ['daily', 'weekly', 'monthly'], true))
            ->values();

        return view('coordinator.mopping.show', [
            'monthKey' => $monthKey,
            'monthStart' => $monthStart,
            'assignment' => $assignment,
            'summary' => $summary,
            'attendanceLogs' => $attendanceLogs,
            'arLogs' => $arLogs,
        ]);
    }
}
