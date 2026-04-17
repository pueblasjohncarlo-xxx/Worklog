<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\Assignment;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class StudentAnnouncementController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();

        $assignment = Assignment::resolveActiveForStudent($user->id);
        $supervisorId = $assignment?->supervisor_id;

        $announcements = Announcement::where(function ($query) {
            // Announcements from Coordinators (audience 'all' or 'students')
            $query->whereHas('user', function ($q) {
                $q->where('role', User::ROLE_COORDINATOR);
            })->whereIn('audience', ['all', 'students']);
        })->orWhere(function ($query) use ($supervisorId) {
            // Announcements from MY Supervisor (audience 'students')
            if ($supervisorId) {
                $query->where('user_id', $supervisorId)
                    ->where('audience', 'students');
            }
        })
            ->latest()
            ->paginate(10);

        return view('student.announcements.index', compact('announcements'));
    }
}
