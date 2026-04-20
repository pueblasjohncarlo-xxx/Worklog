<?php

namespace App\Services;

use App\Models\WorkLog;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class MoppingAnalyzer
{
    public const STATUS_MATCH = 'match';
    public const STATUS_INCOMPLETE = 'incomplete';
    public const STATUS_INCONSISTENT = 'inconsistent';

    /**
     * Analyze a single assignment's logs for a given month.
     *
     * @return array{
     *   month: string,
     *   attendance_days: int,
     *   attendance_hours: float,
     *   attendance_open_count: int,
     *   ar_count: int,
     *   ar_hours: float,
     *   ar_status_counts: array<string,int>,
     *   uncovered_attendance_dates: array<int,string>,
     *   extra_daily_ar_dates: array<int,string>,
     *   status: string,
     *   hours_delta: float
     * }
     */
    public function analyzeMonth(Collection $monthLogs, Carbon $monthStart): array
    {
        $monthStart = $monthStart->copy()->startOfMonth();
        $monthKey = $monthStart->format('Y-m');

        $attendanceLogs = $monthLogs->filter(fn (WorkLog $log) => ! is_null($log->time_in));
        $attendanceCompleteLogs = $attendanceLogs->filter(fn (WorkLog $log) => ! is_null($log->time_out));
        $attendanceOpenCount = $attendanceLogs->filter(fn (WorkLog $log) => is_null($log->time_out))->count();

        $attendanceDates = $attendanceLogs
            ->pluck('work_date')
            ->filter()
            ->map(fn ($d) => $d instanceof Carbon ? $d->toDateString() : Carbon::parse($d)->toDateString())
            ->unique()
            ->values();

        $attendanceHours = (float) $attendanceCompleteLogs->sum('hours');

        $arLogs = $monthLogs->filter(fn (WorkLog $log) => $this->isAccomplishmentReport($log));
        $arCount = $arLogs->count();
        $arHours = (float) $arLogs->sum('hours');

        $arStatusCounts = $arLogs
            ->pluck('status')
            ->map(fn ($s) => $s ?: 'unknown')
            ->countBy()
            ->toArray();

        $dailyArDates = $arLogs
            ->where('type', 'daily')
            ->pluck('work_date')
            ->filter()
            ->map(fn ($d) => $d instanceof Carbon ? $d->toDateString() : Carbon::parse($d)->toDateString())
            ->unique()
            ->values();

        $weeklyArWeeks = $arLogs
            ->where('type', 'weekly')
            ->pluck('work_date')
            ->filter()
            ->map(function ($d) {
                $date = $d instanceof Carbon ? $d : Carbon::parse($d);
                return sprintf('%d-W%02d', $date->isoWeekYear, $date->isoWeek);
            })
            ->unique()
            ->values();

        $hasMonthlyAr = $arLogs->where('type', 'monthly')->isNotEmpty();

        $uncoveredAttendanceDates = [];
        foreach ($attendanceDates as $dateStr) {
            if ($hasMonthlyAr) {
                continue;
            }

            $date = Carbon::parse($dateStr);
            $weekKey = sprintf('%d-W%02d', $date->isoWeekYear, $date->isoWeek);

            $covered = $dailyArDates->contains($dateStr) || $weeklyArWeeks->contains($weekKey);
            if (! $covered) {
                $uncoveredAttendanceDates[] = $dateStr;
            }
        }

        $extraDailyArDates = $dailyArDates
            ->reject(fn (string $dateStr) => $attendanceDates->contains($dateStr))
            ->values()
            ->all();

        $hoursDelta = round($arHours - $attendanceHours, 2);
        $hoursMatch = abs($hoursDelta) < 0.01;

        $status = self::STATUS_INCOMPLETE;

        if ($attendanceDates->isEmpty()) {
            // With no attendance, we can't validate AR vs attendance.
            $status = self::STATUS_INCOMPLETE;
        } elseif (! empty($uncoveredAttendanceDates) || $attendanceOpenCount > 0 || $arCount === 0) {
            $status = self::STATUS_INCOMPLETE;
        } elseif (! empty($extraDailyArDates)) {
            $status = self::STATUS_INCONSISTENT;
        } elseif (! $hoursMatch) {
            $status = self::STATUS_INCONSISTENT;
        } else {
            $status = self::STATUS_MATCH;
        }

        return [
            'month' => $monthKey,
            'attendance_days' => $attendanceDates->count(),
            'attendance_hours' => round($attendanceHours, 2),
            'attendance_open_count' => $attendanceOpenCount,
            'ar_count' => $arCount,
            'ar_hours' => round($arHours, 2),
            'ar_status_counts' => $arStatusCounts,
            'uncovered_attendance_dates' => array_values($uncoveredAttendanceDates),
            'extra_daily_ar_dates' => array_values($extraDailyArDates),
            'status' => $status,
            'hours_delta' => $hoursDelta,
        ];
    }

    public function monthRangeFromKey(?string $monthKey): Carbon
    {
        if (! $monthKey) {
            return Carbon::now()->startOfMonth();
        }

        try {
            return Carbon::createFromFormat('Y-m', $monthKey)->startOfMonth();
        } catch (\Throwable $e) {
            return Carbon::now()->startOfMonth();
        }
    }

    public function monthKey(Carbon $monthStart): string
    {
        return $monthStart->copy()->startOfMonth()->format('Y-m');
    }

    public function monthEnd(Carbon $monthStart): Carbon
    {
        return $monthStart->copy()->endOfMonth();
    }

    private function isAccomplishmentReport(WorkLog $log): bool
    {
        if (! is_null($log->time_in) || ! is_null($log->time_out)) {
            return false;
        }

        if (! in_array($log->type, ['daily', 'weekly', 'monthly'], true)) {
            return false;
        }

        return ! empty($log->attachment_path);
    }
}
