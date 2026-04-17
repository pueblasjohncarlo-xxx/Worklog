<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\Leave;
use App\Models\User;
use App\Notifications\LeaveSubmittedNotification;
use App\Models\WorkLog;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class LeaveController extends Controller
{
    public function index(Request $request): View
    {
        $assignment = Assignment::resolveActiveForStudent((int) Auth::id());
        if ($assignment) {
            $assignment->loadMissing(['company', 'supervisor', 'ojtAdviser']);
        }

        $assignmentIds = Assignment::where('student_id', Auth::id())->pluck('id');

        $query = Leave::with(['assignment.company', 'assignment.student', 'reviewer'])
            ->whereIn('assignment_id', $assignmentIds)
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
                    ->orWhere('reviewer_remarks', 'like', '%'.$search.'%');
            });
        }

        $leaves = $query->paginate(20)->withQueryString();

        // Get leave balance and other enhancements
        $leaveBalance = $assignment ? $this->getLeaveBalance($assignment) : [];
        $leaveTypeDescriptions = self::getLeaveTypeDescriptions();

        return view('student.leaves.index', [
            'assignment' => $assignment,
            'leaves' => $leaves,
            'leaveBalance' => $leaveBalance,
            'leaveTypeDescriptions' => $leaveTypeDescriptions,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $assignment = Assignment::resolveActiveForStudent((int) Auth::id());

        if (! $assignment) {
            return redirect()->back()->withErrors([
                'leave' => 'No active assignment found.',
            ]);
        }

        // Check if required leave columns exist (for production migration safety)
        if (!Schema::hasColumn('leaves', 'number_of_days')) {
            Log::error('Required leave database columns missing', [
                'student_id' => Auth::id(),
                'missing_column' => 'number_of_days',
            ]);
            return redirect()->back()->withErrors([
                'leave' => 'System is currently being updated. Please try again in a few moments.',
            ]);
        }

        $action = $request->input('action', 'submit');
        $isDraft = $action === 'draft';
        Log::info('Student leave store requested', [
            'student_id' => Auth::id(),
            'action' => $action,
        ]);

        $validated = $request->validate([
            'type' => [$isDraft ? 'nullable' : 'required', 'string', 'max:50'],
            'start_date' => [$isDraft ? 'nullable' : 'required', 'date'],
            'end_date' => [$isDraft ? 'nullable' : 'required', 'date', 'after_or_equal:start_date'],
            'reason' => [$isDraft ? 'nullable' : 'required', 'string', 'max:2000'],
            'student_name' => ['nullable', 'string', 'max:255'],
            'course_major' => ['nullable', 'string', 'max:255'],
            'year_section' => ['nullable', 'string', 'max:255'],
            'cellphone_no' => ['nullable', 'string', 'max:255'],
            'company_name' => ['nullable', 'string', 'max:255'],
            'date_filed' => ['nullable', 'date'],
            'job_designation' => ['nullable', 'string', 'max:255'],
            'prepared_by' => ['nullable', 'string', 'max:255'],
            'attachment' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png,doc,docx', 'max:5120'],
        ]);

        $start = null;
        $end = null;
        $days = null;
        if (! empty($validated['start_date']) && ! empty($validated['end_date'])) {
            $start = Carbon::parse($validated['start_date'])->startOfDay();
            $end = Carbon::parse($validated['end_date'])->endOfDay();
            $days = $start->diffInDays($end) + 1;
        }

        if (! $isDraft && $days !== null && $days > 30) {
            return back()->withErrors(['end_date' => 'Leave request cannot exceed 30 days.'])->withInput();
        }

        if (! $isDraft && $start && $end) {
            $hasOpenAttendance = WorkLog::where('assignment_id', $assignment->id)
                ->whereBetween('work_date', [$start->toDateString(), $end->toDateString()])
                ->whereNotNull('time_in')
                ->whereNull('time_out')
                ->exists();

            if ($hasOpenAttendance) {
                return back()
                    ->withErrors(['start_date' => 'Please clock out all open attendance logs before submitting leave.'])
                    ->withInput();
            }

            $hasOverlap = Leave::where('assignment_id', $assignment->id)
                ->whereIn('status', [Leave::STATUS_SUBMITTED, Leave::STATUS_PENDING, Leave::STATUS_APPROVED])
                ->whereDate('start_date', '<=', $end->toDateString())
                ->whereDate('end_date', '>=', $start->toDateString())
                ->exists();

            if ($hasOverlap) {
                return back()
                    ->withErrors(['start_date' => 'Leave dates overlap with an existing submitted/pending/approved leave request.'])
                    ->withInput();
            }
        }

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('leave_attachments', 'public');
        }

        $status = $isDraft ? Leave::STATUS_DRAFT : Leave::STATUS_SUBMITTED;

        $leave = Leave::create([
            'assignment_id' => $assignment->id,
            'type' => $validated['type'] ?? 'Draft',
            'start_date' => $start?->toDateString(),
            'end_date' => $end?->toDateString(),
            'number_of_days' => $days,
            'reason' => $validated['reason'] ?? null,
            'attachment_path' => $attachmentPath,
            'status' => $status,
            'submitted_at' => $isDraft ? null : now(),
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

        if (! $isDraft) {
            $recipients = collect();
            if ($assignment->supervisor) {
                $recipients->push($assignment->supervisor);
            }
            $adminRecipients = User::where('role', User::ROLE_ADMIN)->get();
            $recipients = $recipients->merge($adminRecipients)->unique('id');

            foreach ($recipients as $recipient) {
                $recipient->notify(new LeaveSubmittedNotification($leave->load('assignment.student')));
            }

            Log::info('Leave submission notified reviewers', [
                'leave_id' => $leave->id,
                'recipient_ids' => $recipients->pluck('id')->all(),
                'status' => $leave->status,
            ]);
        }

        Log::info('Student leave stored', [
            'student_id' => Auth::id(),
            'leave_id' => $leave->id,
            'status' => $leave->status,
        ]);

        return redirect()->route('student.leaves.index')->with('status', $isDraft ? 'Leave draft saved.' : 'Leave request submitted successfully.');
    }

    public function edit(Leave $leave): View
    {
        $this->authorizeStudentLeave($leave);
        if (! in_array($leave->status, [Leave::STATUS_DRAFT, Leave::STATUS_REJECTED], true)) {
            abort(403, 'Only draft or rejected leaves can be edited.');
        }

        $assignment = Assignment::with(['company', 'supervisor'])
            ->where('student_id', Auth::id())
            ->where('status', 'active')
            ->first();

        return view('student.leaves.edit', compact('leave', 'assignment'));
    }

    public function update(Request $request, Leave $leave): RedirectResponse
    {
        $this->authorizeStudentLeave($leave);
        if (! in_array($leave->status, [Leave::STATUS_DRAFT, Leave::STATUS_REJECTED], true)) {
            return back()->withErrors(['leave' => 'Only draft or rejected leaves can be updated.']);
        }

        // Check if required leave columns exist (for production migration safety)
        if (!Schema::hasColumn('leaves', 'number_of_days')) {
            Log::error('Required leave database columns missing', [
                'student_id' => Auth::id(),
                'missing_column' => 'number_of_days',
            ]);
            return back()->withErrors([
                'leave' => 'System is currently being updated. Please try again in a few moments.',
            ]);
        }

        $action = $request->input('action', 'submit');
        $isDraft = $action === 'draft';
        Log::info('Student leave update requested', [
            'student_id' => Auth::id(),
            'leave_id' => $leave->id,
            'action' => $action,
        ]);
        $validated = $request->validate([
            'type' => [$isDraft ? 'nullable' : 'required', 'string', 'max:50'],
            'start_date' => [$isDraft ? 'nullable' : 'required', 'date'],
            'end_date' => [$isDraft ? 'nullable' : 'required', 'date', 'after_or_equal:start_date'],
            'reason' => [$isDraft ? 'nullable' : 'required', 'string', 'max:2000'],
            'attachment' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png,doc,docx', 'max:5120'],
        ]);

        $start = ! empty($validated['start_date']) ? Carbon::parse($validated['start_date'])->startOfDay() : null;
        $end = ! empty($validated['end_date']) ? Carbon::parse($validated['end_date'])->endOfDay() : null;
        $days = ($start && $end) ? ($start->diffInDays($end) + 1) : null;

        if (! $isDraft && $days !== null && $days > 30) {
            return back()->withErrors(['end_date' => 'Leave request cannot exceed 30 days.'])->withInput();
        }

        $attachmentPath = $leave->attachment_path;
        if ($request->hasFile('attachment')) {
            if ($attachmentPath) {
                Storage::disk('public')->delete($attachmentPath);
            }
            $attachmentPath = $request->file('attachment')->store('leave_attachments', 'public');
        }

        $status = $isDraft ? Leave::STATUS_DRAFT : Leave::STATUS_SUBMITTED;
        $leave->update([
            'type' => $validated['type'] ?? $leave->type,
            'start_date' => $start?->toDateString(),
            'end_date' => $end?->toDateString(),
            'number_of_days' => $days,
            'reason' => $validated['reason'] ?? $leave->reason,
            'attachment_path' => $attachmentPath,
            'status' => $status,
            'submitted_at' => $isDraft ? null : now(),
            'reviewed_at' => null,
            'reviewer_id' => null,
            'reviewer_remarks' => null,
        ]);

        if (! $isDraft) {
            $assignment = $leave->assignment;
            $recipients = collect();
            if ($assignment?->supervisor) {
                $recipients->push($assignment->supervisor);
            }
            $adminRecipients = User::where('role', User::ROLE_ADMIN)->get();
            $recipients = $recipients->merge($adminRecipients)->unique('id');

            foreach ($recipients as $recipient) {
                $recipient->notify(new LeaveSubmittedNotification($leave->fresh()->load('assignment.student')));
            }
        }

        return redirect()->route('student.leaves.index')->with('status', $isDraft ? 'Leave draft updated.' : 'Leave request resubmitted successfully.');
    }

    public function destroy(Leave $leave): RedirectResponse
    {
        $this->authorizeStudentLeave($leave);
        if ($leave->status !== Leave::STATUS_DRAFT) {
            return back()->withErrors(['leave' => 'Only draft leaves can be deleted.']);
        }

        if ($leave->attachment_path) {
            Storage::disk('public')->delete($leave->attachment_path);
        }

        $leave->delete();
        Log::info('Student leave draft deleted', [
            'student_id' => Auth::id(),
            'leave_id' => $leave->id,
        ]);
        return back()->with('status', 'Draft leave deleted.');
    }

    public function cancel(Request $request, Leave $leave): RedirectResponse
    {
        $this->authorizeStudentLeave($leave);
        if (! in_array($leave->status, [Leave::STATUS_SUBMITTED, Leave::STATUS_PENDING], true)) {
            return back()->withErrors(['leave' => 'Only submitted/pending leaves can be cancelled.']);
        }

        $validated = $request->validate([
            'cancellation_reason' => ['nullable', 'string', 'max:1000'],
        ]);

        $leave->update([
            'status' => Leave::STATUS_CANCELLED,
            'cancelled_at' => now(),
            'cancellation_reason' => $validated['cancellation_reason'] ?? null,
        ]);

        Log::info('Student leave cancelled', [
            'student_id' => Auth::id(),
            'leave_id' => $leave->id,
            'status' => $leave->status,
        ]);

        return back()->with('status', 'Leave request cancelled.');
    }

    private function authorizeStudentLeave(Leave $leave): void
    {
        $assignmentIds = Assignment::where('student_id', Auth::id())->pluck('id');
        if (! $assignmentIds->contains($leave->assignment_id)) {
            abort(403, 'Unauthorized leave access.');
        }
    }

    /**
     * Calculate and return leave balance information for the assignment
     * Safe version that handles missing columns gracefully for production
     */
    public function getLeaveBalance(Assignment $assignment): array
    {
        try {
            // Check if number_of_days column exists before querying
            if (!Schema::hasColumn('leaves', 'number_of_days')) {
                Log::warning('number_of_days column missing from leaves table', [
                    'method' => 'getLeaveBalance',
                    'assignment_id' => $assignment->id,
                ]);
                // Return safe defaults if column doesn't exist yet
                return $this->getDefaultLeaveBalance($assignment);
            }

            $totalApproved = Leave::where('assignment_id', $assignment->id)
                ->where('status', Leave::STATUS_APPROVED)
                ->sum('number_of_days') ?? 0;

            $totalPending = Leave::where('assignment_id', $assignment->id)
                ->where('status', Leave::STATUS_PENDING)
                ->sum('number_of_days') ?? 0;

            $annualLimit = $assignment->annual_leave_limit ?? 15;
            $sickLeaveLimit = $assignment->sick_leave_limit ?? 10;

            $approvedAnnual = Leave::where('assignment_id', $assignment->id)
                ->where('status', Leave::STATUS_APPROVED)
                ->where('type', 'Annual')
                ->sum('number_of_days') ?? 0;

            $approvedSick = Leave::where('assignment_id', $assignment->id)
                ->where('status', Leave::STATUS_APPROVED)
                ->where('type', 'Sick Leave')
                ->sum('number_of_days') ?? 0;

            return [
                'total_approved' => $totalApproved,
                'total_pending' => $totalPending,
                'annual_limit' => $annualLimit,
                'annual_used' => $approvedAnnual,
                'annual_remaining' => max(0, $annualLimit - $approvedAnnual),
                'sick_limit' => $sickLeaveLimit,
                'sick_used' => $approvedSick,
                'sick_remaining' => max(0, $sickLeaveLimit - $approvedSick),
                'can_submit' => true,
            ];
        } catch (\Exception $e) {
            Log::error('Error calculating leave balance', [
                'error' => $e->getMessage(),
                'assignment_id' => $assignment->id,
            ]);
            // Return safe defaults on any error
            return $this->getDefaultLeaveBalance($assignment);
        }
    }

    /**
     * Return default leave balance if column doesn't exist yet
     * Used as fallback during production migration
     */
    private function getDefaultLeaveBalance(Assignment $assignment): array
    {
        return [
            'total_approved' => 0,
            'total_pending' => 0,
            'annual_limit' => $assignment->annual_leave_limit ?? 15,
            'annual_used' => 0,
            'annual_remaining' => $assignment->annual_leave_limit ?? 15,
            'sick_limit' => $assignment->sick_leave_limit ?? 10,
            'sick_used' => 0,
            'sick_remaining' => $assignment->sick_leave_limit ?? 10,
            'can_submit' => true,
        ];
    }

    /**
     * Get leave type descriptions for better UX
     */
    public static function getLeaveTypeDescriptions(): array
    {
        return [
            'Sick Leave' => 'For illness or medical reasons',
            'Discretionary' => 'Personal or family matters',
            'Maternity' => 'Maternity or parental leave',
            'Exam' => 'Educational examination purposes',
            'Bereavement' => 'Death of family member',
            'No Pay Leave' => 'Unpaid leave (deducted from salary)',
            'Annual' => 'Regular annual leave',
            'Vacation' => 'Vacation and rest days',
        ];
    }

    /**
     * Get approval timeline estimate
     */
    public static function getApprovalTimeline(string $leaveType): string
    {
        return match($leaveType) {
            'Sick Leave' => '1-2 business days',
            'Exam' => '2-3 business days',
            'Annual', 'Vacation' => '3-5 business days',
            'Maternity', 'Bereavement' => 'Same day - 1 business day',
            'No Pay Leave' => '5-7 business days',
            'Discretionary' => '3-5 business days',
            default => '2-3 business days'
        };
    }
}
