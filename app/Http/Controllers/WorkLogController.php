<?php

namespace App\Http\Controllers;

use App\Http\Requests\Student\StoreWorkLogRequest;
use App\Http\Requests\Student\UpdateWorkLogRequest;
use App\Models\Assignment;
use App\Models\User;
use App\Models\WorkLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class WorkLogController extends Controller
{
    public function create(Request $request): View
    {
        $user = Auth::user();
        $type = $request->query('type', 'daily');
        $date = $request->query('date', now()->format('Y-m-d'));

        $assignment = Assignment::where('student_id', $user->id)
            ->where('status', 'active')
            ->firstOrFail();

        $attendance = null;
        $approvedDates = collect();
        if ($type === 'daily') {
            // List of approved attendance dates without an existing DAILY journal
            $approvedAttendanceDates = WorkLog::where('assignment_id', $assignment->id)
                ->whereNotNull('time_in')
                ->where('status', 'approved')
                ->orderByDesc('work_date')
                ->pluck('work_date')
                ->map(fn ($d) => $d->toDateString())
                ->unique()
                ->values();

            $datesWithDailyJournal = WorkLog::where('assignment_id', $assignment->id)
                ->where('type', 'daily')
                ->whereNull('time_in') // actual journal entries (not attendance)
                ->pluck('work_date')
                ->map(fn ($d) => $d->toDateString())
                ->unique()
                ->values();

            $allowedDates = collect($approvedAttendanceDates)->diff($datesWithDailyJournal)->values();

            $approvedDates = $allowedDates->map(fn ($s) => \Carbon\Carbon::parse($s));

            $approvedStrings = $allowedDates;
            if (! $approvedStrings->contains($date) && $approvedStrings->isNotEmpty()) {
                $date = $approvedStrings->first();
            }

            if ($date) {
                $attendance = WorkLog::where('assignment_id', $assignment->id)
                    ->where('work_date', $date)
                    ->whereNotNull('time_in')
                    ->first();
            }
        }

        return view('student.worklogs.create', [
            'assignment' => $assignment,
            'type' => $type,
            'date' => $date,
            'attendance' => $attendance,
            'approvedDates' => $approvedDates,
        ]);
    }

    public function store(StoreWorkLogRequest $request): RedirectResponse
    {
        $user = $request->user();
        $validated = $request->validated();
        $type = $request->input('type', 'daily');

        $assignment = Assignment::where('student_id', $user->id)
            ->where('status', 'active')
            ->firstOrFail();

        if ($type === 'daily') {
            $hasApprovedAttendance = WorkLog::where('assignment_id', $assignment->id)
                ->where('work_date', $validated['work_date'])
                ->whereNotNull('time_in')
                ->whereNotNull('time_out')
                ->where('status', 'approved')
                ->exists();

            $existingDailyReport = WorkLog::where('assignment_id', $assignment->id)
                ->where('type', 'daily')
                ->where('work_date', $validated['work_date'])
                ->exists();

            if (! $hasApprovedAttendance) {
                return redirect()->route('student.dashboard')
                    ->with('error', 'You can only submit a daily accomplishment report after your attendance has been approved.');
            }

            if ($existingDailyReport) {
                return redirect()->route('student.dashboard')
                    ->with('error', 'A daily accomplishment report for this date already exists.');
            }
        }

        $data = [
            'assignment_id' => $assignment->id,
            'type' => $type,
            'work_date' => $validated['work_date'],
            'hours' => $validated['hours'],
            'description' => $validated['description'],
            'skills_applied' => $validated['skills_applied'] ?? null,
            'reflection' => $validated['reflection'] ?? null,
            'status' => 'draft',
        ];

        if ($request->hasFile('attachment')) {
            $path = $request->file('attachment')->store('attachments', 'public');
            $data['attachment_path'] = $path;
        }

        WorkLog::create($data);

        return redirect()->route('student.dashboard')
            ->with('status', 'Worklog created successfully.');
    }

    public function edit($id): View
    {
        $workLog = WorkLog::findOrFail($id);
        $user = Auth::user();

        // Load assignment explicitly
        $assignment = Assignment::where('id', $workLog->assignment_id)
            ->where('student_id', $user->id)
            ->first();

        if (! $assignment) {
            $userRole = $user->role;
            $userId = $user->id;
            abort(403, "Forbidden: Kini nga worklog (ID: {$workLog->id}) wala ma-assign sa imong account (User ID: {$userId}, Role: {$userRole}). Assignment ID: {$workLog->assignment_id}");
        }

        return view('student.worklogs.edit', [
            'workLog' => $workLog,
        ]);
    }

    public function update(UpdateWorkLogRequest $request, $id): RedirectResponse
    {
        $workLog = WorkLog::findOrFail($id);
        $user = $request->user();

        $assignment = Assignment::where('id', $workLog->assignment_id)
            ->where('student_id', $user->id)
            ->first();

        if (! $assignment) {
            abort(403);
        }

        $data = $request->validated();

        if ($request->hasFile('attachment')) {
            // Delete old attachment if exists
            if ($workLog->attachment_path) {
                Storage::disk('public')->delete($workLog->attachment_path);
            }
            $path = $request->file('attachment')->store('attachments', 'public');
            $data['attachment_path'] = $path;
        }

        // If editing a submitted/approved log, reset to draft
        $data['status'] = 'draft';
        // Reset approval info
        $data['reviewer_id'] = null;
        $data['reviewed_at'] = null;
        $data['grade'] = null;
        $data['reviewer_comment'] = null;

        $workLog->update($data);

        return redirect()->route('student.dashboard')
            ->with('status', 'Worklog updated and reset to draft. Please submit again.');
    }

    public function submit(Request $request, $id): RedirectResponse
    {
        $workLog = WorkLog::findOrFail($id);
        $user = Auth::user();

        $assignment = Assignment::where('id', $workLog->assignment_id)
            ->where('student_id', $user->id)
            ->first();

        if (! $assignment || $workLog->status !== 'draft') {
            abort(403);
        }

        // Force submission to supervisor
        $submittedTo = 'supervisor';

        $workLog->update([
            'status' => 'submitted',
            'submitted_to' => $submittedTo,
        ]);

        return redirect()->route('student.dashboard')
            ->with('status', 'Worklog submitted to Supervisor for approval.');
    }

    public function print($id): View
    {
        $workLog = WorkLog::with(['assignment.student', 'assignment.company', 'assignment.supervisor', 'assignment.ojtAdviser'])->findOrFail($id);
        $user = Auth::user();

        if (! $user) {
            abort(403);
        }

        if (! $workLog->assignment || ! $workLog->assignment->student) {
            abort(404);
        }

        // Check if student owns it, or if reviewer/coordinator/admin
        if ($user->role === User::ROLE_STUDENT && $workLog->assignment->student_id !== $user->id) {
            abort(403);
        }

        if ($user->role === User::ROLE_SUPERVISOR && $workLog->assignment->supervisor_id !== $user->id) {
            abort(403);
        }

        if ($user->role === User::ROLE_OJT_ADVISER && $workLog->assignment->ojt_adviser_id !== $user->id) {
            abort(403);
        }

        $reports = collect([$workLog]);
        if ($workLog->type === 'daily') {
            // Fetch all daily reports for the same week
            $startOfWeek = $workLog->work_date->copy()->startOfWeek(\Carbon\CarbonInterface::MONDAY);
            $endOfWeek = $workLog->work_date->copy()->endOfWeek(\Carbon\CarbonInterface::FRIDAY);

            $reports = WorkLog::where('assignment_id', $workLog->assignment_id)
                ->where('type', 'daily')
                ->whereBetween('work_date', [$startOfWeek, $endOfWeek])
                ->orderBy('work_date')
                ->get();
        } elseif ($workLog->type === 'weekly') {
            // Fetch all weekly reports for the same month
            $startOfMonth = $workLog->work_date->copy()->startOfMonth();
            $endOfMonth = $workLog->work_date->copy()->endOfMonth();

            $reports = WorkLog::where('assignment_id', $workLog->assignment_id)
                ->where('type', 'weekly')
                ->whereBetween('work_date', [$startOfMonth, $endOfMonth])
                ->orderBy('work_date')
                ->get();
        }

        $view = match ($workLog->type) {
            'weekly' => 'student.worklogs.print_weekly',
            'monthly' => 'student.worklogs.print_monthly',
            default => 'student.worklogs.print_daily',
        };

        return view($view, [
            'report' => $workLog,
            'reports' => $reports,
            'student' => $workLog->assignment->student,
            'assignment' => $workLog->assignment,
        ]);
    }
}
