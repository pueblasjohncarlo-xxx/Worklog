<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class NormalizeStudentAcademicFields extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'students:normalize-academics {--dry-run : Show changes without saving}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Normalize legacy student section and major values to canonical WorkLog options';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');

        $students = User::query()
            ->where('role', User::ROLE_STUDENT)
            ->select(['id', 'name', 'email', 'section', 'department'])
            ->get();

        $changed = 0;

        foreach ($students as $student) {
            $normalizedDepartment = User::normalizeStudentDepartment($student->department);
            $normalizedSection = User::normalizeStudentSection($student->section, $normalizedDepartment ?? $student->department);

            if ($normalizedDepartment === null) {
                $normalizedDepartment = User::inferStudentDepartmentFromSection($normalizedSection);
            }

            if ($normalizedSection === null) {
                $normalizedSection = User::normalizeStudentSection(null, $normalizedDepartment)
                    ?? User::STUDENT_SECTION_BSIT_4A;
            }

            $sectionChanged = $student->section !== $normalizedSection;
            $departmentChanged = $student->department !== $normalizedDepartment;

            if (! $sectionChanged && ! $departmentChanged) {
                continue;
            }

            $changed++;

            $this->line(sprintf(
                '#%d %s <%s> | section: "%s" -> "%s" | major: "%s" -> "%s"',
                $student->id,
                $student->name,
                $student->email,
                (string) $student->section,
                (string) $normalizedSection,
                (string) $student->department,
                (string) $normalizedDepartment
            ));

            if (! $dryRun) {
                $student->forceFill([
                    'section' => $normalizedSection,
                    'department' => $normalizedDepartment,
                ])->save();
            }
        }

        if ($changed === 0) {
            $this->info('No student academic values needed normalization.');

            return self::SUCCESS;
        }

        $this->info($dryRun
            ? "Dry run complete. {$changed} student record(s) would be updated."
            : "Normalization complete. {$changed} student record(s) updated.");

        return self::SUCCESS;
    }
}
