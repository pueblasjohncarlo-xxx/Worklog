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

        // Get statistics with safe fallbacks
        $totalStudents = 0;
        $activeOJTs = 0;
        $totalCompanies = 0;
        $advisersCount = 0;
        $pendingReviews = 0;
        $sectionProgress = collect();
        $attendanceTrend = collect();
        $ojtAdvisers = collect();

        try {
            // Get total students count
            $totalStudents = User::where('role', User::ROLE_STUDENT)->count();
        } catch (\Exception $e) {
            \Log::error('Dashboard: Failed to count students', ['error' => $e->getMessage()]);
            $totalStudents = 0;
        }

        try {
            // Count students with active assignments (OJT in progress)
            $activeOJTs = Assignment::where('status', 'active')
                ->distinct()
                ->count('student_id');
        } catch (\Exception $e) {
            \Log::error('Dashboard: Failed to count active OJTs', ['error' => $e->getMessage()]);
            $activeOJTs = 0;
        }

        try {
            // Get total companies count
            $totalCompanies = Company::count();
        } catch (\Exception $e) {
            \Log::error('Dashboard: Failed to count companies', ['error' => $e->getMessage()]);
            $totalCompanies = 0;
        }

        try {
            // Get OJT advisers count
            $advisersCount = User::where('role', User::ROLE_OJT_ADVISER)->count();
        } catch (\Exception $e) {
            \Log::error('Dashboard: Failed to count advisers', ['error' => $e->getMessage()]);
            $advisersCount = 0;
        }

        try {
            // Get performance evaluations count - with existence check
            if (DB::connection()->getSchemaBuilder()->hasTable('performance_evaluations')) {
                $pendingReviews = DB::table('performance_evaluations')->count();
            } else {
                $pendingReviews = 0;
            }
        } catch (\Exception $e) {
            \Log::error('Dashboard: Failed to count performance evaluations', ['error' => $e->getMessage()]);
            $pendingReviews = 0;
        }

        try {
            // Get OJT Students section progress data
            $sectionProgress = Assignment::select(
                    DB::raw('users.section as section'),
                    DB::raw('COUNT(DISTINCT assignments.student_id) as count')
                )
                ->join('users', 'assignments.student_id', '=', 'users.id')
                ->where('assignments.status', 'active')
                ->groupBy('users.section')
                ->orderBy('count', 'desc')
                ->get()
                ->map(function ($row) {
                    return [
                        'section' => User::normalizeStudentSection($row->section) ?? User::STUDENT_SECTION_BSIT_4A,
                        'count' => (int) $row->count,
                    ];
                })
                ->groupBy('section')
                ->map(function ($rows, $section) {
                    return (object) [
                        'section' => $section,
                        'count' => $rows->sum('count'),
                    ];
                })
                ->values();
            
            // If no results, provide empty safe state
            if ($sectionProgress->isEmpty()) {
                $sectionProgress = collect([
                    (object)['section' => User::STUDENT_SECTION_BSIT_4A, 'count' => 0]
                ]);
            }
        } catch (\Exception $e) {
            \Log::error('Dashboard: Failed to get section progress', ['error' => $e->getMessage()]);
            $sectionProgress = collect([
                (object)['section' => User::STUDENT_SECTION_BSIT_4A, 'count' => 0]
            ]);
        }

        // Get Daily Attendance Trends (last 7 days)
        // Only count submitted work logs (where status is not 'draft')
        try {
            for ($i = 6; $i >= 0; $i--) {
                $date = now()->subDays($i)->format('Y-m-d');
                
                // Verify work_logs table exists before querying
                if (!DB::connection()->getSchemaBuilder()->hasTable('work_logs')) {
                    throw new \Exception('work_logs table not found');
                }
                
                // Count all submitted work logs for the day
                $total = DB::table('work_logs')
                    ->whereDate('work_date', $date)
                    ->whereIn('status', ['submitted', 'approved', 'graded'])
                    ->count();
                
                // Count submitted work logs where time_in is null/missing (incomplete submissions)
                $incomplete = DB::table('work_logs')
                    ->whereDate('work_date', $date)
                    ->whereIn('status', ['submitted', 'approved', 'graded'])
                    ->whereNull('time_in')
                    ->count();
                
                $attendanceTrend->push((object)[
                    'day' => now()->subDays($i)->format('M d'),
                    'total' => $total,
                    'late' => $incomplete  // Using incomplete as a proxy for incomplete submissions
                ]);
            }
        } catch (\Exception $e) {
            \Log::error('Dashboard: Failed to get attendance trends', ['error' => $e->getMessage()]);
            // Provide safe empty state for last 7 days
            for ($i = 6; $i >= 0; $i--) {
                $attendanceTrend->push((object)[
                    'day' => now()->subDays($i)->format('M d'),
                    'total' => 0,
                    'late' => 0
                ]);
            }
        }

        try {
            // Get OJT Advisers 
            $ojtAdvisers = User::where('role', User::ROLE_OJT_ADVISER)
                ->get()
                ->map(function ($adviser) {
                    return (object)[
                        'id' => $adviser->id,
                        'name' => $adviser->name ?? 'Unknown',
                        'email' => $adviser->email ?? 'N/A',
                        'photo_url' => $adviser->profile_photo_path ? \Storage::url($adviser->profile_photo_path) : null,
                        'assigned_students_count' => 1
                    ];
                });
            
            // If no advisers, provide empty collection
            if ($ojtAdvisers->isEmpty()) {
                $ojtAdvisers = collect();
            }
        } catch (\Exception $e) {
            \Log::error('Dashboard: Failed to get OJT advisers', ['error' => $e->getMessage()]);
            $ojtAdvisers = collect();
        }

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
