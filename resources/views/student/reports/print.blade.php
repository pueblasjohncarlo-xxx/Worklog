<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Worklog Report</title>
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
        .header {
            display: flex;
            align-items: center;
            justify-content: flex-start;
            margin-bottom: 22px;
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
            margin: 10px 0 20px 0;
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
        .meta { margin-bottom: 20px; display: flex; justify-content: space-between; font-size: 10pt; border-bottom: 1px solid #eee; padding-bottom: 15px; }
        .meta div { width: 48%; }
        .meta strong { display: block; margin-bottom: 5px; color: #111; font-weight: bold; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; font-size: 9pt; border: 1px solid #000; }
        th, td { padding: 8px; border: 1px solid #000; text-align: left; }
        th { background-color: #fce4bd; font-weight: bold; text-transform: uppercase; color: #000; text-align: center; }
        .footer { margin-top: 18px; text-align: center; font-size: 8.5pt; border-top: 1.5px solid #2b5797; padding-top: 8px; color: #2b5797; font-weight: bold; }
        .status-badge { padding: 2px 6px; border-radius: 4px; font-weight: bold; font-size: 8pt; text-transform: uppercase; }
        .status-approved { background-color: #d1fae5; color: #065f46; }
        .status-pending { background-color: #eff6ff; color: #1e40af; }
        .status-rejected { background-color: #ffe4e6; color: #9f1239; }
        
        @media print {
            body { background-color: #fff; padding: 0; }
            .no-print { display: none; }
            .page-container {
                margin: 0;
                padding: 14mm 12mm 16mm 12mm;
                width: 100%;
                border: none;
                box-shadow: none;
            }
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
                background-color: #fff;
            }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="page-container">
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
            <div class="report-title-sub">Worklog Summary Report</div>
            <p style="font-size: 9pt; color: #666; margin-top: 5px;">Generated on {{ now()->format('F d, Y h:i A') }}</p>
        </div>

    <div class="meta">
        <div>
            <strong>Student Information</strong>
            <p>{{ Auth::user()->name }}</p>
            <p>{{ Auth::user()->email }}</p>
        </div>
        <div>
            <strong>Placement Details</strong>
            <p>Company: {{ $assignment->company->name ?? 'N/A' }}</p>
            <p>Supervisor: {{ $assignment->supervisor->name ?? 'N/A' }}</p>
        </div>
    </div>

    <div class="meta">
        <div>
            <strong>Report Period</strong>
            <p>
                From: {{ request('start_date') ? \Carbon\Carbon::parse(request('start_date'))->format('M d, Y') : 'Start' }} 
                To: {{ request('end_date') ? \Carbon\Carbon::parse(request('end_date'))->format('M d, Y') : 'Present' }}
            </p>
        </div>
        <div>
            <strong>Summary</strong>
            <p>Total Entries: {{ $workLogs->count() }}</p>
            <p>Total Hours: {{ number_format($workLogs->sum('hours'), 2) }}</p>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Time In</th>
                <th>Time Out</th>
                <th>Hours</th>
                <th>Status</th>
                <th>Description</th>
                <th>Reviewer</th>
            </tr>
        </thead>
        <tbody>
            @foreach($workLogs as $log)
                <tr>
                    <td>{{ $log->work_date->format('M d, Y') }}</td>
                    <td>{{ $log->time_in ? \Carbon\Carbon::parse($log->time_in)->format('h:i A') : '-' }}</td>
                    <td>{{ $log->time_out ? \Carbon\Carbon::parse($log->time_out)->format('h:i A') : '-' }}</td>
                    <td>{{ number_format($log->hours, 2) }}</td>
                    <td>
                        <span class="status-badge status-{{ $log->status }}">
                            {{ $log->status }}
                        </span>
                    </td>
                    <td>{{ $log->description }}</td>
                    <td>{{ $log->reviewer->name ?? '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Website: <a href="http://www.llcc.edu.ph" style="color: #2b5797;">www.llcc.edu.ph</a> | Fb page: LLCC Public Information Office | Email: llccadmin@llcc.edu.ph
    </div>
    </div>
</body>
</html>
