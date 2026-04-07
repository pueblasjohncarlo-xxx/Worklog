<x-ojt-adviser-layout>
    <x-slot name="header">
        Performance Evaluations Review
    </x-slot>

    <div class="space-y-6">
        <div class="bg-white/5 border border-white/10 rounded-2xl overflow-hidden shadow-xl backdrop-blur-md">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-black/30">
                        <tr>
                            <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Student</th>
                            <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Supervisor Evaluation</th>
                            <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Academic Assessment</th>
                            <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        @forelse($assignments as $assignment)
                            @php 
                                $evaluations = \App\Models\PerformanceEvaluation::where('student_id', $assignment->student_id)->get();
                                $latestEval = $evaluations->sortByDesc('evaluation_date')->first();
                            @endphp
                            <tr class="hover:bg-white/5 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex flex-col">
                                        <span class="text-sm font-bold text-gray-200">{{ $assignment->student->name }}</span>
                                        <span class="text-xs text-gray-500">{{ $assignment->company->name }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    @if($latestEval)
                                        <div class="flex items-center gap-2">
                                            <span class="px-2 py-1 rounded bg-indigo-500/20 text-indigo-400 font-black text-xs">{{ number_format($latestEval->final_rating, 1) }}</span>
                                            <span class="text-[10px] text-gray-500 uppercase font-bold tracking-tighter">{{ $latestEval->semester }}</span>
                                        </div>
                                    @else
                                        <span class="text-xs text-gray-600 italic">No evaluation yet</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right">
                                    @if($latestEval)
                                        <a href="{{ route('coordinator.evaluations.export', $latestEval) }}" class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-indigo-600/20 text-indigo-400 text-xs font-bold hover:bg-indigo-600 hover:text-white transition-all">
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
                                <td colspan="4" class="px-6 py-12 text-center text-gray-500 italic">No assigned OJT students found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-ojt-adviser-layout>
