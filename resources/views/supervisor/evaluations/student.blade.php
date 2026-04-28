<x-supervisor-layout>
    <x-slot name="header">
        Evaluations - {{ $student->name }}
    </x-slot>

    <div class="space-y-6 supervisor-evaluation-student-shell">
        <style>
            .supervisor-evaluation-student-shell select,
            .supervisor-evaluation-student-shell option,
            .supervisor-evaluation-student-shell optgroup {
                color: #0f172a;
                background-color: #ffffff;
                font-weight: 600;
            }
        </style>

        <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-4">
            @php
                $selectedPeriod = '';
                $selectedFreq = '';
                if ($semester) {
                    if (strpos($semester, ' (') !== false) {
                        $parts = explode(' (', rtrim($semester, ')'));
                        $selectedPeriod = $parts[0];
                        $selectedFreq = $parts[1];
                    } else {
                        $selectedPeriod = $semester;
                        $selectedFreq = 'Final';
                    }
                }
            @endphp
            <form class="flex flex-wrap items-end gap-3" x-data="{
                period: @js($selectedPeriod),
                freq: @js($selectedFreq)
            }">
                <div>
                    <label class="text-xs uppercase text-slate-600 dark:text-slate-300 font-black tracking-[0.16em]">Semester</label>
                    <select x-model="period" class="mt-1 rounded-md border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100 text-slate-900 font-bold">
                        <option value="">All Periods</option>
                        @foreach(['1st Semester', '2nd Semester', 'Summer'] as $s)
                            <option value="{{ $s }}">{{ $s }}</option>
                        @endforeach
                    </select>
                </div>
                <div x-show="period" x-transition>
                    <label class="text-xs uppercase text-slate-600 dark:text-slate-300 font-black tracking-[0.16em]">Type</label>
                    <select x-model="freq" class="mt-1 rounded-md border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100 text-slate-900 font-bold">
                        <option value="">All Types</option>
                        <option value="Weekly">Weekly</option>
                        <option value="Monthly">Monthly</option>
                        <option value="Final">Final</option>
                    </select>
                </div>
                <input type="hidden" name="semester" :value="!period ? '' : (!freq ? period : (freq === 'Final' ? period : (period + ' (' + freq + ')')))">
                <div class="ml-auto">
                    <button class="px-4 py-2 rounded-lg bg-slate-900 text-white text-sm font-black uppercase tracking-[0.14em] hover:bg-black focus:outline-none focus:ring-2 focus:ring-indigo-500">Apply</button>
                </div>
                <div>
                    <a href="{{ route('supervisor.evaluations.create', ['student_id' => $student->id]) }}" class="inline-flex items-center px-4 py-2 rounded-lg bg-indigo-700 text-white text-sm font-black uppercase tracking-[0.14em] hover:bg-indigo-800 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        + New Evaluation
                    </a>
                </div>
            </form>
        </div>

        <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg">
            <ul class="divide-y divide-gray-200 dark:divide-gray-700">
                @forelse($evaluations as $e)
                <li class="p-6">
                    <div class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
                        <div class="space-y-2">
                            <div>
                                <div class="text-xs font-black uppercase tracking-[0.16em] text-slate-600 dark:text-slate-300">Evaluation Date</div>
                                <div class="font-bold text-slate-950 dark:text-slate-100">{{ $e->evaluation_date->format('M d, Y') }}</div>
                            </div>
                            <div class="text-sm font-semibold text-slate-700 dark:text-slate-300">{{ $e->semester ?: 'No period recorded' }}</div>
                            <div class="text-xs font-semibold text-slate-600 dark:text-slate-300">Submitted: {{ $e->submitted_at ? $e->submitted_at->format('M d, Y h:i A') : 'Not submitted' }}</div>
                        </div>
                        <div>
                            @if((float) $e->final_rating > 0)
                                <span class="px-3 py-1 inline-flex text-sm leading-5 font-black rounded-full {{ $e->final_rating >= 4.0 ? 'bg-emerald-100 text-emerald-900 ring-1 ring-emerald-200' : ($e->final_rating >= 3.0 ? 'bg-amber-100 text-amber-900 ring-1 ring-amber-200' : 'bg-rose-100 text-rose-900 ring-1 ring-rose-200') }}">
                                    {{ number_format($e->final_rating,2) }} / 5.0
                                </span>
                            @else
                                <span class="px-3 py-1 inline-flex text-sm leading-5 font-black rounded-full bg-sky-100 text-sky-900 ring-1 ring-sky-200">
                                    Template File
                                </span>
                            @endif
                        </div>
                    </div>
                    @if((float) $e->final_rating > 0)
                        <div class="mt-4 grid grid-cols-1 gap-3 text-sm text-slate-800 dark:text-slate-200 sm:grid-cols-2 xl:grid-cols-3">
                            <div class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 dark:border-slate-700 dark:bg-slate-900/40"><span class="font-black text-slate-900 dark:text-slate-100">Attendance:</span> {{ $e->attendance_punctuality }}/5</div>
                            <div class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 dark:border-slate-700 dark:bg-slate-900/40"><span class="font-black text-slate-900 dark:text-slate-100">Quality:</span> {{ $e->quality_of_work }}/5</div>
                            <div class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 dark:border-slate-700 dark:bg-slate-900/40"><span class="font-black text-slate-900 dark:text-slate-100">Initiative:</span> {{ $e->initiative }}/5</div>
                            <div class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 dark:border-slate-700 dark:bg-slate-900/40"><span class="font-black text-slate-900 dark:text-slate-100">Cooperation:</span> {{ $e->cooperation }}/5</div>
                            <div class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 dark:border-slate-700 dark:bg-slate-900/40"><span class="font-black text-slate-900 dark:text-slate-100">Dependability:</span> {{ $e->dependability }}/5</div>
                            <div class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 dark:border-slate-700 dark:bg-slate-900/40"><span class="font-black text-slate-900 dark:text-slate-100">Communication:</span> {{ $e->communication_skills }}/5</div>
                        </div>
                    @endif
                    @if($e->remarks)
                        <div class="mt-3 text-sm font-medium text-slate-800 dark:text-slate-200 bg-slate-50 dark:bg-slate-700/40 p-4 rounded-xl border border-slate-200 dark:border-slate-700">
                            "{{ $e->remarks }}"
                        </div>
                    @endif
                    <div class="mt-4">
                        <a href="{{ route('supervisor.evaluations.export', $e) }}"
                           class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-black uppercase tracking-[0.14em] bg-indigo-700 text-white hover:bg-indigo-800 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            Download Submitted File
                        </a>
                    </div>
                </li>
                @empty
                <li class="px-6 py-10 text-center text-slate-600 dark:text-slate-300 font-semibold">No evaluations found.</li>
                @endforelse
            </ul>
            <div class="p-4">
                {{ $evaluations->links() }}
            </div>
        </div>
    </div>
</x-supervisor-layout>
