<?php

namespace App\Http\Controllers;

use App\Models\Leave;
use App\Models\User;
use App\Notifications\LeaveStatusUpdatedNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class AdminLeaveController extends Controller
{
    public function index(Request $request): View
    {
        $query = Leave::with(['assignment.student', 'assignment.company', 'reviewer'])
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

        return view('admin.leaves.index', compact('leaves'));
    }

    public function approve(Request $request, Leave $leave): RedirectResponse
    {
        $validated = $request->validate([
            'reviewer_remarks' => ['nullable', 'string', 'max:1000'],
        ]);

        if (! in_array($leave->status, [Leave::STATUS_SUBMITTED, Leave::STATUS_PENDING], true)) {
            return back()->withErrors(['leave' => 'Only submitted/pending leave requests can be approved.']);
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

        Log::info('Admin approved leave', [
            'leave_id' => $leave->id,
            'admin_id' => Auth::id(),
            'status' => $leave->status,
        ]);

        return back()->with('status', 'Leave request approved by admin.');
    }

    public function reject(Request $request, Leave $leave): RedirectResponse
    {
        $validated = $request->validate([
            'reviewer_remarks' => ['required', 'string', 'max:1000'],
        ]);

        if (! in_array($leave->status, [Leave::STATUS_SUBMITTED, Leave::STATUS_PENDING], true)) {
            return back()->withErrors(['leave' => 'Only submitted/pending leave requests can be rejected.']);
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

        Log::info('Admin rejected leave', [
            'leave_id' => $leave->id,
            'admin_id' => Auth::id(),
            'status' => $leave->status,
        ]);

        return back()->with('status', 'Leave request rejected by admin.');
    }
}
