<?php

namespace App\Http\Controllers\Supervisor;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\Assignment;
use App\Models\User;
use App\Notifications\NewAnnouncementNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Illuminate\View\View;

class SupervisorAnnouncementController extends Controller
{
    public function index(): View
    {
        $announcements = Announcement::where('user_id', Auth::id())
            ->orWhere(function ($query) {
                // Announcements from Coordinators (audience 'all' or 'supervisors')
                $query->whereHas('user', function ($q) {
                    $q->where('role', User::ROLE_COORDINATOR);
                })->whereIn('audience', ['all', 'supervisors']);
            })
            ->latest()
            ->paginate(10);

        return view('supervisor.announcements.index', compact('announcements'));
    }

    public function create(): View
    {
        return view('supervisor.announcements.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'type' => 'required|in:announcement,update',
            'attachment' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,jpg,png,zip|max:10240', // 10MB max
        ]);

        $validated['user_id'] = Auth::id();
        $validated['audience'] = 'students'; // Supervisors always announce to students

        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $path = $file->store('announcements', 'public');
            $validated['attachment'] = $path;
            $validated['original_filename'] = $file->getClientOriginalName();
        }

        $announcement = Announcement::create($validated);

        // Send notifications to assigned students
        $supervisorId = Auth::id();
        $studentIds = Assignment::query()
            ->where('supervisor_id', $supervisorId)
            ->active()
            ->whereHas('student', fn ($q) => $q->eligibleStudentForRoster())
            ->pluck('student_id')
            ->unique();

        $students = User::eligibleStudentForRoster()->whereIn('id', $studentIds)->get();

        Notification::send($students, new NewAnnouncementNotification($announcement));

        return redirect()->route('supervisor.announcements.index')
            ->with('status', 'Announcement created successfully!');
    }
}
