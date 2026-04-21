<?php

namespace App\Services;

use App\Models\Assignment;
use App\Models\WorkLog;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Support\Collection;

class MappingCalendarBuilder
{
    /**
     * Build a calendar-style attendance (time in/out) mapping for an assignment across a month range.
     *
     * @return array{
     *   fromKey: string,
     *   toKey: string,
     *   months: array<int, array{
     *     key: string,
     *     label: string,
     *     monthStart: Carbon,
     *     weeks: array<int, array{days: array<int, array{date: ?Carbon, day: ?int, hours: ?float}>, total: float}>,
     *     month_total: float,
     *     open_count: int,
     *     validation: array<string,mixed>|null
     *   }>,
     *   summary: array<int, array{key: string, label: string, hours: float, status: ?string}>,
     *   overall_total: float
     * }
     */
    public function buildForAssignment(Assignment $assignment, Carbon $fromMonth, Carbon $toMonth, ?MoppingAnalyzer $validator = null): array
    {
        $fromMonth = $fromMonth->copy()->startOfMonth();
        $toMonth = $toMonth->copy()->startOfMonth();

        if ($toMonth->lt($fromMonth)) {
            [$fromMonth, $toMonth] = [$toMonth, $fromMonth];
        }

        // Keep the page printable: cap to 12 months.
        $monthsDiff = $fromMonth->diffInMonths($toMonth);
        if ($monthsDiff > 11) {
            $toMonth = $fromMonth->copy()->addMonths(11);
        }

        $rangeStart = $fromMonth->copy()->startOfMonth();
        $rangeEnd = $toMonth->copy()->endOfMonth();

        $allLogs = WorkLog::query()
            ->where('assignment_id', $assignment->id)
            ->whereBetween('work_date', [$rangeStart->toDateString(), $rangeEnd->toDateString()])
            ->orderBy('work_date')
            ->get();

        $logsByMonth = $allLogs->groupBy(function (WorkLog $log) {
            $date = $log->work_date instanceof Carbon ? $log->work_date : Carbon::parse($log->work_date);

            return $date->format('Y-m');
        });

        $months = [];
        $summary = [];
        $overallTotal = 0.0;

        $cursor = $fromMonth->copy();
        while ($cursor->lte($toMonth)) {
            $monthKey = $cursor->format('Y-m');
            $monthLogs = $logsByMonth->get($monthKey, collect());

            [$hoursByDate, $openCount] = $this->hoursByDate($monthLogs);

            $weeks = $this->calendarWeeks($cursor, $hoursByDate);
            $monthTotal = round(collect($hoursByDate)->sum(), 2);

            $validation = null;
            $status = null;
            if ($validator) {
                $validation = $validator->analyzeMonth($monthLogs, $cursor);
                $status = $validation['status'] ?? null;
            }

            $months[] = [
                'key' => $monthKey,
                'label' => strtoupper($cursor->format('F Y')),
                'monthStart' => $cursor->copy(),
                'weeks' => $weeks,
                'month_total' => $monthTotal,
                'open_count' => $openCount,
                'validation' => $validation,
            ];

            $summary[] = [
                'key' => $monthKey,
                'label' => $cursor->format('M-Y'),
                'hours' => $monthTotal,
                'status' => $status,
            ];

            $overallTotal += $monthTotal;
            $cursor->addMonthNoOverflow()->startOfMonth();
        }

        return [
            'fromKey' => $fromMonth->format('Y-m'),
            'toKey' => $toMonth->format('Y-m'),
            'months' => $months,
            'summary' => $summary,
            'overall_total' => round($overallTotal, 2),
        ];
    }

    /**
     * @return array{0: array<string,float>, 1: int}
     */
    private function hoursByDate(Collection $monthLogs): array
    {
        $attendanceLogs = $monthLogs->filter(fn (WorkLog $log) => ! is_null($log->time_in));
        $openCount = $attendanceLogs->filter(fn (WorkLog $log) => is_null($log->time_out))->count();

        $complete = $attendanceLogs->filter(fn (WorkLog $log) => ! is_null($log->time_out));

        $hoursByDate = [];
        foreach ($complete as $log) {
            $dateStr = ($log->work_date instanceof Carbon ? $log->work_date : Carbon::parse($log->work_date))->toDateString();
            $hours = $this->computeAttendanceHours($log);

            if (! isset($hoursByDate[$dateStr])) {
                $hoursByDate[$dateStr] = 0.0;
            }

            $hoursByDate[$dateStr] += $hours;
        }

        // Round daily totals to 2 decimals for stable display.
        foreach ($hoursByDate as $d => $h) {
            $hoursByDate[$d] = round((float) $h, 2);
        }

        return [$hoursByDate, $openCount];
    }

    private function computeAttendanceHours(WorkLog $log): float
    {
        if (! is_null($log->hours)) {
            return (float) $log->hours;
        }

        if (is_null($log->time_in) || is_null($log->time_out)) {
            return 0.0;
        }

        try {
            $in = $log->time_in instanceof Carbon ? $log->time_in : Carbon::parse($log->time_in);
            $out = $log->time_out instanceof Carbon ? $log->time_out : Carbon::parse($log->time_out);

            if ($out->lessThanOrEqualTo($in)) {
                return 0.0;
            }

            $minutes = $in->diffInMinutes($out);

            return round($minutes / 60, 2);
        } catch (\Throwable) {
            return 0.0;
        }
    }

    /**
     * @param array<string,float> $hoursByDate
     * @return array<int, array{days: array<int, array{date: ?Carbon, day: ?int, hours: ?float}>, total: float}>
     */
    private function calendarWeeks(Carbon $monthStart, array $hoursByDate): array
    {
        $start = $monthStart->copy()->startOfMonth();
        $end = $monthStart->copy()->endOfMonth();

        $gridStart = $start->copy()->startOfWeek(CarbonInterface::SUNDAY);
        $gridEnd = $end->copy()->endOfWeek(CarbonInterface::SATURDAY);

        $weeks = [];
        $cursor = $gridStart->copy();

        while ($cursor->lte($gridEnd)) {
            $days = [];
            $weekTotal = 0.0;

            for ($i = 0; $i < 7; $i++) {
                $date = $cursor->copy();
                $isCurrentMonth = $date->month === $monthStart->month && $date->year === $monthStart->year;

                if (! $isCurrentMonth) {
                    $days[] = ['date' => null, 'day' => null, 'hours' => null];
                } else {
                    $dateStr = $date->toDateString();
                    $hours = (float) ($hoursByDate[$dateStr] ?? 0.0);
                    $displayHours = $hours > 0 ? $hours : null;

                    if ($hours > 0) {
                        $weekTotal += $hours;
                    }

                    $days[] = ['date' => $date, 'day' => (int) $date->day, 'hours' => $displayHours];
                }

                $cursor->addDay();
            }

            $weeks[] = [
                'days' => $days,
                'total' => round($weekTotal, 2),
            ];
        }

        return $weeks;
    }
}
