<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\Leave;
use App\Models\Task;
use App\Models\WorkLog;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class StudentController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();

        // DEBUG: Log execution
        \Log::info('========== STUDENT DASHBOARD CALLED ==========');
        \Log::info('Route: student.dashboard | Controller: StudentController@index | User: ' . $user->id . ' (' . $user->name . ')');

        $assignment = Assignment::with(['company', 'supervisor'])
            ->where('student_id', $user->id)
            ->where('status', 'active')
            ->first();

        // Get calendar current date from request or use today
        $calendarCurrentDate = Carbon::now();
        if (request()->has('attendance_month')) {
            try {
                $calendarCurrentDate = Carbon::createFromFormat('Y-m', request('attendance_month'))->startOfMonth();
            } catch (\Exception $e) {
                $calendarCurrentDate = Carbon::now();
            }
        }

        $workLogs = collect();
        $todayLog = null;
        $activeTasks = collect();
        $pastPendingLog = null;
        $monthlyTotalHours = 0;

        if ($assignment) {
            $workLogs = WorkLog::where('assignment_id', $assignment->id)
                ->orderByDesc('work_date')
                ->get();

            $todayLog = WorkLog::where('assignment_id', $assignment->id)
                ->where('work_date', now()->toDateString())
                ->first();

            // Check for past pending logs (clocked in but not clocked out, and not today)
            $pastPendingLog = WorkLog::where('assignment_id', $assignment->id)
                ->whereNotNull('time_in')
                ->whereNull('time_out')
                ->where('work_date', '<', now()->toDateString())
                ->first();

            $activeTasks = Task::where('assignment_id', $assignment->id)
                ->where('status', '!=', 'completed')
                ->orderBy('due_date')
                ->get();

            // Weekly Hours for Bar Chart
            $fromDate = Carbon::now()->subDays(6)->toDateString();
            $logsForWeek = WorkLog::where('assignment_id', $assignment->id)
                ->where('work_date', '>=', $fromDate)
                ->get();

            $weeklyHours = collect(range(0, 6))->map(function ($offset) use ($logsForWeek) {
                $date = Carbon::now()->subDays(6 - $offset)->toDateString();
                $label = Carbon::parse($date)->format('D');
                $total = $logsForWeek
                    ->filter(fn ($log) => $log->work_date?->toDateString() === $date)
                    ->sum('hours');

                return [
                    'day' => $label,
                    'total_hours' => round((float) $total, 2),
                ];
            });

            // Completion Stats for Doughnut Chart
            $completedHours = $assignment->totalApprovedHours();
            $remainingHours = max(0, $assignment->required_hours - $completedHours);
            $completionStats = [
                'Completed' => $completedHours,
                'Remaining' => $remainingHours,
            ];

            // Monthly Hours and Calendar for Calendar View
            $monthStart = $calendarCurrentDate->copy()->startOfMonth();
            $monthEnd = $calendarCurrentDate->copy()->endOfMonth();
            $monthlyLogs = WorkLog::where('assignment_id', $assignment->id)
                ->whereBetween('work_date', [$monthStart->toDateString(), $monthEnd->toDateString()])
                ->get();
            
            $monthlyTotalHours = $monthlyLogs->sum('hours');
            $monthlyApprovedHours = $monthlyLogs->where('status', 'approved')->sum('hours');
            $monthlyPendingHours = $monthlyLogs->whereIn('status', ['submitted', 'draft'])->sum('hours');
            $monthlyRejectedHours = $monthlyLogs->where('status', 'rejected')->sum('hours');
            $monthlyRemainingHours = max(0, $assignment->required_hours - $monthlyApprovedHours);

            // Build Attendance Calendar for Month
            $attendanceCalendar = [];
            $firstDayOfMonth = $monthStart->copy();
            $daysInMonth = $monthEnd->day;
            $startingDayOfWeek = $firstDayOfMonth->dayOfWeek; // 0 = Sunday

            // Add empty days from previous month
            for ($i = 0; $i < $startingDayOfWeek; $i++) {
                $prevDate = $firstDayOfMonth->copy()->subDays($startingDayOfWeek - $i);
                $attendanceCalendar[] = [
                    'date' => $prevDate,
                    'is_current_month' => false,
                    'status' => null,
                    'time_in' => null,
                    'time_out' => null,
                    'hours' => null,
                ];
            }

            // Add days of current month
            for ($day = 1; $day <= $daysInMonth; $day++) {
                $currentDate = $monthStart->copy()->addDays($day - 1);
                $log = $monthlyLogs->first(fn ($l) => $l->work_date->toDateString() === $currentDate->toDateString());

                $attendanceCalendar[] = [
                    'date' => $currentDate,
                    'is_current_month' => true,
                    'status' => $log?->status,
                    'time_in' => $log?->time_in,
                    'time_out' => $log?->time_out,
                    'hours' => $log?->hours,
                ];
            }

            // Add empty days from next month to fill the grid
            $totalCells = count($attendanceCalendar);
            $remainingCells = (ceil($totalCells / 7) * 7) - $totalCells;
            for ($i = 1; $i <= $remainingCells; $i++) {
                $nextDate = $monthEnd->copy()->addDays($i);
                $attendanceCalendar[] = [
                    'date' => $nextDate,
                    'is_current_month' => false,
                    'status' => null,
                    'time_in' => null,
                    'time_out' => null,
                    'hours' => null,
                ];
            }
        } else {
            $weeklyHours = collect();
            $completionStats = ['Completed' => 0, 'Remaining' => 0];
            $attendanceCalendar = [];
            $monthlyTotalHours = 0;
            $monthlyApprovedHours = 0;
            $monthlyPendingHours = 0;
            $monthlyRejectedHours = 0;
            $monthlyRemainingHours = 0;
        }

        return view('dashboards.student', [
            'assignment' => $assignment,
            'workLogs' => $workLogs,
            'todayLog' => $todayLog,
            'activeTasks' => $activeTasks,
            'pastPendingLog' => $pastPendingLog,
            'weeklyHours' => $weeklyHours,
            'completionStats' => $completionStats,
            'calendarCurrentDate' => $calendarCurrentDate,
            'monthlyTotalHours' => $monthlyTotalHours,
            'monthlyApprovedHours' => $monthlyApprovedHours,
            'monthlyPendingHours' => $monthlyPendingHours,
            'monthlyRejectedHours' => $monthlyRejectedHours,
            'monthlyRemainingHours' => $monthlyRemainingHours,
            'attendanceCalendar' => $attendanceCalendar,
        ]);
    }

    public function clockIn(): RedirectResponse
    {
        $user = Auth::user();
        $assignment = Assignment::where('student_id', $user->id)
            ->where('status', 'active')
            ->first();

        if (! $assignment) {
            return redirect()->back()->with('error', 'No active assignment found.');
        }

        // Check for any open session (even from previous days)
        $openLog = WorkLog::where('assignment_id', $assignment->id)
            ->whereNotNull('time_in')
            ->whereNull('time_out')
            ->first();

        if ($openLog) {
            // Check if the open log is from today
            if ($openLog->work_date->isToday()) {
                return redirect()->back()->with('error', 'You have a pending clock-in session. Please clock out first.');
            } else {
                return redirect()->back()->with('error', 'You have a pending session from '.$openLog->work_date->format('M d, Y').'. Please manually clock out that session first.');
            }
        }

        // Check if already clocked in today
        $existingLog = WorkLog::where('assignment_id', $assignment->id)
            ->where('work_date', now()->toDateString())
            ->first();

        if ($existingLog) {
            return redirect()->back()->with('error', 'You have already clocked in today.');
        }

        WorkLog::create([
            'assignment_id' => $assignment->id,
            'work_date' => now()->toDateString(),
            'time_in' => now()->toTimeString(),
            'status' => 'draft',
            'hours' => 0,
            'description' => 'Daily attendance log',
        ]);

        return redirect()->back()->with('status', 'Successfully clocked in.');
    }

    public function clockOut(Request $request): RedirectResponse
    {
        $user = Auth::user();
        $assignment = Assignment::where('student_id', $user->id)
            ->where('status', 'active')
            ->first();

        if (! $assignment) {
            return redirect()->back()->with('error', 'No active assignment found.');
        }

        $log = WorkLog::where('assignment_id', $assignment->id)
            ->where('work_date', now()->toDateString())
            ->whereNull('time_out')
            ->first();

        if (! $log) {
            return redirect()->back()->with('error', 'No active session found to clock out.');
        }

        $timeOutStr = now()->format('H:i:s');

        $log->update([
            'time_out' => $timeOutStr,
            'status' => 'submitted', // Auto-submit for approval
        ]);

        // Recalculate precise hours (avoid concatenating a date with a Carbon datetime string).
        $workDateStr = $log->work_date?->toDateString() ?? now()->toDateString();
        $timeInStr = $log->time_in instanceof Carbon
            ? $log->time_in->format('H:i:s')
            : (string) $log->time_in;

        $start = Carbon::createFromFormat('Y-m-d H:i:s', $workDateStr.' '.$timeInStr);
        $end = Carbon::createFromFormat('Y-m-d H:i:s', $workDateStr.' '.$timeOutStr);

        // If time out is earlier than time in, assume it's next day (night shift)
        if ($end->lessThan($start)) {
            $end->addDay();
        }

        $hours = $start->diffInMinutes($end) / 60;
        $log->update(['hours' => round($hours, 2)]);

        return redirect()->back()->with('status', 'Successfully clocked out.');
    }

    public function manualClockOut(Request $request, WorkLog $workLog): RedirectResponse
    {
        $user = Auth::user();

        if ($workLog->assignment->student_id !== $user->id) {
            abort(403);
        }

        $validated = $request->validate([
            'time_out' => 'required',
            'remarks' => 'nullable|string|max:255',
        ]);

        // Calculate hours
        // We assume the date is the work_date
        // Carbon::parse on a date object might be returning a formatted string that gets appended weirdly if not careful.
        // Let's use string formatting explicitly.
        $workDateStr = $workLog->work_date->format('Y-m-d');

        // Handle time_in safely, extracting just the time component
        $timeInValue = $workLog->time_in;
        if ($timeInValue instanceof \Carbon\Carbon) {
            $timeInStr = $timeInValue->format('H:i:s');
        } else {
            // It might be a string like "06:12:54" or "2026-02-27 06:12:54"
            $timeInStr = Carbon::parse($timeInValue)->format('H:i:s');
        }

        $timeIn = Carbon::parse($workDateStr.' '.$timeInStr);
        $timeOut = Carbon::parse($workDateStr.' '.$validated['time_out']);

        // If timeOut is before timeIn, assume it crossed midnight to the next day
        if ($timeOut->lt($timeIn)) {
            $timeOut->addDay();
        }

        $hours = $timeIn->diffInMinutes($timeOut) / 60;

        $description = $workLog->description;
        if (isset($validated['remarks']) && $validated['remarks']) {
            $description .= "\n(Manual Clock-out: ".$validated['remarks'].')';
        }

        $workLog->update([
            'time_out' => $validated['time_out'],
            'hours' => round($hours, 2),
            'status' => 'submitted', // Submit for approval
            'description' => $description,
        ]);

        return redirect()->back()->with('status', 'Manual clock-out submitted for approval.');
    }
}
