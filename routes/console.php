<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schedule;
use App\Models\Assignment;
use App\Models\User;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('tasks:check-due')->daily();

Artisan::command('worklog:audit-assignments {--adviser=} {--supervisor=} {--student=} {--apply-inactive : Mark invalid/duplicate active assignments as inactive}', function () {
    $adviserOpt = trim((string) ($this->option('adviser') ?? ''));
    $supervisorOpt = trim((string) ($this->option('supervisor') ?? ''));
    $studentOpt = trim((string) ($this->option('student') ?? ''));

    $applyInactive = (bool) $this->option('apply-inactive');
    $verbose = $this->getOutput() ? $this->getOutput()->isVerbose() : false;

    $assignmentQuery = Assignment::query()->with(['student', 'ojtAdviser', 'supervisor', 'company']);

    if ($adviserOpt !== '') {
        $adviserIds = is_numeric($adviserOpt)
            ? [(int) $adviserOpt]
            : User::query()
                ->where('role', User::ROLE_OJT_ADVISER)
                ->where('name', 'like', '%'.$adviserOpt.'%')
                ->pluck('id')
                ->all();

        $assignmentQuery->whereIn('ojt_adviser_id', $adviserIds);
    }

    if ($supervisorOpt !== '') {
        $assignmentQuery->where('supervisor_id', (int) $supervisorOpt);
    }

    if ($studentOpt !== '') {
        $assignmentQuery->where('student_id', (int) $studentOpt);
    }

    $activeAssignments = (clone $assignmentQuery)->active();

    $invalidStudentAssignments = (clone $activeAssignments)
        ->whereDoesntHave('student', fn ($q) => $q->eligibleStudentForRoster())
        ->get();

    $this->info('Assignment audit (active only)');
    $this->line('');

    $this->line('Invalid student accounts on active assignments: '.$invalidStudentAssignments->count());

    $invalidIds = $invalidStudentAssignments->pluck('id')->values()->all();
    if ($verbose && $invalidStudentAssignments->isNotEmpty()) {
        foreach ($invalidStudentAssignments as $a) {
            $s = $a->student;
            $this->line(sprintf(
                ' - assignment #%d | student #%s %s | status=%s is_approved=%s rejected_at=%s | adviser=%s | supervisor=%s | company=%s',
                $a->id,
                $a->student_id,
                $s?->name ?? '(missing)',
                (string) ($s?->status ?? 'null'),
                (string) ($s?->is_approved ?? 'null'),
                (string) ($s?->rejected_at ?? 'null'),
                (string) ($a->ojtAdviser?->name ?? 'N/A'),
                (string) ($a->supervisor?->name ?? 'N/A'),
                (string) ($a->company?->name ?? 'N/A'),
            ));
        }
    }

    $dupStudentIds = (clone $activeAssignments)
        ->select('student_id', DB::raw('COUNT(*) as cnt'))
        ->groupBy('student_id')
        ->having('cnt', '>', 1)
        ->pluck('student_id');

    $duplicateAssignments = $dupStudentIds->isNotEmpty()
        ? (clone $activeAssignments)->whereIn('student_id', $dupStudentIds)->orderByDesc('updated_at')->get()->groupBy('student_id')
        : collect();

    $this->line('');
    $this->line('Students with multiple active assignments: '.$duplicateAssignments->count());

    $dupToInactivate = [];
    if ($verbose && $duplicateAssignments->isNotEmpty()) {
        foreach ($duplicateAssignments as $studentId => $group) {
            $keep = $group->sortByDesc('updated_at')->first();
            $drop = $group->where('id', '!=', $keep->id);
            $dupToInactivate = array_merge($dupToInactivate, $drop->pluck('id')->all());

            $studentName = $group->first()?->student?->name ?? 'N/A';
            $this->line(" - student #{$studentId} {$studentName} | keep assignment #{$keep->id} | other active: ".$drop->pluck('id')->implode(', '));
        }
    } else {
        foreach ($duplicateAssignments as $studentId => $group) {
            $keep = $group->sortByDesc('updated_at')->first();
            $drop = $group->where('id', '!=', $keep->id);
            $dupToInactivate = array_merge($dupToInactivate, $drop->pluck('id')->all());
        }
    }

    $toInactivate = array_values(array_unique(array_merge($invalidIds, $dupToInactivate)));

    $this->line('');
    $this->line('Recommended to mark inactive: '.count($toInactivate));

    if ($applyInactive) {
        if (count($toInactivate) === 0) {
            $this->info('Nothing to update.');
            return;
        }

        $updated = Assignment::whereIn('id', $toInactivate)->update(['status' => 'inactive']);
        $this->info('Updated assignments set to status=inactive: '.$updated);
    } else {
        $this->line('Dry run only. Re-run with --apply-inactive to update assignment.status to inactive.');
    }
})->purpose('Audit active assignments for stale/ineligible students and duplicate active deployments (safe by default)');
