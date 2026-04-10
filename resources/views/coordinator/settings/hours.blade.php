<x-coordinator-layout>
    <x-slot name="header">
        OJT Required Hours
    </x-slot>

    <div class="space-y-6">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900 dark:text-gray-100 space-y-4">
                @if (session('status'))
                    <div class="text-sm text-green-600 dark:text-green-400">
                        {{ session('status') }}
                    </div>
                @endif
                @if ($errors->any())
                    <div class="text-sm text-red-600 dark:text-red-400">
                        {{ $errors->first() }}
                    </div>
                @endif

                <form method="POST" action="{{ route('coordinator.settings.hours.update') }}" class="space-y-4">
                    @csrf
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div class="space-y-1">
                            <label for="required_hours" class="block text-sm font-medium">
                                Required Hours (global)
                            </label>
                            <input
                                id="required_hours"
                                name="required_hours"
                                type="number"
                                min="1"
                                max="5000"
                                value="{{ old('required_hours', $currentRequiredHours) }}"
                                required
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            >
                            <p class="text-xs text-gray-500 dark:text-gray-400">This will update all assignments in selected scope.</p>
                        </div>
                        <div class="space-y-1">
                            <label for="scope" class="block text-sm font-medium">
                                Apply To
                            </label>
                            <select
                                id="scope"
                                name="scope"
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            >
                                <option value="active" @selected(old('scope') === 'active')>Active assignments only</option>
                                <option value="all" @selected(old('scope') === 'all')>All assignments</option>
                            </select>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-3">
                        <a href="{{ route('coordinator.deployment.index') }}" class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100">Back to deployment management</a>
                        <button type="submit" class="inline-flex items-center px-4 py-2 rounded-md bg-indigo-600 text-xs font-semibold uppercase tracking-wide text-white hover:bg-indigo-700">
                            Update Hours For Students
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-coordinator-layout>
