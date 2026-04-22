<?php

namespace App\Http\Controllers;

use App\Models\Leave;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class LeaveController extends Controller
{
    public function print($id): View|RedirectResponse
    {
        $leave = Leave::with(['assignment.student', 'assignment.company', 'assignment.supervisor', 'assignment.ojtAdviser', 'reviewer'])
            ->findOrFail($id);

        $user = Auth::user();
        if (! $user) {
            abort(403);
        }

        if ($user->role === User::ROLE_STUDENT) {
            return redirect()->route('student.dashboard')->with('status', 'Leave requests are no longer available from the student menu.');
        }

        $assignment = $leave->assignment;
        if (! $assignment || ! $assignment->student) {
            abort(404);
        }

        if ($user->role === User::ROLE_SUPERVISOR && $assignment->supervisor_id !== $user->id) {
            abort(403);
        }

        if ($user->role === User::ROLE_OJT_ADVISER && $assignment->ojt_adviser_id !== $user->id) {
            abort(403);
        }

        return view('student.leaves.print', [
            'leave' => $leave,
            'assignment' => $assignment,
            'student' => $assignment->student,
        ]);
    }
}
