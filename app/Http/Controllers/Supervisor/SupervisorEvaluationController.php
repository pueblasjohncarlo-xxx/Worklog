<?php

namespace App\Http\Controllers\Supervisor;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\PerformanceEvaluation;
use App\Models\User;
use App\Notifications\PerformanceEvaluationSubmittedNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\View\View;

class SupervisorEvaluationController extends Controller
{
    private const TEMPLATE_UPLOAD_MAX_KB = 51200; // 50MB

    public function index(Request $request): View
    {
        $semester = $request->input('semester');
        $q = $request->input('q');
        $studentIds = Assignment::query()
            ->where('supervisor_id', Auth::id())
            ->active()
            ->whereHas('student', fn ($q) => $q->eligibleStudentForRoster())
            ->pluck('student_id')
            ->unique();

        $studentsQuery = User::eligibleStudentForRoster()->whereIn('id', $studentIds)->orderBy('name');
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

    public function create(Request $request): View
    {
        $students = Assignment::query()
            ->where('supervisor_id', Auth::id())
            ->active()
            ->whereHas('student', fn ($q) => $q->eligibleStudentForRoster())
            ->with('student')
            ->orderByDesc('updated_at')
            ->get()
            ->pluck('student')
            ->unique('id')
            ->values();

        $selectedStudentId = (int) $request->integer('student_id');
        if ($selectedStudentId > 0 && ! $students->pluck('id')->contains($selectedStudentId)) {
            $selectedStudentId = 0;
        }

        return view('supervisor.evaluations.create', [
            'students' => $students,
            'selectedStudentId' => $selectedStudentId,
            'maxUploadMb' => (int) (self::TEMPLATE_UPLOAD_MAX_KB / 1024),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $supervisorId = (int) Auth::id();

        $activeStudentIds = Assignment::query()
            ->where('supervisor_id', $supervisorId)
            ->active()
            ->pluck('student_id')
            ->unique()
            ->values();

        $validated = $request->validate([
            'student_id' => ['required', 'integer', Rule::in($activeStudentIds->all())],
            'evaluation_date' => ['required', 'date'],
            'semester' => ['required', 'string', 'max:32'],
            'remarks' => ['nullable', 'string', function ($attr, $val, $fail) {
                if ($val) {
                    $words = preg_match_all('/\S+/', $val);
                    if ($words > 1000) {
                        $fail('Remarks may not exceed 1000 words.');
                    }
                }
            }],
            'attachment' => ['required', 'file', 'mimes:doc,docx,odt,pdf', 'max:'.self::TEMPLATE_UPLOAD_MAX_KB],
            'confirm_submission' => ['required', 'accepted'],
        ], [
            'student_id.in' => 'Selected student is not assigned to your active roster.',
            'attachment.max' => 'The evaluation file must not exceed '.(int) (self::TEMPLATE_UPLOAD_MAX_KB / 1024).'MB.',
        ]);

        $evaluation = PerformanceEvaluation::create([
            'student_id' => (int) $validated['student_id'],
            'supervisor_id' => $supervisorId,
            'evaluation_date' => $validated['evaluation_date'],
            'semester' => $validated['semester'],
            // Preserve backward compatibility with existing non-null rating columns.
            'attendance_punctuality' => 0,
            'quality_of_work' => 0,
            'initiative' => 0,
            'cooperation' => 0,
            'dependability' => 0,
            'communication_skills' => 0,
            'remarks' => $validated['remarks'] ?? null,
            'final_rating' => 0,
            'submitted_at' => now(),
        ]);

        $storedPath = $request->file('attachment')->store('evaluations/submitted', 'public');
        $ext = strtolower((string) $request->file('attachment')->getClientOriginalExtension());
        $evaluation->document_path = $storedPath;
        $evaluation->document_type = $ext ?: 'file';
        $evaluation->save();

        $assignment = Assignment::with(['coordinator', 'ojtAdviser'])
            ->where('student_id', $evaluation->student_id)
            ->where('supervisor_id', $evaluation->supervisor_id)
            ->where('status', 'active')
            ->latest('start_date')
            ->first();

        $student = User::find($evaluation->student_id);
        if ($student) {
            $student->notify(new PerformanceEvaluationSubmittedNotification($evaluation, route('student.dashboard')));
        }

        if ($assignment?->coordinator) {
            $assignment->coordinator->notify(new PerformanceEvaluationSubmittedNotification($evaluation, route('coordinator.evaluations.index')));
        }

        if ($assignment?->ojtAdviser) {
            $assignment->ojtAdviser->notify(new PerformanceEvaluationSubmittedNotification($evaluation, route('ojt_adviser.evaluations.student', ['student' => $evaluation->student_id])));
        }

        return redirect()->route('supervisor.evaluations.student', ['student' => $evaluation->student_id])
            ->with('status', 'Performance evaluation file submitted successfully.');
    }

    public function template(Request $request): Response
    {
        $supervisorId = (int) Auth::id();

        $studentId = (int) $request->integer('student_id');
        $evaluationDate = (string) $request->query('evaluation_date', now()->toDateString());
        $semester = (string) $request->query('semester', '1st Semester');

        $assignment = null;
        if ($studentId > 0) {
            $assignment = Assignment::query()
                ->where('supervisor_id', $supervisorId)
                ->where('student_id', $studentId)
                ->active()
                ->with(['student', 'company'])
                ->latest('updated_at')
                ->first();
        }

        $templateBaseDir = storage_path('app/templates/performance-evaluation');
        $candidateTemplates = [
            $templateBaseDir.DIRECTORY_SEPARATOR.'official-template.doc',
        ];

        foreach ($candidateTemplates as $path) {
            if (! is_file($path)) {
                continue;
            }

            $ext = strtolower((string) pathinfo($path, PATHINFO_EXTENSION));
            $downloadName = 'OJT_Performance_Evaluation_Template.'.($ext ?: 'doc');
            $contentType = match ($ext) {
                'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'odt' => 'application/vnd.oasis.opendocument.text',
                default => 'application/msword',
            };

            return response()->download(
                $path,
                $downloadName,
                [
                    'Content-Type' => $contentType,
                    'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
                ]
            );
        }

        $studentName = $assignment?->student?->name ?? '________________________';
        $companyName = $assignment?->company?->name ?? '________________________';

        $html = view('templates.performance-evaluation-word', [
            'studentName' => $studentName,
            'companyName' => $companyName,
            'semester' => $semester,
            'evaluationDate' => $evaluationDate,
            'supervisorName' => Auth::user()?->name ?? '________________________',
        ])->render();

        return response($html, 200)
            ->header('Content-Type', 'application/msword; charset=UTF-8')
            ->header('Content-Disposition', 'attachment; filename="OJT_Performance_Evaluation_Template.doc"')
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
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

        if ($evaluation->document_path && Storage::disk('public')->exists($evaluation->document_path)) {
            $path = $evaluation->document_path;
            $fileName = basename($path);
        } else {
            [$path, $fileName] = $this->generateDoc($evaluation);
        }

        $full = Storage::disk('public')->path($path);

        $contentType = match (strtolower((string) pathinfo($fileName, PATHINFO_EXTENSION))) {
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'pdf' => 'application/pdf',
            'odt' => 'application/vnd.oasis.opendocument.text',
            default => 'application/msword',
        };

        return response()->download($full, $fileName, ['Content-Type' => $contentType]);
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
