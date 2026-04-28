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
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class SupervisorAnnouncementController extends Controller
{
    public function index(): View
    {
        $recipientTargetingEnabled = Announcement::recipientsTableExists();
        $withRelations = ['user'];

        if ($recipientTargetingEnabled) {
            $withRelations[] = 'recipients:id,name';
        }

        $announcements = Announcement::with($withRelations)
            ->where('user_id', Auth::id())
            ->orWhere(function ($query) {
                // Announcements from Coordinators (audience 'all' or 'supervisors')
                $query->whereHas('user', function ($q) {
                    $q->where('role', User::ROLE_COORDINATOR);
                })->whereIn('audience', ['all', 'supervisors']);
            })
            ->latest()
            ->paginate(10);

        return view('supervisor.announcements.index', compact('announcements', 'recipientTargetingEnabled'));
    }

    public function create(): View
    {
        $assignedStudents = Assignment::rosterForSupervisor((int) Auth::id(), ['student', 'company'])
            ->map(function (Assignment $assignment) {
                $student = $assignment->student;
                $companyName = $assignment->resolvedCompany()?->name ?? 'No company assigned';

                return [
                    'id' => $student?->id,
                    'name' => $student?->name ?? 'Student',
                    'email' => $student?->email ?? '',
                    'section' => $student?->section ?: 'No section',
                    'company' => $companyName,
                ];
            })
            ->filter(fn (array $student) => ! empty($student['id']))
            ->sortBy('name')
            ->values();

        return view('supervisor.announcements.create', compact('assignedStudents'));
    }

    public function store(Request $request): RedirectResponse
    {
        if (! Announcement::recipientsTableExists()) {
            return redirect()
                ->back()
                ->withInput()
                ->with('status', 'Recipient targeting is temporarily unavailable until the latest announcements migration is applied.');
        }

        $supervisorId = (int) Auth::id();
        $activeStudentIds = Assignment::rosterForSupervisor($supervisorId)
            ->pluck('student_id')
            ->filter()
            ->unique()
            ->values();

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'type' => 'required|in:announcement,update',
            'recipient_ids' => ['required', 'array', 'min:1'],
            'recipient_ids.*' => ['integer', Rule::in($activeStudentIds->all())],
            'attachment' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,jpg,png,zip|max:10240', // 10MB max
        ], [
            'recipient_ids.required' => 'Select at least one assigned OJT student.',
            'recipient_ids.min' => 'Select at least one assigned OJT student.',
            'recipient_ids.*.in' => 'One or more selected students are no longer in your active roster.',
        ]);

        $validated['user_id'] = Auth::id();
        $validated['audience'] = 'students'; // Supervisors always announce to students

        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $path = $file->store('announcements', 'public');
            $validated['attachment'] = $path;
            $validated['original_filename'] = $file->getClientOriginalName();
        }

        $announcement = Announcement::create(collect($validated)->except('recipient_ids')->all());
        $announcement->recipients()->sync($validated['recipient_ids']);

        // Send notifications only to the selected assigned students.
        $students = User::eligibleStudentForRoster()
            ->whereIn('id', $validated['recipient_ids'])
            ->get();

        Notification::send($students, new NewAnnouncementNotification($announcement));

        return redirect()->route('supervisor.announcements.index')
            ->with('status', 'Announcement created successfully!');
    }
}
