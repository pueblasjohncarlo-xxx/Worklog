<?php

namespace App\Http\Controllers;

use App\Http\Requests\Coordinator\StoreCompanyRequest;
use App\Models\Assignment;
use App\Models\Company;
use App\Models\Message;
use App\Models\User;
use App\Models\WorkLog;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
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
                return [
                    'student_name' => $assignment->student->name,
                    'student_section' => $assignment->student->section ?? 'No Section',
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
            ->groupBy(fn ($a) => $a->student->section ?? 'No Section')
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
                    'photo_url' => $adviser->profile_photo_path ? Storage::url($adviser->profile_photo_path) : null,
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
            'Computer Technology' => ['4A', '4B', '4C', '4D'],
            'Electronics Technology' => ['4AE'],
        ];

        $departmentKeywords = [
            'Computer Technology' => ['computer', 'comptech'],
            'Electronics Technology' => ['electronics', 'electronic'],
        ];

        // Get students with active assignments first, then include all students for display
        $activeStudentIds = Assignment::where('status', 'active')->pluck('student_id')->unique();
        $studentsForDepartments = User::where('role', User::ROLE_STUDENT)
            ->orderBy('name')
            ->get();

        $normalizeSection = function (?string $section) {
            $value = strtoupper(trim((string) $section));
            if ($value === '') {
                return null;
            }

            $compact = str_replace([' ', '_'], '', $value);
            $compact = str_replace(['—', '–'], '-', $compact);

            if (preg_match('/\b(4A[E]?|4B|4C|4D)\b/', $compact, $m)) {
                return $m[1];
            }

            if (preg_match('/(4A[E]?|4B|4C|4D)$/', $compact, $m)) {
                return $m[1];
            }

            $parts = explode('-', $compact);
            $tail = end($parts) ?: null;
            if ($tail && preg_match('/^(4A[E]?|4B|4C|4D)$/', $tail)) {
                return $tail;
            }

            return null;
        };

        $departmentsData = collect($departmentSectionOptions)->map(function (array $sectionOptions, string $department) use ($studentsForDepartments, $normalizeSection, $departmentKeywords, $activeStudentIds) {
            $students = $studentsForDepartments->filter(function (User $u) use ($department, $departmentKeywords) {
                $dept = (string) ($u->department ?? '');
                if ($dept === '') {
                    return false;
                }

                $keywords = $departmentKeywords[$department] ?? [$department];
                foreach ($keywords as $keyword) {
                    if (stripos($dept, $keyword) !== false) {
                        return true;
                    }
                }

                return false;
            });

            $studentsBySection = collect($sectionOptions)->mapWithKeys(function (string $section) use ($students, $normalizeSection, $activeStudentIds) {
                $filtered = $students->filter(function (User $u) use ($normalizeSection, $section) {
                    return $normalizeSection($u->section) === $section;
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
            'mopping' => [
                'label' => 'Mopping',
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

        // Get messages for current coordinator
        $userId = auth()->id();
        $sentTo = Message::where('sender_id', $userId)->pluck('receiver_id');
        $receivedFrom = Message::where('receiver_id', $userId)->pluck('sender_id');
        $contactIds = $sentTo->merge($receivedFrom)->unique();

        $contacts = User::whereIn('id', $contactIds)->get()->map(function ($contact) use ($userId) {
            $lastMessage = Message::where(function ($q) use ($userId, $contact) {
                $q->where('sender_id', $userId)->where('receiver_id', $contact->id);
            })->orWhere(function ($q) use ($userId, $contact) {
                $q->where('sender_id', $contact->id)->where('receiver_id', $userId);
            })->latest()->first();

            $contact->last_message = $lastMessage;
            $contact->is_unread = $lastMessage && $lastMessage->receiver_id === $userId && is_null($lastMessage->read_at);

            return $contact;
        })->sortByDesc('last_message.created_at')->take(8); // Get top 8 contacts

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
            'messageContacts' => $contacts,
        ]);
    }

    public function studentOverview(Request $request): View
    {
        $query = User::where('role', User::ROLE_STUDENT)
            ->with(['studentAssignments.company', 'studentAssignments.supervisor']);

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

        // Group by Section/Department if no specific filters that break grouping are applied
        // For the overview, we want to see students grouped by Section (e.g. BSIT-4A)
        $groupedStudents = $students->groupBy(function ($item, $key) {
            $section = $item->section ?? 'No Section';
            $department = $item->department ? "({$item->department})" : '';

            return trim("$section $department");
        })->sortKeys();

        return view('coordinator.student-overview', compact('groupedStudents', 'companies'));
    }

    public function supervisorOverview(): View
    {
        $supervisors = User::where('role', User::ROLE_SUPERVISOR)
            ->with(['supervisorAssignments.student', 'supervisorAssignments.company'])
            ->orderBy('name')
            ->paginate(10);

        return view('coordinator.supervisor-overview', compact('supervisors'));
    }

    public function adviserOverview(): View
    {
        $advisers = User::where('role', User::ROLE_OJT_ADVISER)
            ->with([
                'ojtAdviserProfile',
                'ojtAdviserAssignments' => function ($query) {
                    $query->where('status', 'active')
                        ->with(['student', 'company', 'supervisor']);
                },
            ])
            ->orderBy('name')
            ->get();

        $advisersData = $advisers->map(function (User $adviser) {
            $studentsBySection = $adviser->ojtAdviserAssignments
                ->groupBy(fn ($assignment) => $assignment->student?->section ?? 'No Section')
                ->map(function ($assignments) {
                    return $assignments
                        ->map(function ($assignment) {
                            $student = $assignment->student;

                            return [
                                'id' => $student?->id,
                                'name' => $student?->name,
                                'email' => $student?->email,
                                'section' => $student?->section ?? 'No Section',
                                'company' => $assignment->company?->name,
                                'supervisor' => $assignment->supervisor?->name,
                            ];
                        })
                        ->filter(fn ($row) => ! empty($row['id']))
                        ->values();
                })
                ->toArray();

            return [
                'id' => $adviser->id,
                'name' => $adviser->name,
                'email' => $adviser->email,
                'department' => $adviser->ojtAdviserProfile?->department ?? 'N/A',
                'photo_url' => $adviser->profile_photo_path ? Storage::url($adviser->profile_photo_path) : null,
                'studentsBySection' => $studentsBySection,
            ];
        })->values();

        $sections = $advisersData
            ->flatMap(fn ($adviser) => array_keys($adviser['studentsBySection'] ?? []))
            ->unique()
            ->sort()
            ->values();

        return view('coordinator.adviser-overview', [
            'advisersData' => $advisersData,
            'sections' => $sections,
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
            $section = $student->section ?? 'No Section';

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
        $workLogs = WorkLog::with(['assignment.student', 'assignment.company', 'reviewer'])
            ->whereNotNull('description')
            ->orderByDesc('work_date')
            ->get();

        // Group by Section/Department like student overview
        $groupedReports = $workLogs->groupBy(function ($log) {
            $student = $log->assignment?->student;
            if (!$student) {
                return 'No Section';
            }
            
            $section = $student->section ?? 'No Section';
            $department = $student->department ? "({$student->department})" : '';

            return trim("$section $department");
        })->sortKeys();

        return view('coordinator.accomplishment-reports.index', compact('groupedReports', 'workLogs'));
    }

    public function complianceOverview(): View
    {
        return view('coordinator.compliance-overview');
    }

    public function companiesIndex(): View
    {
        $companies = Company::with(['assignments.student'])
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

    public function assignmentsIndex(): View
    {
        $assignments = Assignment::with(['student', 'supervisor', 'company', 'ojtAdviser'])
            ->orderByDesc('created_at')
            ->get();

        // Get students and group by section for Select2
        $students = User::where('role', User::ROLE_STUDENT)
            ->orderBy('section')
            ->orderBy('lastname')
            ->get()
            ->groupBy(function ($student) {
                return $student->section ? "Section: {$student->section}" : 'No Section';
            });

        $supervisors = User::where('role', User::ROLE_SUPERVISOR)->orderBy('name')->get();
        $ojtAdvisers = User::where('role', User::ROLE_OJT_ADVISER)->orderBy('name')->get();
        $companies = Company::orderBy('name')->get();

        return view('coordinator.assignments.index', [
            'assignments' => $assignments,
            'groupedStudents' => $students,
            'supervisors' => $supervisors,
            'ojtAdvisers' => $ojtAdvisers,
            'companies' => $companies,
        ]);
    }

    public function assignmentsStore(Request $request): RedirectResponse
    {
        $request->validate([
            'student_ids' => 'nullable|array',
            'student_ids.*' => 'exists:users,id',
            'student_id' => 'nullable|exists:users,id',
            'supervisor_id' => 'required|exists:users,id',
            'ojt_adviser_id' => 'nullable|exists:users,id',
            'company_id' => 'required|exists:companies,id',
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

        $requiredHours = Assignment::select('required_hours', DB::raw('COUNT(*) as c'))
            ->groupBy('required_hours')
            ->orderByDesc('c')
            ->value('required_hours') ?? 1600;

        foreach ($studentIds as $studentId) {
            // Check if active assignment exists to avoid duplicates (optional but good practice)
            $exists = Assignment::where('student_id', $studentId)
                ->where('status', 'active')
                ->exists();

            if (! $exists) {
                Assignment::create([
                    'student_id' => $studentId,
                    'supervisor_id' => $request->supervisor_id,
                    'ojt_adviser_id' => $request->ojt_adviser_id,
                    'coordinator_id' => $request->user()->id,
                    'company_id' => $request->company_id,
                    'start_date' => $request->start_date,
                    'end_date' => $request->end_date,
                    'status' => 'active',
                    'required_hours' => $requiredHours, // Default (configurable)
                ]);
            }
        }

        return redirect()->route('coordinator.assignments.index')
            ->with('status', 'Assignments created successfully.');
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


}
