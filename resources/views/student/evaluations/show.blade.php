<x-student-layout>
    <x-slot name="header">
        Evaluation Details
    </x-slot>

    @php
        $isCompleted = $evaluation->submitted_at !== null;
        $statusLabel = $isCompleted ? 'Completed' : 'Pending';
    @endphp

    <div class="space-y-6">
        <div class="flex items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-black text-white">Student Performance Evaluation</h1>
                <p class="mt-1 text-sm font-medium text-slate-300">{{ $evaluation->semester ?: 'General Evaluation' }}</p>
            </div>
            <div class="flex items-center gap-2">
                <x-status-badge
                    :status="$isCompleted ? 'approved' : 'pending'"
                    :label="$statusLabel"
                    size="sm"
                    :class="$isCompleted ? '!border-emerald-600 !bg-emerald-100 !text-emerald-950 shadow-sm' : '!border-amber-600 !bg-amber-100 !text-amber-950 shadow-sm'"
                />
                <a href="{{ route('student.evaluations.index') }}" class="inline-flex items-center rounded-lg border border-white/10 bg-white/10 px-4 py-2 text-sm font-bold text-white transition hover:bg-white/15">
                    Back
                </a>
            </div>
        </div>

        @if(! $isCompleted)
            <div class="rounded-2xl border border-amber-300 bg-amber-100 px-5 py-4 text-sm font-semibold text-amber-950 shadow-sm">
                This evaluation is still pending. You may review the record details, but download and print actions will only become available after your supervisor finalizes the evaluation.
            </div>
        @endif

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            <div class="student-light-card p-6 lg:col-span-2">
                <div class="flex items-start justify-between gap-4 border-b border-slate-200 pb-5">
                    <div>
                        <p class="text-xs font-black uppercase tracking-[0.18em] text-slate-500">Evaluation Summary</p>
                        <h2 class="mt-2 text-xl font-black text-slate-950">Read-only supervisor evaluation</h2>
                        <p class="mt-2 text-sm font-medium text-slate-600">This page is view-only to protect the original supervisor submission.</p>
                    </div>
                    @if($isCompleted)
                        <div class="flex flex-wrap justify-end gap-2">
                            <a href="{{ route('student.evaluations.download', $evaluation) }}" class="inline-flex items-center rounded-lg border border-slate-800 bg-slate-800 px-3 py-2 text-xs font-black uppercase tracking-[0.14em] text-white shadow-sm transition-colors hover:bg-black">
                                Download
                            </a>
                            <a href="{{ route('student.evaluations.print', $evaluation) }}" target="_blank" class="inline-flex items-center rounded-lg border border-emerald-700 bg-emerald-700 px-3 py-2 text-xs font-black uppercase tracking-[0.14em] text-white shadow-sm transition-colors hover:bg-emerald-800">
                                Print
                            </a>
                        </div>
                    @endif
                </div>

                <div class="mt-6 grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                        <p class="text-xs font-black uppercase tracking-[0.16em] text-slate-500">Supervisor</p>
                        <p class="mt-2 text-lg font-black text-slate-950">{{ $evaluation->supervisor?->name ?? 'N/A' }}</p>
                    </div>
                    <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                        <p class="text-xs font-black uppercase tracking-[0.16em] text-slate-500">Company</p>
                        <p class="mt-2 text-lg font-black text-slate-950">{{ $assignment?->company?->name ?? 'N/A' }}</p>
                    </div>
                    <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                        <p class="text-xs font-black uppercase tracking-[0.16em] text-slate-500">Evaluation Date</p>
                        <p class="mt-2 text-lg font-black text-slate-950">{{ $evaluation->evaluation_date?->format('M d, Y') ?? 'Not set' }}</p>
                    </div>
                    <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                        <p class="text-xs font-black uppercase tracking-[0.16em] text-slate-500">Date Submitted</p>
                        <p class="mt-2 text-lg font-black text-slate-950">{{ $evaluation->submitted_at?->format('M d, Y h:i A') ?? 'Pending submission' }}</p>
                    </div>
                </div>

                <div class="mt-6 overflow-hidden rounded-2xl border border-slate-200">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-slate-100">
                            <tr>
                                <th class="px-5 py-4 text-xs font-black uppercase tracking-[0.16em] text-slate-700">Criteria</th>
                                <th class="px-5 py-4 text-xs font-black uppercase tracking-[0.16em] text-slate-700 text-right">Rating</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 bg-white">
                            <tr>
                                <td class="px-5 py-4 font-semibold text-slate-800">Attendance and Punctuality</td>
                                <td class="px-5 py-4 text-right font-black text-slate-950">{{ number_format((float) ($evaluation->attendance_punctuality ?? 0), 1) }} / 5</td>
                            </tr>
                            <tr>
                                <td class="px-5 py-4 font-semibold text-slate-800">Quality of Work</td>
                                <td class="px-5 py-4 text-right font-black text-slate-950">{{ number_format((float) ($evaluation->quality_of_work ?? 0), 1) }} / 5</td>
                            </tr>
                            <tr>
                                <td class="px-5 py-4 font-semibold text-slate-800">Initiative</td>
                                <td class="px-5 py-4 text-right font-black text-slate-950">{{ number_format((float) ($evaluation->initiative ?? 0), 1) }} / 5</td>
                            </tr>
                            <tr>
                                <td class="px-5 py-4 font-semibold text-slate-800">Cooperation</td>
                                <td class="px-5 py-4 text-right font-black text-slate-950">{{ number_format((float) ($evaluation->cooperation ?? 0), 1) }} / 5</td>
                            </tr>
                            <tr>
                                <td class="px-5 py-4 font-semibold text-slate-800">Dependability</td>
                                <td class="px-5 py-4 text-right font-black text-slate-950">{{ number_format((float) ($evaluation->dependability ?? 0), 1) }} / 5</td>
                            </tr>
                            <tr>
                                <td class="px-5 py-4 font-semibold text-slate-800">Communication Skills</td>
                                <td class="px-5 py-4 text-right font-black text-slate-950">{{ number_format((float) ($evaluation->communication_skills ?? 0), 1) }} / 5</td>
                            </tr>
                            <tr class="bg-slate-50">
                                <td class="px-5 py-4 font-black text-slate-950">Final Rating</td>
                                <td class="px-5 py-4 text-right text-lg font-black text-indigo-700">{{ number_format((float) ($evaluation->final_rating ?? 0), 2) }} / 5</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="space-y-6">
                <div class="student-light-card p-6">
                    <p class="text-xs font-black uppercase tracking-[0.18em] text-slate-500">Remarks</p>
                    <div class="mt-4 rounded-2xl border border-slate-200 bg-slate-50 p-4 text-sm font-medium leading-7 text-slate-800">
                        {{ $evaluation->remarks ?: 'No remarks provided.' }}
                    </div>
                </div>

                <div class="student-light-card p-6">
                    <p class="text-xs font-black uppercase tracking-[0.18em] text-slate-500">Document Access</p>
                    <div class="mt-4 space-y-3">
                        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                            <p class="text-sm font-bold text-slate-900">Original evaluation file</p>
                            <p class="mt-1 text-sm font-medium text-slate-600">
                                {{ $isCompleted ? 'You can safely download or print your finalized evaluation from this page.' : 'The evaluation file is locked until the submission is finalized.' }}
                            </p>
                        </div>
                        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                            <p class="text-sm font-bold text-slate-900">Access restriction</p>
                            <p class="mt-1 text-sm font-medium text-slate-600">Only your own evaluation records are visible in this section.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-student-layout>
