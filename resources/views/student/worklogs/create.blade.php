<x-student-layout>
    <x-slot name="header">
        New {{ ucfirst($type) }} Accomplishment Report
    </x-slot>

    <div class="space-y-6">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900 dark:text-gray-100 space-y-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-bold text-indigo-600 dark:text-indigo-400 uppercase tracking-widest">
                        {{ ucfirst($type) }} Report Form
                    </h3>
                    <div class="flex gap-2">
                        <a href="{{ route('student.worklogs.create', ['type' => 'daily']) }}" class="px-3 py-1 text-xs font-bold rounded-full {{ $type === 'daily' ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">Daily</a>
                        <a href="{{ route('student.worklogs.create', ['type' => 'weekly']) }}" class="px-3 py-1 text-xs font-bold rounded-full {{ $type === 'weekly' ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">Weekly</a>
                        <a href="{{ route('student.worklogs.create', ['type' => 'monthly']) }}" class="px-3 py-1 text-xs font-bold rounded-full {{ $type === 'monthly' ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">Monthly</a>
                    </div>
                </div>

                @if ($errors->any())
                    <div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400" role="alert">
                        {{ $errors->first() }}
                    </div>
                @endif

                <form method="POST" action="{{ route('student.worklogs.store') }}" class="space-y-4" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="type" value="{{ $type }}">

                    <div class="space-y-1">
                        <label for="work_date" class="block text-sm font-medium">
                            @if($type === 'daily') Date @elseif($type === 'weekly') Week Ending Date @else Month Ending Date @endif
                        </label>
                        @if($type === 'daily')
                            @php
                                $allowed = isset($approvedDates) ? collect($approvedDates)->map->toDateString() : collect();
                            @endphp
                            @if($allowed->isEmpty())
                                <div class="mt-1 px-4 py-3 rounded-md bg-yellow-50 text-yellow-700 text-sm">
                                    Walay available nga approved attendance dates para sa journal.
                                </div>
                            @else
                                <select
                                    id="work_date"
                                    name="work_date"
                                    required
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                >
                                    @foreach($allowed as $d)
                                        <option value="{{ $d }}" @selected(old('work_date', $date) === $d)>
                                            {{ \Carbon\Carbon::parse($d)->format('M d, Y') }}
                                        </option>
                                    @endforeach
                                </select>
                                @if($attendance)
                                    <p class="text-[10px] text-emerald-600 font-bold uppercase mt-1">
                                        Present: {{ \Carbon\Carbon::parse($attendance->time_in)->format('h:i A') }} - {{ $attendance->time_out ? \Carbon\Carbon::parse($attendance->time_out)->format('h:i A') : 'Ongoing' }}
                                    </p>
                                @endif
                            @endif
                        @else
                            <input
                                id="work_date"
                                name="work_date"
                                type="date"
                                value="{{ old('work_date', $date) }}"
                                required
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-500 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            >
                        @endif
                    </div>

                    @if($type === 'daily')
                    <div class="space-y-1">
                        <label for="hours" class="block text-sm font-medium">
                            Hours Rendered
                        </label>
                        <input
                            id="hours"
                            name="hours"
                            type="number"
                            step="0.25"
                            min="0"
                            max="24"
                            value="{{ old('hours', '8') }}"
                            required
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        >
                    </div>
                    @else
                        <input type="hidden" name="hours" value="0">
                    @endif

                    <div class="space-y-1">
                        <label for="description" class="block text-sm font-medium">
                            Task/Activity Description
                        </label>
                        <textarea
                            id="description"
                            name="description"
                            rows="4"
                            required
                            placeholder="Describe the tasks and activities you completed..."
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        >{{ old('description') }}</textarea>
                    </div>

                    <div class="space-y-1">
                        <label for="skills_applied" class="block text-sm font-medium">
                            Skills Applied/Learned
                        </label>
                        <textarea
                            id="skills_applied"
                            name="skills_applied"
                            rows="3"
                            placeholder="What technical or soft skills did you use or learn?"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        >{{ old('skills_applied') }}</textarea>
                    </div>

                    <div class="space-y-1">
                        <label for="reflection" class="block text-sm font-medium">
                            Remarks/Reflection
                        </label>
                        <textarea
                            id="reflection"
                            name="reflection"
                            rows="3"
                            placeholder="Reflect on your experience during this period..."
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        >{{ old('reflection') }}</textarea>
                    </div>

                    <div class="space-y-1">
                        <label for="attachment" class="block text-sm font-medium">
                            Attach Supporting Document (Optional)
                        </label>
                        <input
                            id="attachment"
                            name="attachment"
                            type="file"
                            accept=".doc,.docx,.ppt,.pptx,.pdf"
                            class="mt-1 block w-full text-sm text-gray-500 dark:text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 dark:file:bg-indigo-900 dark:file:text-indigo-300"
                        >
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-4">
                        <a
                            href="{{ route('student.journal.index') }}"
                            class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100"
                        >
                            Cancel
                        </a>
                        @php $disableSubmit = ($type === 'daily') && (isset($approvedDates) && collect($approvedDates)->isEmpty()); @endphp
                        <button
                            type="submit"
                            @if($disableSubmit) disabled @endif
                            class="inline-flex items-center px-6 py-3 rounded-xl bg-indigo-600 text-sm font-bold uppercase tracking-widest text-white hover:bg-indigo-700 shadow-lg shadow-indigo-200 dark:shadow-none transition-all disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            Submit Report
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-student-layout>
