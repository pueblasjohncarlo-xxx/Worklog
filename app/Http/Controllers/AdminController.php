<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
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
        $totalUsers = User::count();
        $admins = User::where('role', User::ROLE_ADMIN)->count();
        $coordinators = User::where('role', User::ROLE_COORDINATOR)->count();
        $supervisors = User::where('role', User::ROLE_SUPERVISOR)->count();
        $students = User::where('role', User::ROLE_STUDENT)->count();
        $advisers = User::where('role', User::ROLE_OJT_ADVISER)->count();
        $companies = Company::count();
        $assignments = Assignment::count();
        $workLogs = WorkLog::count();
        $pendingReviews = WorkLog::where('status', 'submitted')->count();
        $recentUsers = User::where('has_requested_account', true) // Only show users who have actually "joined" (claimed account or created manually)
            ->latest()
            ->take(5)
            ->get(['id', 'name', 'email', 'role', 'is_approved', 'created_at']);
        $pendingApprovals = User::where('is_approved', false)
            ->where('has_requested_account', true)
            ->count();

        // User Distribution for Doughnut Chart
        $userDistribution = [
            'Admins' => $admins,
            'Coordinators' => $coordinators,
            'Supervisors' => $supervisors,
            'OJT Students' => $students,
            'OJT Advisers' => $advisers,
        ];

        // Registration Trends (last 6 months) for Line Chart
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

        return view('dashboards.admin', [
            'totalUsers' => $totalUsers,
            'admins' => $admins,
            'coordinators' => $coordinators,
            'supervisors' => $supervisors,
            'students' => $students,
            'advisers' => $advisers,
            'companies' => $companies,
            'assignments' => $assignments,
            'workLogs' => $workLogs,
            'pendingReviews' => $pendingReviews,
            'recentUsers' => $recentUsers,
            'pendingApprovals' => $pendingApprovals,
            'userDistribution' => $userDistribution,
            'registrationTrends' => $registrationTrends,
        ]);
    }
}
