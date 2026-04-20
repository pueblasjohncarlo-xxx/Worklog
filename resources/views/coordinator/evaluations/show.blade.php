<x-coordinator-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('coordinator.evaluations.index') }}" class="p-2 rounded-full bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-200 transition-colors">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            <div>
                <h2 class="text-xl font-bold text-gray-900 dark:text-gray-100 leading-tight">
                    Evaluations by {{ $supervisor->name }}
                </h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">Viewing OJT students assigned to this supervisor</p>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">
        <!-- Filters -->
        <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-6">
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
            <form class="flex flex-wrap items-end gap-4" x-data="{ 
                period: @js($selectedPeriod),
                freq: @js($selectedFreq)
            }">
                <div>
                    <label class="text-xs font-bold uppercase text-gray-500 mb-1 block">Time Range</label>
                    <select name="range" onchange="this.form.submit()" class="rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                        @foreach(['daily'=>'Today','weekly'=>'This Week','monthly'=>'This Month','all'=>'All Time'] as $k=>$label)
                            <option value="{{ $k }}" {{ $range===$k ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="text-xs font-bold uppercase text-gray-500 mb-1 block">Semester</label>
                    <select x-model="period" class="rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">All Periods</option>
                        @foreach(['1st Semester', '2nd Semester', 'Summer'] as $s)
                            <option value="{{ $s }}">{{ $s }}</option>
                        @endforeach
                    </select>
                </div>

                <div x-show="period" x-transition>
                    <label class="text-xs font-bold uppercase text-gray-500 mb-1 block">Frequency</label>
                    <select x-model="freq" @change="$nextTick(() => $el.form.submit())" class="rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">All Types</option>
                        <option value="Weekly">Weekly</option>
                        <option value="Monthly">Monthly</option>
                        <option value="Final">Final</option>
                    </select>
                </div>

                <input type="hidden" name="semester" :value="!period ? '' : (!freq ? period : (freq === 'Final' ? period : (period + ' (' + freq + ')')))">
            </form>
        </div>

        <!-- Student List (Grouped Display) -->
        <div class="space-y-4">
            @forelse($students as $student)
                <div x-data="{ open: false }" class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <!-- Student Header -->
                    <button @click="open = !open" class="w-full px-6 py-4 flex flex-col sm:flex-row sm:items-center justify-between gap-4 hover:bg-gray-50 dark:hover:bg-gray-900/50 transition-colors">
                        <div class="flex items-center gap-4 text-left">
                            <div class="h-12 w-12 rounded-full bg-indigo-100 dark:bg-indigo-900 flex items-center justify-center text-indigo-600 dark:text-indigo-300 font-bold text-lg">
                                {{ substr($student->name, 0, 1) }}
                            </div>
                            <div>
                                <h4 class="text-lg font-bold text-gray-900 dark:text-gray-100">{{ $student->name }}</h4>
                                <p class="text-sm text-gray-500 dark:text-gray-400">{{ $student->assignment->company->name }} • {{ $student->department ?? 'N/A' }}</p>
                            </div>
                        </div>
                        
                        <div class="flex items-center gap-6">
                            <div class="text-right hidden sm:block">
                                <span class="block text-xs font-bold text-gray-400 uppercase tracking-wider">Evaluations</span>
                                <span class="text-lg font-black text-indigo-600 dark:text-indigo-400">{{ $student->evaluations->count() }}</span>
                            </div>
                            <div class="p-2 rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-500 transition-transform duration-200" :class="{'rotate-180': open}">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                        </div>
                    </button>

                    <!-- Evaluations List -->
                    <div x-show="open" x-transition class="border-t border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-900/30">
                        <div class="p-6">
                            @if($student->evaluations->count() > 0)
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                    @foreach($student->evaluations as $e)
                                        <div class="bg-white dark:bg-gray-800 p-4 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-md transition-shadow">
                                            <div class="flex justify-between items-start mb-3">
                                                <div>
                                                    <span class="text-xs font-bold text-indigo-600 dark:text-indigo-400 uppercase tracking-tighter">{{ $e->semester }}</span>
                                                    <h5 class="font-bold text-gray-900 dark:text-gray-100">{{ $e->evaluation_date->format('M d, Y') }}</h5>
                                                    <div class="text-[10px] text-gray-400 mt-0.5">
                                                        Sent: {{ $e->submitted_at ? $e->submitted_at->format('M d, Y h:i A') : 'N/A' }}
                                                    </div>
                                                </div>
                                                <div class="px-2 py-1 bg-emerald-100 dark:bg-emerald-900/50 rounded text-emerald-700 dark:text-emerald-300 font-black text-sm">
                                                    {{ number_format($e->final_rating, 1) }}
                                                </div>
                                            </div>
                                            <p class="text-sm text-gray-600 dark:text-gray-400 line-clamp-2 mb-4 italic">
                                                "{{ $e->remarks ?? 'No remarks provided.' }}"
                                            </p>
                                            <a href="{{ route('coordinator.evaluations.export', $e) }}" class="w-full inline-flex justify-center items-center gap-2 px-4 py-2 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold transition-colors">
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a2 2 0 002 2h12a2 2 0 002-2v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                                </svg>
                                                Download Report
                                            </a>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-8">
                                    <p class="text-gray-500 italic">No submitted evaluations found for this student in the selected range.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-12 bg-white dark:bg-gray-800 rounded-xl border-2 border-dashed border-gray-200 dark:border-gray-700">
                    <p class="text-gray-500 font-medium">No OJT students assigned to this supervisor were found.</p>
                </div>
            @endforelse
        </div>
    </div>
</x-coordinator-layout>
