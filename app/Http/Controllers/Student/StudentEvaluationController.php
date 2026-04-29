<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\PerformanceEvaluation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class StudentEvaluationController extends Controller
{
    public function index(): View
    {
        $studentId = (int) Auth::id();
        $assignment = Assignment::resolveActiveForStudent($studentId);
        $assignment?->loadMissing(['company', 'supervisor']);

        $evaluations = PerformanceEvaluation::query()
            ->with(['supervisor:id,name'])
            ->where('student_id', $studentId)
            ->orderByDesc('submitted_at')
            ->orderByDesc('evaluation_date')
            ->paginate(10);

        $completedEvaluationsCount = PerformanceEvaluation::query()
            ->where('student_id', $studentId)
            ->whereNotNull('submitted_at')
            ->count();

        $pendingEvaluationsCount = PerformanceEvaluation::query()
            ->where('student_id', $studentId)
            ->whereNull('submitted_at')
            ->count();

        $latestCompletedEvaluation = PerformanceEvaluation::query()
            ->with(['supervisor:id,name'])
            ->where('student_id', $studentId)
            ->whereNotNull('submitted_at')
            ->latest('submitted_at')
            ->first();

        $overallStatus = $completedEvaluationsCount > 0
            ? 'Completed'
            : ($pendingEvaluationsCount > 0 ? 'Pending' : 'Not yet evaluated');

        return view('student.evaluations.index', [
            'assignment' => $assignment,
            'evaluations' => $evaluations,
            'completedEvaluationsCount' => $completedEvaluationsCount,
            'pendingEvaluationsCount' => $pendingEvaluationsCount,
            'latestCompletedEvaluation' => $latestCompletedEvaluation,
            'overallStatus' => $overallStatus,
        ]);
    }

    public function show(PerformanceEvaluation $evaluation): View
    {
        $evaluation = $this->studentOwnedEvaluation($evaluation);
        $evaluation->loadMissing(['student:id,name', 'supervisor:id,name']);

        $assignment = Assignment::resolveActiveForSupervisorStudent((int) $evaluation->supervisor_id, (int) $evaluation->student_id);
        $assignment?->loadMissing('company');

        return view('student.evaluations.show', [
            'evaluation' => $evaluation,
            'assignment' => $assignment,
        ]);
    }

    public function download(PerformanceEvaluation $evaluation)
    {
        $evaluation = $this->completedStudentOwnedEvaluation($evaluation);

        if ($evaluation->document_path && Storage::disk('public')->exists($evaluation->document_path)) {
            $path = $evaluation->document_path;
            $fileName = basename($path);
        } else {
            [$path, $fileName] = $this->generateFallbackDocument($evaluation);
        }

        $fullPath = Storage::disk('public')->path($path);
        $contentType = match (strtolower((string) pathinfo($fileName, PATHINFO_EXTENSION))) {
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'pdf' => 'application/pdf',
            'odt' => 'application/vnd.oasis.opendocument.text',
            default => 'application/msword',
        };

        return response()->download($fullPath, $fileName, ['Content-Type' => $contentType]);
    }

    public function print(PerformanceEvaluation $evaluation): Response
    {
        $evaluation = $this->completedStudentOwnedEvaluation($evaluation);
        $evaluation->loadMissing(['student:id,name', 'supervisor:id,name']);

        $assignment = Assignment::resolveActiveForSupervisorStudent((int) $evaluation->supervisor_id, (int) $evaluation->student_id);
        $assignment?->loadMissing('company');

        return response()->view('student.evaluations.print', [
            'evaluation' => $evaluation,
            'assignment' => $assignment,
        ]);
    }

    private function studentOwnedEvaluation(PerformanceEvaluation $evaluation): PerformanceEvaluation
    {
        abort_unless((int) $evaluation->student_id === (int) Auth::id(), 403);

        return $evaluation;
    }

    private function completedStudentOwnedEvaluation(PerformanceEvaluation $evaluation): PerformanceEvaluation
    {
        $evaluation = $this->studentOwnedEvaluation($evaluation);
        abort_unless($evaluation->submitted_at !== null, 403);

        return $evaluation;
    }

    private function generateFallbackDocument(PerformanceEvaluation $evaluation): array
    {
        $evaluation->loadMissing(['student:id,name', 'supervisor:id,name']);

        $student = $evaluation->student;
        $supervisor = $evaluation->supervisor;
        $date = $evaluation->evaluation_date?->format('F d, Y') ?? now()->format('F d, Y');
        $assignment = Assignment::resolveActiveForSupervisorStudent((int) $evaluation->supervisor_id, (int) $evaluation->student_id);
        $assignment?->loadMissing('company');
        $company = $assignment?->company?->name ?? 'N/A';

        $html = "<html><head><meta charset='utf-8'><style>
            body{font-family:Arial,Helvetica,sans-serif;font-size:12pt;color:#111}
            table{border-collapse:collapse;width:100%}
            th,td{border:1px solid #ccc;padding:8px;text-align:left}
        </style></head><body>
            <h1>Student Performance Evaluation</h1>
            <p><strong>Student:</strong> ".e($student?->name ?? 'N/A')."</p>
            <p><strong>Supervisor:</strong> ".e($supervisor?->name ?? 'N/A')."</p>
            <p><strong>Date:</strong> ".e($date)."</p>
            <p><strong>Company:</strong> ".e($company)."</p>
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
            <p>".nl2br(e($evaluation->remarks ?? 'No remarks provided.')).'</p>
        </body></html>';

        $fileName = 'evaluation-'.Str::slug(($student?->name ?? 'student').'-'.$date).'.doc';
        $path = "evaluations/{$fileName}";
        Storage::disk('public')->put($path, $html);

        return [$path, $fileName];
    }
}
