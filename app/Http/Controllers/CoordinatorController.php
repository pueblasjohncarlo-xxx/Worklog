<?php

namespace App\Http\Controllers;

use App\Http\Requests\Coordinator\StoreCompanyRequest;
use App\Models\Assignment;
use App\Models\Company;
use App\Models\PerformanceEvaluation;
use App\Models\User;
use App\Models\WorkLog;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CoordinatorController extends Controller
{
    public function index(): View
    {
        $students = User::where('role', User::ROLE_STUDENT)->count();
        $supervisors = User::where('role', User::ROLE_SUPERVISOR)->count();
        $advisersCount = User::where('role', User::ROLE_OJT_ADVISER)->count();
        $companies = Company::count();
        $activeAssignments = Assignment::where('status', 'active')->count();

        // Recent system activities (removed from dashboard view)
        $recentActivities = collect();

        // Student progress overview (for active assignments) - grouped by section
        $studentProgressRaw = Assignment::with(['student', 'company', 'supervisor'])
            ->where('status', 'active')
            ->get()
            ->map(function ($assignment) {
                $student = $assignment->student;

                return [
                    'student_name' => $student->name,
                    'student_section' => $student->normalizedStudentSection() ?? User::STUDENT_SECTION_BSIT_4A,
                    'industry_name' => $assignment->company->industry ?: $assignment->company->name,
                    'supervisor_name' => optional($assignment->supervisor)->name,
                    'progress' => $assignment->progressPercentage(),
                    'hours_completed' => $assignment->totalApprovedHours(),
                    'required_hours' => $assignment->required_hours,
                ];
            })
            ->sortBy('student_name'); // Sort alphabetically by student name
        
        // Group student progress by section
        $studentProgress = $studentProgressRaw->groupBy('student_section')->sortKeys();

        // Monitoring alerts
        $monitorStart = Carbon::now()->subDays(6)->startOfDay();
        $monitorEnd = Carbon::now()->endOfDay();

        $activeAssignmentsForAlerts = Assignment::with(['student', 'company', 'supervisor', 'workLogs'])
            ->where('status', 'active')
            ->get();

        $studentsAlerts = [];
        $supervisorsAlerts = [];
        $companiesAlerts = [];

        foreach ($activeAssignmentsForAlerts as $assignment) {
            $attendanceDates = $assignment->workLogs
                ->whereBetween('work_date', [$monitorStart->toDateString(), $monitorEnd->toDateString()])
                ->filter(function ($log) {
                    return ! empty($log->time_in);
                })
                ->pluck('work_date')
                ->map(fn ($d) => $d->toDateString())
                ->unique()
                ->values();

            $recentAbsentDays = 0;
            for ($i = 0; $i < 7; $i++) {
                $day = Carbon::now()->subDays($i)->toDateString();
                if (! $attendanceDates->contains($day)) {
                    $recentAbsentDays++;
                }
            }

            $pendingCount = $assignment->workLogs->where('status', 'submitted')->count();
            $lastAttendance = $assignment->workLogs
                ->filter(fn ($l) => ! empty($l->time_in))
                ->sortByDesc('work_date')
                ->first();

            $reasons = [];
            if ($recentAbsentDays >= 3) {
                $reasons[] = "Absent {$recentAbsentDays}/7 days";
            }
            if ($lastAttendance && Carbon::parse($lastAttendance->work_date)->lt(Carbon::now()->subDays(7))) {
                $reasons[] = 'No attendance in 7+ days';
            }
            if ($pendingCount >= 3) {
                $reasons[] = "{$pendingCount} pending approvals";
            }
            if ($assignment->progressPercentage() < 5 && ($assignment->start_date ? Carbon::parse($assignment->start_date)->lt(Carbon::now()->subDays(14)) : true)) {
                $reasons[] = 'Progress below 5%';
            }

            if (! empty($reasons)) {
                $studentsAlerts[] = [
                    'student' => $assignment->student->name,
                    'supervisor' => optional($assignment->supervisor)->name,
                    'company' => optional($assignment->company)->name,
                    'reasons' => $reasons,
                ];
            }
        }

        // Supervisors with high pending approvals
        $supervisorPending = [];
        foreach ($activeAssignmentsForAlerts as $assignment) {
            $sid = optional($assignment->supervisor)->id;
            if ($sid) {
                $supervisorPending[$sid] = ($supervisorPending[$sid] ?? 0)
                    + $assignment->workLogs->where('status', 'submitted')->count();
            }
        }
        foreach ($supervisorPending as $sid => $count) {
            if ($count >= 5) {
                $sup = User::find($sid);
                if ($sup) {
                    $supervisorsAlerts[] = [
                        'supervisor' => $sup->name,
                        'pending' => $count,
                    ];
                }
            }
        }

        // Companies with multiple students flagged
        $companyFlags = [];
        foreach ($studentsAlerts as $alert) {
            $companyFlags[$alert['company']] = ($companyFlags[$alert['company']] ?? 0) + 1;
        }
        foreach ($companyFlags as $cname => $count) {
            if ($count >= 2 && $cname) {
                $companiesAlerts[] = [
                    'company' => $cname,
                    'flagged_students' => $count,
                ];
            }
        }

        // OJT Students Progress by Section for Bar Chart
        $sectionProgress = Assignment::with('student')
            ->where('status', 'active')
            ->get()
            ->groupBy(fn ($a) => $a->student->normalizedStudentSection() ?? User::STUDENT_SECTION_BSIT_4A)
            ->map(function ($assignments, $section) {
                return [
                    'section' => $section,
                    'avg_progress' => round($assignments->avg(fn ($a) => $a->progressPercentage()), 1),
                ];
            })
            ->values()
            ->sortByDesc('avg_progress')
            ->take(10);

        // Daily Attendance Trend by Section (Last 7 days) including Lates
        $fromDate = Carbon::now()->subDays(6)->toDateString();
        $logsForWeek = WorkLog::with(['assignment.student'])
            ->where('work_date', '>=', $fromDate)
            ->whereNotNull('time_in')
            ->get();

        $attendanceTrend = collect(range(0, 6))->map(function ($offset) use ($logsForWeek) {
            $date = Carbon::now()->subDays(6 - $offset)->toDateString();
            $label = Carbon::parse($date)->format('M d');

            $dayLogs = $logsForWeek->filter(fn ($log) => $log->work_date?->toDateString() === $date);
            $total = $dayLogs->count();
            $late = $dayLogs->filter(function ($log) {
                $timeIn = $log->time_in;
                $timeInStr = $timeIn instanceof Carbon ? $timeIn->format('H:i:s') : (string) $timeIn;

                return $timeInStr > '08:00:00';
            })->count();

            return [
                'day' => $label,
                'total' => $total,
                'late' => $late,
            ];
        });

        $ojtAdvisers = User::query()
            ->where('role', User::ROLE_OJT_ADVISER)
            ->with(['ojtAdviserProfile', 'ojtAdviserAssignments.student'])
            ->withCount([
                'ojtAdviserAssignments as active_assignments_count' => function ($q) {
                    $q->where('status', 'active');
                },
            ])
            ->orderBy('name')
            ->get()
            ->map(function (User $adviser) {
                $students = $adviser->ojtAdviserAssignments
                    ->where('status', 'active')
                    ->pluck('student')
                    ->filter();

                return [
                    'id' => $adviser->id,
                    'name' => $adviser->name,
                    'email' => $adviser->email,
                    'department' => $adviser->ojtAdviserProfile?->department ?? $adviser->department,
                    'phone' => $adviser->ojtAdviserProfile?->phone,
                    'address' => $adviser->ojtAdviserProfile?->address,
                    'photo_url' => $adviser->profile_photo_path ? $adviser->profile_photo_url : null,
                    'active_assignments_count' => $adviser->active_assignments_count ?? 0,
                    'comptech_students' => $students->filter(function ($s) {
                        $dept = (string) ($s?->department ?? '');

                        return stripos($dept, 'computer') !== false || stripos($dept, 'comptech') !== false;
                    })->count(),
                    'electronics_students' => $students->filter(function ($s) {
                        $dept = (string) ($s?->department ?? '');

                        return stripos($dept, 'electronics') !== false || stripos($dept, 'electronic') !== false;
                    })->count(),
                ];
            })
            ->values();

        $departmentSectionOptions = [
            User::STUDENT_MAJOR_COMPUTER_TECHNOLOGY => [
                User::STUDENT_SECTION_BSIT_4A,
                User::STUDENT_SECTION_BSIT_4B,
                User::STUDENT_SECTION_BSIT_4C,
                User::STUDENT_SECTION_BSIT_4D,
            ],
            User::STUDENT_MAJOR_ELECTRONICS_TECHNOLOGY => [
                User::STUDENT_SECTION_BSIT_4AE,
            ],
        ];

        // Get students with active assignments first, then include all students for display
        $activeStudentIds = Assignment::where('status', 'active')->pluck('student_id')->unique();
        $studentsForDepartments = User::where('role', User::ROLE_STUDENT)
            ->orderBy('name')
            ->get();

        $departmentsData = collect($departmentSectionOptions)->map(function (array $sectionOptions, string $department) use ($studentsForDepartments, $activeStudentIds) {
            $students = $studentsForDepartments->filter(function (User $u) use ($department) {
                return $u->normalizedStudentDepartment() === $department;
            });

            $studentsBySection = collect($sectionOptions)->mapWithKeys(function (string $section) use ($students, $activeStudentIds) {
                $filtered = $students->filter(function (User $u) use ($section) {
                    return $u->normalizedStudentSection() === $section;
                });

                return [
                    $section => $filtered
                        ->map(function (User $u) use ($section, $activeStudentIds) {
                            return [
                                'id' => $u->id,
                                'name' => $u->name,
                                'email' => $u->email,
                                'section' => $section,
                                'has_assignment' => $activeStudentIds->contains($u->id),
                            ];
                        })
                        ->values(),
                ];
            });

            return [
                'name' => $department,
                'section_options' => $sectionOptions,
                'students_by_section' => $studentsBySection,
            ];
        })->values();

        $companiesForMap = Company::orderBy('name')
            ->get()
            ->map(function (Company $company) {
                $addressParts = collect([
                    $company->address,
                    $company->state,
                    $company->city,
                    $company->postal_code,
                    $company->country,
                ])->filter()->values();

                return [
                    'id' => $company->id,
                    'name' => $company->name,
                    'industry' => $company->industry,
                    'address' => $addressParts->implode(', '),
                    'latitude' => $company->latitude,
                    'longitude' => $company->longitude,
                ];
            })
            ->values();

        $activeAssignmentsForStats = Assignment::with('workLogs')
            ->where('status', 'active')
            ->get();

        $totalActive = $activeAssignmentsForStats->count();
        $submittedStatuses = collect(['submitted', 'approved']);

        $dailyFrom = Carbon::now()->subDays(6)->toDateString();
        $weeklyFrom = Carbon::now()->subDays(29)->toDateString();
        $monthlyFrom = Carbon::now()->subDays(89)->toDateString();

        $countDailyAr = $activeAssignmentsForStats->filter(function (Assignment $assignment) use ($submittedStatuses, $dailyFrom) {
            return $assignment->workLogs
                ->where('type', 'daily')
                ->whereIn('status', $submittedStatuses)
                ->where('work_date', '>=', $dailyFrom)
                ->filter(fn (WorkLog $log) => empty($log->time_in))
                ->isNotEmpty();
        })->count();

        $countWeeklyAr = $activeAssignmentsForStats->filter(function (Assignment $assignment) use ($submittedStatuses, $weeklyFrom) {
            return $assignment->workLogs
                ->where('type', 'weekly')
                ->whereIn('status', $submittedStatuses)
                ->where('work_date', '>=', $weeklyFrom)
                ->filter(fn (WorkLog $log) => empty($log->time_in))
                ->isNotEmpty();
        })->count();

        $countMonthlyAr = $activeAssignmentsForStats->filter(function (Assignment $assignment) use ($submittedStatuses, $monthlyFrom) {
            return $assignment->workLogs
                ->where('type', 'monthly')
                ->whereIn('status', $submittedStatuses)
                ->where('work_date', '>=', $monthlyFrom)
                ->filter(fn (WorkLog $log) => empty($log->time_in))
                ->isNotEmpty();
        })->count();

        $countAttendance = $activeAssignmentsForStats->filter(function (Assignment $assignment) use ($dailyFrom) {
            return $assignment->workLogs
                ->where('work_date', '>=', $dailyFrom)
                ->filter(fn (WorkLog $log) => ! empty($log->time_in))
                ->isNotEmpty();
        })->count();

        $countJournals = $activeAssignmentsForStats->filter(function (Assignment $assignment) use ($submittedStatuses, $dailyFrom) {
            return $assignment->workLogs
                ->whereIn('status', $submittedStatuses)
                ->where('work_date', '>=', $dailyFrom)
                ->filter(fn (WorkLog $log) => empty($log->time_in))
                ->isNotEmpty();
        })->count();

        $asPercent = function (int $count) use ($totalActive) {
            if ($totalActive === 0) {
                return 0;
            }

            return round(($count / $totalActive) * 100, 1);
        };

        $arMetrics = collect([
            [
                'key' => 'ar_daily',
                'label' => 'Accomplishment Report (Daily)',
                'percent' => $asPercent($countDailyAr),
            ],
            [
                'key' => 'ar_weekly',
                'label' => 'Accomplishment Report (Weekly)',
                'percent' => $asPercent($countWeeklyAr),
            ],
            [
                'key' => 'ar_monthly',
                'label' => 'Accomplishment Report (Monthly)',
                'percent' => $asPercent($countMonthlyAr),
            ],
        ]);

        $trackingBoxes = [
            'attendance_meeting' => [
                'label' => 'OJT Attendance (OJT Meeting)',
                'count' => $countAttendance,
                'total' => $totalActive,
                'period' => 'Last 7 days',
            ],
            'mapping' => [
                'label' => 'Mapping',
                'count' => 0,
                'total' => null,
                'period' => 'Not configured',
            ],
            'journals' => [
                'label' => 'Journals',
                'count' => $countJournals,
                'total' => $totalActive,
                'period' => 'Last 7 days',
            ],
        ];

        $journalsLogsForWeek = WorkLog::where('work_date', '>=', $fromDate)
            ->whereNull('time_in')
            ->whereIn('status', $submittedStatuses)
            ->where('type', 'daily')
            ->get();

        $journalsTrend = collect(range(0, 6))->map(function ($offset) use ($journalsLogsForWeek) {
            $date = Carbon::now()->subDays(6 - $offset)->toDateString();
            $label = Carbon::parse($date)->format('M d');

            $count = $journalsLogsForWeek->filter(fn (WorkLog $log) => $log->work_date?->toDateString() === $date)->count();

            return [
                'day' => $label,
                'total' => $count,
            ];
        });

        return view('dashboards.coordinator', [
            'totalStudents' => $students,
            'supervisorsCount' => $supervisors,
            'advisersCount' => $advisersCount,
            'companiesCount' => $companies,
            'activeAssignmentsCount' => $activeAssignments,
            'recentActivities' => $recentActivities,
            'studentProgress' => $studentProgress,
            'studentsAlerts' => $studentsAlerts,
            'supervisorsAlerts' => $supervisorsAlerts,
            'companiesAlerts' => $companiesAlerts,
            'sectionProgress' => $sectionProgress,
            'attendanceTrend' => $attendanceTrend,
            'ojtAdvisers' => $ojtAdvisers,
            'departmentsData' => $departmentsData,
            'companiesForMap' => $companiesForMap,
            'arMetrics' => $arMetrics,
            'trackingBoxes' => $trackingBoxes,
            'journalsTrend' => $journalsTrend,
        ]);
    }

    public function studentOverview(Request $request): View
    {
        $query = User::where('role', User::ROLE_STUDENT)
            ->with(['studentAssignments.company', 'studentAssignments.supervisor']);

        $query = $this->applyApprovedStudentScope($query);

        // Search by Name or Email
        if ($request->has('search') && $request->search) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%")
                    ->orWhere('email', 'like', "%{$searchTerm}%")
                    ->orWhere('lastname', 'like', "%{$searchTerm}%")
                    ->orWhere('firstname', 'like', "%{$searchTerm}%");
            });
        }

        // Filter by Company
        if ($request->has('company_id') && $request->company_id) {
            $query->whereHas('studentAssignments', function ($q) use ($request) {
                $q->where('company_id', $request->company_id)
                    ->where('status', 'active');
            });
        }

        // Sorting Logic
        $sort = $request->query('sort', 'name'); // Default sort by name
        $direction = $request->query('direction', 'asc'); // Default direction asc

        if ($sort === 'name') {
            $query->orderBy('name', $direction);
        } elseif ($sort === 'company') {
            // Sort by related company name
            $query->join('assignments', 'users.id', '=', 'assignments.student_id')
                ->join('companies', 'assignments.company_id', '=', 'companies.id')
                ->where('assignments.status', 'active')
                ->select('users.*') // Avoid selecting joined columns
                ->orderBy('companies.name', $direction);
        } elseif ($sort === 'hours') {
            // Sort by progress/hours logic is complex in DB, skipping for now or doing in memory (if small dataset)
            // For scalable apps, caching total hours on user model is better.
            // We'll keep basic sorting for DB columns.
        }

        $companies = Company::orderBy('name')->get();

        // Get all students matching filters
        $students = $query->get();

        $students = $students->map(function (User $student) {
            $student->section = $student->normalizedStudentSection();
            $student->department = $student->normalizedStudentDepartment();

            return $student;
        });

        // Group students using normalized section-major labels only.
        $groupedStudents = $students->groupBy(function (User $student) {
            $section = $student->section ?? User::STUDENT_SECTION_BSIT_4A;
            $department = $student->department ?? User::STUDENT_MAJOR_COMPUTER_TECHNOLOGY;

            return sprintf('%s (%s)', $section, $department);
        })->sortKeys();

        return view('coordinator.student-overview', compact('groupedStudents', 'companies'));
    }

    public function supervisorOverview(): View
    {
        $supervisors = User::where('role', User::ROLE_SUPERVISOR)
            ->with([
                'supervisorProfile',
                'supervisorAssignments' => function ($query) {
                    $query->with(['student.studentProfile', 'company', 'workLogs', 'tasks']);
                },
            ])
            ->orderBy('name')
            ->get();

        // Calculate summary metrics
        $totalSupervisors = $supervisors->count();
        $totalCompanies = $supervisors
            ->flatMap(fn ($s) => $s->supervisorAssignments->pluck('company_id'))
            ->unique()
            ->count();
        $totalStudents = $supervisors
            ->flatMap(fn ($s) => $s->supervisorAssignments->pluck('student_id'))
            ->unique()
            ->count();

        // Prepare supervisor data with metrics
        $supervisorData = $supervisors->map(function (User $supervisor) {
            $assignments = $supervisor->supervisorAssignments;
            
            $companies = $assignments
                ->pluck('company')
                ->unique('id')
                ->values();

            $totalStudents = $assignments->count();
            $activeAssignments = $assignments->where('status', 'active');
            $activeStudents = $activeAssignments->count();

            // Get evaluations for students under this supervisor
            $studentIds = $assignments->pluck('student_id')->unique();
            $completedEvaluations = PerformanceEvaluation::whereIn('student_id', $studentIds)
                ->where('supervisor_id', $supervisor->id)
                ->where('submitted_at', '!=', null)
                ->count();
            
            $pendingEvaluations = $activeStudents - $completedEvaluations;

            // Get active tasks/monitoring
            $activeTasks = $assignments
                ->flatMap(fn ($a) => $a->tasks)
                ->where('status', '!=', 'submitted')
                ->count();

            $students = $assignments->map(function ($assignment) {
                return [
                    'id' => $assignment->student->id,
                    'name' => $assignment->student->name,
                    'email' => $assignment->student->email,
                    'program' => $assignment->student->studentProfile?->program ?? 'N/A',
                    'company_id' => $assignment->company?->id,
                    'company_name' => $assignment->company?->name,
                    'status' => $assignment->status,
                ];
            })->values();

            return [
                'id' => $supervisor->id,
                'name' => $supervisor->name,
                'email' => $supervisor->email,
                'phone' => $supervisor->supervisorProfile?->phone ?? 'N/A',
                'position_title' => $supervisor->supervisorProfile?->position_title ?? 'N/A',
                'department' => $supervisor->supervisorProfile?->department ?? 'N/A',
                'photo_url' => $supervisor->profile_photo_path ? $supervisor->profile_photo_url : null,
                'companies' => $companies->toArray(),
                'total_students' => $totalStudents,
                'active_students' => $activeStudents,
                'completed_evaluations' => $completedEvaluations,
                'pending_evaluations' => $pendingEvaluations,
                'active_tasks' => $activeTasks,
                'students' => $students->toArray(),
                'status' => $activeStudents > 0 ? 'Active' : 'Inactive',
            ];
        })->values();

        $activeSupervisors = $supervisorData->filter(fn ($s) => $s['status'] === 'Active')->count();

        $companies = Company::orderBy('name')->get();

        return view('coordinator.supervisor-overview', [
            'supervisors' => $supervisorData,
            'companies' => $companies,
            'totalSupervisors' => $totalSupervisors,
            'totalCompanies' => $totalCompanies,
            'totalStudents' => $totalStudents,
            'activeSupervisors' => $activeSupervisors,
        ]);
    }

    public function adviserOverview(): View
    {
        $advisers = User::where('role', User::ROLE_OJT_ADVISER)
            ->with([
                'ojtAdviserProfile',
                'ojtAdviserAssignments' => function ($query) {
                    $query->with(['student.studentProfile', 'company', 'supervisor', 'workLogs', 'tasks']);
                },
            ])
            ->orderBy('name')
            ->get();

        $advisersData = $advisers->map(function (User $adviser) {
            $assignments = $adviser->ojtAdviserAssignments;

            $studentData = $assignments->map(function ($assignment) {
                $requiredHours = $assignment->required_hours ?? 0;
                $completedHours = $assignment->workLogs()
                    ->where('status', '=', 'approved')
                    ->sum('hours');

                $tasks = $assignment->tasks;
                $submittedTasks = $tasks->where('status', 'submitted')->count();
                $totalTasks = $tasks->count();

                $hoursPercentage = $requiredHours > 0 ? ($completedHours / $requiredHours) * 100 : 0;
                $tasksPercentage = $totalTasks > 0 ? ($submittedTasks / $totalTasks) * 100 : 0;

                $student = $assignment->student;

                return [
                    'id' => $student?->id,
                    'name' => $student?->name,
                    'email' => $student?->email,
                    'program' => $student->studentProfile?->program ?? 'N/A',
                    'year_level' => $student->studentProfile?->year_level ?? 'N/A',
                    'section' => $student?->normalizedStudentSection() ?? User::STUDENT_SECTION_BSIT_4A,
                    'company' => $assignment->company?->name,
                    'company_id' => $assignment->company?->id,
                    'supervisor' => $assignment->supervisor?->name,
                    'assignment_id' => $assignment->id,
                    'status' => $assignment->status,
                    'start_date' => $assignment->start_date?->format('M d, Y'),
                    'end_date' => $assignment->end_date?->format('M d, Y'),
                    'required_hours' => $requiredHours,
                    'completed_hours' => $completedHours,
                    'hours_percentage' => round($hoursPercentage, 1),
                    'submitted_tasks' => $submittedTasks,
                    'total_tasks' => $totalTasks,
                    'tasks_percentage' => round($tasksPercentage, 1),
                    'evaluation_status' => $assignment->workLogs->where('status', 'pending')->count() > 0 ? 'Pending' : 'Evaluated',
                ];
            })->values()->toArray();

            $totalStudents = count($studentData);
            $activeStudents = collect($studentData)->filter(fn ($s) => $s['status'] === 'active')->count();
            $completedStudents = collect($studentData)->filter(fn ($s) => $s['status'] === 'completed')->count();
            $pendingEvaluations = collect($studentData)->filter(fn ($s) => $s['evaluation_status'] === 'Pending')->count();

            return [
                'id' => $adviser->id,
                'name' => $adviser->name,
                'email' => $adviser->email,
                'department' => $adviser->ojtAdviserProfile?->department ?? 'N/A',
                'phone' => $adviser->ojtAdviserProfile?->phone ?? 'N/A',
                'photo_url' => $adviser->profile_photo_path ? $adviser->profile_photo_url : null,
                'total_students' => $totalStudents,
                'active_students' => $activeStudents,
                'completed_students' => $completedStudents,
                'pending_evaluations' => $pendingEvaluations,
                'students' => $studentData,
                'companies_supervised' => collect($studentData)->pluck('company_id')->unique()->count(),
            ];
        })->values();

        $sectionAssignments = Assignment::query()
            ->with([
                'student.studentProfile',
                'ojtAdviser.ojtAdviserProfile',
            ])
            ->active()
            ->whereHas('student', fn ($query) => $query->eligibleStudentForRoster())
            ->latestRelevant()
            ->get()
            ->unique('student_id')
            ->values();

        $monitoredSections = [
            User::STUDENT_SECTION_BSIT_4A,
            User::STUDENT_SECTION_BSIT_4B,
            User::STUDENT_SECTION_BSIT_4C,
            User::STUDENT_SECTION_BSIT_4D,
        ];

        $sectionAdvisoryOverview = collect($monitoredSections)->map(function (string $section) use ($sectionAssignments) {
            $sectionRows = $sectionAssignments
                ->filter(fn (Assignment $assignment) => ($assignment->student?->normalizedStudentSection() ?? User::STUDENT_SECTION_BSIT_4A) === $section)
                ->values();

            $assignedRows = $sectionRows
                ->filter(fn (Assignment $assignment) => ! empty($assignment->ojt_adviser_id))
                ->values();

            $adviserGroups = $assignedRows
                ->groupBy('ojt_adviser_id')
                ->map(function ($assignments) {
                    /** @var Assignment $firstAssignment */
                    $firstAssignment = $assignments->first();
                    $adviser = $firstAssignment?->ojtAdviser;

                    return [
                        'id' => $adviser?->id,
                        'name' => $adviser?->name ?? 'Unknown Adviser',
                        'email' => $adviser?->email ?? 'N/A',
                        'phone' => $adviser?->ojtAdviserProfile?->phone ?? null,
                        'department' => $adviser?->ojtAdviserProfile?->department ?? ($adviser?->department ?? null),
                        'student_count' => $assignments->count(),
                        'students' => $assignments->map(function (Assignment $assignment) {
                            $student = $assignment->student;

                            return [
                                'id' => $student?->id,
                                'name' => $student?->name ?? 'Unknown Student',
                                'email' => $student?->email ?? 'N/A',
                                'section' => $student?->normalizedStudentSection() ?? ($student?->section ?? ''),
                                'program' => $student?->studentProfile?->program ?? ($student?->department ?? 'N/A'),
                            ];
                        })->sortBy('name', SORT_NATURAL | SORT_FLAG_CASE)->values(),
                    ];
                })
                ->sortBy('name', SORT_NATURAL | SORT_FLAG_CASE)
                ->values();

            $unassignedStudents = $sectionRows
                ->filter(fn (Assignment $assignment) => empty($assignment->ojt_adviser_id))
                ->map(function (Assignment $assignment) {
                    $student = $assignment->student;

                    return [
                        'id' => $student?->id,
                        'name' => $student?->name ?? 'Unknown Student',
                        'email' => $student?->email ?? 'N/A',
                        'program' => $student?->studentProfile?->program ?? ($student?->department ?? 'N/A'),
                    ];
                })
                ->sortBy('name', SORT_NATURAL | SORT_FLAG_CASE)
                ->values();

            return [
                'section' => $section,
                'total_students' => $sectionRows->count(),
                'assigned_students' => $assignedRows->count(),
                'unassigned_students_count' => $unassignedStudents->count(),
                'advisers' => $adviserGroups,
                'unassigned_students' => $unassignedStudents,
                'has_assignment' => $adviserGroups->isNotEmpty(),
            ];
        })->values();

        $companies = Company::orderBy('name')->get();

        return view('coordinator.adviser-overview', [
            'advisersData' => $advisersData,
            'companies' => $companies,
            'sectionAdvisoryOverview' => $sectionAdvisoryOverview,
        ]);
    }

    public function assignTask(): View
    {
        return view('coordinator.assign-task');
    }

    public function dailyJournals(): View
    {
        // Get all work logs, ordered by date
        $journals = WorkLog::with(['assignment.student', 'assignment.company', 'reviewer'])
            ->latest('work_date')
            ->get();

        // Group by Student Section and Name
        $groupedJournals = $journals->groupBy(function ($log) {
            $student = $log->assignment->student;
            $section = $student?->normalizedStudentSection() ?? User::STUDENT_SECTION_BSIT_4A;

            return $section;
        })->sortKeys()->map(function ($logs) {
            // Inside each section, group by Student Name
            return $logs->groupBy(function ($log) {
                return $log->assignment->student->name;
            })->sortKeys();
        });

        return view('coordinator.daily-journals', compact('groupedJournals'));
    }

    public function accomplishmentReports(): View
    {
        $reportTypes = ['daily', 'weekly', 'monthly'];
        $submittedStatuses = ['submitted', 'approved', 'graded'];
        $reportWindows = [
            'daily' => Carbon::now()->subDays(7)->startOfDay(),
            'weekly' => Carbon::now()->subDays(30)->startOfDay(),
            'monthly' => Carbon::now()->subDays(90)->startOfDay(),
        ];

        $approvedStudentIds = $this->applyApprovedStudentScope(
            User::query()->where('role', User::ROLE_STUDENT)
        )->pluck('id');

        $assignments = Assignment::query()
            ->with([
                'student',
                'company',
                'workLogs' => function ($query) use ($reportTypes) {
                    $query->whereNull('time_in')
                        ->whereIn('type', $reportTypes)
                        ->orderByDesc('work_date')
                        ->orderByDesc('updated_at');
                },
            ])
            ->where('status', 'active')
            ->when($approvedStudentIds->isNotEmpty(), function ($query) use ($approvedStudentIds) {
                $query->whereIn('student_id', $approvedStudentIds->all());
            })
            ->when($approvedStudentIds->isEmpty(), function ($query) {
                $query->whereRaw('1 = 0');
            })
            ->get()
            ->filter(fn (Assignment $assignment) => $assignment->student !== null)
            ->values();

        $studentReports = $assignments->map(function (Assignment $assignment) use ($reportTypes, $reportWindows, $submittedStatuses) {
            $student = $assignment->student;
            $section = $student->normalizedStudentSection() ?? User::STUDENT_SECTION_BSIT_4A;
            $department = $student->normalizedStudentDepartment() ?? User::STUDENT_MAJOR_COMPUTER_TECHNOLOGY;
            $companyName = $assignment->company?->name ?? 'No company assigned';
            $reports = collect($assignment->workLogs ?? [])
                ->filter(fn (WorkLog $log) => in_array($log->type, $reportTypes, true))
                ->values();

            $typeStatuses = [];

            foreach ($reportTypes as $type) {
                $recentReports = $reports
                    ->filter(function (WorkLog $log) use ($type, $reportWindows) {
                        return $log->type === $type
                            && $log->work_date !== null
                            && $log->work_date->greaterThanOrEqualTo($reportWindows[$type]);
                    })
                    ->values();

                $latestReport = $recentReports->first();
                $statusLabel = 'Not Submitted';

                if ($latestReport) {
                    if (in_array((string) $latestReport->status, $submittedStatuses, true)) {
                        $statusLabel = 'Submitted';
                    } elseif ($latestReport->status === 'draft') {
                        $statusLabel = 'Pending';
                    } else {
                        $statusLabel = 'Incomplete';
                    }
                }

                $typeStatuses[$type] = [
                    'label' => $statusLabel,
                    'last_date' => $latestReport?->work_date?->format('M d, Y'),
                    'report_count' => $recentReports->count(),
                ];
            }

            $overallStatus = 'Not Submitted';
            $statusLabels = collect($typeStatuses)->pluck('label');

            if ($statusLabels->every(fn ($label) => $label === 'Submitted')) {
                $overallStatus = 'Submitted';
            } elseif ($statusLabels->contains('Pending')) {
                $overallStatus = 'Pending';
            } elseif ($statusLabels->contains('Incomplete') || $statusLabels->contains('Submitted')) {
                $overallStatus = 'Incomplete';
            }

            return [
                'assignment_id' => $assignment->id,
                'student_id' => $student->id,
                'student_name' => $student->name,
                'student_email' => $student->email,
                'section' => $section,
                'department' => $department,
                'section_label' => sprintf('%s (%s)', $section, $department),
                'company' => $companyName,
                'approved_hours' => round((float) $assignment->totalApprovedHours(), 1),
                'overall_status' => $overallStatus,
                'type_statuses' => $typeStatuses,
                'report_count' => $reports->count(),
                'reports' => $reports->map(function (WorkLog $log) use ($submittedStatuses) {
                    $statusLabel = 'Incomplete';

                    if (in_array((string) $log->status, $submittedStatuses, true)) {
                        $statusLabel = 'Submitted';
                    } elseif ($log->status === 'draft') {
                        $statusLabel = 'Pending';
                    }

                    return [
                        'id' => $log->id,
                        'type' => $log->type,
                        'date' => $log->work_date?->format('M d, Y') ?? 'No date',
                        'status' => $statusLabel,
                        'attachment_url' => $log->attachment_path ? route('coordinator.worklogs.attachment', $log->id) : null,
                        'print_url' => route('coordinator.worklogs.print', $log->id),
                    ];
                })->values()->all(),
            ];
        })->sortBy([
            ['section', 'asc'],
            ['student_name', 'asc'],
        ])->values();

        $summary = [
            'total_students' => $studentReports->count(),
            'submitted' => $studentReports->where('overall_status', 'Submitted')->count(),
            'pending' => $studentReports->where('overall_status', 'Pending')->count(),
            'incomplete' => $studentReports->where('overall_status', 'Incomplete')->count(),
            'not_submitted' => $studentReports->where('overall_status', 'Not Submitted')->count(),
        ];

        $sectionSummary = $studentReports
            ->groupBy('section_label')
            ->map(function ($rows, $sectionLabel) {
                return [
                    'section' => $sectionLabel,
                    'total_students' => $rows->count(),
                    'submitted' => $rows->where('overall_status', 'Submitted')->count(),
                    'pending' => $rows->where('overall_status', 'Pending')->count(),
                    'needs_follow_up' => $rows->filter(function ($row) {
                        return in_array($row['overall_status'], ['Incomplete', 'Not Submitted'], true);
                    })->count(),
                ];
            })
            ->sortKeys()
            ->values();

        return view('coordinator.accomplishment-reports.index', compact('studentReports', 'summary', 'sectionSummary'));
    }

    public function complianceOverview(): View
    {
        $topSummary = $this->buildCoordinatorTopSummary();

        $assignments = Assignment::with([
            'student.studentProfile',
            'company',
            'workLogs',
            'tasks'
        ])->get();

        // Calculate overall metrics
        $totalStudents = $assignments->count();
        $onTrackCount = 0;
        $atRiskCount = 0;
        $totalHoursRequired = 0;
        $totalHoursCompleted = 0;
        $totalTasksSubmitted = 0;
        $totalTasksOutstanding = 0;

        $studentMetrics = [];

        foreach ($assignments as $assignment) {
            $requiredHours = $assignment->required_hours ?? 0;
            $completedHours = $assignment->workLogs()
                ->where('status', '=', 'approved')
                ->sum('hours');

            $tasks = $assignment->tasks;
            $submittedTasks = $tasks->where('status', 'submitted')->count();
            $totalTasks = $tasks->count();

            $hoursPercentage = $requiredHours > 0 ? ($completedHours / $requiredHours) * 100 : 0;
            $tasksPercentage = $totalTasks > 0 ? ($submittedTasks / $totalTasks) * 100 : 0;

            $isOnTrack = $hoursPercentage >= 80 && $tasksPercentage >= 80;
            if ($isOnTrack) {
                $onTrackCount++;
            } else {
                $atRiskCount++;
            }

            $totalHoursRequired += $requiredHours;
            $totalHoursCompleted += $completedHours;
            $totalTasksSubmitted += $submittedTasks;
            $totalTasksOutstanding += $totalTasks - $submittedTasks;

            $studentMetrics[] = [
                'assignment' => $assignment,
                'student' => $assignment->student,
                'studentProfile' => $assignment->student->studentProfile,
                'company' => $assignment->company,
                'requiredHours' => $requiredHours,
                'completedHours' => $completedHours,
                'hoursPercentage' => round($hoursPercentage, 1),
                'submittedTasks' => $submittedTasks,
                'totalTasks' => $totalTasks,
                'tasksPercentage' => round($tasksPercentage, 1),
                'isOnTrack' => $isOnTrack,
                'status' => $assignment->status,
                'daysRemaining' => $assignment->end_date ? $assignment->end_date->diffInDays(now(), false) : null,
            ];
        }

        $overallHoursPercentage = $totalHoursRequired > 0 ? round(($totalHoursCompleted / $totalHoursRequired) * 100, 1) : 0;
        $overallTasksPercentage = $totalTasksSubmitted + $totalTasksOutstanding > 0 
            ? round(($totalTasksSubmitted / ($totalTasksSubmitted + $totalTasksOutstanding)) * 100, 1) 
            : 0;

        $complianceScore = round(($overallHoursPercentage + $overallTasksPercentage) / 2, 1);

        return view('coordinator.compliance-overview', [
            'topSummary' => $topSummary,
            'totalStudents' => $totalStudents,
            'onTrackCount' => $onTrackCount,
            'atRiskCount' => $atRiskCount,
            'totalHoursRequired' => $totalHoursRequired,
            'totalHoursCompleted' => $totalHoursCompleted,
            'overallHoursPercentage' => $overallHoursPercentage,
            'totalTasksSubmitted' => $totalTasksSubmitted,
            'totalTasksOutstanding' => $totalTasksOutstanding,
            'overallTasksPercentage' => $overallTasksPercentage,
            'complianceScore' => $complianceScore,
            'studentMetrics' => collect($studentMetrics),
        ]);
    }

    public function companiesIndex(): View
    {
        $companies = Company::with([
            'assignments.student',
            'assignments.supervisor',
            'assignments.ojtAdviser',
            'supervisorProfiles.user',
        ])
            ->orderBy('name')
            ->get();

        return view('coordinator.companies.index', [
            'companies' => $companies,
        ]);
    }

    public function companiesStore(StoreCompanyRequest $request): RedirectResponse
    {
        Company::create($request->validated());

        return redirect()->route('coordinator.companies.index')
            ->with('status', 'company-created');
    }

    public function companiesUpdate(Request $request, Company $company): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:companies,name,'.$company->id],
            'industry' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string'],
            'city' => ['nullable', 'string', 'max:255'],
            'state' => ['nullable', 'string', 'max:255'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'country' => ['nullable', 'string', 'max:100'],
            'contact_person' => ['nullable', 'string', 'max:255'],
            'contact_email' => ['nullable', 'string', 'email', 'max:255'],
            'contact_phone' => ['nullable', 'string', 'max:50'],
        ]);

        $company->update($validated);

        return redirect()->route('coordinator.companies.index')
            ->with('status', 'company-updated');
    }

    public function companiesDestroy(Company $company): RedirectResponse
    {
        $assignmentCount = $company->assignments()->count();
        $supervisorProfilesCount = $company->supervisorProfiles()->count();

        if ($assignmentCount > 0 || $supervisorProfilesCount > 0) {
            return redirect()->route('coordinator.companies.index')
                ->withErrors([
                    'error' => 'Cannot delete company while it is linked to deployment records or supervisor accounts.',
                ]);
        }

        $company->delete();

        return redirect()->route('coordinator.companies.index')
            ->with('status', 'company-deleted');
    }

    public function deploymentIndex(): View
    {
        $topSummary = $this->buildCoordinatorTopSummary();

        $assignments = Assignment::rosterForCoordinator([
            'student.studentProfile',
            'supervisor.supervisorProfile.company',
            'company',
            'ojtAdviser',
        ]);

        // Get only deployable students and group by section for Select2
        $students = User::eligibleStudentForDeployment()
            ->orderBy('section')
            ->orderBy('lastname')
            ->get()
            ->groupBy(function ($student) {
                $normalizedSection = $student->normalizedStudentSection() ?? User::STUDENT_SECTION_BSIT_4A;

                return "Section: {$normalizedSection}";
            });

        $supervisors = User::where('role', User::ROLE_SUPERVISOR)
            ->with('supervisorProfile.company')
            ->orderBy('name')
            ->get();
        $ojtAdvisers = User::where('role', User::ROLE_OJT_ADVISER)->orderBy('name')->get();
        $companies = Company::orderBy('name')->get();

        // Calculate summary metrics
        $totalDeployed = $assignments->count();
        $supervisorOnly = $assignments->filter(fn ($a) => $a->supervisor_id && !$a->ojt_adviser_id)->count();
        $adviserOnly = $assignments->filter(fn ($a) => $a->ojt_adviser_id && !$a->supervisor_id)->count();
        $fullyAssigned = $assignments->filter(fn ($a) => $a->supervisor_id && $a->ojt_adviser_id)->count();
        $incomplete = $assignments->filter(fn ($a) => !$a->supervisor_id || !$a->ojt_adviser_id)->count();
        $active = $assignments->filter(fn ($a) => $a->status === 'active')->count();

        // Prepare detailed deployment data
        $deploymentData = $assignments->map(function (Assignment $assignment) {
            $student = $assignment->student;
            $supervisorAssigned = !empty($assignment->supervisor_id);
            $adviserAssigned = !empty($assignment->ojt_adviser_id);
            $company = $assignment->resolvedCompany();
            $companyId = $assignment->resolvedCompanyId();
            $status = strtolower(trim((string) ($assignment->status ?? 'unknown')));
            $renderedHours = round($assignment->approvedHoursTotal(), 2);
            $requiredHours = (int) ($assignment->required_hours ?? 0);

            return [
                'id' => $assignment->id,
                'student_id' => $student?->id,
                'student_name' => $student?->name ?? 'Unknown Student',
                'student_email' => $student?->email ?? '',
                'student_program' => $student?->studentProfile?->program ?? ($student?->department ?? 'N/A'),
                'student_section' => $student?->normalizedStudentSection() ?? ($student?->section ?? ''),
                'supervisor_id' => $assignment->supervisor_id,
                'supervisor_name' => $assignment->supervisor?->name ?? 'Not Assigned',
                'adviser_id' => $assignment->ojt_adviser_id,
                'adviser_name' => $assignment->ojtAdviser?->name ?? 'Not Assigned',
                'company_id' => $companyId,
                'company_name' => $company?->name ?? 'N/A',
                'start_date' => $assignment->start_date?->format('Y-m-d'),
                'end_date' => $assignment->end_date?->format('Y-m-d'),
                'duration_label' => $assignment->start_date && $assignment->end_date
                    ? $assignment->start_date->format('M d, Y').' to '.$assignment->end_date->format('M d, Y')
                    : 'Not specified',
                'status' => $status,
                'required_hours' => $requiredHours,
                'rendered_hours' => $renderedHours,
                'is_fully_assigned' => $supervisorAssigned && $adviserAssigned,
                'is_partially_assigned' => ($supervisorAssigned || $adviserAssigned) && !($supervisorAssigned && $adviserAssigned),
                'is_unassigned' => !$supervisorAssigned && !$adviserAssigned,
                'deployment_status' => $supervisorAssigned && $adviserAssigned ? 'complete' : (($supervisorAssigned || $adviserAssigned) ? 'incomplete' : 'unassigned'),
            ];
        })->values();

        $filterCompanies = $deploymentData
            ->filter(fn (array $deployment) => !empty($deployment['company_id']))
            ->map(fn (array $deployment) => [
                'id' => $deployment['company_id'],
                'name' => $deployment['company_name'],
            ])
            ->unique('id')
            ->sortBy('name', SORT_NATURAL | SORT_FLAG_CASE)
            ->values();

        $filterSupervisors = $deploymentData
            ->filter(fn (array $deployment) => !empty($deployment['supervisor_id']))
            ->map(fn (array $deployment) => [
                'id' => $deployment['supervisor_id'],
                'name' => $deployment['supervisor_name'],
            ])
            ->unique('id')
            ->sortBy('name', SORT_NATURAL | SORT_FLAG_CASE)
            ->values();

        $filterAdvisers = $deploymentData
            ->filter(fn (array $deployment) => !empty($deployment['adviser_id']))
            ->map(fn (array $deployment) => [
                'id' => $deployment['adviser_id'],
                'name' => $deployment['adviser_name'],
            ])
            ->unique('id')
            ->sortBy('name', SORT_NATURAL | SORT_FLAG_CASE)
            ->values();

        $statusOptions = $deploymentData
            ->pluck('status')
            ->filter()
            ->unique()
            ->sort()
            ->values();

        return view('coordinator.deployment.index', [
            'topSummary' => $topSummary,
            'deploymentData' => $deploymentData,
            'assignments' => $assignments,
            'groupedStudents' => $students,
            'supervisors' => $supervisors,
            'ojtAdvisers' => $ojtAdvisers,
            'companies' => $companies,
            'filterCompanies' => $filterCompanies,
            'filterSupervisors' => $filterSupervisors,
            'filterAdvisers' => $filterAdvisers,
            'statusOptions' => $statusOptions,
            'totalDeployed' => $totalDeployed,
            'supervisorOnly' => $supervisorOnly,
            'adviserOnly' => $adviserOnly,
            'fullyAssigned' => $fullyAssigned,
            'incomplete' => $incomplete,
            'active' => $active,
        ]);
    }

    private function buildCoordinatorTopSummary(): array
    {
        $approvedStudentsQuery = User::query()->where('role', User::ROLE_STUDENT);

        if (Schema::hasColumn('users', 'status')) {
            $approvedStudentsQuery->where(function ($query) {
                $query->whereIn('status', ['approved', 'active'])
                    ->orWhereNull('status');
            });
        }

        if (Schema::hasColumn('users', 'is_approved')) {
            $approvedStudentsQuery->where('is_approved', true);
        }

        $approvedStudentIds = $approvedStudentsQuery->pluck('id');
        $totalStudents = $approvedStudentIds->count();

        $activeAssignments = Assignment::with(['workLogs'])
            ->where('status', 'active')
            ->when($approvedStudentIds->isNotEmpty(), function ($query) use ($approvedStudentIds) {
                $query->whereIn('student_id', $approvedStudentIds->all());
            }, function ($query) {
                $query->whereRaw('1 = 0');
            })
            ->get()
            ->unique('student_id')
            ->values();

        $activeOJTs = $activeAssignments->count();
        $totalCompanies = Company::count();
        $advisersCount = User::where('role', User::ROLE_OJT_ADVISER)->count();
        $supervisorsCount = User::where('role', User::ROLE_SUPERVISOR)->count();

        $pendingApprovalsQuery = User::query()
            ->whereIn('role', [User::ROLE_STUDENT, User::ROLE_SUPERVISOR, User::ROLE_OJT_ADVISER]);

        if (Schema::hasColumn('users', 'has_requested_account')) {
            $pendingApprovalsQuery->where('has_requested_account', true);
        }

        if (Schema::hasColumn('users', 'status')) {
            $pendingApprovalsQuery->where('status', 'pending');
        } elseif (Schema::hasColumn('users', 'is_approved')) {
            $pendingApprovalsQuery->where('is_approved', false);
        }

        $pendingApprovals = (int) $pendingApprovalsQuery->count();

        $pendingAccomplishmentReports = 0;
        $studentsNeedingAttention = 0;
        $submittedStatuses = ['submitted', 'approved', 'graded'];
        $reportTypes = ['daily', 'weekly', 'monthly'];
        $reportWindows = [
            'daily' => Carbon::now()->subDays(7),
            'weekly' => Carbon::now()->subDays(30),
            'monthly' => Carbon::now()->subDays(90),
        ];

        foreach ($activeAssignments as $assignment) {
            $logs = $assignment->workLogs ?? collect();
            $requiredHours = max(1, (int) ($assignment->required_hours ?? 1600));
            $approvedHours = (float) $logs->where('status', 'approved')->sum('hours');
            $progress = ($approvedHours / $requiredHours) * 100;

            $missingTypes = 0;
            foreach ($reportTypes as $type) {
                $hasRecent = $logs->contains(function ($log) use ($type, $submittedStatuses, $reportWindows) {
                    return $log->type === $type
                        && in_array($log->status, $submittedStatuses, true)
                        && is_null($log->time_in)
                        && $log->work_date
                        && Carbon::parse($log->work_date)->greaterThanOrEqualTo($reportWindows[$type]);
                });

                if (! $hasRecent) {
                    $missingTypes++;
                }
            }

            if ($missingTypes > 0) {
                $pendingAccomplishmentReports++;
            }

            $hasRecentAttendance = $logs->contains(function ($log) {
                return ! is_null($log->time_in)
                    && $log->work_date
                    && Carbon::parse($log->work_date)->greaterThanOrEqualTo(Carbon::now()->subDays(7));
            });

            if ($missingTypes >= 2 || ! $hasRecentAttendance || $progress < 5) {
                $studentsNeedingAttention++;
            }
        }

        return [
            'totalStudents' => $totalStudents,
            'activeOJTs' => $activeOJTs,
            'advisersCount' => $advisersCount,
            'supervisorsCount' => $supervisorsCount,
            'totalCompanies' => $totalCompanies,
            'pendingApprovals' => $pendingApprovals,
            'pendingAccomplishmentReports' => $pendingAccomplishmentReports,
            'studentsNeedingAttention' => $studentsNeedingAttention,
        ];
    }

    public function deploymentStore(Request $request): RedirectResponse
    {
        $request->validate([
            'student_ids' => 'nullable|array',
            'student_ids.*' => 'exists:users,id',
            'student_id' => 'nullable|exists:users,id',
            'supervisor_id' => 'required|exists:users,id',
            'supervisor_ids' => 'nullable|array',
            'supervisor_ids.*' => 'exists:users,id',
            'ojt_adviser_id' => 'nullable|exists:users,id',
            'company_id' => 'nullable|exists:companies,id',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $studentIds = $request->input('student_ids', []);
        if (empty($studentIds) && $request->filled('student_id')) {
            $studentIds = [$request->input('student_id')];
        }
        if (empty($studentIds)) {
            return redirect()->back()->withErrors([
                'student_ids' => 'The student ids field is required.',
            ]);
        }

        $eligibleStudentIds = User::eligibleStudentForDeployment()
            ->whereIn('id', $studentIds)
            ->pluck('id')
            ->map(fn ($id) => (string) $id)
            ->all();

        $invalidStudentIds = array_values(array_diff(array_map('strval', $studentIds), $eligibleStudentIds));
        if (! empty($invalidStudentIds)) {
            return redirect()->back()->withErrors([
                'student_ids' => 'One or more selected students are already deployed or assigned and cannot be deployed again.',
            ])->withInput();
        }

        $supervisorIds = collect($request->input('supervisor_ids', []))
            ->filter()
            ->map(fn ($id) => (int) $id);

        if ($request->filled('supervisor_id')) {
            $supervisorIds->push((int) $request->input('supervisor_id'));
        }

        $supervisorIds = $supervisorIds->unique()->values();

        if ($supervisorIds->isEmpty()) {
            return redirect()->back()->withErrors([
                'supervisor_id' => 'Please select at least one supervisor.',
            ])->withInput();
        }

        $supervisors = User::whereIn('id', $supervisorIds)
            ->where('role', User::ROLE_SUPERVISOR)
            ->with('supervisorProfile')
            ->get();

        if ($supervisors->count() !== $supervisorIds->count()) {
            return redirect()->back()->withErrors([
                'supervisor_id' => 'One or more selected supervisors are invalid.',
            ])->withInput();
        }

        $missingCompanySupervisor = $supervisors->first(fn (User $supervisor) => ! $supervisor->supervisorProfile?->company_id);
        if ($missingCompanySupervisor) {
            return redirect()->back()->withErrors([
                'supervisor_id' => 'Selected supervisor '.$missingCompanySupervisor->name.' has no assigned company. Please update supervisor profile first.',
            ])->withInput();
        }

        $companyIds = $supervisors
            ->map(fn (User $supervisor) => (int) $supervisor->supervisorProfile->company_id)
            ->unique()
            ->values();

        if ($companyIds->count() !== 1) {
            return redirect()->back()->withErrors([
                'supervisor_id' => 'Selected supervisors belong to different companies. Please select supervisors from the same company only.',
            ])->withInput();
        }

        $resolvedCompanyId = (int) $companyIds->first();

        if ($request->filled('company_id') && (int) $request->input('company_id') !== $resolvedCompanyId) {
            return redirect()->back()->withErrors([
                'company_id' => 'Company must match the selected supervisor company.',
            ])->withInput();
        }

        $primarySupervisorId = (int) ($request->input('supervisor_id') ?: $supervisorIds->first());

        $requiredHours = Assignment::select('required_hours', DB::raw('COUNT(*) as c'))
            ->groupBy('required_hours')
            ->orderByDesc('c')
            ->value('required_hours') ?? 1600;

        foreach ($studentIds as $studentId) {
            // Prevent duplicate deployment records for already assigned students.
            $exists = Assignment::where('student_id', $studentId)
                ->active()
                ->exists();

            if (! $exists) {
                Assignment::create([
                    'student_id' => $studentId,
                    'supervisor_id' => $primarySupervisorId,
                    'ojt_adviser_id' => $request->ojt_adviser_id,
                    'coordinator_id' => $request->user()->id,
                    'company_id' => $resolvedCompanyId,
                    'start_date' => $request->start_date,
                    'end_date' => $request->end_date,
                    'status' => 'active',
                    'required_hours' => $requiredHours, // Default (configurable)
                ]);
            }
        }

        return redirect()->route('coordinator.deployment.index')
            ->with('status', 'Deployments created successfully.');
    }

    public function deploymentUpdate(Request $request, Assignment $assignment): JsonResponse|RedirectResponse
    {
        $request->validate([
            'supervisor_id' => 'nullable|exists:users,id',
            'ojt_adviser_id' => 'nullable|exists:users,id',
            'company_id' => 'nullable|exists:companies,id',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'required_hours' => 'nullable|integer|min:1|max:5000',
            'status' => 'nullable|string|in:active,inactive,completed',
        ]);

        $requestedCompanyId = $request->input('company_id') ?: null;
        $payload = [
            'supervisor_id' => $request->input('supervisor_id') ?: null,
            'ojt_adviser_id' => $request->input('ojt_adviser_id') ?: null,
            'company_id' => $requestedCompanyId ?: $assignment->company_id,
            'start_date' => $request->input('start_date') ?: null,
            'end_date' => $request->input('end_date') ?: null,
            'status' => $request->input('status') ?: ($assignment->status ?: 'active'),
        ];

        if ($request->filled('required_hours')) {
            $payload['required_hours'] = (int) $request->input('required_hours');
        }

        if (! empty($payload['supervisor_id'])) {
            $supervisor = User::where('id', $payload['supervisor_id'])
                ->where('role', User::ROLE_SUPERVISOR)
                ->with('supervisorProfile.company')
                ->first();

            if (! $supervisor) {
                return response()->json([
                    'message' => 'Invalid supervisor selected.',
                    'errors' => [
                        'supervisor_id' => ['Invalid supervisor selected.'],
                    ],
                ], 422);
            }

            $supervisorCompanyId = $supervisor->supervisorProfile?->company_id;

            if ($supervisorCompanyId) {
                if (! empty($requestedCompanyId) && (int) $requestedCompanyId !== (int) $supervisorCompanyId) {
                    return response()->json([
                        'message' => 'Selected company must match the chosen supervisor.',
                        'errors' => [
                            'company_id' => ['Selected company must match the chosen supervisor.'],
                        ],
                    ], 422);
                }

                $payload['company_id'] = (int) $supervisorCompanyId;
            }
        }

        if (! empty($payload['ojt_adviser_id'])) {
            $adviser = User::where('id', $payload['ojt_adviser_id'])
                ->where('role', User::ROLE_OJT_ADVISER)
                ->first();

            if (! $adviser) {
                return response()->json([
                    'message' => 'Invalid adviser selected.',
                    'errors' => [
                        'ojt_adviser_id' => ['Invalid adviser selected.'],
                    ],
                ], 422);
            }
        }

        $assignment->update($payload);
        $assignment->loadMissing([
            'student.studentProfile',
            'supervisor.supervisorProfile.company',
            'company',
            'ojtAdviser',
        ]);

        $company = $assignment->resolvedCompany();
        $companyId = $assignment->resolvedCompanyId();
        $supervisorAssigned = ! empty($assignment->supervisor_id);
        $adviserAssigned = ! empty($assignment->ojt_adviser_id);

        $updatedDeployment = [
            'id' => $assignment->id,
            'student_id' => $assignment->student?->id,
            'student_name' => $assignment->student?->name ?? 'Unknown Student',
            'student_email' => $assignment->student?->email ?? '',
            'student_program' => $assignment->student?->studentProfile?->program ?? ($assignment->student?->department ?? 'N/A'),
            'student_section' => $assignment->student?->normalizedStudentSection() ?? ($assignment->student?->section ?? ''),
            'supervisor_id' => $assignment->supervisor_id,
            'supervisor_name' => $assignment->supervisor?->name ?? 'Not Assigned',
            'adviser_id' => $assignment->ojt_adviser_id,
            'adviser_name' => $assignment->ojtAdviser?->name ?? 'Not Assigned',
            'company_id' => $companyId,
            'company_name' => $company?->name ?? 'N/A',
            'start_date' => $assignment->start_date?->format('Y-m-d'),
            'end_date' => $assignment->end_date?->format('Y-m-d'),
            'duration_label' => $assignment->start_date && $assignment->end_date
                ? $assignment->start_date->format('M d, Y').' to '.$assignment->end_date->format('M d, Y')
                : 'Not specified',
            'status' => strtolower(trim((string) ($assignment->status ?? 'unknown'))),
            'required_hours' => (int) ($assignment->required_hours ?? 0),
            'rendered_hours' => round($assignment->approvedHoursTotal(), 2),
            'is_fully_assigned' => $supervisorAssigned && $adviserAssigned,
            'is_partially_assigned' => ($supervisorAssigned || $adviserAssigned) && ! ($supervisorAssigned && $adviserAssigned),
            'is_unassigned' => ! $supervisorAssigned && ! $adviserAssigned,
            'deployment_status' => $supervisorAssigned && $adviserAssigned ? 'complete' : (($supervisorAssigned || $adviserAssigned) ? 'incomplete' : 'unassigned'),
        ];

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'deployment' => $updatedDeployment,
            ]);
        }

        return redirect()->route('coordinator.deployment.index')
            ->with('status', 'Deployment updated successfully.');
    }

    public function updateRequiredHours(Request $request, Assignment $assignment): RedirectResponse
    {
        $request->validate([
            'required_hours' => ['required', 'integer', 'min:1', 'max:5000'],
        ]);

        $assignment->update([
            'required_hours' => $request->input('required_hours'),
        ]);

        return redirect()->route('coordinator.assignments.index')
            ->with('status', 'Required hours updated.');
    }

    public function hoursSettings(): View
    {
        $common = Assignment::select('required_hours', DB::raw('COUNT(*) as c'))
            ->groupBy('required_hours')
            ->orderByDesc('c')
            ->first();

        $current = $common?->required_hours ?? 1600;

        return view('coordinator.settings.hours', [
            'currentRequiredHours' => $current,
        ]);
    }

    public function bulkUpdateHours(Request $request): RedirectResponse
    {
        $request->validate([
            'required_hours' => ['required', 'integer', 'min:1', 'max:5000'],
            'scope' => ['nullable', 'in:active,all'],
        ]);

        $query = Assignment::query();
        if ($request->input('scope', 'active') === 'active') {
            $query->where('status', 'active');
        }

        $updated = $query->update(['required_hours' => $request->integer('required_hours')]);

        return redirect()->route('coordinator.settings.hours')
            ->with('status', "Updated required hours for {$updated} assignment(s).");
    }

    public function registrationApprovals(Request $request): View
    {
        $allowedRoles = [User::ROLE_STUDENT, User::ROLE_SUPERVISOR, User::ROLE_OJT_ADVISER];

        $query = User::query()
            ->whereIn('role', $allowedRoles)
            ->where('has_requested_account', true)
            ->orderByDesc('created_at');

        if (Schema::hasColumn('users', 'status')) {
            $query->where('status', 'pending');
        } elseif (Schema::hasColumn('users', 'is_approved')) {
            $query->where('is_approved', false);
        }

        if ($request->filled('q')) {
            $term = trim((string) $request->string('q'));
            $query->where(function ($q) use ($term) {
                $q->where('name', 'like', '%'.$term.'%')
                    ->orWhere('email', 'like', '%'.$term.'%')
                    ->orWhere('role', 'like', '%'.$term.'%');
            });
        }

        $users = $query->paginate(20)->withQueryString();

        return view('coordinator.registrations.pending', [
            'users' => $users,
        ]);
    }

    public function approveRegistration(User $user): RedirectResponse
    {
        if (!in_array((string) $user->role, [User::ROLE_STUDENT, User::ROLE_SUPERVISOR, User::ROLE_OJT_ADVISER], true)) {
            return redirect()->route('coordinator.registrations.pending')
                ->withErrors(['error' => 'This account role is not eligible for coordinator approval.']);
        }

        $updates = [];
        if (Schema::hasColumn('users', 'is_approved')) {
            $updates['is_approved'] = true;
        }
        if (Schema::hasColumn('users', 'status')) {
            $updates['status'] = 'approved';
        }
        if (Schema::hasColumn('users', 'approved_at')) {
            $updates['approved_at'] = now();
        }
        if (Schema::hasColumn('users', 'approved_by')) {
            $updates['approved_by'] = auth()->id();
        }
        if (Schema::hasColumn('users', 'rejected_at')) {
            $updates['rejected_at'] = null;
        }
        if (Schema::hasColumn('users', 'rejection_reason')) {
            $updates['rejection_reason'] = null;
        }

        if (!empty($updates)) {
            $user->update($updates);
        }

        return redirect()->route('coordinator.registrations.pending')
            ->with('status', 'Account approved successfully.');
    }

    public function rejectRegistration(Request $request, User $user): RedirectResponse
    {
        if (!in_array((string) $user->role, [User::ROLE_STUDENT, User::ROLE_SUPERVISOR, User::ROLE_OJT_ADVISER], true)) {
            return redirect()->route('coordinator.registrations.pending')
                ->withErrors(['error' => 'This account role is not eligible for coordinator review.']);
        }

        $validated = $request->validate([
            'reason' => ['nullable', 'string', 'max:500'],
        ]);

        $updates = [];
        if (Schema::hasColumn('users', 'is_approved')) {
            $updates['is_approved'] = false;
        }
        if (Schema::hasColumn('users', 'status')) {
            $updates['status'] = 'rejected';
        }
        if (Schema::hasColumn('users', 'rejected_at')) {
            $updates['rejected_at'] = now();
        }
        if (Schema::hasColumn('users', 'rejection_reason')) {
            $updates['rejection_reason'] = $validated['reason'] ?? null;
        }
        if (Schema::hasColumn('users', 'approved_at')) {
            $updates['approved_at'] = null;
        }
        if (Schema::hasColumn('users', 'has_requested_account')) {
            $updates['has_requested_account'] = false;
        }

        if (!empty($updates)) {
            $user->update($updates);
        }

        $this->invalidateUserSessions($user);

        return redirect()->route('coordinator.registrations.pending')
            ->with('status', 'Account request rejected.');
    }

    private function applyApprovedStudentScope($query)
    {
        if (Schema::hasColumn('users', 'status')) {
            $query->where(function ($q) {
                $q->whereIn('status', ['approved', 'active']);

                if (Schema::hasColumn('users', 'is_approved')) {
                    $q->orWhere(function ($fallback) {
                        $fallback->whereNull('status')->where('is_approved', true);
                    });
                }
            });
        } elseif (Schema::hasColumn('users', 'is_approved')) {
            $query->where('is_approved', true);
        }

        if (Schema::hasColumn('users', 'status')) {
            $query->where('status', '!=', 'rejected');
        }

        return $query;
    }

    private function invalidateUserSessions(User $user): void
    {
        if (Schema::hasTable('sessions') && Schema::hasColumn('sessions', 'user_id')) {
            DB::table('sessions')->where('user_id', $user->id)->delete();
        }

        if (Schema::hasColumn('users', 'remember_token')) {
            $user->forceFill([
                'remember_token' => Str::random(60),
            ])->save();
        }
    }


}
