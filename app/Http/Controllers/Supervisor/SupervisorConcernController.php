<?php

namespace App\Http\Controllers\Supervisor;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\ConcernReport;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class SupervisorConcernController extends Controller
{
    public function index(): View
    {
        $supervisorId = Auth::id();

        $concerns = ConcernReport::with(['assignment.company', 'student'])
            ->where('supervisor_id', $supervisorId)
            ->orderByDesc('created_at')
            ->paginate(30);

        return view('supervisor.concerns.index', compact('concerns'));
    }

    public function create(): View
    {
        $supervisorId = Auth::id();

        $assignments = Assignment::with(['student', 'company'])
            ->where('supervisor_id', $supervisorId)
            ->where('status', 'active')
            ->orderByDesc('updated_at')
            ->get()
            ->sortBy(fn ($a) => $a->student?->name);

        return view('supervisor.concerns.create', compact('assignments'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'assignment_id' => ['required', 'exists:assignments,id'],
            'type' => ['required', 'in:concern,incident'],
            'title' => ['required', 'string', 'max:255'],
            'details' => ['required', 'string', 'max:5000'],
            'occurred_on' => ['nullable', 'date'],
        ]);

        $assignment = Assignment::with('student')
            ->where('id', $validated['assignment_id'])
            ->firstOrFail();

        if ($assignment->supervisor_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $concern = ConcernReport::create([
            'assignment_id' => $assignment->id,
            'supervisor_id' => Auth::id(),
            'student_id' => $assignment->student_id,
            'type' => $validated['type'],
            'title' => $validated['title'],
            'details' => $validated['details'],
            'occurred_on' => $validated['occurred_on'] ?? null,
        ]);

        return redirect()
            ->route('supervisor.concerns.index')
            ->with('status', "{$concern->type} report created successfully.");
    }
}
