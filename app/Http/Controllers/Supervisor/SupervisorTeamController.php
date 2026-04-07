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

        $teamMembers = Assignment::with(['student', 'workLogs', 'tasks'])
            ->where('supervisor_id', $supervisorId)
            ->where('status', 'active')
            ->get()
            ->map(function ($assignment) {
                return [
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
}
