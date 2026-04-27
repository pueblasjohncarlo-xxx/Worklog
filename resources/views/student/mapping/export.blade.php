<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Mapping Export</title>
    <style>
        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            color: #0f172a;
            margin: 24px;
            font-size: 12px;
            line-height: 1.45;
            background: #ffffff;
        }

        .page-header {
            margin-bottom: 20px;
        }

        .title {
            font-size: 22px;
            font-weight: 800;
            margin: 0 0 6px;
        }

        .subtitle {
            margin: 0;
            color: #334155;
            font-weight: 600;
        }

        .meta-grid {
            width: 100%;
            border-collapse: collapse;
            margin: 18px 0 24px;
        }

        .meta-grid td {
            vertical-align: top;
            width: 50%;
            padding: 4px 10px 4px 0;
        }

        .meta-label {
            font-weight: 800;
            color: #0f172a;
        }

        .export-note {
            margin-bottom: 18px;
            padding: 10px 12px;
            border: 1px solid #cbd5e1;
            background: #f8fafc;
            border-radius: 8px;
            color: #334155;
        }

        .mapping-shell .space-y-6 > * + * {
            margin-top: 18px;
        }

        .mapping-shell table {
            width: 100%;
            border-collapse: collapse;
        }

        .mapping-shell th,
        .mapping-shell td {
            border: 1px solid #cbd5e1;
            padding: 6px 8px;
        }

        .mapping-shell th {
            background: #f8fafc;
            font-size: 11px;
            color: #334155;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        .mapping-shell .month-card {
            margin-bottom: 18px;
            border: 1px solid #cbd5e1;
            border-radius: 12px;
            overflow: hidden;
        }

        .mapping-shell .month-header {
            background: #f8fafc;
            border-bottom: 1px solid #cbd5e1;
            padding: 10px 12px;
        }

        .mapping-shell .month-title {
            font-size: 15px;
            font-weight: 800;
        }

        .mapping-shell .month-total {
            margin-top: 4px;
            font-size: 12px;
            color: #334155;
            font-weight: 700;
        }

        .mapping-shell .hours {
            color: #1d4ed8;
            font-weight: 800;
        }

        .mapping-shell .day-cell {
            height: 54px;
            vertical-align: top;
        }

        .mapping-shell .day-number {
            font-size: 10px;
            color: #475569;
            font-weight: 700;
            margin-bottom: 4px;
        }

        .mapping-shell .summary-card {
            border: 1px solid #cbd5e1;
            border-radius: 12px;
            overflow: hidden;
        }

        .mapping-shell .summary-header {
            background: #f8fafc;
            border-bottom: 1px solid #cbd5e1;
            padding: 10px 12px;
            font-size: 15px;
            font-weight: 800;
        }
    </style>
</head>
<body>
    <div class="page-header">
        <h1 class="title">Mapping of OJT Hours</h1>
        <p class="subtitle">Attendance-based monthly summary paired with accomplishment reports.</p>
    </div>

    <table class="meta-grid">
        <tr>
            <td><span class="meta-label">Student Name:</span> {{ $assignment->student?->name ?? '-' }}</td>
            <td><span class="meta-label">Course/Program:</span> {{ $assignment->student?->studentProfile?->program ?? '-' }}</td>
        </tr>
        <tr>
            <td><span class="meta-label">Section:</span> {{ $assignment->student?->normalizedStudentSection() ?? ($assignment->student?->section ?? '-') }}</td>
            <td><span class="meta-label">Student No.:</span> {{ $assignment->student?->studentProfile?->student_number ?? '-' }}</td>
        </tr>
        <tr>
            <td><span class="meta-label">Company:</span> {{ $assignment->company?->name ?? '-' }}</td>
            <td><span class="meta-label">Department:</span> {{ $assignment->student?->department ?? ($assignment->student?->studentProfile?->program ?? '-') }}</td>
        </tr>
        <tr>
            <td><span class="meta-label">Covered Period:</span> {{ $fromKey }} to {{ $toKey }}</td>
            <td><span class="meta-label">Date Generated:</span> {{ $submittedAt->format('F d, Y') }}</td>
        </tr>
    </table>

    <div class="export-note">
        This export contains the same mapping data, monthly totals, student details, and attendance summary shown in the Student Mapping screen.
    </div>

    <div class="mapping-shell">
        <div class="space-y-6">
            @foreach(($mapping['months'] ?? []) as $m)
                <div class="month-card">
                    <div class="month-header">
                        <div class="month-title">{{ $m['label'] ?? '' }}</div>
                        <div class="month-total">Monthly Total: <span class="hours">{{ rtrim(rtrim(number_format((float)($m['month_total'] ?? 0), 2), '0'), '.') }}</span></div>
                    </div>

                    <table>
                        <thead>
                            <tr>
                                @foreach(['Sun','Mon','Tue','Wed','Thu','Fri','Sat'] as $d)
                                    <th>{{ $d }}</th>
                                @endforeach
                                <th>Total OJT Hr per week</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach(($m['weeks'] ?? []) as $week)
                                <tr>
                                    @foreach(($week['days'] ?? []) as $day)
                                        <td class="day-cell">
                                            @if(!empty($day['day']))
                                                <div class="day-number">{{ $day['day'] }}</div>
                                                <div class="hours">
                                                    {{ empty($day['hours']) ? '' : rtrim(rtrim(number_format((float)$day['hours'], 2), '0'), '.') }}
                                                </div>
                                            @endif
                                        </td>
                                    @endforeach
                                    <td style="text-align:center;">
                                        <span class="hours">
                                            {{ ($week['total'] ?? 0) > 0 ? rtrim(rtrim(number_format((float)$week['total'], 2), '0'), '.') : '' }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                            <tr>
                                <td colspan="7" style="text-align:right; font-weight:800;">Monthly Total:</td>
                                <td style="text-align:center;"><span class="hours">{{ rtrim(rtrim(number_format((float)($m['month_total'] ?? 0), 2), '0'), '.') }}</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            @endforeach

            <div class="summary-card">
                <div class="summary-header">Summary</div>
                <table>
                    <thead>
                        <tr>
                            <th style="text-align:left;">OJT Hr per month</th>
                            <th style="text-align:right;">Sub Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach(($mapping['summary'] ?? []) as $row)
                            <tr>
                                <td>{{ $row['label'] ?? '' }}</td>
                                <td style="text-align:right;"><span class="hours">{{ rtrim(rtrim(number_format((float)($row['hours'] ?? 0), 2), '0'), '.') }}</span></td>
                            </tr>
                        @endforeach
                        <tr>
                            <td style="text-align:right; font-weight:800;">TOTAL OJT HOURS</td>
                            <td style="text-align:right;"><span class="hours">{{ rtrim(rtrim(number_format((float)($mapping['overall_total'] ?? 0), 2), '0'), '.') }}</span></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
