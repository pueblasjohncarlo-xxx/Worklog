<x-supervisor-layout>
    <x-slot name="header">
        Task Submission
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto">
            <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-100">
                <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
                    <h3 class="font-bold text-gray-800 text-lg">Submission File</h3>
                    <p class="text-sm text-gray-600 mt-1">{{ $task->title ?? 'Task' }}</p>
                </div>

                <div class="p-6">
                    @php
                        $message = ($reason ?? '') === 'missing-on-disk'
                            ? 'A submission was recorded, but the file is missing from storage.'
                            : 'No submission file is attached for this task.';
                    @endphp

                    <div class="rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-sm font-medium text-amber-900">
                        {{ $message }}
                    </div>

                    <div class="mt-6 flex justify-end">
                        <a href="{{ route('supervisor.tasks.index') }}" class="inline-flex items-center justify-center px-4 py-2 rounded-lg border border-gray-300 text-gray-700 font-semibold hover:bg-gray-50">
                            Back to Tasks
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-supervisor-layout>
