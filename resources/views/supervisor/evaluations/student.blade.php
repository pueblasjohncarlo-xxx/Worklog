<x-supervisor-layout>
    <x-slot name="header">
        Evaluations • {{ $student->name }}
    </x-slot>

    <div class="space-y-6">
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
                    <label class="text-xs uppercase text-gray-500">Semester</label>
                    <select x-model="period" class="mt-1 rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
                        <option value="">All Periods</option>
                        @foreach(['1st Semester', '2nd Semester', 'Summer'] as $s)
                            <option value="{{ $s }}">{{ $s }}</option>
                        @endforeach
                    </select>
                </div>
                <div x-show="period" x-transition>
                    <label class="text-xs uppercase text-gray-500">Type</label>
                    <select x-model="freq" class="mt-1 rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
                        <option value="">All Types</option>
                        <option value="Weekly">Weekly</option>
                        <option value="Monthly">Monthly</option>
                        <option value="Final">Final</option>
                    </select>
                </div>
                <input type="hidden" name="semester" :value="!period ? '' : (!freq ? period : (freq === 'Final' ? period : (period + ' (' + freq + ')')))">
                <div class="ml-auto">
                    <button class="px-4 py-2 rounded bg-indigo-600 text-white text-sm font-bold hover:bg-indigo-700">Apply</button>
                </div>
                <div>
                    <a href="{{ route('supervisor.evaluations.create', ['student_id' => $student->id]) }}" class="inline-flex items-center px-4 py-2 rounded bg-indigo-600 text-white text-sm font-extrabold uppercase tracking-wider hover:bg-indigo-700">
                        + New Evaluation
                    </a>
                </div>
            </form>
        </div>

        <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg">
            <ul class="divide-y divide-gray-200 dark:divide-gray-700">
                @forelse($evaluations as $e)
                <li class="p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="text-sm text-gray-500">Evaluation Date</div>
                            <div class="font-semibold text-gray-900 dark:text-gray-100">{{ $e->evaluation_date->format('M d, Y') }}</div>
                            <div class="text-xs text-gray-500 mt-1">{{ $e->semester ?? '—' }}</div>
                            <div class="text-[11px] text-gray-500 mt-1">Submitted: {{ $e->submitted_at ? $e->submitted_at->format('M d, Y h:i A') : 'No' }}</div>
                        </div>
                        <div>
                            @if((float) $e->final_rating > 0)
                                <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full {{ $e->final_rating >= 4.0 ? 'bg-green-100 text-green-800' : ($e->final_rating >= 3.0 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                    {{ number_format($e->final_rating,2) }} / 5.0
                                </span>
                            @else
                                <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                    Template File
                                </span>
                            @endif
                        </div>
                    </div>
                    @if((float) $e->final_rating > 0)
                        <div class="mt-4 grid grid-cols-2 gap-4 text-xs text-gray-600 dark:text-gray-300 sm:grid-cols-3">
                            <div><span class="font-bold">Attendance:</span> {{ $e->attendance_punctuality }}/5</div>
                            <div><span class="font-bold">Quality:</span> {{ $e->quality_of_work }}/5</div>
                            <div><span class="font-bold">Initiative:</span> {{ $e->initiative }}/5</div>
                            <div><span class="font-bold">Cooperation:</span> {{ $e->cooperation }}/5</div>
                            <div><span class="font-bold">Dependability:</span> {{ $e->dependability }}/5</div>
                            <div><span class="font-bold">Communication:</span> {{ $e->communication_skills }}/5</div>
                        </div>
                    @endif
                    @if($e->remarks)
                        <div class="mt-3 text-sm text-gray-700 dark:text-gray-200 bg-gray-50 dark:bg-gray-700/40 p-3 rounded">
                            "{{ $e->remarks }}"
                        </div>
                    @endif
                    <div class="mt-4">
                        <a href="{{ route('supervisor.evaluations.export', $e) }}"
                           class="inline-flex items-center px-3 py-1.5 rounded-md text-sm font-semibold bg-indigo-600 text-white hover:bg-indigo-700">
                            Download Submitted File
                        </a>
                    </div>
                </li>
                @empty
                <li class="px-6 py-10 text-center text-gray-500">No evaluations found.</li>
                @endforelse
            </ul>
            <div class="p-4">
                {{ $evaluations->links() }}
            </div>
        </div>
    </div>
</x-supervisor-layout>

