<x-supervisor-layout>
    <x-slot name="header">
        Generate Performance Report
    </x-slot>

    <div class="max-w-4xl mx-auto py-6">
        <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-100">
            <div class="bg-indigo-600 px-6 py-4">
                <h3 class="font-bold text-white text-lg">Report Configuration</h3>
            </div>
            <div class="p-8">
                <form method="POST" action="{{ route('supervisor.reports.store') }}" class="space-y-6">
                    @csrf
                    
                    <div>
                        <label for="assignment_id" class="block text-sm font-bold text-gray-800">Select Student</label>
                        <select id="assignment_id" name="assignment_id" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 bg-white text-gray-900 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                            @foreach($assignments as $assignment)
                                <option value="{{ $assignment->id }}">{{ $assignment->student->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="start_date" class="block text-sm font-bold text-gray-800">Start Date</label>
                            <input type="date" name="start_date" id="start_date" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 bg-white text-gray-900 rounded-md">
                        </div>
                        <div>
                            <label for="end_date" class="block text-sm font-bold text-gray-800">End Date</label>
                            <input type="date" name="end_date" id="end_date" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 bg-white text-gray-900 rounded-md">
                        </div>
                    </div>

                    <div class="flex justify-end pt-4">
                        <button type="submit" class="inline-flex items-center gap-2 justify-center py-2 px-6 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            Generate Report
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-supervisor-layout>