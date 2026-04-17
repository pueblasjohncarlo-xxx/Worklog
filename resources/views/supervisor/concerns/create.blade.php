<x-supervisor-layout>
    <x-slot name="header">
        New Concern / Incident
    </x-slot>

    <div class="max-w-4xl mx-auto py-6">
        <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-100">
            <div class="bg-indigo-600 px-6 py-4">
                <h3 class="font-bold text-white text-lg">Report Details</h3>
            </div>

            <div class="p-8">
                @if ($errors->any())
                    <div class="mb-6 rounded-lg border border-red-200 bg-red-50 p-3 text-sm text-red-800">
                        {{ $errors->first() }}
                    </div>
                @endif

                @if (($assignments->count() ?? 0) === 0)
                    <div class="rounded-lg border border-amber-200 bg-amber-50 p-4 text-sm text-amber-900">
                        No active students found under your supervision.
                    </div>
                @endif

                <form method="POST" action="{{ route('supervisor.concerns.store') }}" class="space-y-6">
                    @csrf

                    <div>
                        <label for="assignment_id" class="block text-sm font-bold text-gray-800">Student</label>
                        <select id="assignment_id" name="assignment_id" required @disabled(($assignments->count() ?? 0) === 0) class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 bg-white text-gray-900 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                            @foreach($assignments as $assignment)
                                <option value="{{ $assignment->id }}" @selected((string) old('assignment_id') === (string) $assignment->id)>
                                    {{ $assignment->student?->name ?? 'Student' }} — {{ $assignment->company?->name ?? 'N/A' }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="type" class="block text-sm font-bold text-gray-800">Type</label>
                            <select id="type" name="type" required class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 bg-white text-gray-900 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                                <option value="concern" @selected(old('type', 'concern') === 'concern')>Concern</option>
                                <option value="incident" @selected(old('type') === 'incident')>Incident</option>
                            </select>
                        </div>
                        <div>
                            <label for="occurred_on" class="block text-sm font-bold text-gray-800">Occurred On (optional)</label>
                            <input type="date" name="occurred_on" id="occurred_on" value="{{ old('occurred_on') }}" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 bg-white text-gray-900 rounded-md">
                        </div>
                    </div>

                    <div>
                        <label for="title" class="block text-sm font-bold text-gray-800">Title</label>
                        <input type="text" name="title" id="title" required value="{{ old('title') }}" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 bg-white text-gray-900 rounded-md" placeholder="Short summary">
                    </div>

                    <div>
                        <label for="details" class="block text-sm font-bold text-gray-800">Details</label>
                        <textarea name="details" id="details" rows="6" required class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 bg-white text-gray-900 rounded-md" placeholder="Describe what happened, context, and any actions taken.">{{ old('details') }}</textarea>
                    </div>

                    <div class="flex justify-end gap-3 pt-4">
                        <a href="{{ route('supervisor.concerns.index') }}" class="inline-flex items-center justify-center px-4 py-2 rounded-lg border border-gray-300 text-gray-700 font-semibold hover:bg-gray-50">
                            Cancel
                        </a>
                        <button type="submit" @disabled(($assignments->count() ?? 0) === 0) class="inline-flex items-center justify-center px-4 py-2 rounded-lg bg-indigo-600 text-white font-semibold hover:bg-indigo-700 disabled:opacity-60 disabled:cursor-not-allowed">
                            Create Report
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-supervisor-layout>
