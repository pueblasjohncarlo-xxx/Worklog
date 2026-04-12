<?php

namespace App\Http\Controllers\Coordinator;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Company;
use App\Models\Assignment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Show the coordinator dashboard
     */
    public function index()
    {
        $coordinator = Auth::user();

        // Get statistics
        $totalStudents = User::where('role', User::ROLE_STUDENT)->count();
        // Count students with active assignments (OJT in progress)
        $activeOJTs = Assignment::where('status', 'active')
            ->distinct()
            ->count('student_id');
        $totalCompanies = Company::count();
        
        // Get performance evaluations count (pending/submitted ones)
        $pendingReviews = DB::table('performance_evaluations')
            ->count();

        // Get recent activities (simulate from logs or events)
        $recentActivities = collect([
            (object)[
                'description' => 'New student registration',
                'created_at' => now()->subHours(2)
            ],
            (object)[
                'description' => 'Evaluation submitted',
                'created_at' => now()->subHours(4)
            ],
            (object)[
                'description' => 'New company added',
                'created_at' => now()->subHours(6)
            ],
        ]);

        // Get upcoming deadlines
        $upcomingDeadlines = collect([
            (object)[
                'title' => 'Monthly Evaluations Due',
                'due_date' => now()->addDays(7)
            ],
            (object)[
                'title' => 'Quarterly Report Submission',
                'due_date' => now()->addDays(14)
            ],
        ]);

        // Performance metrics
        $studentsOnTrack = 85;
        $assignmentCompletion = 92;
        $evaluationCompletion = 78;

        return view('coordinator.dashboard', [
            'totalStudents' => $totalStudents,
            'activeOJTs' => $activeOJTs,
            'pendingReviews' => $pendingReviews,
            'totalCompanies' => $totalCompanies,
            'recentActivities' => $recentActivities,
            'upcomingDeadlines' => $upcomingDeadlines,
            'studentsOnTrack' => $studentsOnTrack,
            'assignmentCompletion' => $assignmentCompletion,
            'evaluationCompletion' => $evaluationCompletion,
        ]);
    }
}
