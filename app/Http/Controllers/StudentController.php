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
    public function index(Request $request): View
    {
        $user = Auth::user();

        $assignment = Assignment::with(['company', 'supervisor'])
            ->where('student_id', $user->id)
            ->where('status', 'active')
            ->first();

        $workLogs = collect();
        $todayLog = null;
        $activeTasks = collect();
        $pastPendingLog = null;
        $earlyClockOut = false;
        $needsDailyReportReminder = false;
        $attendanceCalendar = collect();

        $attendanceMonth = $request->input('attendance_month', Carbon::now()->format('Y-m'));
        if (!preg_match('/^\d{4}-\d{2}$/', $attendanceMonth)) {
            $attendanceMonth = Carbon::now()->format('Y-m');
        }

        $calendarCurrentDate = Carbon::createFromFormat('Y-m', $attendanceMonth)->startOfMonth();

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

            $earlyClockOut = false;
            $needsDailyReportReminder = false;

            if ($todayLog && $todayLog->time_in && ! $todayLog->time_out) {
                $clockInTime = Carbon::parse($todayLog->time_in);
                $elapsedHours = $clockInTime->diffInHours(now());
                $earlyClockOut = $elapsedHours < 8;
            }

            // If today's attendance is approved and no daily accomplishment report exists, show reminder.
            if ($todayLog && $todayLog->time_in && $todayLog->time_out && $todayLog->status === 'approved') {
                $dailyReportExists = WorkLog::where('assignment_id', $assignment->id)
                    ->where('type', 'daily')
                    ->where('work_date', now()->toDateString())
                    ->exists();

                $needsDailyReportReminder = ! $dailyReportExists;
            }

            // Weekly Hours for Bar Chart (original, but replaced in UI by calendar)
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

            // Calendar for the selected month (attendance statuses)
            $startOfMonth = $calendarCurrentDate->copy()->startOfMonth();
            $endOfMonth = $calendarCurrentDate->copy()->endOfMonth();
            $currentDay = $startOfMonth->copy()->startOfWeek(Carbon::SUNDAY);
            $endDay = $endOfMonth->copy()->endOfWeek(Carbon::SATURDAY);

            $monthWorkLogs = WorkLog::where('assignment_id', $assignment->id)
                ->whereBetween('work_date', [$startOfMonth->toDateString(), $endOfMonth->toDateString()])
                ->get();

            $monthlyTotalHours = $monthWorkLogs->sum('hours');
            $monthlyApprovedHours = $monthWorkLogs->where('status', 'approved')->sum('hours');
            $monthlyPendingHours = $monthWorkLogs->where('status', 'submitted')->sum('hours');
            $monthlyRejectedHours = $monthWorkLogs->where('status', 'rejected')->sum('hours');
            $monthlyRemainingHours = max(0, $assignment->required_hours - $monthlyApprovedHours);

            $dayLogsKeyed = $monthWorkLogs->keyBy(fn ($log) => $log->work_date?->toDateString());
            $attendanceCalendar = [];

            while ($currentDay <= $endDay) {
                $dayString = $currentDay->toDateString();
                $dayLog = $dayLogsKeyed->get($dayString);

                $attendanceCalendar[] = [
                    'date' => $currentDay->copy(),
                    'is_current_month' => $currentDay->month === $calendarCurrentDate->month,
                    'status' => $dayLog ? $dayLog->status : null,
                    'hours' => $dayLog ? $dayLog->hours : null,
                    'time_in' => $dayLog ? $dayLog->time_in : null,
                    'time_out' => $dayLog ? $dayLog->time_out : null,
                ];

                $currentDay->addDay();
            }

            $pendingHours = $workLogs->where('status', 'submitted')->sum('hours');
            $rejectedHours = $workLogs->where('status', 'rejected')->sum('hours');

            // Completion Stats for Doughnut Chart
            $completedHours = $assignment->totalApprovedHours();
            $remainingHours = max(0, $assignment->required_hours - $completedHours);
            $completionStats = [
                'Completed' => $completedHours,
                'Pending' => $pendingHours,
                'Rejected' => $rejectedHours,
                'Remaining' => $remainingHours,
            ];
        } else {
            $weeklyHours = collect();
            $completionStats = ['Completed' => 0, 'Remaining' => 0];
        }

        return view('dashboards.student', [
            'assignment' => $assignment,
            'workLogs' => $workLogs,
            'todayLog' => $todayLog,
            'activeTasks' => $activeTasks,
            'pastPendingLog' => $pastPendingLog,
            'weeklyHours' => $weeklyHours,
            'completionStats' => $completionStats,
            'attendanceCalendar' => $attendanceCalendar,
            'calendarCurrentDate' => $calendarCurrentDate,
            'monthlyTotalHours' => $monthlyTotalHours ?? 0,
            'monthlyApprovedHours' => $monthlyApprovedHours ?? 0,
            'monthlyPendingHours' => $monthlyPendingHours ?? 0,
            'monthlyRejectedHours' => $monthlyRejectedHours ?? 0,
            'monthlyRemainingHours' => $monthlyRemainingHours ?? 0,
            'earlyClockOut' => $earlyClockOut,
            'needsDailyReportReminder' => $needsDailyReportReminder,
        ]);
    }

    public function clockIn(Request $request): RedirectResponse
    {
        $request->validate([
            'time_in' => 'required|date_format:H:i',
        ]);

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

        $timeIn = Carbon::parse($request->input('time_in'))->format('H:i:s');

        WorkLog::create([
            'assignment_id' => $assignment->id,
            'work_date' => now()->toDateString(),
            'time_in' => $timeIn,
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

        $now = now();

        // Parse time_in safely in case it is string or Carbon instance.
        if ($log->time_in instanceof Carbon) {
            $timeInString = $log->time_in->format('H:i:s');
        } else {
            $timeInString = Carbon::parse($log->time_in)->format('H:i:s');
        }

        $startTime = Carbon::parse($log->work_date->format('Y-m-d').' '.$timeInString);
        $hoursElapsed = $startTime->diffInHours($now);

        $earlyReason = $request->input('early_reason');

        if ($hoursElapsed < 8 && ! $earlyReason) {
            return redirect()->back()->with('error', 'You are clocking out early. Please provide an early departure reason first.');
        }

        $description = $log->description;
        if ($earlyReason) {
            $description .= "\n(Extra: Early clock-out reason: {$earlyReason})";
        }

        $log->update([
            'time_out' => $now->toTimeString(),
            'hours' => $hoursElapsed,
            'status' => 'submitted', // Auto-submit for approval
            'description' => $description,
        ]);

        // Recalculate precise hours
        $timeInValue = $log->time_in;
        if ($timeInValue instanceof Carbon) {
            $timeInString = $timeInValue->format('H:i:s');
        } else {
            $timeInString = Carbon::parse($timeInValue)->format('H:i:s');
        }

        $start = Carbon::parse($log->work_date->format('Y-m-d').' '.$timeInString);
        $end = now();
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

    public function updateTaskStatus(Request $request, Task $task): RedirectResponse
    {
        $request->validate([
            'status' => 'required|in:pending,in_progress,completed',
        ]);

        // Check if task belongs to the authenticated student
        $user = Auth::user();
        $assignment = Assignment::where('student_id', $user->id)
            ->where('id', $task->assignment_id)
            ->firstOrFail();

        $task->update([
            'status' => $request->status,
        ]);

        return redirect()->back()->with('status', 'Task status updated successfully.');
    }

    public function leavesIndex(): View
    {
        $assignment = Assignment::with(['company', 'supervisor', 'ojtAdviser'])
            ->where('student_id', Auth::id())
            ->where('status', 'active')
            ->first();

        $assignmentIds = Assignment::where('student_id', Auth::id())->pluck('id');

        $leaves = Leave::with(['assignment.company', 'assignment.student'])
            ->whereIn('assignment_id', $assignmentIds)
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('student.leaves.index', [
            'assignment' => $assignment,
            'leaves' => $leaves,
        ]);
    }

    public function leavesStore(Request $request): RedirectResponse
    {
        $assignment = Assignment::where('student_id', Auth::id())
            ->where('status', 'active')
            ->first();

        if (! $assignment) {
            return redirect()->back()->withErrors([
                'leave' => 'No active assignment found.',
            ]);
        }

        $validated = $request->validate([
            'type' => ['required', 'string', 'max:50'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'reason' => ['nullable', 'string', 'max:2000'],
            'student_name' => ['nullable', 'string', 'max:255'],
            'course_major' => ['nullable', 'string', 'max:255'],
            'year_section' => ['nullable', 'string', 'max:255'],
            'cellphone_no' => ['nullable', 'string', 'max:255'],
            'company_name' => ['nullable', 'string', 'max:255'],
            'date_filed' => ['nullable', 'date'],
            'job_designation' => ['nullable', 'string', 'max:255'],
            'prepared_by' => ['nullable', 'string', 'max:255'],
        ]);

        $start = Carbon::parse($validated['start_date'])->startOfDay();
        $end = Carbon::parse($validated['end_date'])->endOfDay();

        $hasAttendance = WorkLog::where('assignment_id', $assignment->id)
            ->whereBetween('work_date', [$start->toDateString(), $end->toDateString()])
            ->whereNotNull('time_in')
            ->exists();

        if ($hasAttendance) {
            return redirect()->back()
                ->withErrors(['start_date' => 'Attendance exists within the selected dates.'])
                ->withInput();
        }

        $leave = Leave::create([
            'assignment_id' => $assignment->id,
            'type' => $validated['type'],
            'start_date' => $start->toDateString(),
            'end_date' => $end->toDateString(),
            'reason' => $validated['reason'] ?? null,
            'status' => 'pending',
            'student_name' => $validated['student_name'] ?? null,
            'course_major' => $validated['course_major'] ?? null,
            'year_section' => $validated['year_section'] ?? null,
            'cellphone_no' => $validated['cellphone_no'] ?? null,
            'company_name' => $validated['company_name'] ?? null,
            'date_filed' => $validated['date_filed'] ?? now()->toDateString(),
            'job_designation' => $validated['job_designation'] ?? null,
            'prepared_by' => $validated['prepared_by'] ?? null,
        ]);

        // Save signature if provided (base64 PNG)
        if ($request->filled('signature')) {
            $dataUrl = $request->input('signature');
            if (str_starts_with($dataUrl, 'data:image')) {
                [$meta, $content] = explode(',', $dataUrl, 2);
                $binary = base64_decode($content);
                $path = 'signatures/leave_'.$leave->id.'.png';
                Storage::disk('public')->put($path, $binary);
                $leave->update(['signature_path' => $path]);
            }
        }

        return redirect()->route('student.leaves.index')->with('status', 'Leave request submitted.');
    }
}
