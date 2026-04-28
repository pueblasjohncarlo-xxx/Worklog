<x-ojt-adviser-layout>
    <x-slot name="header">
        Assigned OJT Students Monitoring
    </x-slot>

    <div class="space-y-6">
        <div class="rounded-2xl border border-slate-200/80 bg-white shadow-2xl ring-1 ring-slate-200/70 overflow-hidden dark:border-slate-700/80 dark:bg-slate-900 dark:ring-slate-700/70">
            <div class="border-b border-slate-200 bg-slate-50/90 px-6 py-5 dark:border-slate-700 dark:bg-slate-800/90">
                <h2 class="text-lg font-black tracking-tight text-slate-900 dark:text-white">Assigned OJT Students Monitoring</h2>
                <p class="mt-1 text-sm font-medium text-slate-600 dark:text-slate-300">Review assigned students, current company placement, supervisor, progress, and quick actions with stronger contrast and clearer row separation.</p>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-slate-100 dark:bg-slate-800">
                        <tr>
                            <th class="px-6 py-4 text-xs font-black text-slate-700 dark:text-slate-200 uppercase tracking-[0.18em] border-b border-slate-200 dark:border-slate-700">Student</th>
                            <th class="px-6 py-4 text-xs font-black text-slate-700 dark:text-slate-200 uppercase tracking-[0.18em] border-b border-slate-200 dark:border-slate-700">Dept/Program</th>
                            <th class="px-6 py-4 text-xs font-black text-slate-700 dark:text-slate-200 uppercase tracking-[0.18em] border-b border-slate-200 dark:border-slate-700">Company</th>
                            <th class="px-6 py-4 text-xs font-black text-slate-700 dark:text-slate-200 uppercase tracking-[0.18em] border-b border-slate-200 dark:border-slate-700">Supervisor</th>
                            <th class="px-6 py-4 text-xs font-black text-slate-700 dark:text-slate-200 uppercase tracking-[0.18em] border-b border-slate-200 dark:border-slate-700">Progress</th>
                            <th class="px-6 py-4 text-xs font-black text-slate-700 dark:text-slate-200 uppercase tracking-[0.18em] border-b border-slate-200 dark:border-slate-700 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                        @forelse($assignments as $assignment)
                            <tr class="bg-white even:bg-slate-50/80 hover:bg-indigo-50/70 transition-colors dark:bg-slate-900 dark:even:bg-slate-800/70 dark:hover:bg-slate-800">
                                <td class="px-6 py-4 align-top">
                                    <div class="flex flex-col">
                                        <span class="text-sm font-black text-slate-900 dark:text-slate-100">{{ $assignment->student->name }}</span>
                                        <span class="mt-1 text-xs font-medium text-slate-600 dark:text-slate-300">{{ $assignment->student->email }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 align-top">
                                    <span class="inline-flex items-center rounded-full border border-sky-200 bg-sky-50 px-3 py-1 text-sm font-semibold text-sky-900 dark:border-sky-800/60 dark:bg-sky-900/20 dark:text-sky-100">
                                        {{ $assignment->student->studentProgramDisplay() }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 align-top">
                                    <span class="text-sm font-semibold text-slate-800 dark:text-slate-100">{{ $assignment->company->name }}</span>
                                </td>
                                <td class="px-6 py-4 align-top">
                                    <span class="text-sm font-semibold text-slate-800 dark:text-slate-100">{{ $assignment->supervisor->name ?? 'N/A' }}</span>
                                </td>
                                <td class="px-6 py-4 align-top">
                                    <div class="flex items-center gap-3">
                                        <div class="w-28 rounded-full bg-slate-200 h-2.5 overflow-hidden dark:bg-slate-700">
                                            <div class="h-2.5 rounded-full bg-indigo-600 dark:bg-indigo-400" style="width: <?php echo $assignment->progressPercentage(); ?>%;"></div>
                                        </div>
                                        <span class="text-xs font-black text-indigo-700 dark:text-indigo-300">{{ $assignment->progressPercentage() }}%</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 align-top text-right">
                                    <div class="flex justify-end gap-2">
                                        <a href="{{ route('ojt_adviser.student-logs', $assignment->student) }}" class="inline-flex items-center justify-center rounded-lg border border-indigo-200 bg-indigo-600 p-2 text-white shadow-sm transition-colors hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 active:bg-indigo-800 dark:border-indigo-500/60 dark:bg-indigo-500 dark:hover:bg-indigo-400 dark:active:bg-indigo-300" title="View Logs">
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        </a>
                                        <a href="{{ route('ojt_adviser.student-journals', $assignment->student) }}" class="inline-flex items-center justify-center rounded-lg border border-emerald-200 bg-emerald-600 p-2 text-white shadow-sm transition-colors hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 active:bg-emerald-800 dark:border-emerald-500/60 dark:bg-emerald-500 dark:hover:bg-emerald-400 dark:active:bg-emerald-300" title="View Journals">
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-sm font-semibold text-slate-600 dark:text-slate-300 italic bg-white dark:bg-slate-900">No assigned OJT students found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-ojt-adviser-layout>
