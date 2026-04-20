<x-supervisor-layout>
    <x-slot name="header">
        Student Performance Evaluations
    </x-slot>

    <div class="space-y-6 max-w-6xl mx-auto" x-data="{ q: @js($q ?? ''), submit(){ const url = new URL(window.location.href); if(this.q){ url.searchParams.set('q', this.q);} else { url.searchParams.delete('q'); } window.location = url.toString(); } }">
        <div class="bg-black/30 rounded-xl p-4">
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
                    <label class="text-xs uppercase text-white font-bold">Semester</label>
                    <select x-model="period"
                            class="mt-1 w-full rounded-md border-gray-300 bg-white text-gray-900 font-semibold focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">All Periods</option>
                        @foreach(['1st Semester', '2nd Semester', 'Summer'] as $s)
                            <option value="{{ $s }}">{{ $s }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="md:col-span-3" x-show="period" x-transition>
                    <label class="text-xs uppercase text-white font-bold">Type</label>
                    <select x-model="freq" @change="$nextTick(() => $el.form.submit())"
                            class="mt-1 w-full rounded-md border-gray-300 bg-white text-gray-900 font-semibold focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">All Types</option>
                        <option value="Weekly">Weekly</option>
                        <option value="Monthly">Monthly</option>
                        <option value="Final">Final</option>
                    </select>
                </div>
                <input type="hidden" name="semester" :value="!period ? '' : (!freq ? period : (freq === 'Final' ? period : (period + ' (' + freq + ')')))">
                <div :class="period ? 'md:col-span-4' : 'md:col-span-7'">
                    <label class="text-xs uppercase text-white font-bold">Search</label>
                    <input x-model="q" @input.debounce.150ms="submit()" list="students-list"
                           class="mt-1 w-full rounded-md border-gray-300 bg-white text-gray-900 font-semibold placeholder-gray-500 focus:ring-indigo-500 focus:border-indigo-500"
                           name="q" placeholder="Type an OJT student name..." />
                    <datalist id="students-list">
                        @foreach($students as $s)
                            <option value="{{ $s->name }}"></option>
                        @endforeach
                    </datalist>
                </div>
                <div class="md:col-span-2 flex items-center gap-3">
                    <button class="w-full h-[42px] mt-6 px-5 py-2 rounded bg-indigo-600 text-white text-xs font-bold hover:bg-indigo-700">Apply</button>
                    <a href="{{ route('supervisor.evaluations.create') }}" class="w-full h-[42px] mt-6 inline-flex items-center justify-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition">
                        + New
                    </a>
                </div>
            </form>
        </div>

        @if (session('status'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">{{ session('status') }}</span>
            </div>
        @endif

        <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg w-full">
            @if($students->isEmpty())
                <div class="text-center text-gray-500 py-10">No OJT students found.</div>
            @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-900/40">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Name</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Evaluations</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Latest Score</th>
                            <th class="px-6 py-3 text-right text-xs font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Action</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($students as $s)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-3">
                                    <div class="h-8 w-8 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 text-xs font-bold">
                                        {{ strtoupper(substr($s->name,0,2)) }}
                                    </div>
                                    <div class="font-semibold text-gray-900 dark:text-gray-100">{{ $s->name }}</div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-gray-700 dark:text-gray-300">
                                {{ $s->evaluations_count }}{{ $semester ? ' • '.$semester : '' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($s->latest_evaluation)
                                <span class="px-2 py-0.5 text-xs rounded-full {{ $s->latest_evaluation->final_rating >= 3 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ number_format($s->latest_evaluation->final_rating,2) }}/5
                                </span>
                                @else
                                <span class="text-xs text-gray-400">—</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <a href="{{ route('supervisor.evaluations.student', $s) }}@if($semester)&semester={{ urlencode($semester) }}@endif"
                                   class="inline-flex items-center px-3 py-1.5 rounded bg-indigo-600 text-white text-xs font-bold hover:bg-indigo-700">
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
