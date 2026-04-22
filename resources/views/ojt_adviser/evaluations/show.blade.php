<x-ojt-adviser-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-bold text-white">Evaluation</h2>
                <p class="text-gray-400 text-xs">{{ $student->name }} · {{ $assignment->company?->name ?? 'N/A' }}</p>
            </div>
            <a href="{{ route('ojt_adviser.evaluations') }}" class="text-indigo-300 hover:text-indigo-200 text-sm font-bold uppercase tracking-wider transition">Back</a>
        </div>
    </x-slot>

    <div class="space-y-6">
        <div class="bg-white/5 border border-white/10 rounded-2xl p-6 shadow-xl backdrop-blur-md">
            <div class="flex items-center justify-between gap-4">
                <div class="min-w-0">
                    <h3 class="text-lg font-bold text-white truncate">{{ $student->name }}</h3>
                    <p class="text-sm text-gray-400 truncate">
                        Supervisor: {{ $assignment->supervisor?->name ?? 'N/A' }} · Company: {{ $assignment->company?->name ?? 'N/A' }}
                    </p>
                </div>

                @if ($latestEval)
                    <div class="flex items-center gap-3 flex-shrink-0">
                        @if((float) ($latestEval->final_rating ?? 0) > 0)
                            <span class="px-3 py-1.5 rounded-xl bg-indigo-500/20 text-indigo-300 font-black text-sm">
                                {{ number_format((float) ($latestEval->final_rating ?? 0), 1) }}
                            </span>
                        @else
                            <span class="px-3 py-1.5 rounded-xl bg-blue-500/20 text-blue-300 font-black text-[11px] uppercase tracking-wide">
                                Template File
                            </span>
                        @endif
                        <span class="text-[10px] text-gray-400 uppercase font-bold tracking-wider">
                            {{ $latestEval->semester ?? '—' }}
                        </span>
                    </div>
                @endif
            </div>

            @if (! $latestEval)
                <div class="mt-6 p-4 bg-black/30 border border-white/10 rounded-xl text-sm text-gray-300">
                    No supervisor evaluation is available yet for this student.
                </div>
            @else
                <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="p-4 bg-black/30 border border-white/10 rounded-xl">
                        <p class="text-xs text-gray-400 uppercase tracking-wider font-bold">Evaluation Date</p>
                        <p class="text-sm text-white font-semibold mt-1">{{ optional($latestEval->evaluation_date)->format('M d, Y') ?? '—' }}</p>
                    </div>
                    <div class="p-4 bg-black/30 border border-white/10 rounded-xl">
                        <p class="text-xs text-gray-400 uppercase tracking-wider font-bold">Export</p>
                        <div class="mt-2">
                            @if ($latestEval->submitted_at)
                                <a href="{{ route('ojt_adviser.evaluations.export', $latestEval) }}" class="inline-flex items-center gap-2 px-3 py-2 rounded-xl bg-indigo-600/20 text-indigo-300 text-sm font-bold hover:bg-indigo-600 hover:text-white transition-all">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a2 2 0 002 2h12a2 2 0 002-2v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                    </svg>
                                    Export Latest
                                </a>
                            @else
                                <span class="text-xs text-gray-400 italic">Not submitted yet</span>
                            @endif
                        </div>
                        <p class="text-xs text-gray-500 mt-2">Exports the stored evaluation document when available.</p>
                    </div>
                </div>

                <div class="mt-6 bg-white/5 border border-white/10 rounded-2xl overflow-hidden">
                    <div class="px-6 py-4 border-b border-white/10">
                        <h4 class="text-sm font-bold text-white">All Evaluations</h4>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm">
                            <thead class="bg-black/30">
                                <tr>
                                    <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Date</th>
                                    <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Semester</th>
                                    <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Rating</th>
                                    <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider text-right">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-white/5">
                                @foreach ($evaluations as $ev)
                                    <tr class="hover:bg-white/5 transition-colors">
                                        <td class="px-6 py-4 text-gray-200">{{ optional($ev->evaluation_date)->format('M d, Y') ?? '—' }}</td>
                                        <td class="px-6 py-4 text-gray-400">{{ $ev->semester ?? '—' }}</td>
                                        <td class="px-6 py-4 text-indigo-300 font-bold">
                                            @if((float) ($ev->final_rating ?? 0) > 0)
                                                {{ number_format((float) ($ev->final_rating ?? 0), 1) }}
                                            @else
                                                <span class="text-blue-300 text-xs uppercase tracking-wide">Template File</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            @if ($ev->submitted_at)
                                                <a href="{{ route('ojt_adviser.evaluations.export', $ev) }}" class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl bg-indigo-600/20 text-indigo-300 text-xs font-bold hover:bg-indigo-600 hover:text-white transition-all">
                                                    Export
                                                </a>
                                            @else
                                                <span class="text-xs text-gray-400 italic">—</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>

        <div class="flex flex-wrap gap-3">
            <a href="{{ route('ojt_adviser.student-logs', $student) }}" class="px-4 py-2 rounded-xl bg-white/5 border border-white/10 text-gray-200 hover:bg-white/10 transition font-semibold">
                View Logs
            </a>
            <a href="{{ route('ojt_adviser.student-journals', $student) }}" class="px-4 py-2 rounded-xl bg-white/5 border border-white/10 text-gray-200 hover:bg-white/10 transition font-semibold">
                View Journals
            </a>
        </div>
    </div>
</x-ojt-adviser-layout>
