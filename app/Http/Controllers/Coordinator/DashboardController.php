<?php

namespace App\Http\Controllers\Coordinator;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Company;
use App\Models\Assignment;
use App\Models\WorkLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Collection;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Show the coordinator dashboard
     */
    public function index()
    {
        $coordinator = Auth::user();
        $submittedStatuses = ['submitted', 'approved', 'graded'];
        $reportTypes = ['daily', 'weekly', 'monthly'];

        $totalStudents = 0;
        $activeOJTs = 0;
        $totalCompanies = 0;
        $advisersCount = 0;
        $supervisorsCount = 0;
        $pendingApprovals = 0;
        $pendingAccomplishmentReports = 0;
        $studentsNeedingAttention = collect();
        $sectionProgress = collect();
        $attendanceTrend = collect();
        $recentActivity = collect();
        $sectionReportOverview = collect();
        $ojtAdvisers = collect();
        $incompleteLogsCount = 0;
        $currentRequiredHours = 1600;

        try {
            $approvedStudents = $this->approvedStudentsQuery()
                ->select('id', 'name', 'email', 'section', 'department', 'profile_photo_path')
                ->get()
                ->keyBy('id');

            $approvedStudentIds = $approvedStudents->keys();
            $totalStudents = $approvedStudents->count();

            $totalCompanies = Company::count();
            $advisersCount = User::where('role', User::ROLE_OJT_ADVISER)->count();
            $supervisorsCount = User::where('role', User::ROLE_SUPERVISOR)->count();

            $pendingApprovals = $this->pendingApprovalsCount();

            $commonHours = Assignment::select('required_hours', DB::raw('COUNT(*) as c'))
                ->groupBy('required_hours')
                ->orderByDesc('c')
                ->first();
            $currentRequiredHours = (int) ($commonHours?->required_hours ?? 1600);

            $activeAssignments = Assignment::with([
                    'student:id,name,email,section,department,profile_photo_path',
                    'company:id,name',
                    'ojtAdviser:id,name,email,profile_photo_path',
                    'supervisor:id,name',
                    'workLogs:id,assignment_id,type,work_date,time_in,hours,status',
                ])
                ->where('status', 'active')
                ->when($approvedStudentIds->isNotEmpty(), function ($query) use ($approvedStudentIds) {
                    $query->whereIn('student_id', $approvedStudentIds->all());
                }, function ($query) {
                    $query->whereRaw('1 = 0');
                })
                ->orderByDesc('start_date')
                ->get()
                ->unique('student_id')
                ->values();

            $activeOJTs = $activeAssignments->count();

            $sectionProgress = $activeAssignments
                ->groupBy(function ($assignment) {
                    return User::normalizeStudentSection($assignment->student?->section, $assignment->student?->department)
                        ?? User::STUDENT_SECTION_BSIT_4A;
                })
                ->map(function ($assignments, $section) {
                    return (object) [
                        'section' => $section,
                        'count' => $assignments->count(),
                    ];
                })
                ->sortByDesc('count')
                ->values();

            $studentDashboardRows = collect();
            $sectionReportOverview = collect();

            foreach ($activeAssignments as $assignment) {
                $student = $assignment->student;
                if (! $student) {
                    continue;
                }

                $section = User::normalizeStudentSection($student->section, $student->department)
                    ?? User::STUDENT_SECTION_BSIT_4A;

                $requiredHours = max(1, (int) ($assignment->required_hours ?? $currentRequiredHours));
                $logs = $assignment->workLogs ?? collect();

                $approvedHours = (float) $logs
                    ->where('status', 'approved')
                    ->sum('hours');

                $progress = (float) min(100, round(($approvedHours / $requiredHours) * 100, 1));

                $reportWindows = [
                    'daily' => Carbon::now()->subDays(7),
                    'weekly' => Carbon::now()->subDays(30),
                    'monthly' => Carbon::now()->subDays(90),
                ];

                $reportStatusByType = [];
                foreach ($reportTypes as $type) {
                    $windowStart = $reportWindows[$type];
                    $reportStatusByType[$type] = $logs->contains(function ($log) use ($type, $submittedStatuses, $windowStart) {
                        return $log->type === $type
                            && in_array($log->status, $submittedStatuses, true)
                            && is_null($log->time_in)
                            && $log->work_date
                            && Carbon::parse($log->work_date)->greaterThanOrEqualTo($windowStart);
                    });
                }

                $missingTypes = collect($reportStatusByType)->filter(fn ($status) => ! $status)->count();
                $hasRecentAttendance = $logs->contains(function ($log) {
                    return ! is_null($log->time_in)
                        && $log->work_date
                        && Carbon::parse($log->work_date)->greaterThanOrEqualTo(Carbon::now()->subDays(7));
                });

                if ($missingTypes > 0) {
                    $pendingAccomplishmentReports++;
                }

                $studentStatus = 'In Progress';
                if ($progress >= 100) {
                    $studentStatus = 'Completed';
                } elseif ($missingTypes === 0 && $hasRecentAttendance && $progress >= 60) {
                    $studentStatus = 'On Track';
                } elseif ($missingTypes >= 2 || ! $hasRecentAttendance) {
                    $studentStatus = 'Needs Attention';
                }

                $studentRow = [
                    'assignment_id' => $assignment->id,
                    'student_id' => $student->id,
                    'name' => $student->name,
                    'email' => $student->email,
                    'section' => $section,
                    'company' => $assignment->company?->name ?? 'N/A',
                    'required_hours' => $requiredHours,
                    'rendered_hours' => round($approvedHours, 2),
                    'progress' => $progress,
                    'daily_submitted' => $reportStatusByType['daily'],
                    'weekly_submitted' => $reportStatusByType['weekly'],
                    'monthly_submitted' => $reportStatusByType['monthly'],
                    'has_recent_attendance' => $hasRecentAttendance,
                    'status' => $studentStatus,
                ];

                $studentDashboardRows->push($studentRow);

                if (! $sectionReportOverview->has($section)) {
                    $sectionReportOverview->put($section, [
                        'section' => $section,
                        'total_students' => 0,
                        'submitted_any' => 0,
                        'daily' => ['submitted' => [], 'not_submitted' => []],
                        'weekly' => ['submitted' => [], 'not_submitted' => []],
                        'monthly' => ['submitted' => [], 'not_submitted' => []],
                    ]);
                }

                $entry = $sectionReportOverview->get($section);
                $entry['total_students']++;

                if ($reportStatusByType['daily'] || $reportStatusByType['weekly'] || $reportStatusByType['monthly']) {
                    $entry['submitted_any']++;
                }

                foreach ($reportTypes as $type) {
                    $targetBucket = $reportStatusByType[$type] ? 'submitted' : 'not_submitted';
                    $entry[$type][$targetBucket][] = [
                        'student_id' => $student->id,
                        'name' => $student->name,
                        'section' => $section,
                        'progress' => $progress,
                        'required_hours' => $requiredHours,
                        'rendered_hours' => round($approvedHours, 2),
                    ];
                }

                $sectionReportOverview->put($section, $entry);
            }

            $sectionReportOverview = $sectionReportOverview
                ->map(function (array $entry) {
                    $total = max(1, (int) $entry['total_students']);
                    $entry['submitted_percentage'] = round(($entry['submitted_any'] / $total) * 100, 1);
                    $entry['not_submitted_percentage'] = round(100 - $entry['submitted_percentage'], 1);

                    return $entry;
                })
                ->sortByDesc('submitted_percentage')
                ->values();

            $studentsNeedingAttention = $studentDashboardRows
                ->filter(fn ($row) => $row['status'] === 'Needs Attention')
                ->values();

            $incompleteLogsCount = $studentDashboardRows->filter(function ($row) {
                return ! $row['daily_submitted'] || ! $row['weekly_submitted'] || ! $row['monthly_submitted'];
            })->count();

            $attendanceTrend = $this->buildAttendanceTrend($submittedStatuses);

            $recentActivity = WorkLog::query()
                ->with(['assignment.student:id,name,section,department', 'assignment.company:id,name'])
                ->whereIn('status', $submittedStatuses)
                ->whereHas('assignment', function ($query) use ($approvedStudentIds) {
                    $query->where('status', 'active')
                        ->whereIn('student_id', $approvedStudentIds->all());
                })
                ->orderByDesc('work_date')
                ->limit(8)
                ->get()
                ->map(function ($log) {
                    $student = $log->assignment?->student;
                    $section = User::normalizeStudentSection($student?->section, $student?->department)
                        ?? User::STUDENT_SECTION_BSIT_4A;

                    return [
                        'date' => optional($log->work_date)->format('M d, Y') ?? '-',
                        'type' => ucfirst($log->type),
                        'student' => $student?->name ?? 'Unknown',
                        'status' => ucfirst($log->status),
                        'hours' => (float) $log->hours,
                        'company' => $log->assignment?->company?->name ?? 'N/A',
                        'section' => $section,
                    ];
                });

            $adviserBuckets = $activeAssignments
                ->filter(fn ($assignment) => ! is_null($assignment->ojt_adviser_id))
                ->groupBy('ojt_adviser_id');

            $ojtAdvisers = User::where('role', User::ROLE_OJT_ADVISER)
                ->orderBy('name')
                ->get()
                ->map(function ($adviser) use ($adviserBuckets, $studentDashboardRows) {
                    $assigned = $adviserBuckets->get($adviser->id, collect());
                    $students = $assigned
                        ->map(function ($assignment) use ($studentDashboardRows) {
                            return $studentDashboardRows
                                ->firstWhere('assignment_id', $assignment->id);
                        })
                        ->filter()
                        ->values();

                    return [
                        'id' => $adviser->id,
                        'name' => $adviser->name ?? 'Unknown',
                        'email' => $adviser->email ?? 'N/A',
                        'photo_url' => $adviser->profile_photo_path ? Storage::url($adviser->profile_photo_path) : null,
                        'assigned_students_count' => $students->count(),
                        'on_track_count' => $students->where('status', 'On Track')->count(),
                        'attention_count' => $students->where('status', 'Needs Attention')->count(),
                        'completed_count' => $students->where('status', 'Completed')->count(),
                        'students' => $students,
                    ];
                })
                ->values();
        } catch (\Throwable $e) {
            \Log::error('Dashboard: Failed to build coordinator dashboard data', [
                'error' => $e->getMessage(),
            ]);

            for ($i = 6; $i >= 0; $i--) {
                $attendanceTrend->push((object) [
                    'day' => now()->subDays($i)->format('M d'),
                    'total' => 0,
                    'late' => 0,
                ]);
            }
        }

        return view('coordinator.dashboard', [
            'coordinator' => $coordinator,
            'totalStudents' => $totalStudents,
            'activeOJTs' => $activeOJTs,
            'totalCompanies' => $totalCompanies,
            'advisersCount' => $advisersCount,
            'supervisorsCount' => $supervisorsCount,
            'pendingApprovals' => $pendingApprovals,
            'pendingAccomplishmentReports' => $pendingAccomplishmentReports,
            'incompleteLogsCount' => $incompleteLogsCount,
            'studentsNeedingAttention' => $studentsNeedingAttention,
            'sectionProgress' => $sectionProgress,
            'attendanceTrend' => $attendanceTrend,
            'recentActivity' => $recentActivity,
            'sectionReportOverview' => $sectionReportOverview,
            'ojtAdvisers' => $ojtAdvisers,
            'currentRequiredHours' => $currentRequiredHours,
        ]);
    }

    private function approvedStudentsQuery()
    {
        $query = User::query()->where('role', User::ROLE_STUDENT);

        if (Schema::hasColumn('users', 'status')) {
            $query->where(function ($statusQuery) {
                $statusQuery->whereIn('status', ['approved', 'active'])
                    ->orWhereNull('status');
            });
        }

        if (Schema::hasColumn('users', 'is_approved')) {
            $query->where('is_approved', true);
        }

        return $query;
    }

    private function pendingApprovalsCount(): int
    {
        $query = User::query()
            ->whereIn('role', [User::ROLE_STUDENT, User::ROLE_SUPERVISOR, User::ROLE_OJT_ADVISER]);

        if (Schema::hasColumn('users', 'has_requested_account')) {
            $query->where('has_requested_account', true);
        }

        if (Schema::hasColumn('users', 'status')) {
            $query->where('status', 'pending');
        } elseif (Schema::hasColumn('users', 'is_approved')) {
            $query->where('is_approved', false);
        }

        return (int) $query->count();
    }

    private function buildAttendanceTrend(array $submittedStatuses): Collection
    {
        $attendanceTrend = collect();

        if (! Schema::hasTable('work_logs')) {
            return $attendanceTrend;
        }

        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');

            $total = DB::table('work_logs as wl')
                ->join('assignments as a', 'wl.assignment_id', '=', 'a.id')
                ->whereDate('wl.work_date', $date)
                ->whereIn('wl.status', $submittedStatuses)
                ->where('a.status', 'active')
                ->whereNotNull('wl.time_in')
                ->count();

            $incomplete = DB::table('work_logs as wl')
                ->join('assignments as a', 'wl.assignment_id', '=', 'a.id')
                ->whereDate('wl.work_date', $date)
                ->whereIn('wl.status', $submittedStatuses)
                ->where('a.status', 'active')
                ->whereNull('wl.time_in')
                ->count();

            $attendanceTrend->push((object) [
                'day' => now()->subDays($i)->format('M d'),
                'total' => $total,
                'late' => $incomplete,
            ]);
        }

        return $attendanceTrend;
    }
}
