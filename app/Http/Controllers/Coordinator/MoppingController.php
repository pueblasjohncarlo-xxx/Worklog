<?php

namespace App\Http\Controllers\Coordinator;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\WorkLog;
use App\Services\MappingCalendarBuilder;
use App\Services\MoppingAnalyzer;
use Carbon\Carbon;
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

    public function show(Request $request, Assignment $assignment, MoppingAnalyzer $analyzer, MappingCalendarBuilder $builder): View
    {
        $fromKey = (string) $request->query('from', '');
        $toKey = (string) $request->query('to', '');

        $monthKeyParam = (string) $request->query('month', '');
        if ($fromKey === '' && $monthKeyParam !== '') {
            $fromKey = $monthKeyParam;
        }
        if ($toKey === '' && $monthKeyParam !== '') {
            $toKey = $monthKeyParam;
        }

        $fromMonth = $fromKey !== '' ? $analyzer->monthRangeFromKey($fromKey) : Carbon::now()->subMonths(4)->startOfMonth();
        $toMonth = $toKey !== '' ? $analyzer->monthRangeFromKey($toKey) : Carbon::now()->startOfMonth();

        if ($toMonth->lt($fromMonth)) {
            [$fromMonth, $toMonth] = [$toMonth, $fromMonth];
        }

        // Keep legacy month-based details (defaults to the range start month)
        $detailMonth = $monthKeyParam !== '' ? $analyzer->monthRangeFromKey($monthKeyParam) : $fromMonth;
        $monthKey = $analyzer->monthKey($detailMonth);
        $monthEnd = $analyzer->monthEnd($detailMonth);

        $assignment->loadMissing(['student', 'company']);

        $monthLogs = WorkLog::query()
            ->where('assignment_id', $assignment->id)
            ->whereBetween('work_date', [$detailMonth->toDateString(), $monthEnd->toDateString()])
            ->orderBy('work_date')
            ->get();

        $summary = $analyzer->analyzeMonth($monthLogs, $detailMonth);

        $attendanceLogs = $monthLogs
            ->filter(fn (WorkLog $log) => ! is_null($log->time_in))
            ->values();

        $arLogs = $monthLogs
            ->filter(fn (WorkLog $log) => is_null($log->time_in) && in_array($log->type, ['daily', 'weekly', 'monthly'], true))
            ->values();

        $mapping = $builder->buildForAssignment($assignment, $fromMonth, $toMonth, $analyzer);

        $rangeIsSingleMonth = ($mapping['fromKey'] ?? '') === ($mapping['toKey'] ?? '');

        return view('coordinator.mopping.show', [
            'monthKey' => $monthKey,
            'monthStart' => $detailMonth,
            'assignment' => $assignment,
            'summary' => $summary,
            'attendanceLogs' => $attendanceLogs,
            'arLogs' => $arLogs,
            'mapping' => $mapping,
            'fromKey' => $mapping['fromKey'] ?? $fromMonth->format('Y-m'),
            'toKey' => $mapping['toKey'] ?? $toMonth->format('Y-m'),
            'rangeIsSingleMonth' => $rangeIsSingleMonth,
        ]);
    }
}
