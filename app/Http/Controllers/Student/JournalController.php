<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\WorkLog;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class JournalController extends Controller
{
    public function index(Request $request): View
    {
        $user = Auth::user();
        $date = $request->input('date') ? Carbon::parse($request->input('date')) : Carbon::now();

        $startOfMonth = $date->copy()->startOfMonth();
        $endOfMonth = $date->copy()->endOfMonth();

        $assignment = Assignment::resolveActiveForStudent($user->id);

        $calendar = [];
        $logs = collect();
        $grouped = collect();

        if ($assignment) {
            $logs = WorkLog::where('assignment_id', $assignment->id)
                ->whereBetween('work_date', [$startOfMonth->toDateString(), $endOfMonth->toDateString()])
                ->get();
            $grouped = $logs->groupBy(function ($log) {
                return $log->work_date->toDateString();
            });
        }

        // Fill calendar
        $currentDay = $startOfMonth->copy()->startOfWeek(Carbon::SUNDAY);
        $endDay = $endOfMonth->copy()->endOfWeek(Carbon::SATURDAY);

        while ($currentDay <= $endDay) {
            $dateString = $currentDay->toDateString();
            $dayLogs = $grouped->get($dateString, collect());
            $journal = $dayLogs->first(function ($l) {
                return $l->type === 'daily' && ! empty($l->description) && empty($l->time_in);
            });
            $approvedAttendance = $dayLogs->first(function ($l) {
                return $l->status === 'approved' && ! empty($l->time_in);
            });
            $displayLog = $journal ?: $approvedAttendance;

            $calendar[] = [
                'date' => $currentDay->copy(),
                'is_current_month' => $currentDay->month === $date->month,
                'log' => $displayLog,
                'status' => $displayLog ? $displayLog->status : 'absent',
                'can_write' => $approvedAttendance && ! $journal,
            ];
            $currentDay->addDay();
        }

        return view('student.journal.index', [
            'calendar' => $calendar,
            'currentDate' => $date,
            'prevMonth' => $date->copy()->subMonth(),
            'nextMonth' => $date->copy()->addMonth(),
            'assignment' => $assignment,
        ]);
    }
}
