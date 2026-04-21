<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Services\MappingCalendarBuilder;
use App\Services\MoppingAnalyzer;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class MappingController extends Controller
{
    public function index(Request $request, MappingCalendarBuilder $builder, MoppingAnalyzer $validator): View
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

        return view('student.mapping.index', [
            'assignment' => $assignment,
            'mapping' => $mapping,
            'fromKey' => $mapping['fromKey'] ?? $fromMonth->format('Y-m'),
            'toKey' => $mapping['toKey'] ?? $toMonth->format('Y-m'),
            'submittedAt' => Carbon::now(),
        ]);
    }
}
