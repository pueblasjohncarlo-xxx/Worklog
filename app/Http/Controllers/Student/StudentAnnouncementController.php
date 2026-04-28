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

        $announcements = Announcement::with(['user', 'recipients:id,name'])
            ->where(function ($query) {
            // Announcements from Coordinators (audience 'all' or 'students')
                $query->whereHas('user', function ($q) {
                    $q->where('role', User::ROLE_COORDINATOR);
                })->whereIn('audience', ['all', 'students']);
            })->orWhere(function ($query) use ($supervisorId, $user) {
                // Announcements from MY Supervisor (legacy all-student posts or targeted recipient posts)
                if ($supervisorId) {
                    $query->where('user_id', $supervisorId)
                        ->where('audience', 'students')
                        ->where(function ($supervisorAnnouncements) use ($user) {
                            $supervisorAnnouncements->whereDoesntHave('recipients')
                                ->orWhereHas('recipients', function ($recipientQuery) use ($user) {
                                    $recipientQuery->where('users.id', $user->id);
                                });
                        });
                }
            })
            ->latest()
            ->paginate(10);

        return view('student.announcements.index', compact('announcements'));
    }
}
