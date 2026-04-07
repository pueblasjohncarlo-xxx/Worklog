<?php

namespace App\Http\Controllers;

use App\Models\Leave;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class LeaveController extends Controller
{
    public function print($id): View
    {
        $leave = Leave::with(['assignment.student', 'assignment.company', 'assignment.supervisor', 'assignment.ojtAdviser', 'reviewer'])
            ->findOrFail($id);

        $user = Auth::user();
        if (! $user) {
            abort(403);
        }

        $assignment = $leave->assignment;
        if (! $assignment || ! $assignment->student) {
            abort(404);
        }

        if ($user->role === User::ROLE_STUDENT && $assignment->student_id !== $user->id) {
            abort(403);
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
