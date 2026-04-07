<?php

namespace App\Http\Controllers\Supervisor;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\PerformanceEvaluation;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class SupervisorEvaluationController extends Controller
{
    public function index(Request $request): View
    {
        $semester = $request->input('semester');
        $q = $request->input('q');
        $studentIds = Assignment::where('supervisor_id', Auth::id())->pluck('student_id');
        $studentsQuery = User::whereIn('id', $studentIds)->orderBy('name');
        if ($q) {
            $studentsQuery->where('name', 'like', $q.'%');
        }
        $students = $studentsQuery->get()->map(function ($s) use ($semester) {
            $evQuery = PerformanceEvaluation::where('supervisor_id', Auth::id())->where('student_id', $s->id);
            if ($semester) {
                $evQuery->where('semester', $semester);
            }
            $s->evaluations_count = $evQuery->count();
            $latest = $evQuery->latest('evaluation_date')->first();
            $s->latest_evaluation = $latest;

            return $s;
        });

        return view('supervisor.evaluations.index', compact('students', 'semester', 'q'));
    }

    public function create(): View
    {
        $students = Assignment::where('supervisor_id', Auth::id())
            ->where('status', 'active')
            ->with('student')
            ->get()
            ->pluck('student');

        return view('supervisor.evaluations.create', compact('students'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:users,id',
            'evaluation_date' => 'required|date',
            'semester' => 'required|string|max:32',
            'attendance_punctuality' => 'required|integer|min:1|max:5',
            'quality_of_work' => 'required|integer|min:1|max:5',
            'initiative' => 'required|integer|min:1|max:5',
            'cooperation' => 'required|integer|min:1|max:5',
            'dependability' => 'required|integer|min:1|max:5',
            'communication_skills' => 'required|integer|min:1|max:5',
            'remarks' => ['nullable', 'string', function ($attr, $val, $fail) {
                if ($val) {
                    $words = preg_match_all('/\S+/', $val);
                    if ($words > 1000) {
                        $fail('Remarks may not exceed 1000 words.');
                    }
                }
            }],
        ]);

        $average = (
            $validated['attendance_punctuality'] +
            $validated['quality_of_work'] +
            $validated['initiative'] +
            $validated['cooperation'] +
            $validated['dependability'] +
            $validated['communication_skills']
        ) / 6;

        $validated['supervisor_id'] = Auth::id();
        $validated['final_rating'] = round($average, 2);

        $evaluation = PerformanceEvaluation::create($validated);
        // Generate document, store path, and mark as submitted (visible to coordinator page)
        [$path, $fileName] = $this->generateDoc($evaluation);
        $evaluation->submitted_at = now();
        $evaluation->document_path = $path;
        $evaluation->document_type = 'doc';
        $evaluation->save();

        return redirect()->route('supervisor.evaluations.index')
            ->with('status', 'Performance evaluation submitted. Coordinators can view it in Performance Evaluation.');
    }

    protected function generateDoc(PerformanceEvaluation $evaluation): array
    {
        $student = $evaluation->student;
        $supervisor = $evaluation->supervisor ?: Auth::user();
        $date = $evaluation->evaluation_date->format('F d, Y');
        $assignment = Assignment::where('student_id', $evaluation->student_id)
            ->where('supervisor_id', $evaluation->supervisor_id)
            ->with('company')->latest('start_date')->first();
        $companyName = $assignment && $assignment->company ? $assignment->company->name : 'N/A';
        $safeName = Str::slug($student->name.'-'.$date);
        $fileName = "evaluation-{$safeName}.doc";
        $path = "evaluations/{$fileName}";

        $html = "<html><head><meta charset='utf-8'><style>
            body{font-family:Arial,Helvetica,sans-serif;font-size:12pt;color:#111}
            h1{font-size:18pt;margin-bottom:6pt}
            table{border-collapse:collapse;width:100%}
            th,td{border:1px solid #ccc;padding:8px;text-align:left}
            .muted{color:#666}
        </style></head><body>
            <h1>Student Performance Evaluation</h1>
            <p><strong>Student:</strong> {$student->name}</p>
            <p><strong>Supervisor:</strong> {$supervisor->name}</p>
            <p><strong>Date:</strong> {$date}</p>
            <p><strong>Company:</strong> {$companyName}</p>
            <p><strong>Type / Semester:</strong> ".e($evaluation->semester ?? 'N/A')."</p>
            <br/>
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
            <br/>
            <p><strong>Remarks</strong></p>
            <p class='muted'>".nl2br(e($evaluation->remarks ?? 'N/A')).'</p>
        </body></html>';

        Storage::disk('public')->put($path, $html);

        return [$path, $fileName];
    }

    public function export(PerformanceEvaluation $evaluation)
    {
        abort_unless($evaluation->supervisor_id === Auth::id(), 403);
        [$path, $fileName] = $this->generateDoc($evaluation);
        $full = Storage::disk('public')->path($path);

        return response()->download($full, $fileName, ['Content-Type' => 'application/msword']);
    }

    // Sending via Messages removed by request; coordinator now views within their Performance Evaluation pages.

    public function unsubmit(PerformanceEvaluation $evaluation): RedirectResponse
    {
        abort_unless($evaluation->supervisor_id === Auth::id(), 403);
        $evaluation->submitted_at = null;
        $evaluation->save();

        return back()->with('status', 'Evaluation was unsubmitted and will no longer appear to coordinators.');
    }

    public function student(User $student, Request $request): View
    {
        abort_unless(Assignment::where('supervisor_id', Auth::id())->where('student_id', $student->id)->exists(), 403);
        $semester = $request->input('semester');
        $query = PerformanceEvaluation::where('supervisor_id', Auth::id())->where('student_id', $student->id)->orderByDesc('evaluation_date');
        if ($semester) {
            $query->where('semester', $semester);
        }
        $evaluations = $query->paginate(12)->withQueryString();

        return view('supervisor.evaluations.student', compact('student', 'evaluations', 'semester'));
    }
}
