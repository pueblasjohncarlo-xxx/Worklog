<?php

namespace App\Http\Controllers\Supervisor;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class SupervisorTeamController extends Controller
{
    public function index(): View
    {
        $supervisorId = Auth::id();

        $teamMembers = Assignment::query()
            ->where('supervisor_id', $supervisorId)
            ->active()
            ->whereHas('student', fn ($q) => $q->eligibleStudentForRoster())
            ->with(['student', 'company', 'workLogs', 'tasks'])
            ->orderByDesc('updated_at')
            ->get()
            ->unique('student_id')
            ->values()
            ->map(function ($assignment) {
                return [
                    'assignment_id' => $assignment->id,
                    'student' => $assignment->student,
                    'company' => $assignment->company,
                    'total_hours' => $assignment->workLogs->where('status', 'approved')->sum('hours'),
                    'required_hours' => $assignment->required_hours,
                    'active_tasks' => $assignment->tasks->whereIn('status', ['pending', 'in_progress'])->count(),
                    'last_log' => $assignment->workLogs->sortByDesc('work_date')->first(),
                ];
            })
            ->sortBy(function ($item) {
                return $item['student']->name;
            });

        return view('supervisor.team.index', compact('teamMembers'));
    }

    public function show(Assignment $assignment): View
    {
        if ($assignment->supervisor_id !== Auth::id()) {
            abort(404);
        }

        $assignment->loadMissing([
            'student',
            'company',
            'workLogs',
            'tasks',
        ]);

        $approvedHours = $assignment->workLogs->where('status', 'approved')->sum('hours');
        $requiredHours = (float) ($assignment->required_hours ?: 0);
        $percentage = $requiredHours > 0 ? min(100, ($approvedHours / $requiredHours) * 100) : 0;

        $activeTasks = $assignment->tasks
            ->whereIn('status', ['pending', 'in_progress', 'submitted'])
            ->sortByDesc('updated_at')
            ->take(10);

        $recentLogs = $assignment->workLogs
            ->sortByDesc('work_date')
            ->take(10);

        return view('supervisor.team.show', [
            'assignment' => $assignment,
            'approvedHours' => $approvedHours,
            'requiredHours' => $requiredHours,
            'percentage' => $percentage,
            'activeTasks' => $activeTasks,
            'recentLogs' => $recentLogs,
        ]);
    }
}
