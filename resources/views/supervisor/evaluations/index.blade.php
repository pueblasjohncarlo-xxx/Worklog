<x-supervisor-layout>
    <x-slot name="header">
        Student Performance Evaluations
    </x-slot>

    <div class="space-y-6 max-w-6xl mx-auto supervisor-evaluations-shell" x-data="{ q: @js($q ?? ''), submit(){ const url = new URL(window.location.href); if(this.q){ url.searchParams.set('q', this.q);} else { url.searchParams.delete('q'); } window.location = url.toString(); } }">
        <style>
            .supervisor-evaluations-shell select,
            .supervisor-evaluations-shell option,
            .supervisor-evaluations-shell optgroup {
                color: #0f172a;
                background-color: #ffffff;
                font-weight: 600;
            }

            .supervisor-evaluations-shell input::placeholder {
                color: #64748b;
                opacity: 1;
            }
        </style>

        <div class="bg-white/95 dark:bg-gray-800 rounded-xl p-5 border border-white/20 dark:border-gray-700 shadow-lg">
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
            <form method="GET" action="{{ route('supervisor.evaluations.index') }}" class="grid grid-cols-1 md:grid-cols-12 gap-3 items-end" x-data="{
                period: @js($selectedPeriod),
                freq: @js($selectedFreq)
            }">
                <div class="md:col-span-3">
                    <label class="text-xs uppercase text-slate-700 dark:text-slate-200 font-black tracking-[0.16em]">Semester</label>
                    <select x-model="period"
                            class="mt-1 w-full rounded-md border-slate-300 bg-white text-slate-900 font-bold focus:ring-indigo-500 focus:border-indigo-500 dark:border-slate-600 dark:bg-slate-900 dark:text-slate-100">
                        <option value="">All Periods</option>
                        @foreach(['1st Semester', '2nd Semester', 'Summer'] as $s)
                            <option value="{{ $s }}">{{ $s }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="md:col-span-3" x-show="period" x-transition>
                    <label class="text-xs uppercase text-slate-700 dark:text-slate-200 font-black tracking-[0.16em]">Type</label>
                    <select x-model="freq" @change="$nextTick(() => $el.form.submit())"
                            class="mt-1 w-full rounded-md border-slate-300 bg-white text-slate-900 font-bold focus:ring-indigo-500 focus:border-indigo-500 dark:border-slate-600 dark:bg-slate-900 dark:text-slate-100">
                        <option value="">All Types</option>
                        <option value="Weekly">Weekly</option>
                        <option value="Monthly">Monthly</option>
                        <option value="Final">Final</option>
                    </select>
                </div>
                <input type="hidden" name="semester" :value="!period ? '' : (!freq ? period : (freq === 'Final' ? period : (period + ' (' + freq + ')')))">
                <div :class="period ? 'md:col-span-4' : 'md:col-span-7'">
                    <label class="text-xs uppercase text-slate-700 dark:text-slate-200 font-black tracking-[0.16em]">Search</label>
                    <input x-model="q" @input.debounce.150ms="submit()" list="students-list"
                           class="mt-1 w-full rounded-md border-slate-300 bg-white text-slate-900 font-bold placeholder:text-slate-500 focus:ring-indigo-500 focus:border-indigo-500 dark:border-slate-600 dark:bg-slate-900 dark:text-slate-100"
                           name="q" placeholder="Type an OJT student name..." />
                    <datalist id="students-list">
                        @foreach($students as $s)
                            <option value="{{ $s->name }}"></option>
                        @endforeach
                    </datalist>
                </div>
                <div class="md:col-span-2 flex items-center gap-3">
                    <button class="w-full h-[42px] mt-6 px-5 py-2 rounded-lg bg-slate-900 text-white text-xs font-black uppercase tracking-[0.16em] hover:bg-black focus:outline-none focus:ring-2 focus:ring-indigo-500">Apply</button>
                    <a href="{{ route('supervisor.evaluations.create') }}" class="w-full h-[42px] mt-6 inline-flex items-center justify-center px-4 py-2 bg-indigo-700 border border-transparent rounded-lg font-black text-xs text-white uppercase tracking-[0.16em] hover:bg-indigo-800 focus:bg-indigo-800 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition">
                        + New
                    </a>
                </div>
            </form>
            <p class="mt-3 text-sm text-slate-700 dark:text-slate-300 font-semibold">
                Template workflow: click <strong>+ New</strong>, download the official evaluation template, fill it externally, upload, review, and submit.
            </p>
        </div>

        @if (session('status'))
            <div class="bg-green-100 border border-green-400 text-green-900 px-4 py-3 rounded-lg relative font-semibold" role="alert">
                <span class="block sm:inline">{{ session('status') }}</span>
            </div>
        @endif

        <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg w-full">
            @if($students->isEmpty())
                <div class="text-center text-slate-600 dark:text-slate-300 font-semibold py-10">No OJT students found.</div>
            @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-900/40">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-black text-slate-700 dark:text-slate-200 uppercase tracking-[0.16em]">Name</th>
                            <th class="px-6 py-3 text-left text-xs font-black text-slate-700 dark:text-slate-200 uppercase tracking-[0.16em]">Evaluations</th>
                            <th class="px-6 py-3 text-left text-xs font-black text-slate-700 dark:text-slate-200 uppercase tracking-[0.16em]">Latest Score</th>
                            <th class="px-6 py-3 text-right text-xs font-black text-slate-700 dark:text-slate-200 uppercase tracking-[0.16em]">Action</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($students as $s)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-3">
                                    <div class="h-8 w-8 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-700 text-xs font-black">
                                        {{ strtoupper(substr($s->name,0,2)) }}
                                    </div>
                                    <div class="font-bold text-slate-950 dark:text-slate-100">{{ $s->name }}</div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-slate-700 dark:text-slate-300 font-semibold">
                                {{ $s->evaluations_count }}{{ $semester ? ' - '.$semester : '' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($s->latest_evaluation)
                                    @if((float) $s->latest_evaluation->final_rating > 0)
                                        <span class="px-3 py-1 text-xs rounded-full font-black {{ $s->latest_evaluation->final_rating >= 3 ? 'bg-emerald-100 text-emerald-900 ring-1 ring-emerald-200' : 'bg-rose-100 text-rose-900 ring-1 ring-rose-200' }}">
                                            {{ number_format($s->latest_evaluation->final_rating,2) }}/5
                                        </span>
                                    @else
                                        <span class="px-3 py-1 text-xs rounded-full bg-sky-100 text-sky-900 ring-1 ring-sky-200 font-black">
                                            Template File
                                        </span>
                                    @endif
                                @else
                                    <span class="text-xs font-bold text-slate-500 dark:text-slate-400">None</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <a href="{{ route('supervisor.evaluations.student', $s) }}@if($semester)&semester={{ urlencode($semester) }}@endif"
                                   class="inline-flex items-center px-4 py-2 rounded-lg bg-indigo-700 text-white text-xs font-black uppercase tracking-[0.14em] hover:bg-indigo-800 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                   View Evaluations
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>
    </div>
</x-supervisor-layout>
