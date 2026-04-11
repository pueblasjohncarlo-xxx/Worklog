<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\Assignment;
use App\Models\AuditLog;
use App\Models\Company;
use App\Models\User;
use App\Models\WorkLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AdminController extends Controller
{
    public function index(): View
    {
        // ===== User Metrics =====
        $totalUsers = User::count();
        $totalApprovedUsers = User::where('is_approved', true)->count();
        $admins = User::where('role', User::ROLE_ADMIN)->count();
        $coordinators = User::where('role', User::ROLE_COORDINATOR)->count();
        $supervisors = User::where('role', User::ROLE_SUPERVISOR)->count();
        $students = User::where('role', User::ROLE_STUDENT)->count();
        $advisers = User::where('role', User::ROLE_OJT_ADVISER)->count();
        $activeUsers = User::where('last_login_at', '>=', Carbon::now()->subDays(7))->count();
        $pendingApprovals = User::where('is_approved', false)
            ->where('has_requested_account', true)
            ->count();

        // ===== Company & Assignment Metrics =====
        $companies = Company::count();
        $assignments = Assignment::count();

        // ===== Work Log & Review Metrics =====
        $workLogs = WorkLog::count();
        $pendingReviews = WorkLog::where('status', 'submitted')->count();
        $approvedWorkLogs = WorkLog::where('status', 'approved')->count();

        // ===== Announcement Metrics =====
        $announcements = Announcement::count();

        // ===== Audit Logs =====
        $recentAuditLogs = AuditLog::latest()
            ->take(8)
            ->get(['id', 'user_id', 'action', 'auditable_type', 'auditable_id', 'ip_address', 'created_at'])
            ->load('user:id,name,email');

        // ===== User Role Distribution for Chart =====
        $userDistribution = [
            'Admins' => $admins,
            'Coordinators' => $coordinators,
            'Supervisors' => $supervisors,
            'Students' => $students,
            'OJT Advisers' => $advisers,
        ];

        // ===== User Status Breakdown =====
        $userApprovalStatus = [
            'Approved' => $totalApprovedUsers,
            'Pending' => $pendingApprovals,
        ];

        // ===== Registration Trends (Last 6 Months) =====
        $driver = DB::connection()->getDriverName();
        $monthExpression = $driver === 'sqlite'
            ? "strftime('%Y-%m', created_at)"
            : "DATE_FORMAT(created_at, '%Y-%m')";

        $registrationTrends = User::select(
            DB::raw('count(id) as count'),
            DB::raw("$monthExpression as ym")
        )
            ->where('created_at', '>=', Carbon::now()->subMonths(6))
            ->groupBy('ym')
            ->orderBy('ym', 'asc')
            ->get()
            ->map(function ($row) {
                $month = Carbon::createFromFormat('Y-m', $row->ym)->format('M Y');
                return [
                    'month' => $month,
                    'count' => (int) $row->count,
                ];
            });

        // ===== Work Log Submission Trends (Last 6 Months) =====
        $workLogTrends = WorkLog::select(
            DB::raw('count(id) as count'),
            DB::raw("$monthExpression as ym")
        )
            ->where('created_at', '>=', Carbon::now()->subMonths(6))
            ->groupBy('ym')
            ->orderBy('ym', 'asc')
            ->get()
            ->map(function ($row) {
                $month = Carbon::createFromFormat('Y-m', $row->ym)->format('M Y');
                return [
                    'month' => $month,
                    'count' => (int) $row->count,
                ];
            });

        // ===== Work Log Status Breakdown =====
        // (Removed - not used in view)

        return view('dashboards.admin', [
            // User Metrics
            'totalUsers' => $totalUsers,
            'totalApprovedUsers' => $totalApprovedUsers,
            'admins' => $admins,
            'coordinators' => $coordinators,
            'supervisors' => $supervisors,
            'students' => $students,
            'advisers' => $advisers,
            'activeUsers' => $activeUsers,
            'pendingApprovals' => $pendingApprovals,

            // Other Metrics
            'companies' => $companies,
            'assignments' => $assignments,
            'workLogs' => $workLogs,
            'pendingReviews' => $pendingReviews,
            'approvedWorkLogs' => $approvedWorkLogs,
            'announcements' => $announcements,

            // Audit Logs
            'recentAuditLogs' => $recentAuditLogs,

            // Chart Data
            'userDistribution' => $userDistribution,
            'userApprovalStatus' => $userApprovalStatus,
            'registrationTrends' => $registrationTrends,
            'workLogTrends' => $workLogTrends,
        ]);
    }
}
