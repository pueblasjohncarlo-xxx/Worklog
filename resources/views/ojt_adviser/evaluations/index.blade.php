<x-ojt-adviser-layout>
    <x-slot name="header">
        Performance Evaluations Review
    </x-slot>

    <div class="space-y-6">
        <div class="rounded-2xl overflow-hidden border border-slate-200/80 bg-white shadow-xl ring-1 ring-slate-200/70 dark:border-slate-700/80 dark:bg-slate-900 dark:ring-slate-700/70">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-slate-100 dark:bg-slate-800">
                        <tr>
                            <th class="px-6 py-4 text-xs font-black text-slate-700 dark:text-slate-200 uppercase tracking-[0.18em] border-b border-slate-200 dark:border-slate-700">Student</th>
                            <th class="px-6 py-4 text-xs font-black text-slate-700 dark:text-slate-200 uppercase tracking-[0.18em] border-b border-slate-200 dark:border-slate-700">Supervisor Evaluation</th>
                            <th class="px-6 py-4 text-xs font-black text-slate-700 dark:text-slate-200 uppercase tracking-[0.18em] border-b border-slate-200 dark:border-slate-700">Academic Assessment</th>
                            <th class="px-6 py-4 text-xs font-black text-slate-700 dark:text-slate-200 uppercase tracking-[0.18em] border-b border-slate-200 dark:border-slate-700 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                        @forelse($assignments as $assignment)
                            @php
                                $latestEval = $latestEvaluations->get($assignment->student_id.'-'.$assignment->supervisor_id);
                            @endphp
                            <tr class="bg-white even:bg-slate-50/80 hover:bg-indigo-50/70 transition-colors dark:bg-slate-900 dark:even:bg-slate-800/70 dark:hover:bg-slate-800">
                                <td class="px-6 py-4">
                                    <div class="flex flex-col">
                                        <span class="text-sm font-black text-slate-900 dark:text-slate-100">{{ $assignment->student->name }}</span>
                                        <span class="mt-1 text-xs font-medium text-slate-600 dark:text-slate-300">{{ $assignment->company->name }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    @if($latestEval)
                                        <div class="flex items-center gap-2">
                                            <span class="px-2 py-1 rounded bg-indigo-100 text-indigo-800 dark:bg-indigo-500/20 dark:text-indigo-200 font-black text-xs">{{ number_format($latestEval->final_rating, 1) }}</span>
                                            <span class="text-[10px] text-slate-600 dark:text-slate-300 uppercase font-bold tracking-[0.12em]">{{ $latestEval->semester }}</span>
                                        </div>
                                    @else
                                        <span class="text-xs font-semibold text-slate-500 dark:text-slate-400 italic">No evaluation yet</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-xs font-semibold text-slate-600 dark:text-slate-300">No academic assessment record</span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    @if($latestEval)
                                        <a href="{{ route('ojt_adviser.evaluations.export', $latestEval) }}" class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-indigo-600 text-white text-xs font-bold shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 active:bg-indigo-800 transition-all">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a2 2 0 002 2h12a2 2 0 002-2v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                            </svg>
                                            Export
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center text-slate-500 dark:text-slate-400 italic bg-white dark:bg-slate-900">No assigned OJT students found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-ojt-adviser-layout>
