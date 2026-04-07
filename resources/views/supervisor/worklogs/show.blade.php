<x-supervisor-layout>
    <x-slot name="header">
        Review worklog
    </x-slot>

    <div class="space-y-6">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900 dark:text-gray-100 space-y-2 text-sm">
                <div>
                    <span class="font-medium">Student:</span>
                    {{ $workLog->assignment->student->name }}
                </div>
                <div>
                    <span class="font-medium">Company:</span>
                    {{ $workLog->assignment->company->name }}
                </div>
                <div>
                    <span class="font-medium">Date:</span>
                    {{ $workLog->work_date->format('Y-m-d') }}
                </div>
                <div>
                    <span class="font-medium">Hours:</span>
                    {{ number_format($workLog->hours, 2) }}
                </div>
                <div class="mt-4">
                    <span class="font-medium">Task/Activity Description:</span>
                    <p class="mt-1 whitespace-pre-line text-gray-600 dark:text-gray-400 italic">
                        {{ $workLog->description }}
                    </p>
                </div>
                @if($workLog->skills_applied)
                <div class="mt-4">
                    <span class="font-medium">Skills Applied/Learned:</span>
                    <p class="mt-1 whitespace-pre-line text-gray-600 dark:text-gray-400 italic">
                        {{ $workLog->skills_applied }}
                    </p>
                </div>
                @endif
                @if($workLog->reflection)
                <div class="mt-4">
                    <span class="font-medium">Remarks/Reflection:</span>
                    <p class="mt-1 whitespace-pre-line text-gray-600 dark:text-gray-400 italic">
                        {{ $workLog->reflection }}
                    </p>
                </div>
                @endif
            </div>
        </div>

        @if ($workLog->status === 'submitted')
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100 space-y-4">
                    @if ($errors->any())
                        <div class="text-sm text-red-600 dark:text-red-400">
                            {{ $errors->first() }}
                        </div>
                    @endif

                    <form
                        method="POST"
                        action="{{ route('supervisor.worklogs.review', $workLog) }}"
                        class="space-y-4"
                    >
                        @csrf

                        <div class="space-y-1">
                            <label for="status" class="block text-sm font-medium">
                                Decision
                            </label>
                            <select
                                id="status"
                                name="status"
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            >
                                <option value="approved">
                                    Approve
                                </option>
                                <option value="rejected">
                                    Reject
                                </option>
                            </select>
                        </div>

                        <div class="space-y-1">
                            <label for="grade" class="block text-sm font-medium">
                                Grade (optional)
                            </label>
                            <input
                                id="grade"
                                name="grade"
                                type="text"
                                value="{{ old('grade', $workLog->grade) }}"
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            >
                        </div>

                        <div class="space-y-1">
                            <label for="reviewer_comment" class="block text-sm font-medium">
                                Comment
                            </label>
                            <textarea
                                id="reviewer_comment"
                                name="reviewer_comment"
                                rows="4"
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            >{{ old('reviewer_comment', $workLog->reviewer_comment) }}</textarea>
                        </div>

                        <div class="flex items-center justify-end gap-3">
                            <a
                                href="{{ route('supervisor.dashboard') }}"
                                class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100"
                            >
                                Back
                            </a>
                            <button
                                type="submit"
                                class="inline-flex items-center px-4 py-2 rounded-md bg-indigo-600 text-xs font-semibold uppercase tracking-wide text-white hover:bg-indigo-700"
                            >
                                Save review
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        @else
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100 text-sm">
                    This worklog has already been reviewed.
                </div>
            </div>
        @endif
    </div>
</x-supervisor-layout>

