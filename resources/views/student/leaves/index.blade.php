<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Leave Request') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if($errors->has('leave'))
                        <div class="mb-4 p-3 bg-red-50 text-red-800 rounded">
                            {{ $errors->first('leave') }}
                        </div>
                    @endif

                    @if (session('status'))
                        <div class="mb-4 p-3 bg-green-50 text-green-800 rounded">
                            {{ session('status') }}
                        </div>
                    @endif

                    <div class="mb-6">
                        @if(!$assignment)
                            <div class="p-4 bg-yellow-50 text-yellow-800 rounded">
                                No active assignment found.
                            </div>
                        @else
                            <style>
                                .leave-format { width: 100%; background: #fff; border: 1px solid #e5e7eb; border-radius: 0.5rem; overflow: hidden; }
                                .leave-format .page-container { position: relative; padding: 20px 16px 22px 16px; }
                                .leave-format .top-bar { position: absolute; top: 0; left: 0; width: 100%; height: 10px; display: flex; }
                                .leave-format .top-bar .gold { width: 30%; background: #f6b333; }
                                .leave-format .top-bar .blue { width: 70%; background: #2b5797; }
                                .leave-format .header { display: flex; align-items: center; justify-content: flex-start; padding-top: 14px; margin-bottom: 14px; gap: 12px; }
                                .leave-format .header-logo { width: 64px; height: 64px; }
                                .leave-format .header-text { text-align: left; }
                                .leave-format .header-text h1 { font-size: 20px; font-weight: 800; color: #111827; margin: 0; }
                                .leave-format .header-text p { margin: 0; font-size: 11px; color: #374151; }
                                .leave-format .report-title { text-align: center; margin: 8px 0 12px 0; }
                                .leave-format .report-title .main { font-weight: 700; font-size: 11px; }
                                .leave-format .report-title .sub { font-weight: 800; font-size: 16px; }
                                .leave-format table { width: 100%; border-collapse: collapse; }
                                .leave-format .info-table td { padding: 4px 0; vertical-align: top; }
                                .leave-format .field { display: flex; align-items: baseline; gap: 8px; }
                                .leave-format .label { font-weight: 700; font-size: 12px; color: #111827; flex: 0 0 140px; }
                                .leave-format .value-underline { flex: 1 1 auto; min-width: 0; padding-bottom: 2px; border-bottom: 1px solid #111827; }
                                .leave-format .value-underline input { width: 100%; border: none; outline: none; padding: 0; font-size: 12px; background: transparent; }
                                .leave-format .value-underline input[type="date"] { padding: 2px 0; }
                                .leave-format .reason-header { background: #fce4bd; border: 1px solid #111827; padding: 6px 8px; font-weight: 700; text-transform: uppercase; font-size: 12px; }
                                .leave-format .reason-box { border: 1px solid #111827; border-top: none; padding: 0; }
                                .leave-format .reason-box textarea { width: 100%; min-height: 160px; border: none; outline: none; resize: vertical; padding: 10px; font-size: 13px; }
                                .leave-format .sig { display: grid; grid-template-columns: 1fr 180px; gap: 18px; margin-top: 18px; }
                                .leave-format .sig .sig-block { text-align: center; }
                                .leave-format .sig .sig-title { font-weight: 700; font-size: 12px; margin-bottom: 18px; }
                                .leave-format .sig .sig-line { border-bottom: 1px solid #111827; font-weight: 700; text-transform: uppercase; padding-bottom: 2px; }
                                .leave-format .sig .sig-sub { font-size: 11px; margin-top: 4px; color: #374151; }
                                .leave-format .sig .date { display: flex; align-items: center; gap: 8px; justify-content: flex-end; }
                                .leave-format .sig .date .lab { font-weight: 700; font-size: 12px; }
                                .leave-format .sig .date .line { border-bottom: 1px solid #111827; width: 150px; height: 18px; }
                                .leave-format .actions { display: flex; justify-content: flex-end; margin-top: 16px; gap: 10px; }
                            </style>
                            <div class="leave-format">
                                <div class="page-container">
                                    <div class="top-bar"><div class="gold"></div><div class="blue"></div></div>
                                    <div class="header">
                                        <img src="{{ asset('images/logo.png') }}" alt="Logo" class="header-logo" onerror="this.style.display='none'">
                                        <div class="header-text">
                                            <h1>Lapu-Lapu City College</h1>
                                            <p>Don B. Benedicto Rd., Gun-ob, Lapu-Lapu City, 6015</p>
                                            <p>School Code: 7174</p>
                                        </div>
                                    </div>
                                    <div class="report-title">
                                        <div class="main">ON-THE-JOB TRAINING (OJT)</div>
                                        <div class="sub">Leave Request Form</div>
                                    </div>

                                    <form method="POST" action="{{ route('student.leaves.store') }}">
                                        @csrf
                                        <table class="info-table">
                                            <tr>
                                                <td style="width:50%;">
                                                    <div class="field">
                                                        <div class="label">Student's Name:</div>
                                                        <div class="value-underline">
                                                            <input name="student_name" value="{{ old('student_name', $assignment->student->lastname . ', ' . $assignment->student->firstname . ' ' . $assignment->student->middlename) }}">
                                                        </div>
                                                    </div>
                                                </td>
                                                <td style="width:50%;">
                                                    <div class="field">
                                                        <div class="label">Course & Major:</div>
                                                        <div class="value-underline">
                                                            <input name="course_major" value="{{ old('course_major', ($assignment->student->section ?? 'N/A') . ' - ' . ($assignment->student->department ?? 'N/A')) }}">
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <div class="field">
                                                        <div class="label">Year & Section:</div>
                                                        <div class="value-underline">
                                                            <input name="year_section" value="{{ old('year_section', $assignment->student->section ?? 'N/A') }}">
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="field">
                                                        <div class="label">Cellphone No.:</div>
                                                        <div class="value-underline">
                                                            <input name="cellphone_no" value="{{ old('cellphone_no', optional($assignment->student->studentProfile)->phone ?? 'N/A') }}">
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <div class="field">
                                                        <div class="label">Company's Name:</div>
                                                        <div class="value-underline">
                                                            <input name="company_name" value="{{ old('company_name', optional($assignment->company)->name) }}">
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="field">
                                                        <div class="label">Date Filed:</div>
                                                        <div class="value-underline">
                                                            <input type="date" name="date_filed" value="{{ old('date_filed', now()->toDateString()) }}">
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <div class="field">
                                                        <div class="label">Student OJT Job Designation:</div>
                                                        <div class="value-underline">
                                                            <input name="job_designation" value="{{ old('job_designation', optional($assignment->student->studentProfile)->program ?? 'Intern') }}">
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="field">
                                                        <div class="label">Leave Type:</div>
                                                        <div class="value-underline">
                                                            <input name="type" value="{{ old('type') }}" required>
                                                        </div>
                                                    </div>
                                                    @error('type')<div class="text-sm text-red-600 mt-1">{{ $message }}</div>@enderror
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <div class="field" style="align-items:center;">
                                                        <div class="label">Inclusive Dates:</div>
                                                        <div class="value-underline" style="flex:0 1 150px;">
                                                            <input type="date" name="start_date" value="{{ old('start_date') }}" required>
                                                        </div>
                                                        <div style="flex:0 0 auto; padding:0 6px;">-</div>
                                                        <div class="value-underline" style="flex:0 1 150px;">
                                                            <input type="date" name="end_date" value="{{ old('end_date') }}" required>
                                                        </div>
                                                    </div>
                                                    @error('start_date')<div class="text-sm text-red-600 mt-1">{{ $message }}</div>@enderror
                                                    @error('end_date')<div class="text-sm text-red-600 mt-1">{{ $message }}</div>@enderror
                                                </td>
                                                <td>
                                                    <div class="field">
                                                        <div class="label">Status:</div>
                                                        <div class="value-underline">PENDING</div>
                                                    </div>
                                                </td>
                                            </tr>
                                        </table>

                                        <div class="reason-header">Reason / Explanation</div>
                                        <div class="reason-box">
                                            <textarea name="reason">{{ old('reason') }}</textarea>
                                        </div>
                                        @error('reason')<div class="text-sm text-red-600 mt-1">{{ $message }}</div>@enderror

                                        <div class="sig">
                                            <div class="sig-block">
                                                <div class="sig-title">Prepared by:</div>
                                                <div style="min-height:88px; display:flex; align-items:flex-end; justify-content:center;">
                                                    <div style="text-align:center;">
                                                        <canvas id="signaturePad" width="260" height="60" style="border:1px solid #111827; background:#fff;"></canvas>
                                                        <input type="hidden" name="signature" id="signatureInput">
                                                        <div class="mt-2">
                                                            <button type="button" onclick="clearSignature()" class="px-3 py-1 bg-gray-200 hover:bg-gray-300 rounded">Clear</button>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="sig-line" style="margin-top:6px;">
                                                    <input name="prepared_by" value="{{ old('prepared_by', $assignment->student->lastname . ', ' . $assignment->student->firstname . ' ' . $assignment->student->middlename) }}" style="width:100%; border:none; outline:none; background:transparent; text-align:center; font-weight:700; text-transform:uppercase;">
                                                </div>
                                                <div class="sig-sub">OJTee's Signature over Printed Name</div>
                                            </div>
                                            <div class="date">
                                                <div class="lab">Date:</div>
                                                <div class="line"></div>
                                            </div>
                                        </div>

                                        <div class="actions">
                                            <button type="submit" onclick="captureSignature()" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-md font-semibold">
                                                Submit Leave Request
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead>
                                <tr class="border-b bg-gray-50">
                                    <th class="text-left px-3 py-2">Type</th>
                                    <th class="text-left px-3 py-2">Dates</th>
                                    <th class="text-left px-3 py-2">Reason</th>
                                    <th class="text-left px-3 py-2">Status</th>
                                    <th class="text-left px-3 py-2">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($leaves as $leave)
                                    <tr class="border-b">
                                        <td class="px-3 py-2 uppercase">{{ $leave->type }}</td>
                                        <td class="px-3 py-2">
                                            {{ \Carbon\Carbon::parse($leave->start_date)->format('M d, Y') }} - {{ \Carbon\Carbon::parse($leave->end_date)->format('M d, Y') }}
                                        </td>
                                        <td class="px-3 py-2 max-w-md truncate">{{ $leave->reason }}</td>
                                        <td class="px-3 py-2 uppercase">{{ $leave->status }}</td>
                                        <td class="px-3 py-2">
                                            <a href="{{ route('student.leaves.print', $leave->id) }}" target="_blank" class="text-indigo-600 hover:underline font-semibold">
                                                Print / Doc
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-3 py-6 text-center text-gray-500">
                                            No leave requests yet.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $leaves->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        let sigPad, drawing = false, ctx, last = null;
        document.addEventListener('DOMContentLoaded', () => {
            sigPad = document.getElementById('signaturePad');
            if (!sigPad) return;
            ctx = sigPad.getContext('2d');
            ctx.lineWidth = 2;
            ctx.lineCap = 'round';
            const getPos = (e) => {
                const r = sigPad.getBoundingClientRect();
                const x = (e.touches ? e.touches[0].clientX : e.clientX) - r.left;
                const y = (e.touches ? e.touches[0].clientY : e.clientY) - r.top;
                return {x, y};
            };
            const start = (e) => { drawing = true; last = getPos(e); e.preventDefault(); };
            const move = (e) => {
                if (!drawing) return;
                const pos = getPos(e);
                ctx.beginPath();
                ctx.moveTo(last.x, last.y);
                ctx.lineTo(pos.x, pos.y);
                ctx.stroke();
                last = pos;
                e.preventDefault();
            };
            const end = () => { drawing = false; last = null; };
            sigPad.addEventListener('mousedown', start);
            sigPad.addEventListener('mousemove', move);
            sigPad.addEventListener('mouseup', end);
            sigPad.addEventListener('mouseleave', end);
            sigPad.addEventListener('touchstart', start, {passive:false});
            sigPad.addEventListener('touchmove', move, {passive:false});
            sigPad.addEventListener('touchend', end);
        });
        function clearSignature() {
            if (!sigPad) return;
            sigPad.getContext('2d').clearRect(0,0,sigPad.width,sigPad.height);
            const input = document.getElementById('signatureInput');
            if (input) input.value = '';
        }
        function captureSignature() {
            if (!sigPad) return;
            const data = sigPad.toDataURL('image/png');
            document.getElementById('signatureInput').value = data;
        }
    </script>
</x-app-layout>
