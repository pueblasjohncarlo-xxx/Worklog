<?php

namespace App\Http\Controllers\Coordinator;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\User;
use App\Notifications\NewAnnouncementNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Illuminate\View\View;

class AnnouncementController extends Controller
{
    public function index(): View
    {
        $announcements = Announcement::latest()->paginate(10);

        return view('coordinator.announcements.index', compact('announcements'));
    }

    public function create(): View
    {
        return view('coordinator.announcements.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'type' => 'required|in:announcement,update',
            'audience' => 'required|in:all,students,supervisors',
            'attachment' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,jpg,png,zip|max:10240', // 10MB max
        ]);

        $validated['user_id'] = Auth::id();

        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $path = $file->store('announcements', 'public');
            $validated['attachment'] = $path;
            $validated['original_filename'] = $file->getClientOriginalName();
        }

        $announcement = Announcement::create($validated);

        // Send notifications
        $users = collect();
        if ($request->audience === 'all') {
            $users = User::whereIn('role', [User::ROLE_STUDENT, User::ROLE_SUPERVISOR])->get();
        } elseif ($request->audience === 'students') {
            $users = User::where('role', User::ROLE_STUDENT)->get();
        } elseif ($request->audience === 'supervisors') {
            $users = User::where('role', User::ROLE_SUPERVISOR)->get();
        }

        Notification::send($users, new NewAnnouncementNotification($announcement));

        return redirect()->route('coordinator.announcements.index')
            ->with('status', 'Announcement created successfully!');
    }
}
