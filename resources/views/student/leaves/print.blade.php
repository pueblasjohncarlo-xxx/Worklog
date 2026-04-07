<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leave Request - {{ $student->name }}</title>
    <style>
        @page {
            size: A4;
            margin: 0;
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 10pt;
            line-height: 1.3;
            margin: 0;
            padding: 0;
            background-color: #fff;
        }
        .page-container {
            width: 210mm;
            min-height: 297mm;
            padding: 14mm 12mm 16mm 12mm;
            margin: auto;
            position: relative;
            box-sizing: border-box;
        }
        .top-bar {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 15px;
            display: flex;
        }
        .top-bar-gold {
            width: 30%;
            background-color: #f6b333;
        }
        .top-bar-blue {
            width: 70%;
            background-color: #2b5797;
        }
        .bottom-bar {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 10px;
            background-color: #2b5797;
        }
        .header {
            display: flex;
            align-items: center;
            justify-content: flex-start;
            margin-bottom: 18px;
            padding-top: 15px;
            gap: 15px;
        }
        .header-logo {
            width: 80px;
            height: 80px;
        }
        .header-text {
            text-align: left;
        }
        .header-text h1 {
            font-family: Arial, sans-serif;
            font-size: 24pt;
            margin: 0;
            color: #333;
            font-weight: bold;
        }
        .header-text p {
            margin: 0;
            font-size: 9.5pt;
            color: #555;
        }
        .report-title {
            text-align: center;
            margin: 10px 0 12px 0;
        }
        .report-title-main {
            font-weight: bold;
            font-size: 11pt;
            text-transform: uppercase;
        }
        .report-title-sub {
            font-weight: bold;
            font-size: 13pt;
        }
        .info-table {
            width: 100%;
            border: none;
            margin-bottom: 12px;
            font-size: 9.5pt;
        }
        .info-table td {
            border: none;
            padding: 2px 0;
            vertical-align: top;
        }
        .info-label {
            font-weight: bold;
            width: 180px;
            display: inline-block;
        }
        .info-value {
            border-bottom: 1px solid #000;
            display: inline-block;
            min-width: 260px;
            padding-bottom: 1px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #000;
        }
        th {
            background-color: #fce4bd;
            border: 1px solid #000;
            padding: 8px 6px;
            font-size: 9pt;
            text-transform: uppercase;
            text-align: center;
            font-weight: bold;
        }
        td {
            border: 1px solid #000;
            padding: 8px 8px;
            font-size: 9pt;
            vertical-align: top;
        }
        .box {
            height: 130px;
        }
        .signatures {
            margin-top: 22px;
        }
        .sig-table {
            width: 100%;
            border: none;
        }
        .sig-table td {
            border: none;
            padding: 10px 0;
            vertical-align: bottom;
        }
        .sig-label-top {
            font-weight: bold;
            font-size: 9.5pt;
            margin-bottom: 22px;
        }
        .sig-line-container {
            text-align: center;
            width: 320px;
        }
        .sig-line {
            border-bottom: 1px solid #000;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 9.5pt;
            padding-bottom: 2px;
            display: block;
            margin: 0 auto;
        }
        .sig-label-bottom {
            font-size: 8.5pt;
            margin-top: 3px;
            display: block;
        }
        .date-line-container {
            display: flex;
            align-items: baseline;
            justify-content: flex-end;
        }
        .date-label {
            font-weight: bold;
            font-size: 9.5pt;
            margin-right: 8px;
        }
        .date-line {
            border-bottom: 1px solid #000;
            width: 160px;
            min-height: 15px;
        }
        .footer {
            margin-top: 18px;
            text-align: center;
            font-size: 8.5pt;
            border-top: 1.5px solid #2b5797;
            padding-top: 8px;
            color: #2b5797;
            font-weight: bold;
        }
        .footer a {
            color: #2b5797;
            text-decoration: underline;
        }
        .no-print {
            position: fixed;
            top: 15px;
            right: 15px;
            z-index: 1000;
            display: flex;
            gap: 12px;
        }
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: bold;
            color: white;
            text-decoration: none;
            font-size: 10pt;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            transition: transform 0.2s;
        }
        .btn:hover { transform: translateY(-2px); }
        .btn-print { background-color: #4f46e5; }
        .btn-doc { background-color: #10b981; }
        .btn-back { background-color: #6b7280; }
        @media print {
            .no-print { display: none; }
            body { background-color: #fff; padding: 0; }
            .page-container {
                margin: 0;
                padding: 14mm 12mm 16mm 12mm;
                width: 100%;
                border: none;
                box-shadow: none;
            }
            table, tr, td, th { page-break-inside: avoid; }
        }
        @media screen {
            body {
                background-color: #e5e7eb;
                padding: 30px 0;
            }
            .page-container {
                box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
                border-radius: 8px;
                margin: 0 auto;
            }
        }
    </style>
</head>
<body>
    <div class="no-print">
        <button onclick="window.print()" class="btn btn-print">Print PDF</button>
        <button onclick="exportToDoc()" class="btn btn-doc">Download .doc</button>
        <button onclick="window.history.back()" class="btn btn-back">Back</button>
    </div>

    <div class="page-container" id="reportContent">
        <div class="top-bar">
            <div class="top-bar-gold"></div>
            <div class="top-bar-blue"></div>
        </div>

        <div class="header">
            <img src="{{ asset('images/logo.png') }}" alt="Logo" class="header-logo" onerror="this.style.display='none'">
            <div class="header-text">
                <h1>Lapu-Lapu City College</h1>
                <p>Don B. Benedicto Rd., Gun-ob, Lapu-Lapu City, 6015</p>
                <p>School Code: 7174</p>
            </div>
        </div>

        <div class="report-title">
            <div class="report-title-main">ON-THE-JOB TRAINING (OJT)</div>
            <div class="report-title-sub">Leave Request Form</div>
        </div>

        <table class="info-table">
            <tr>
                <td style="width: 55%;">
                    <span class="info-label">Student's Name:</span> <span class="info-value">{{ $leave->student_name ?? ($student->lastname . ', ' . $student->firstname . ' ' . $student->middlename) }}</span>
                </td>
                <td style="width: 45%;">
                    <span class="info-label">Course & Major:</span> <span class="info-value">{{ $leave->course_major ?? (($student->section ?? 'N/A') . ' - ' . ($student->department ?? 'N/A')) }}</span>
                </td>
            </tr>
            <tr>
                <td>
                    <span class="info-label">Year & Section:</span> <span class="info-value">{{ $leave->year_section ?? ($student->section ?? 'N/A') }}</span>
                </td>
                <td>
                    <span class="info-label">Cellphone No.:</span> <span class="info-value">{{ $leave->cellphone_no ?? (optional($student->studentProfile)->phone ?? 'N/A') }}</span>
                </td>
            </tr>
            <tr>
                <td>
                    <span class="info-label">Company's Name:</span> <span class="info-value">{{ $leave->company_name ?? optional($assignment->company)->name }}</span>
                </td>
                <td>
                    <span class="info-label">Date Filed:</span> <span class="info-value">{{ ($leave->date_filed ?? now())->format('F d, Y') }}</span>
                </td>
            </tr>
            <tr>
                <td>
                    <span class="info-label">Student OJT Job Designation:</span> <span class="info-value">{{ $leave->job_designation ?? (optional($student->studentProfile)->program ?? 'Intern') }}</span>
                </td>
                <td>
                    <span class="info-label">Leave Type:</span> <span class="info-value">{{ $leave->type }}</span>
                </td>
            </tr>
            <tr>
                <td>
                    <span class="info-label">Inclusive Dates:</span> <span class="info-value">{{ $leave->start_date->format('M d, Y') }} - {{ $leave->end_date->format('M d, Y') }}</span>
                </td>
                <td>
                    <span class="info-label">Status:</span> <span class="info-value">{{ $leave->status }}</span>
                </td>
            </tr>
        </table>

        <table>
            <thead>
                <tr>
                    <th>Reason / Explanation</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="box">
                        {{ $leave->reason }}
                    </td>
                </tr>
            </tbody>
        </table>

        <div class="signatures">
            <table class="sig-table">
                <tr>
                    <td>
                        <div class="sig-label-top">Prepared by:</div>
                        <div class="sig-line-container">
                            @if($leave->signature_path)
                                <div style="height:62px; display:flex; align-items:flex-end; justify-content:center; margin-bottom:6px;">
                                    <img src="{{ asset('storage/'.$leave->signature_path) }}" alt="Signature" style="max-width:260px; max-height:60px;">
                                </div>
                            @else
                                <div style="height:62px;"></div>
                            @endif
                            <span class="sig-line">{{ $leave->prepared_by ?? ($student->lastname . ', ' . $student->firstname . ' ' . $student->middlename) }}</span>
                            <span class="sig-label-bottom">OJTee's Signature over Printed Name</span>
                        </div>
                    </td>
                    <td>
                        <div class="date-line-container">
                            <span class="date-label">Date:</span>
                            <div class="date-line"></div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div class="sig-label-top">Reviewed by:</div>
                        <div class="sig-line-container">
                            <span class="sig-line">{{ optional($assignment->supervisor)->name ?? '__________________________' }}</span>
                            <span class="sig-label-bottom">Company Supervisor/Manager's Signature over Printed Name</span>
                        </div>
                    </td>
                    <td>
                        <div class="date-line-container">
                            <span class="date-label">Date:</span>
                            <div class="date-line"></div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div class="sig-line-container" style="margin-top: 15px;">
                            <span class="sig-line">{{ optional($assignment->ojtAdviser)->name ?? '__________________________' }}</span>
                            <span class="sig-label-bottom">LLCC-CDT OJT Coordinator Signature over Printed Name</span>
                        </div>
                    </td>
                    <td>
                        <div class="date-line-container">
                            <span class="date-label">Date:</span>
                            <div class="date-line"></div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div class="sig-label-top">Approved by:</div>
                        <div class="sig-line-container">
                            <span class="sig-line">DR. ROBERT B. PABILLARAN</span>
                            <span class="sig-label-bottom">VP Academics/ Dean, CDT Signature over Printed Name</span>
                        </div>
                    </td>
                    <td>
                        <div class="date-line-container">
                            <span class="date-label">Date:</span>
                            <div class="date-line"></div>
                        </div>
                    </td>
                </tr>
            </table>
        </div>

        <div class="footer">
            Website: <a href="http://www.llcc.edu.ph">www.llcc.edu.ph</a> | Fb page: LLCC Public Information Office | Email: llccadmin@llcc.edu.ph
        </div>
        <div class="bottom-bar"></div>
    </div>

    <script>
        function exportToDoc() {
            const content = document.getElementById('reportContent').innerHTML;
            const header = "<html xmlns:o='urn:schemas-microsoft-com:office:office' xmlns:w='urn:schemas-microsoft-com:office:word' xmlns='http://www.w3.org/TR/REC-html40'>" +
                "<head><meta charset='utf-8'><title>Leave Request</title>" +
                "<style>" +
                "@page { size: 8.5in 11in; margin: 0.5in; }" +
                "body { font-family: Arial, sans-serif; font-size: 10pt; }" +
                "table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }" +
                "th, td { border: 1px solid black; padding: 6px; font-size: 9pt; }" +
                ".top-bar { height: 15px; width: 100%; display: table; }" +
                ".top-bar-gold { background: #f6b333; width: 30%; display: table-cell; }" +
                ".top-bar-blue { background: #2b5797; width: 70%; display: table-cell; }" +
                ".header { margin-bottom: 15px; width: 100%; }" +
                ".header-logo { width: 60pt; height: 60pt; float: left; margin-right: 15pt; }" +
                ".header-text h1 { font-size: 20pt; margin: 0; }" +
                ".header-text p { margin: 0; font-size: 9pt; }" +
                ".bottom-bar { background: #2b5797; height: 10px; width: 100%; }" +
                ".sig-line { border-bottom: 1px solid black; min-width: 200px; }" +
                ".info-label { font-weight: bold; }" +
                ".report-title { text-align: center; margin: 10px 0; }" +
                ".report-title-main { font-weight: bold; font-size: 11pt; }" +
                ".report-title-sub { font-weight: bold; font-size: 12pt; }" +
                "</style>" +
                "</head><body>";
            const footer = "</body></html>";
            const html = header + content + footer;

            const blob = new Blob(['\ufeff', html], { type: 'application/msword' });
            const url = URL.createObjectURL(blob);
            const link = document.createElement('a');
            link.href = url;
            const dateStr = "{{ $leave->start_date->format('Y-m-d') }}";
            link.download = "Leave_Request_" + dateStr + ".doc";
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }
    </script>
</body>
</html>
