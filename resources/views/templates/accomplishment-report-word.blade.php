<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Accomplishment Report Template</title>
    <style>
        body { font-family: "Times New Roman", Times, serif; font-size: 12pt; color: #111; }
        .title { text-align: center; font-weight: 700; font-size: 14pt; margin: 12pt 0 14pt; text-transform: uppercase; letter-spacing: .3px; }
        .subtitle { text-align: center; font-size: 11pt; margin: 0 0 14pt; }
        table.meta { width: 100%; border-collapse: collapse; margin: 0 0 12pt; }
        table.meta td { padding: 4pt 6pt; vertical-align: top; }
        .label { width: 22%; font-weight: 700; }
        .value { border-bottom: 1px solid #444; height: 18pt; }
        .section { margin-top: 12pt; }
        .section h3 { font-size: 12pt; margin: 0 0 6pt; font-weight: 700; }
        .lines { border: 1px solid #d1d5db; min-height: 120pt; padding: 8pt; }
        .hint { font-size: 9.5pt; color: #555; margin-top: 4pt; }
        .signature { margin-top: 18pt; width: 100%; }
        .sig-line { border-bottom: 1px solid #333; height: 18pt; }
        .small { font-size: 10pt; color: #444; }
        .lh-img { width: 100%; display: block; }
        .footer-img { width: 100%; display: block; margin-top: 18pt; }
    </style>
</head>
<body>
    @if(!empty($headerSrc))
        <img class="lh-img" src="{{ $headerSrc }}" alt="LLCC Header">
    @endif

    <div class="title">Accomplishment Report</div>
    <div class="subtitle">
        <strong>Type:</strong> {{ strtoupper($type) }}
        @if(!empty($periodLabel))
            &nbsp; | &nbsp; <strong>Covered Period:</strong> {{ $periodLabel }}
        @endif
    </div>

    <table class="meta">
        <tr>
            <td class="label">Student Name</td>
            <td class="value">{{ $studentName }}</td>
            <td class="label">Company</td>
            <td class="value">{{ $companyName }}</td>
        </tr>
        <tr>
            <td class="label">Course/Section</td>
            <td class="value">{{ $studentSection }}</td>
            <td class="label">Date Prepared</td>
            <td class="value">{{ $preparedDate }}</td>
        </tr>
        @if($type === 'daily')
        <tr>
            <td class="label">Hours Rendered</td>
            <td class="value">{{ $hoursRendered }}</td>
            <td class="label">Work Date</td>
            <td class="value">{{ $workDateLabel }}</td>
        </tr>
        @endif
    </table>

    <div class="section">
        <h3>Task/Activity Description</h3>
        <div class="lines"></div>
        <div class="hint">Write your completed tasks and activities for the covered period.</div>
    </div>

    <div class="section">
        <h3>Skills Applied/Learned</h3>
        <div class="lines" style="min-height: 80pt;"></div>
        <div class="hint">List technical and soft skills used or learned.</div>
    </div>

    <div class="section">
        <h3>Remarks/Reflection</h3>
        <div class="lines" style="min-height: 90pt;"></div>
        <div class="hint">Write reflections, challenges, learnings, and recommendations.</div>
    </div>

    <table class="signature">
        <tr>
            <td style="width: 48%;">
                <div class="small"><strong>Prepared by:</strong></div>
                <div class="sig-line"></div>
                <div class="small">Student Signature Over Printed Name</div>
            </td>
            <td style="width: 4%;"></td>
            <td style="width: 48%;">
                <div class="small"><strong>Noted/Reviewed by:</strong></div>
                <div class="sig-line"></div>
                <div class="small">Coordinator / OJT Adviser</div>
            </td>
        </tr>
    </table>

    @if(!empty($footerSrc))
        <img class="footer-img" src="{{ $footerSrc }}" alt="LLCC Footer">
    @endif
</body>
</html>
