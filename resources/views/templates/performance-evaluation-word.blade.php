<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Performance Evaluation Template</title>
    <style>
        body { font-family: "Times New Roman", Times, serif; font-size: 12pt; color: #111; }
        .title { text-align: center; font-weight: 700; font-size: 14pt; margin: 12pt 0 16pt; text-transform: uppercase; }
        table.meta { width: 100%; border-collapse: collapse; margin-bottom: 14pt; }
        table.meta td { padding: 4pt 6pt; vertical-align: top; }
        .label { width: 26%; font-weight: 700; }
        .value { border-bottom: 1px solid #444; height: 18pt; }
        table.rubric { width: 100%; border-collapse: collapse; margin-top: 12pt; }
        table.rubric th, table.rubric td { border: 1px solid #777; padding: 6pt; text-align: left; }
        table.rubric th { background: #f3f4f6; }
        .section { margin-top: 14pt; }
        .lines { border: 1px solid #999; min-height: 120pt; padding: 8pt; }
        .hint { font-size: 9.5pt; color: #555; margin-top: 4pt; }
        .signatures { margin-top: 20pt; width: 100%; border-collapse: collapse; }
        .signatures td { width: 50%; padding: 8pt 10pt 0; vertical-align: top; }
        .sig-line { border-bottom: 1px solid #333; height: 20pt; }
        .small { font-size: 10pt; color: #444; }
    </style>
</head>
<body>
    <div class="title">OJT Student Performance Evaluation</div>

    <table class="meta">
        <tr>
            <td class="label">Student Name</td>
            <td class="value">{{ $studentName }}</td>
            <td class="label">Company</td>
            <td class="value">{{ $companyName }}</td>
        </tr>
        <tr>
            <td class="label">Evaluation Date</td>
            <td class="value">{{ $evaluationDate }}</td>
            <td class="label">Semester / Type</td>
            <td class="value">{{ $semester }}</td>
        </tr>
        <tr>
            <td class="label">Supervisor</td>
            <td class="value">{{ $supervisorName }}</td>
            <td class="label">Department / Unit</td>
            <td class="value"></td>
        </tr>
    </table>

    <table class="rubric">
        <thead>
            <tr>
                <th style="width: 62%;">Criteria</th>
                <th style="width: 38%;">Rating (1-5)</th>
            </tr>
        </thead>
        <tbody>
            <tr><td>Attendance and Punctuality</td><td></td></tr>
            <tr><td>Quality of Work</td><td></td></tr>
            <tr><td>Initiative</td><td></td></tr>
            <tr><td>Cooperation / Teamwork</td><td></td></tr>
            <tr><td>Dependability</td><td></td></tr>
            <tr><td>Communication Skills</td><td></td></tr>
            <tr><td><strong>Final Rating</strong></td><td></td></tr>
        </tbody>
    </table>

    <div class="section">
        <strong>Supervisor Remarks / Comments</strong>
        <div class="lines"></div>
        <p class="hint">Attach additional sheets if needed.</p>
    </div>

    <table class="signatures">
        <tr>
            <td>
                <div class="sig-line"></div>
                <div class="small"><strong>Prepared by:</strong> Supervisor Signature / Name</div>
            </td>
            <td>
                <div class="sig-line"></div>
                <div class="small"><strong>Date:</strong></div>
            </td>
        </tr>
    </table>
</body>
</html>
