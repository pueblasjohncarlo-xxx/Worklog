<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monthly Accomplishment Report - {{ $student->name }}</title>
    <style>
        @page {
            size: A4;
            margin: 0;
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 10pt;
            color: #333;
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
            margin-bottom: 22px;
            position: relative;
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
            font-family: 'Arial', sans-serif;
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
            margin: 10px 0;
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
            margin-bottom: 15px;
            font-size: 9.5pt;
        }
        .info-table td {
            border: none;
            padding: 2px 0;
        }
        .info-label {
            font-weight: bold;
            width: 180px;
            display: inline-block;
        }
        .info-value {
            border-bottom: 1px solid #000;
            display: inline-block;
            min-width: 250px;
            padding-bottom: 1px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            border: 1px solid #000;
        }
        th {
            background-color: #fce4bd;
            border: 1px solid #000;
            padding: 8px 5px;
            font-size: 9pt;
            text-transform: uppercase;
            text-align: center;
            font-weight: bold;
        }
        td {
            border: 1px solid #000;
            padding: 6px 5px;
            font-size: 9pt;
            vertical-align: top;
        }
        .row-date {
            background-color: #fce4bd;
            font-weight: bold;
            text-align: center;
            vertical-align: middle;
        }
        .row-total {
            background-color: #e2f0fe;
            font-weight: bold;
            color: #2b5797;
        }
        .signatures {
            margin-top: 24px;
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
            margin-bottom: 25px;
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
            margin-top: 30px;
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
            <div class="report-title-sub">Monthly Accomplishment Report</div>
        </div>

        <table class="info-table">
            <tr>
                <td style="width: 55%;">
                    <span class="info-label">Student's Name:</span> <span class="info-value">{{ $student->display_name_last_first }}</span>
                </td>
                <td style="width: 45%;">
                    <span class="info-label">Course & Major:</span> <span class="info-value">{{ $student->section ?? 'N/A' }} - {{ $student->department ?? 'N/A' }}</span>
                </td>
            </tr>
            <tr>
                <td>
                    <span class="info-label">Year & Section:</span> <span class="info-value">{{ $student->section ?? 'N/A' }}</span>
                </td>
                <td>
                    <span class="info-label">Cellphone No.:</span> <span class="info-value">{{ optional($student->studentProfile)->phone ?? 'N/A' }}</span>
                </td>
            </tr>
            <tr>
                <td>
                    <span class="info-label">Department Area:</span> <span class="info-value">{{ $student->department ?? 'N/A' }}</span>
                </td>
                <td>
                    <span class="info-label">Month:</span> <span class="info-value">{{ $report->work_date->format('F, Y') }}</span>
                </td>
            </tr>
            <tr>
                <td>
                    <span class="info-label">Student OJT Job Designation:</span> <span class="info-value">{{ optional($student->studentProfile)->program ?? 'Intern' }}</span>
                </td>
                <td>
                    <span class="info-label">Monthly Total Hours:</span> <span class="info-value">&nbsp;</span>
                </td>
            </tr>
        </table>

        <table>
            <thead>
                <tr>
                    <th style="width: 15%;">Date</th>
                    <th>List of Activities Accomplished</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="row-date" style="vertical-align: middle;">
                        &nbsp;
                    </td>
                    <td style="padding: 12px 15px; height: 180px;">
                        &nbsp;
                    </td>
                </tr>
                <tr>
                    <th colspan="2" style="text-align: left; padding: 8px 10px;">Remarks/Status</th>
                </tr>
                <tr>
                    <td colspan="2" style="height: 45px;">&nbsp;</td>
                </tr>
            </tbody>
        </table>

        <div class="signatures">
            <table class="sig-table">
                <tr>
                    <td>
                        <div class="sig-label-top">Prepared by:</div>
                        <div class="sig-line-container">
                            <span class="sig-line">{{ $student->display_name_last_first }}</span>
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
            "<head><meta charset='utf-8'><title>Monthly Accomplishment Report</title>" +
            "<style>" +
            "@page { size: 8.5in 11in; margin: 0.5in; }" +
            "body { font-family: Arial, sans-serif; font-size: 10pt; }" +
            "table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }" +
            "th, td { border: 1px solid black; padding: 5px; font-size: 9pt; }" +
            ".top-bar { height: 15px; width: 100%; display: table; }" +
            ".top-bar-gold { background: #f6b333; width: 30%; display: table-cell; }" +
            ".top-bar-blue { background: #2b5797; width: 70%; display: table-cell; }" +
            ".bottom-bar { background: #2b5797; height: 10px; width: 100%; }" +
            ".sig-line { border-bottom: 1px solid black; min-width: 200px; }" +
            ".info-label { font-weight: bold; }" +
            ".report-title { text-align: center; margin: 10px 0; }" +
            ".report-title-main { font-weight: bold; font-size: 11pt; }" +
            ".report-title-sub { font-weight: bold; font-size: 12pt; }" +
            ".row-date { background-color: #fce4bd; font-weight: bold; text-align: center; }" +
            ".row-total { background-color: #e2f0fe; font-weight: bold; color: #2b5797; }" +
            "</style>" +
            "</head><body>";
            const footer = "</body></html>";
            const html = header + content + footer;

            const blob = new Blob(['\ufeff', html], {
                type: 'application/msword'
            });
            const url = URL.createObjectURL(blob);
            const link = document.createElement('a');
            link.href = url;
            const dateStr = '{{ $report->work_date->format("Y-m-d") }}';
            link.download = "Monthly_Accomplishment_Report_" + dateStr + ".doc";
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }
    </script>
</body>
</html>
