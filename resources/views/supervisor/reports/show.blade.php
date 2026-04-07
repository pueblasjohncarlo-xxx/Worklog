<x-supervisor-layout>
    <x-slot name="header">
        Performance Report Result
    </x-slot>

    <style>
        @media print {
            .no-print, nav, header { display: none !important; }
            body { background-color: white !important; padding: 0 !important; margin: 0 !important; }
            .max-w-4xl { max-width: 100% !important; width: 100% !important; margin: 0 !important; padding: 0 !important; }
            .shadow-md, .rounded-xl, .border-gray-100 { box-shadow: none !important; border: none !important; border-radius: 0 !important; }
            .bg-gray-50, .bg-white { background-color: transparent !important; }
            .print-header { display: block !important; }
            .top-bar { display: flex !important; position: relative !important; margin-bottom: 20px; height: 15px; width: 100%; }
            .top-bar-gold { width: 30%; background-color: #f6b333 !important; print-color-adjust: exact; -webkit-print-color-adjust: exact; }
            .top-bar-blue { width: 70%; background-color: #2b5797 !important; print-color-adjust: exact; -webkit-print-color-adjust: exact; }
            .header-content { display: flex !important; align-items: center; justify-content: flex-start; margin-bottom: 22px; gap: 15px; }
            .header-logo { width: 80px; height: 80px; }
            .header-text { text-align: left; }
            .header-text h1 { font-family: Arial, sans-serif; font-size: 22pt; margin: 0; color: #333; font-weight: bold; }
            .header-text p { margin: 0; font-size: 9pt; color: #555; }
            .report-title { text-align: center; margin: 15px 0 25px 0; }
            .report-title-main { font-weight: bold; font-size: 11pt; text-transform: uppercase; }
            .report-title-sub { font-weight: bold; font-size: 13pt; }
            .footer { margin-top: 30px; text-align: center; font-size: 8pt; border-top: 1.5pt solid #2b5797; padding-top: 8px; color: #2b5797; font-weight: bold; display: block !important; }
        }
        .print-header, .footer { display: none; }
    </style>

    <div class="max-w-4xl mx-auto py-6 space-y-6">
        <!-- Print Only Header -->
        <div class="print-header">
            <div class="top-bar">
                <div class="top-bar-gold"></div>
                <div class="top-bar-blue"></div>
            </div>
            <div class="header-content">
                <img src="{{ asset('images/logo.png') }}" alt="Logo" class="header-logo" onerror="this.style.display='none'">
                <div class="header-text">
                    <h1>Lapu-Lapu City College</h1>
                    <p>Don B. Benedicto Rd., Gun-ob, Lapu-Lapu City, 6015</p>
                    <p>School Code: 7174</p>
                </div>
            </div>
            <div class="report-title">
                <div class="report-title-main">ON-THE-JOB TRAINING (OJT)</div>
                <div class="report-title-sub">Performance Report Summary</div>
            </div>
        </div>

        <!-- Summary Card -->
        <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-100 no-print-shadow">
            <div class="bg-gray-50 px-6 py-4 border-b border-gray-200 flex justify-between items-center no-print-bg">
                <div>
                    <h3 class="font-bold text-gray-800 text-lg">Performance Report</h3>
                    <p class="text-sm text-gray-500">{{ $request->start_date }} to {{ $request->end_date }}</p>
                </div>
                <button onclick="window.print()" class="text-indigo-600 hover:text-indigo-800 font-bold text-sm uppercase flex items-center gap-2">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    Print Report
                </button>
            </div>
            <div class="p-6">
                <div class="flex items-center gap-4 mb-6">
                    <div class="h-16 w-16 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 text-2xl font-bold">
                        {{ substr($assignment->student->name, 0, 1) }}
                    </div>
                    <div>
                        <h4 class="text-xl font-bold text-gray-900">{{ $assignment->student->name }}</h4>
                        <p class="text-gray-500">{{ $assignment->student->email }}</p>
                        <p class="text-gray-500 text-sm mt-1">Company: {{ $assignment->company->name ?? 'N/A' }}</p>
                    </div>
                    <div class="ml-auto text-right">
                        <p class="text-sm text-gray-500 font-bold uppercase tracking-wider">Total Approved Hours</p>
                        <p class="text-3xl font-black text-indigo-600">{{ number_format($totalHours, 2) }}</p>
                    </div>
                </div>

                <div class="border-t border-gray-200 pt-6">
                    <h5 class="font-bold text-gray-700 mb-4">Work Log History</h5>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-2 font-bold text-gray-600 uppercase tracking-wider">Date</th>
                                    <th class="px-4 py-2 font-bold text-gray-600 uppercase tracking-wider">Hours</th>
                                    <th class="px-4 py-2 font-bold text-gray-600 uppercase tracking-wider">Description</th>
                                    <th class="px-4 py-2 font-bold text-gray-600 uppercase tracking-wider">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @foreach($workLogs as $log)
                                    <tr>
                                        <td class="px-4 py-2 font-medium text-gray-900">{{ $log->work_date->format('M d, Y') }}</td>
                                        <td class="px-4 py-2 font-mono text-gray-600">{{ number_format($log->hours, 2) }}</td>
                                        <td class="px-4 py-2 text-gray-500 italic">{{ Str::limit($log->description, 50) }}</td>
                                        <td class="px-4 py-2">
                                            <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase
                                                {{ $log->status === 'approved' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}
                                            ">
                                                {{ $log->status }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="footer">
            Website: <a href="http://www.llcc.edu.ph" style="color: #2b5797;">www.llcc.edu.ph</a> | Fb page: LLCC Public Information Office | Email: llccadmin@llcc.edu.ph
        </div>
    </div>
</x-supervisor-layout>