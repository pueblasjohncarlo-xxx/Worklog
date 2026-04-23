<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ ucfirst((string) $workLog->type) }} Accomplishment Report</title>
    <style>
        @page {
            size: A4;
            margin: 14mm 12mm 16mm 12mm;
        }

        body {
            font-family: Arial, sans-serif;
            background: #eef2f7;
            color: #111827;
            margin: 0;
            padding: 24px 0;
        }

        .page {
            width: 210mm;
            min-height: 297mm;
            margin: 0 auto;
            background: #ffffff;
            box-shadow: 0 20px 40px rgba(15, 23, 42, 0.14);
            box-sizing: border-box;
            padding: 16mm 14mm 18mm 14mm;
        }

        .actions {
            position: fixed;
            top: 16px;
            right: 16px;
            display: flex;
            gap: 10px;
            z-index: 1000;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 10px 16px;
            border-radius: 8px;
            text-decoration: none;
            font-size: 13px;
            font-weight: 700;
            border: none;
            cursor: pointer;
            color: #ffffff;
        }

        .btn-print {
            background: #4f46e5;
        }

        .btn-back {
            background: #475569;
        }

        .report-header {
            border-bottom: 3px solid #2b5797;
            padding-bottom: 14px;
            margin-bottom: 18px;
        }

        .report-header h1 {
            margin: 0 0 6px;
            font-size: 24px;
            line-height: 1.2;
            color: #111827;
        }

        .report-header p {
            margin: 0;
            color: #475569;
            font-size: 13px;
        }

        .meta-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 12px;
            margin-bottom: 18px;
        }

        .meta-card {
            border: 1px solid #dbe4f0;
            border-radius: 10px;
            padding: 12px 14px;
            background: #f8fafc;
        }

        .meta-label {
            display: block;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: #64748b;
            margin-bottom: 4px;
        }

        .meta-value {
            font-size: 14px;
            font-weight: 700;
            color: #111827;
        }

        .section {
            margin-top: 18px;
        }

        .section h2 {
            margin: 0 0 8px;
            font-size: 15px;
            color: #1e293b;
            text-transform: uppercase;
            letter-spacing: 0.06em;
        }

        .content-box {
            border: 1px solid #dbe4f0;
            border-radius: 10px;
            padding: 14px 16px;
            background: #ffffff;
            white-space: pre-wrap;
            line-height: 1.6;
            color: #1f2937;
            min-height: 72px;
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            padding: 5px 10px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.06em;
        }

        .status-draft { background: #fef3c7; color: #92400e; }
        .status-submitted { background: #dbeafe; color: #1d4ed8; }
        .status-approved { background: #d1fae5; color: #065f46; }
        .status-rejected { background: #ffe4e6; color: #be123c; }

        .attachment-note {
            margin-top: 14px;
            padding: 12px 14px;
            border-radius: 10px;
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            color: #1e3a8a;
            font-size: 13px;
            font-weight: 600;
        }

        @media print {
            body {
                background: #ffffff;
                padding: 0;
            }

            .actions {
                display: none;
            }

            .page {
                margin: 0;
                width: 100%;
                min-height: auto;
                box-shadow: none;
                padding: 0;
            }
        }

        @media (max-width: 900px) {
            .page {
                width: auto;
                margin: 0 12px;
            }

            .meta-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="actions">
        <button type="button" class="btn btn-print" onclick="window.print()">Print</button>
        <button type="button" class="btn btn-back" onclick="window.close()">Close</button>
    </div>

    <div class="page">
        <div class="report-header">
            <h1>{{ ucfirst((string) $workLog->type) }} Accomplishment Report</h1>
            <p>Prepared report content for printing on {{ now()->format('F d, Y h:i A') }}</p>
        </div>

        <div class="meta-grid">
            <div class="meta-card">
                <span class="meta-label">Student</span>
                <span class="meta-value">{{ $student?->name ?? 'N/A' }}</span>
            </div>
            <div class="meta-card">
                <span class="meta-label">Company</span>
                <span class="meta-value">{{ $company?->name ?? 'N/A' }}</span>
            </div>
            <div class="meta-card">
                <span class="meta-label">Work Date</span>
                <span class="meta-value">{{ $workLog->work_date?->format('F d, Y') ?? 'N/A' }}</span>
            </div>
            <div class="meta-card">
                <span class="meta-label">Hours</span>
                <span class="meta-value">{{ number_format((float) $workLog->hours, 2) }}</span>
            </div>
            <div class="meta-card">
                <span class="meta-label">Status</span>
                <span class="status-badge status-{{ $workLog->status }}">{{ $workLog->status }}</span>
            </div>
            <div class="meta-card">
                <span class="meta-label">Submitted To</span>
                <span class="meta-value">{{ ucfirst((string) ($workLog->submitted_to ?? 'N/A')) }}</span>
            </div>
            <div class="meta-card">
                <span class="meta-label">Supervisor</span>
                <span class="meta-value">{{ $supervisor?->name ?? 'N/A' }}</span>
            </div>
            <div class="meta-card">
                <span class="meta-label">Coordinator / Adviser</span>
                <span class="meta-value">{{ $coordinator?->name ?? ($ojtAdviser?->name ?? 'N/A') }}</span>
            </div>
        </div>

        <div class="section">
            <h2>Description</h2>
            <div class="content-box">{{ $workLog->description ?: 'No description provided.' }}</div>
        </div>

        @if($workLog->skills_applied)
            <div class="section">
                <h2>Skills Applied</h2>
                <div class="content-box">{{ $workLog->skills_applied }}</div>
            </div>
        @endif

        @if($workLog->reflection)
            <div class="section">
                <h2>Reflection</h2>
                <div class="content-box">{{ $workLog->reflection }}</div>
            </div>
        @endif

        @if($workLog->reviewer_comment)
            <div class="section">
                <h2>Reviewer Comment</h2>
                <div class="content-box">{{ $workLog->reviewer_comment }}</div>
            </div>
        @endif

        @if($workLog->attachment_path)
            <div class="attachment-note">
                An uploaded attachment exists for this report. If your browser opens the file directly from the print route, use that file's print dialog for the original submitted document.
            </div>
        @endif
    </div>
</body>
</html>
