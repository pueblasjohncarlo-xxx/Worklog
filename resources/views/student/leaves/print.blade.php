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
            <div class="header-text" style="text-align: center; width: 100%;">
                <h1 style="font-size: 14pt;">MINISTRY OF LABOUR AND SMALL ENTERPRISE DEVELOPMENT</h1>
                <div class="report-title-main" style="font-size: 14pt;">ON-THE-JOB TRAINING PROGRAMME</div>
                <p>Corner Chaguanas Main Road and Connector Road, Chaguanas</p>
                <p>Tel: (868) 671-4447 / 671-7822 / 671-1764 / 671-3457</p>
                <p>Website: <a href="http://molsed.gov.tt" style="color: black; text-decoration: underline;">molsed.gov.tt</a></p>
                <hr style="border: 1px solid black; margin: 10px 0;">
                <div class="report-title-sub" style="font-size: 14pt;">TRAINEE APPLICATION FOR LEAVE OF ABSENCE FORM</div>
            </div>
        </div>

        <table class="info-table" style="margin-top: 20px; font-size: 11pt; line-height: 1.8;">
            <tr>
                <td style="width: 25%; font-weight: bold;">TO:</td>
                <td style="width: 75%; border-bottom: 1px solid black; text-align: center;">
                    {{ optional($assignment->supervisor)->name ?? '' }}
                </td>
            </tr>
            <tr>
                <td></td>
                <td style="text-align: center; font-size: 9pt;">Name of Supervisor/Head of Department</td>
            </tr>
            
            <tr>
                <td style="font-weight: bold;">NAME OF TRAINEE:</td>
                <td style="border-bottom: 1px solid black;">
                    {{ $leave->student_name ?? ($student->lastname . ', ' . $student->firstname . ' ' . $student->middlename) }}
                </td>
            </tr>
            
            <tr>
                <td style="font-weight: bold;">TRAINING PROVIDER NAME:</td>
                <td style="border-bottom: 1px solid black;">
                    {{ $leave->company_name ?? optional($assignment->company)->name }}
                </td>
            </tr>
            
            <tr>
                <td style="font-weight: bold;">TRAINING ADDRESS:</td>
                <td style="border-bottom: 1px solid black;">
                    {{ optional($assignment->company)->address ?? '' }}
                </td>
            </tr>
        </table>
        
        <p style="font-size: 10pt; font-style: italic; margin-bottom: 5px;">Indicate the 'leave type' being applied for, from the list below:</p>

        <table style="width: 100%; border: 1px solid black; text-align: center; border-collapse: collapse; margin-bottom: 20px;">
            <thead>
                <tr>
                    <th style="border: 1px solid black; width: 60%; background-color: transparent;">LEAVE TYPE</th>
                    <th style="border: 1px solid black; width: 20%; background-color: transparent;">START DATE</th>
                    <th style="border: 1px solid black; width: 20%; background-color: transparent;">END DATE</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="border: 1px solid black; text-align: left; padding: 10px;">
                        @if($leave->type == 'Sick Leave') <strong>✔</strong> @endif SICK <em>(Medical Certificate for &ge; 3 days)</em>
                    </td>
                    <td style="border: 1px solid black;">@if($leave->type == 'Sick Leave') {{ optional($leave->start_date)->format('Y-m-d') }} @endif</td>
                    <td style="border: 1px solid black;">@if($leave->type == 'Sick Leave') {{ optional($leave->end_date)->format('Y-m-d') }} @endif</td>
                </tr>
                <tr>
                    <td style="border: 1px solid black; text-align: left; padding: 10px; font-weight: bold;">
                        @if($leave->type == 'Discretionary') ✔ @endif *DISCRETIONARY
                    </td>
                    <td style="border: 1px solid black;">@if($leave->type == 'Discretionary') {{ optional($leave->start_date)->format('Y-m-d') }} @endif</td>
                    <td style="border: 1px solid black;">@if($leave->type == 'Discretionary') {{ optional($leave->end_date)->format('Y-m-d') }} @endif</td>
                </tr>
                <tr>
                    <td style="border: 1px solid black; text-align: left; padding: 10px; font-weight: bold;">
                        @if($leave->type == 'Maternity') ✔ @endif MATERNITY <em style="font-weight: normal;">(Copy of NI12 form attached and duly completed by the Medical Practitioner and OJT Regional Officer)</em>
                    </td>
                    <td style="border: 1px solid black;">@if($leave->type == 'Maternity') {{ optional($leave->start_date)->format('Y-m-d') }} @endif</td>
                    <td style="border: 1px solid black;">@if($leave->type == 'Maternity') {{ optional($leave->end_date)->format('Y-m-d') }} @endif</td>
                </tr>
                <tr>
                    <td style="border: 1px solid black; text-align: left; padding: 10px; font-weight: bold;">
                        @if($leave->type == 'Exam') ✔ @endif EXAM <em style="font-weight: normal;">(documents attached-stamped and signed)</em>
                    </td>
                    <td style="border: 1px solid black;">@if($leave->type == 'Exam') {{ optional($leave->start_date)->format('Y-m-d') }} @endif</td>
                    <td style="border: 1px solid black;">@if($leave->type == 'Exam') {{ optional($leave->end_date)->format('Y-m-d') }} @endif</td>
                </tr>
                <tr>
                    <td style="border: 1px solid black; text-align: left; padding: 10px; font-weight: bold;">
                        @if($leave->type == 'Bereavement') ✔ @endif *BEREAVEMENT <em style="font-weight: normal;">(Copy of Death Certificate indicating relation)</em>
                    </td>
                    <td style="border: 1px solid black;">@if($leave->type == 'Bereavement') {{ optional($leave->start_date)->format('Y-m-d') }} @endif</td>
                    <td style="border: 1px solid black;">@if($leave->type == 'Bereavement') {{ optional($leave->end_date)->format('Y-m-d') }} @endif</td>
                </tr>
                <tr>
                    <td style="border: 1px solid black; text-align: left; padding: 10px; font-weight: bold;">
                        @if($leave->type == 'No Pay Leave') ✔ @endif *NO PAY LEAVE
                    </td>
                    <td style="border: 1px solid black;">@if($leave->type == 'No Pay Leave') {{ optional($leave->start_date)->format('Y-m-d') }} @endif</td>
                    <td style="border: 1px solid black;">@if($leave->type == 'No Pay Leave') {{ optional($leave->end_date)->format('Y-m-d') }} @endif</td>
                </tr>
            </tbody>
        </table>

        <div style="margin-bottom: 30px;">
            <p style="font-weight: bold; margin-bottom: 5px;">* Reasons: <span style="font-weight: normal; text-decoration: underline;">{{ $leave->reason }}</span></p>
        </div>

        <div style="display: flex; justify-content: space-between; margin-bottom: 30px; text-align: center;">
            <div style="width: 45%;">
                @if($leave->signature_path)
                    <div style="height:40px; display:flex; align-items:flex-end; justify-content:center; border-bottom: 1px solid black;">
                        <img src="{{ asset('storage/'.$leave->signature_path) }}" alt="Signature" style="max-width:200px; max-height:40px;">
                    </div>
                @else
                    <div style="border-bottom: 1px solid black; height: 40px;"></div>
                @endif
                <div style="font-size: 10pt; margin-top: 5px;">TRAINEE'S SIGNATURE</div>
            </div>
            <div style="width: 45%;">
                <div style="border-bottom: 1px solid black; height: 40px; line-height: 50px;">{{ ($leave->date_filed ?? now())->format('Y-m-d') }}</div>
                <div style="font-size: 10pt; margin-top: 5px;">DATE</div>
            </div>
        </div>

        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 30px;">
            <div style="width: 55%;">
                <div style="margin-bottom: 20px;">
                    <span style="font-weight: bold; margin-right: 10px;">Recommended</span> 
                    <span style="display: inline-block; width: 20px; height: 15px; border: 1px solid black; margin-right: 20px; text-align: center;">
                        @if($leave->status !== 'rejected') ✔ @endif
                    </span>
                    <span style="font-weight: bold; margin-right: 10px;">Not Recommended</span> 
                    <span style="display: inline-block; width: 20px; height: 15px; border: 1px solid black; text-align: center;">
                        @if($leave->status === 'rejected') ✔ @endif
                    </span>
                </div>
                
                <div style="display: flex; justify-content: space-between; text-align: center; margin-top: 40px;">
                    <div style="width: 30%;">
                        <div style="border-bottom: 1px solid black; min-height: 20px;">{{ optional($assignment->supervisor)->name ?? '' }}</div>
                        <div style="font-size: 9pt; font-weight: bold; margin-top: 5px;">SUPERVISOR'S NAME</div>
                    </div>
                    <div style="width: 30%;">
                        <div style="border-bottom: 1px solid black; min-height: 20px;">{{ $leave->reviewed_at ? $leave->reviewed_at->format('Y-m-d') : '' }}</div>
                        <div style="font-size: 9pt; font-weight: bold; margin-top: 5px;">DATE</div>
                    </div>
                    <div style="width: 30%;">
                        <div style="border-bottom: 1px solid black; min-height: 20px;">Supervisor</div>
                        <div style="font-size: 9pt; font-weight: bold; margin-top: 5px;">SUPERVISOR'S TITLE</div>
                    </div>
                </div>
            </div>
            <div style="width: 40%; border: 1px solid #ccc; height: 80px; display: flex; align-items: center; justify-content: center; color: #ccc;">
                Signature and Department / Company Stamp
            </div>
        </div>

        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 40px;">
            <div style="width: 50%;">
                <div style="margin-bottom: 40px;">
                    <span style="font-weight: bold; margin-right: 20px;">Approved</span> 
                    <span style="display: inline-block; width: 20px; height: 15px; border: 1px solid black; margin-right: 30px; text-align: center;">
                        @if($leave->status === 'approved') ✔ @endif
                    </span>
                    <span style="font-weight: bold; margin-right: 20px;">Not Approved</span> 
                    <span style="display: inline-block; width: 20px; height: 15px; border: 1px solid black; text-align: center;">
                        @if($leave->status === 'rejected') ✔ @endif
                    </span>
                </div>
                
                <div style="display: flex; justify-content: space-between; text-align: center;">
                    <div style="width: 45%;">
                        <div style="border-bottom: 1px solid black; min-height: 20px;"></div>
                        <div style="font-size: 9pt; font-weight: bold; margin-top: 5px;">PMO I</div>
                    </div>
                    <div style="width: 45%;">
                        <div style="border-bottom: 1px solid black; min-height: 20px;"></div>
                        <div style="font-size: 9pt; font-weight: bold; margin-top: 5px;">DATE</div>
                    </div>
                </div>
            </div>
            
            <div style="width: 45%; border: 1px solid black; padding: 10px; min-height: 80px;">
                <span style="font-weight: bold;">Comments:</span><br>
                {{ $leave->reviewer_remarks }}
            </div>
        </div>

        <div style="font-size: 8pt; font-weight: bold; text-align: justify; margin-bottom: 20px;">
            P.S. Leave taken without prior approval from the OJT and Provider may be treated as a breach of contract. All approved leave applications must be submitted with your monthly timesheets.
        </div>

        <div style="font-size: 7.5pt; text-align: center; border-top: 1px solid black; padding-top: 10px;">
            <strong>OJT Head Office:</strong> Corner Chaguanas Main Road and Connector Road, Chaguanas Tel: 671-7108; <strong>Chaguanas Office:</strong> Corner John & Lange Streets Montrose, Chaguanas Tel: 665-6658<br>
            <strong>Point Fortin Office:</strong> 69 Main Road, Point Fortin Tel: 648-5810; <strong>Port of Spain Office:</strong> Levels 5 & 6, Tower C, IWC, Wrightson Road, Port of Spain Tel: 625-8478<br>
            <strong>San Fernando Office:</strong> 40-42 St James Street, San Fernando Tel: 652-1350, 652-3181<br>
            <strong>Siparia Office:</strong> Siparia Administrative Complex, Corner Allis Street and SS Erin Road, Siparia Tel: 649-0982<br>
            <strong>Tunapuna Office:</strong> Anva Plaza, 16-20 Eastern Main Road, Tunapuna Tel: 645-8261; <strong>Tobago Office:</strong> Lot #2 Glen Road, Scarborough Tobago Tel: 685-8187
        </div>
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
