<?php

namespace App\Http\Controllers\Coordinator;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\PerformanceEvaluation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CoordinatorEvaluationController extends Controller
{
    public function index(Request $request): View
    {
        // Get search and company filters
        $search = $request->input('search');
        $companyId = $request->input('company_id');

        // Build query for supervisors
        $query = User::where('role', User::ROLE_SUPERVISOR)
            ->with(['supervisorProfile', 'supervisorAssignments.company', 'supervisorAssignments.student']);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($companyId) {
            $query->whereHas('supervisorAssignments', function ($q) use ($companyId) {
                $q->where('company_id', $companyId);
            });
        }

        $supervisors = $query->orderBy('name')->get();

        // Group supervisors by company
        $groupedSupervisors = collect();
        foreach ($supervisors as $supervisor) {
            $companies = $supervisor->supervisorAssignments->pluck('company')->unique('id');
            if ($companies->isEmpty()) {
                if (!$groupedSupervisors->has('No Company')) {
                    $groupedSupervisors->put('No Company', collect());
                }
                $groupedSupervisors->get('No Company')->push($supervisor);
            } else {
                foreach ($companies as $company) {
                    $groupName = $company ? $company->name : 'No Company';
                    if (!$groupedSupervisors->has($groupName)) {
                        $groupedSupervisors->put($groupName, collect());
                    }
                    $groupedSupervisors->get($groupName)->push($supervisor);
                }
            }
        }

        $allCompanies = \App\Models\Company::orderBy('name')->get();

        return view('coordinator.evaluations.index', [
            'groupedSupervisors' => $groupedSupervisors->sortKeys(),
            'companies' => $allCompanies,
        ]);
    }

    public function show(User $supervisor, Request $request): View
    {
        // Filters
        $range = $request->input('range', 'all'); // Default to all for this view
        $semester = $request->input('semester');

        // Get students assigned to this supervisor
        $assignments = Assignment::where('supervisor_id', $supervisor->id)
            ->where('status', 'active')
            ->with(['student', 'company'])
            ->get();

        $students = $assignments->map(function ($assignment) use ($range, $semester, $supervisor) {
            $student = $assignment->student;

            // Get evaluations for this specific student and supervisor
            $evQuery = PerformanceEvaluation::where('supervisor_id', $supervisor->id)
                ->where('student_id', $student->id)
                ->whereNotNull('submitted_at');

            $now = Carbon::now();
            if ($range === 'daily') {
                $evQuery->whereDate('evaluation_date', $now->toDateString());
            } elseif ($range === 'weekly') {
                $evQuery->where('evaluation_date', '>=', $now->copy()->subDays(7)->toDateString());
            } elseif ($range === 'monthly') {
                $evQuery->where('evaluation_date', '>=', $now->copy()->subDays(30)->toDateString());
            }

            if ($semester) {
                if (strpos($semester, ' (') === false && ! in_array($semester, ['Weekly', 'Monthly'])) {
                    // It's a base semester (e.g. "1st Semester"), match it exactly OR match its sub-types
                    $evQuery->where(function ($q) use ($semester) {
                        $q->where('semester', $semester)
                            ->orWhere('semester', 'like', $semester.' (%');
                    });
                } else {
                    $evQuery->where('semester', $semester);
                }
            }

            $student->evaluations = $evQuery->latest('evaluation_date')->get();
            $student->assignment = $assignment;

            return $student;
        });

        return view('coordinator.evaluations.show', compact('supervisor', 'students', 'range', 'semester'));
    }

    public function export(PerformanceEvaluation $evaluation)
    {
        // Only allow coordinators and for submitted evaluations
        abort_unless(Auth::user()?->role === User::ROLE_COORDINATOR, 403);
        abort_unless($evaluation->submitted_at !== null, 403);

        // If already has stored doc, use it; else generate a fresh one for coordinator
        if ($evaluation->document_path && Storage::disk('public')->exists($evaluation->document_path)) {
            $path = $evaluation->document_path;
            $name = basename($path);
        } else {
            // Generate minimal doc here with semester and company
            $student = $evaluation->student;
            $supervisor = $evaluation->supervisor;
            $date = $evaluation->evaluation_date->format('F d, Y');
            $assignment = Assignment::where('student_id', $evaluation->student_id)
                ->where('supervisor_id', $evaluation->supervisor_id)
                ->with('company')->latest('start_date')->first();
            $company = $assignment && $assignment->company ? $assignment->company->name : 'N/A';
            $html = "<html><head><meta charset='utf-8'><style>
                body{font-family:Arial,Helvetica,sans-serif;font-size:12pt;color:#111}
                table{border-collapse:collapse;width:100%}
                th,td{border:1px solid #ccc;padding:8px;text-align:left}
            </style></head><body>
                <h1>Student Performance Evaluation</h1>
                <p><strong>Student:</strong> {$student->name}</p>
                <p><strong>Supervisor:</strong> {$supervisor->name}</p>
                <p><strong>Date:</strong> {$date}</p>
                <p><strong>Company:</strong> {$company}</p>
                <p><strong>Type / Semester:</strong> ".e($evaluation->semester ?? 'N/A')."</p>
                <table>
                    <tr><th>Criteria</th><th>Rating</th></tr>
                    <tr><td>Attendance & Punctuality</td><td>{$evaluation->attendance_punctuality} / 5</td></tr>
                    <tr><td>Quality of Work</td><td>{$evaluation->quality_of_work} / 5</td></tr>
                    <tr><td>Initiative</td><td>{$evaluation->initiative} / 5</td></tr>
                    <tr><td>Cooperation</td><td>{$evaluation->cooperation} / 5</td></tr>
                    <tr><td>Dependability</td><td>{$evaluation->dependability} / 5</td></tr>
                    <tr><td>Communication Skills</td><td>{$evaluation->communication_skills} / 5</td></tr>
                    <tr><th>Final Rating</th><th>{$evaluation->final_rating} / 5</th></tr>
                </table>
                <p><strong>Remarks</strong></p>
                <p>".nl2br(e($evaluation->remarks ?? 'N/A')).'</p>
            </body></html>';
            $name = 'evaluation-'.Str::slug($student->name.'-'.$date).'.doc';
            $path = "evaluations/{$name}";
            Storage::disk('public')->put($path, $html);
        }
        $full = Storage::disk('public')->path($path);

        return response()->download($full, $name, ['Content-Type' => 'application/msword']);
    }
}
