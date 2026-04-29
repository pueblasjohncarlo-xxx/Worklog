<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Performance Evaluation Print</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            color: #111827;
            margin: 24px;
            line-height: 1.45;
        }
        .page {
            max-width: 900px;
            margin: 0 auto;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 16px;
            margin-bottom: 24px;
        }
        .title {
            font-size: 28px;
            font-weight: 800;
            margin: 0 0 6px;
        }
        .subtitle {
            margin: 0;
            color: #475569;
            font-size: 14px;
        }
        .badge {
            border: 1px solid #cbd5e1;
            background: #f8fafc;
            padding: 8px 12px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 700;
        }
        .meta {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 12px;
            margin-bottom: 24px;
        }
        .meta-card {
            border: 1px solid #dbe4ee;
            border-radius: 12px;
            padding: 14px 16px;
            background: #fff;
        }
        .meta-label {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #64748b;
            margin-bottom: 6px;
        }
        .meta-value {
            font-size: 16px;
            font-weight: 700;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 24px;
        }
        th, td {
            border: 1px solid #dbe4ee;
            padding: 12px 14px;
            text-align: left;
            vertical-align: top;
        }
        th {
            background: #f8fafc;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.06em;
        }
        .rating {
            font-weight: 800;
            text-align: right;
            width: 120px;
        }
        .remarks {
            border: 1px solid #dbe4ee;
            border-radius: 12px;
            padding: 16px;
            background: #fff;
        }
        .remarks h2 {
            font-size: 14px;
            margin: 0 0 10px;
            text-transform: uppercase;
            letter-spacing: 0.06em;
        }
        .toolbar {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 16px;
        }
        .toolbar button {
            padding: 10px 16px;
            border: 0;
            border-radius: 8px;
            background: #111827;
            color: #fff;
            font-weight: 700;
            cursor: pointer;
        }
        @media print {
            .toolbar {
                display: none;
            }
            body {
                margin: 0;
            }
            .page {
                max-width: none;
            }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="page">
        <div class="toolbar">
            <button type="button" onclick="window.print()">Print</button>
        </div>

        <div class="header">
            <div>
                <h1 class="title">Student Performance Evaluation</h1>
                <p class="subtitle">Student print view of your finalized supervisor evaluation.</p>
            </div>
            <div class="badge">
                {{ $evaluation->semester ?? 'Evaluation' }}
            </div>
        </div>

        <div class="meta">
            <div class="meta-card">
                <div class="meta-label">Student</div>
                <div class="meta-value">{{ $evaluation->student?->name ?? 'N/A' }}</div>
            </div>
            <div class="meta-card">
                <div class="meta-label">Supervisor</div>
                <div class="meta-value">{{ $evaluation->supervisor?->name ?? 'N/A' }}</div>
            </div>
            <div class="meta-card">
                <div class="meta-label">Company</div>
                <div class="meta-value">{{ $assignment?->company?->name ?? 'N/A' }}</div>
            </div>
            <div class="meta-card">
                <div class="meta-label">Evaluation Date</div>
                <div class="meta-value">{{ $evaluation->evaluation_date?->format('F d, Y') ?? 'N/A' }}</div>
            </div>
            <div class="meta-card">
                <div class="meta-label">Submitted At</div>
                <div class="meta-value">{{ $evaluation->submitted_at?->format('F d, Y h:i A') ?? 'N/A' }}</div>
            </div>
            <div class="meta-card">
                <div class="meta-label">Final Rating</div>
                <div class="meta-value">{{ number_format((float) ($evaluation->final_rating ?? 0), 1) }} / 5</div>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Criteria</th>
                    <th class="rating">Rating</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Attendance and Punctuality</td>
                    <td class="rating">{{ number_format((float) ($evaluation->attendance_punctuality ?? 0), 1) }} / 5</td>
                </tr>
                <tr>
                    <td>Quality of Work</td>
                    <td class="rating">{{ number_format((float) ($evaluation->quality_of_work ?? 0), 1) }} / 5</td>
                </tr>
                <tr>
                    <td>Initiative</td>
                    <td class="rating">{{ number_format((float) ($evaluation->initiative ?? 0), 1) }} / 5</td>
                </tr>
                <tr>
                    <td>Cooperation</td>
                    <td class="rating">{{ number_format((float) ($evaluation->cooperation ?? 0), 1) }} / 5</td>
                </tr>
                <tr>
                    <td>Dependability</td>
                    <td class="rating">{{ number_format((float) ($evaluation->dependability ?? 0), 1) }} / 5</td>
                </tr>
                <tr>
                    <td>Communication Skills</td>
                    <td class="rating">{{ number_format((float) ($evaluation->communication_skills ?? 0), 1) }} / 5</td>
                </tr>
                <tr>
                    <td><strong>Final Rating</strong></td>
                    <td class="rating"><strong>{{ number_format((float) ($evaluation->final_rating ?? 0), 1) }} / 5</strong></td>
                </tr>
            </tbody>
        </table>

        <div class="remarks">
            <h2>Remarks</h2>
            <div>{{ $evaluation->remarks ?: 'No remarks provided.' }}</div>
        </div>
    </div>
</body>
</html>
