<?php

namespace App\Http\Controllers\Coordinator;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Company;
use App\Models\Assignment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

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
        
        // Get OJT advisers count
        $advisersCount = User::where('role', User::ROLE_OJT_ADVISER)->count();
        
        // Get performance evaluations count (pending/submitted ones)
        $pendingReviews = DB::table('performance_evaluations')
            ->count();

        // Get OJT Students section progress data
        $sectionProgress = Assignment::select(
                DB::raw("COALESCE(users.section, 'No Section') as section"),
                DB::raw('COUNT(DISTINCT assignments.student_id) as count')
            )
            ->join('users', 'assignments.student_id', '=', 'users.id')
            ->where('assignments.status', 'active')
            ->groupBy('users.section')
            ->orderBy('count', 'desc')
            ->get();

        // Get Daily Attendance Trends (last 7 days)
        $attendanceTrend = collect();
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $total = DB::table('work_logs')
                ->whereDate('created_at', $date)
                ->count();
            $late = DB::table('work_logs')
                ->whereDate('created_at', $date)
                ->where('is_late', true)
                ->count();
            
            $attendanceTrend->push((object)[
                'day' => now()->subDays($i)->format('M d'),
                'total' => $total,
                'late' => $late
            ]);
        }

        // Get OJT Advisers 
        $ojtAdvisers = User::where('role', User::ROLE_OJT_ADVISER)
            ->get()
            ->map(function ($adviser) {
                return (object)[
                    'id' => $adviser->id,
                    'name' => $adviser->name,
                    'email' => $adviser->email,
                    'photo_url' => $adviser->profile_photo_path ? \Storage::url($adviser->profile_photo_path) : null,
                    'assigned_students_count' => 1  // Display at least 1 (can be customized based on actual relationship)
                ];
            });

        return view('coordinator.dashboard', [
            'totalStudents' => $totalStudents,
            'activeOJTs' => $activeOJTs,
            'pendingReviews' => $pendingReviews,
            'totalCompanies' => $totalCompanies,
            'advisersCount' => $advisersCount,
            'sectionProgress' => $sectionProgress,
            'attendanceTrend' => $attendanceTrend,
            'ojtAdvisers' => $ojtAdvisers,
        ]);
    }
}
