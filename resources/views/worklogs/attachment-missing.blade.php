@php
    $role = auth()->user()?->role;

    $layoutComponent = match ($role) {
        \App\Models\User::ROLE_COORDINATOR => 'coordinator-layout',
        \App\Models\User::ROLE_SUPERVISOR => 'supervisor-layout',
        \App\Models\User::ROLE_OJT_ADVISER => 'ojt-adviser-layout',
        \App\Models\User::ROLE_STUDENT => 'student-layout',
        default => 'app-layout',
    };

    $typeLabel = ucfirst((string) ($workLog->type ?? 'report'));
    $dateLabel = $workLog->work_date?->format('M d, Y') ?? 'N/A';
    $studentName = $workLog->assignment?->student?->name ?? 'Student';
    $missingFile = (bool) ($missingFile ?? false);
@endphp

<x-dynamic-component :component="$layoutComponent">
    <x-slot name="header">
        Accomplishment Report File
    </x-slot>

    <div class="max-w-4xl mx-auto space-y-4">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                {{ $missingFile ? 'Uploaded file not found' : 'No uploaded attachment found' }}
            </h2>
            <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">
                This <span class="font-semibold">{{ $typeLabel }}</span> accomplishment report ({{ $dateLabel }}) for <span class="font-semibold">{{ $studentName }}</span>
                {{ $missingFile ? 'has an attachment reference, but the file could not be found in storage.' : 'does not have an uploaded file attached, so there is nothing to preview or print.' }}
            </p>

            <div class="mt-4 text-sm text-gray-500 dark:text-gray-400">
                If this report should have a file, ask the student to re-upload and re-submit the report.
            </div>

            <div class="mt-6 flex flex-wrap gap-2">
                <button type="button" onclick="window.close()" class="px-4 py-2 rounded-md bg-gray-100 dark:bg-gray-900/40 text-gray-800 dark:text-gray-200 text-sm font-semibold hover:bg-gray-200 dark:hover:bg-gray-900/60">
                    Close Tab
                </button>
                <a href="{{ url()->previous() }}" class="px-4 py-2 rounded-md bg-indigo-600 text-white text-sm font-semibold hover:bg-indigo-700">
                    Go Back
                </a>
            </div>
        </div>
    </div>
</x-dynamic-component>
