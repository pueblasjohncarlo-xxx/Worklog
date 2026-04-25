<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Services\MappingCalendarBuilder;
use App\Services\MoppingAnalyzer;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class MappingController extends Controller
{
    public function index(Request $request, MappingCalendarBuilder $builder, MoppingAnalyzer $validator): View
    {
        return view('student.mapping.index', $this->mappingViewData($request, $builder, $validator));
    }

    public function export(Request $request, MappingCalendarBuilder $builder, MoppingAnalyzer $validator)
    {
        $data = $this->mappingViewData($request, $builder, $validator);
        $format = strtolower((string) $request->query('format', 'pdf'));

        abort_unless(in_array($format, ['pdf', 'doc'], true), 404);
        abort_unless($data['assignment'] !== null && $data['mapping'] !== null, 404);

        $filename = $this->buildExportFilename($data['assignment'], $data['fromKey'], $data['toKey']);

        if ($format === 'doc') {
            $html = view('student.mapping.export', $data + ['exportFormat' => 'doc'])->render();

            return response($html, 200, [
                'Content-Type' => 'application/msword; charset=UTF-8',
                'Content-Disposition' => 'attachment; filename="'.$filename.'.doc"',
            ]);
        }

        $pdf = Pdf::loadView('student.mapping.export', $data + ['exportFormat' => 'pdf'])
            ->setPaper('a4', 'portrait');

        return $pdf->download($filename.'.pdf');
    }

    private function mappingViewData(Request $request, MappingCalendarBuilder $builder, MoppingAnalyzer $validator): array
    {
        $user = Auth::user();
        $assignment = Assignment::resolveActiveForStudent((int) $user->id);

        if ($assignment) {
            $assignment->loadMissing(['student.studentProfile', 'company', 'supervisor', 'ojtAdviser']);
        }

        $fromKey = (string) $request->query('from', '');
        $toKey = (string) $request->query('to', '');

        $defaultFrom = $assignment?->start_date ? $assignment->start_date->copy()->startOfMonth() : Carbon::now()->subMonths(4)->startOfMonth();
        $defaultTo = $assignment?->end_date
            ? $assignment->end_date->copy()->min(Carbon::now())->startOfMonth()
            : Carbon::now()->startOfMonth();

        $fromMonth = $fromKey !== '' ? $validator->monthRangeFromKey($fromKey) : $defaultFrom;
        $toMonth = $toKey !== '' ? $validator->monthRangeFromKey($toKey) : $defaultTo;

        $mapping = null;
        if ($assignment) {
            $mapping = $builder->buildForAssignment($assignment, $fromMonth, $toMonth, $validator);
        }

        return [
            'assignment' => $assignment,
            'mapping' => $mapping,
            'fromKey' => $mapping['fromKey'] ?? $fromMonth->format('Y-m'),
            'toKey' => $mapping['toKey'] ?? $toMonth->format('Y-m'),
            'submittedAt' => Carbon::now(),
        ];
    }

    private function buildExportFilename(Assignment $assignment, string $fromKey, string $toKey): string
    {
        $studentName = preg_replace('/[^A-Za-z0-9_-]+/', '_', (string) $assignment->student?->name);

        return trim("student_mapping_{$studentName}_{$fromKey}_to_{$toKey}", '_');
    }
}
