<?php

namespace App\Http\Controllers;

use App\Http\Requests\Student\StoreWorkLogRequest;
use App\Http\Requests\Student\UpdateWorkLogRequest;
use App\Models\Assignment;
use App\Models\User;
use App\Models\WorkLog;
use App\Notifications\WorkLogSubmittedNotification;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
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
                ->whereNull('time_in')
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
            // DB requires a description; new workflow stores the accomplishment content in the uploaded template file.
            'description' => $validated['description'] ?? 'Submitted via accomplishment report template attachment.',
            'skills_applied' => $validated['skills_applied'] ?? null,
            'reflection' => $validated['reflection'] ?? null,
            'status' => 'draft',
        ];

        if ($request->hasFile('attachment')) {
            $path = $request->file('attachment')->store('accomplishment-reports', 'local');
            $data['attachment_path'] = $path;
            $data['attachment_disk'] = 'local';
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

        if (! array_key_exists('description', $data) || $data['description'] === null) {
            $data['description'] = $workLog->description ?? 'Submitted via accomplishment report template attachment.';
        }

        if ($request->hasFile('attachment')) {
            // Delete old attachment if exists
            if ($workLog->attachment_path) {
                $oldDisk = $workLog->attachment_disk ?: 'public';
                Storage::disk($oldDisk)->delete($workLog->attachment_path);
            }

            $path = $request->file('attachment')->store('accomplishment-reports', 'local');
            $data['attachment_path'] = $path;
            $data['attachment_disk'] = 'local';
        }

        // If editing a submitted/approved log, reset to draft
        $data['status'] = 'draft';
        // Reset approval info
        $data['reviewer_id'] = null;
        $data['reviewed_at'] = null;
        $data['grade'] = null;
        $data['reviewer_comment'] = null;

        $workLog->update($data);

        if ($request->boolean('submit_after_save')) {
            return $this->submit($request, $workLog->id);
        }

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

        $isAccomplishmentReport = $workLog->time_in === null
            && $workLog->time_out === null
            && in_array($workLog->type, ['daily', 'weekly', 'monthly'], true)
            && ! empty($workLog->attachment_path);

        // Attendance/HRS logs go to Supervisor; accomplishment reports go to Coordinator/OJT Adviser.
        $submittedTo = $isAccomplishmentReport ? 'coordinator' : 'supervisor';

        $workLog->update([
            'status' => 'submitted',
            'submitted_to' => $submittedTo,
        ]);

        if ($submittedTo === 'supervisor') {
            $assignment->loadMissing(['supervisor']);
            if ($assignment->supervisor) {
                $assignment->supervisor->notify(new WorkLogSubmittedNotification($workLog));
            }

            return redirect()->route('student.dashboard')
                ->with('status', 'Worklog submitted to Supervisor for approval.');
        }

        $assignment->loadMissing(['coordinator', 'ojtAdviser']);
        if ($assignment->coordinator) {
            $assignment->coordinator->notify(new WorkLogSubmittedNotification($workLog));
        }
        if ($assignment->ojtAdviser) {
            $assignment->ojtAdviser->notify(new WorkLogSubmittedNotification($workLog));
        }

        return redirect()->route('student.dashboard')
            ->with('status', 'Accomplishment report submitted to Coordinator/OJT Adviser for review.');
    }

    public function downloadAccomplishmentTemplate(Request $request)
    {
        $user = $request->user();
        if (! $user || $user->role !== User::ROLE_STUDENT) {
            abort(403);
        }

        $type = (string) $request->query('type', 'daily');
        if (! in_array($type, ['daily', 'weekly', 'monthly'], true)) {
            abort(404);
        }

        $workDateStr = (string) $request->query('work_date', now()->toDateString());
        try {
            $workDate = Carbon::parse($workDateStr);
        } catch (\Throwable) {
            $workDate = now();
        }

        $periodLabel = '';
        if ($type === 'daily') {
            $periodLabel = $workDate->format('M d, Y');
        } elseif ($type === 'weekly') {
            $start = $workDate->copy()->startOfWeek(\Carbon\CarbonInterface::MONDAY);
            $end = $workDate->copy()->endOfWeek(\Carbon\CarbonInterface::FRIDAY);
            $periodLabel = $start->format('M d, Y').' - '.$end->format('M d, Y');
        } else {
            $periodLabel = $workDate->format('F Y');
        }

        // If you have a manually created template file, place it here and it will be used.
        // Supported per-type filenames (first match wins): daily.docx|daily.doc|daily.odt (same for weekly/monthly)
        $templateBaseDir = storage_path('app/templates/accomplishment-report');
        $candidateTemplates = [
            $templateBaseDir.DIRECTORY_SEPARATOR.$type.'.docx',
            $templateBaseDir.DIRECTORY_SEPARATOR.$type.'.doc',
            $templateBaseDir.DIRECTORY_SEPARATOR.$type.'.odt',
        ];

        foreach ($candidateTemplates as $templatePath) {
            if (! is_file($templatePath)) {
                continue;
            }

            $ext = strtolower(pathinfo($templatePath, PATHINFO_EXTENSION));
            $downloadName = 'Accomplishment_Report_Template_'.Str::ucfirst($type).'.'.$ext;

            $contentType = match ($ext) {
                'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'odt' => 'application/vnd.oasis.opendocument.text',
                default => 'application/msword',
            };

            return response()->download($templatePath, $downloadName, [
                'Content-Type' => $contentType,
                'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
            ]);
        }

        $assignment = Assignment::with(['company'])
            ->where('student_id', $user->id)
            ->where('status', 'active')
            ->firstOrFail();

        $headerSrc = null;
        $footerSrc = null;

        $headerPath = public_path('assets/letterhead/llcc-header-exact.png');
        if (is_file($headerPath)) {
            $headerSrc = 'data:image/png;base64,'.base64_encode((string) file_get_contents($headerPath));
        }

        $footerPath = public_path('assets/letterhead/llcc-footer-exact.png');
        if (is_file($footerPath)) {
            $footerSrc = 'data:image/png;base64,'.base64_encode((string) file_get_contents($footerPath));
        }

        $hoursRendered = (string) $request->query('hours', '');

        $html = view('templates.accomplishment-report-word', [
            'type' => $type,
            'periodLabel' => $periodLabel,
            'studentName' => $user->name ?? 'N/A',
            'studentSection' => $user->section ?? 'N/A',
            'companyName' => $assignment->company?->name ?? 'N/A',
            'preparedDate' => now()->format('M d, Y'),
            'workDateLabel' => $workDate->format('M d, Y'),
            'hoursRendered' => $hoursRendered,
            'headerSrc' => $headerSrc,
            'footerSrc' => $footerSrc,
        ])->render();

        $safeDate = $workDate->format('Y-m-d');
        $fileName = 'Accomplishment_Report_'.Str::ucfirst($type).'_'.$safeDate.'.doc';

        return response($html, 200)
            ->header('Content-Type', 'application/msword; charset=UTF-8')
            ->header('Content-Disposition', 'attachment; filename="'.$fileName.'"')
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
    }

    public function downloadAttachment(Request $request, WorkLog $workLog)
    {
        if (! $workLog->attachment_path) {
            abort(404);
        }

        $user = $request->user();
        if (! $user) {
            abort(403);
        }

        $workLog->loadMissing(['assignment.student', 'assignment.supervisor', 'assignment.coordinator', 'assignment.ojtAdviser']);

        $assignment = $workLog->assignment;
        if (! $assignment) {
            abort(404);
        }

        $allowed = false;
        if ($user->role === User::ROLE_STUDENT) {
            $allowed = $assignment->student_id === $user->id;
        } elseif ($user->role === User::ROLE_SUPERVISOR) {
            $allowed = $assignment->supervisor_id === $user->id;
        } elseif ($user->role === User::ROLE_COORDINATOR) {
            $allowed = true;
        } elseif ($user->role === User::ROLE_OJT_ADVISER) {
            $allowed = $assignment->ojt_adviser_id === $user->id;
        } elseif (in_array($user->role, [User::ROLE_ADMIN, User::ROLE_STAFF], true)) {
            $allowed = true;
        }

        if (! $allowed) {
            abort(403);
        }

        $path = $workLog->attachment_path;
        $disk = $workLog->attachment_disk ?: 'public';

        // Backward-compatible disk fallback (older records may have been stored on a different disk).
        if (! Storage::disk($disk)->exists($path)) {
            if (Storage::disk('local')->exists($path)) {
                $disk = 'local';
            } elseif (Storage::disk('public')->exists($path)) {
                $disk = 'public';
            } else {
                abort(404);
            }
        }

        $baseName = basename($path);
        $dateLabel = $workLog->work_date?->format('Y-m-d') ?? 'date';
        $typeLabel = Str::ucfirst((string) ($workLog->type ?? 'report'));
        $studentSlug = Str::slug((string) ($assignment->student?->name ?? 'student'));
        $downloadName = $typeLabel.'_Accomplishment_'.$dateLabel.'_'.$studentSlug.'_'.$baseName;

        $disposition = $request->boolean('inline') ? 'inline' : 'attachment';

        return Storage::disk($disk)->response($path, $downloadName, [
            'Content-Disposition' => $disposition.'; filename="'.$downloadName.'"',
            'X-Content-Type-Options' => 'nosniff',
        ]);
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
