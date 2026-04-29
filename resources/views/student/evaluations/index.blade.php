<x-student-layout>
    <x-slot name="header">
        Performance Evaluations
    </x-slot>

    <div class="space-y-6">
        <div class="flex gap-4 border-b border-white/20">
            <a href="{{ route('student.reports.index') }}" class="student-tab student-tab-inactive">
                Hours Log
            </a>
            <a href="{{ route('student.reports.index', ['view' => 'reports']) }}" class="student-tab student-tab-inactive">
                Reports
            </a>
            <a href="{{ route('student.evaluations.index') }}" class="student-tab student-tab-active">
                Performance Evaluations
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="student-light-card p-6">
                <p class="student-card-title mb-1">Completed Evaluations</p>
                <p class="text-4xl font-black text-emerald-600">{{ $completedEvaluationsCount }}</p>
                <p class="mt-3 text-[11px] font-bold uppercase tracking-[0.16em] text-slate-600">Finalized by supervisor</p>
            </div>
            <div class="student-light-card p-6">
                <p class="student-card-title mb-1">Pending Evaluations</p>
                <p class="text-4xl font-black text-amber-600">{{ $pendingEvaluationsCount }}</p>
                <p class="mt-3 text-[11px] font-bold uppercase tracking-[0.16em] text-slate-600">Not finalized yet</p>
            </div>
            <div class="student-light-card p-6">
                <p class="student-card-title mb-1">Current Status</p>
                <div class="mt-2">
                    <x-status-badge
                        :status="$overallStatus === 'Completed' ? 'approved' : ($overallStatus === 'Pending' ? 'pending' : 'draft')"
                        :label="$overallStatus"
                        size="sm"
                        class="!text-[12px] !font-black"
                    />
                </div>
                <p class="mt-4 text-sm font-semibold text-slate-700">
                    {{ $latestCompletedEvaluation?->submitted_at?->format('M d, Y h:i A') ? 'Last completed on '.$latestCompletedEvaluation->submitted_at->format('M d, Y h:i A') : 'No completed evaluation available yet.' }}
                </p>
            </div>
        </div>

        <div class="student-light-card overflow-hidden">
            <div class="border-b border-slate-200 px-6 py-4">
                <h2 class="text-lg font-black text-slate-900">Evaluation Records</h2>
                <p class="mt-1 text-sm font-medium text-slate-600">View finalized performance evaluations submitted by your assigned supervisor. Pending items are read-only and cannot be downloaded until completed.</p>
            </div>

            @if($evaluations->count() === 0)
                <div class="px-6 py-16 text-center">
                    <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-slate-100 text-slate-500">
                        <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <p class="mt-5 text-lg font-black text-slate-900">No evaluations available yet.</p>
                    <p class="mt-2 text-sm font-medium text-slate-600">Your completed supervisor evaluations will appear here once they are finalized and submitted.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-6 py-4 text-xs font-black uppercase tracking-[0.16em] text-slate-700">Evaluation</th>
                                <th class="px-6 py-4 text-xs font-black uppercase tracking-[0.16em] text-slate-700">Supervisor</th>
                                <th class="px-6 py-4 text-xs font-black uppercase tracking-[0.16em] text-slate-700">Submitted</th>
                                <th class="px-6 py-4 text-xs font-black uppercase tracking-[0.16em] text-slate-700">Score</th>
                                <th class="px-6 py-4 text-xs font-black uppercase tracking-[0.16em] text-slate-700">Status</th>
                                <th class="px-6 py-4 text-xs font-black uppercase tracking-[0.16em] text-slate-700 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200">
                            @foreach($evaluations as $evaluation)
                                @php
                                    $isCompleted = $evaluation->submitted_at !== null;
                                    $statusLabel = $isCompleted ? 'Completed' : 'Pending';
                                @endphp
                                <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="px-6 py-4 align-top">
                                        <div class="font-black text-slate-950">Student Performance Evaluation</div>
                                        <div class="mt-1 text-sm font-semibold text-slate-700">{{ $evaluation->semester ?: 'General Evaluation' }}</div>
                                        <div class="mt-1 text-xs font-medium text-slate-600">
                                            Evaluation date: {{ $evaluation->evaluation_date?->format('M d, Y') ?? 'Not set' }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 align-top font-semibold text-slate-800">
                                        {{ $evaluation->supervisor?->name ?? ($assignment?->supervisor?->name ?? 'Supervisor not assigned') }}
                                    </td>
                                    <td class="px-6 py-4 align-top text-slate-700">
                                        {{ $evaluation->submitted_at?->format('M d, Y h:i A') ?? 'Not yet submitted' }}
                                    </td>
                                    <td class="px-6 py-4 align-top">
                                        <span class="inline-flex min-w-[7rem] justify-center rounded-xl border border-slate-300 bg-slate-50 px-3 py-2 text-sm font-black text-slate-950">
                                            {{ (float) ($evaluation->final_rating ?? 0) > 0 ? number_format((float) $evaluation->final_rating, 2).'/5' : 'Awaiting score' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 align-top">
                                        <x-status-badge
                                            :status="$isCompleted ? 'approved' : 'pending'"
                                            :label="$statusLabel"
                                            size="sm"
                                            :class="$isCompleted ? '!border-emerald-700 !bg-emerald-100 !text-emerald-950 shadow-sm' : '!border-amber-700 !bg-amber-100 !text-amber-950 shadow-sm'"
                                        />
                                    </td>
                                    <td class="px-6 py-4 align-top text-right">
                                        <div class="flex flex-wrap justify-end gap-2">
                                            <a href="{{ route('student.evaluations.show', $evaluation) }}" class="inline-flex items-center rounded-lg border border-indigo-700 bg-indigo-700 px-3 py-2 text-xs font-black uppercase tracking-[0.14em] text-white shadow-sm transition-colors hover:bg-indigo-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                                                View Details
                                            </a>

                                            @if($isCompleted)
                                                <a href="{{ route('student.evaluations.download', $evaluation) }}" class="inline-flex items-center rounded-lg border border-slate-800 bg-slate-800 px-3 py-2 text-xs font-black uppercase tracking-[0.14em] text-white shadow-sm transition-colors hover:bg-black focus:outline-none focus:ring-2 focus:ring-slate-500 focus:ring-offset-2">
                                                    Download
                                                </a>
                                                <a href="{{ route('student.evaluations.print', $evaluation) }}" target="_blank" class="inline-flex items-center rounded-lg border border-emerald-700 bg-emerald-700 px-3 py-2 text-xs font-black uppercase tracking-[0.14em] text-white shadow-sm transition-colors hover:bg-emerald-800 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2">
                                                    Print
                                                </a>
                                            @else
                                                <span class="inline-flex items-center rounded-lg border border-slate-300 bg-slate-100 px-3 py-2 text-xs font-black uppercase tracking-[0.14em] text-slate-600">
                                                    Not yet available
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="border-t border-slate-200 px-6 py-4">
                    {{ $evaluations->links() }}
                </div>
            @endif
        </div>
    </div>
</x-student-layout>
