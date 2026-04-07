<x-supervisor-layout>
    <x-slot name="header">
        Assign New Task
    </x-slot>

    <div class="max-w-4xl mx-auto py-6">
        <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-100">
            <div class="bg-gradient-to-r from-purple-600 to-indigo-600 px-6 py-4">
                <h3 class="font-bold text-white text-lg">Create New Task</h3>
            </div>
            <div class="p-8">
                <form method="POST" action="{{ route('supervisor.tasks.store') }}" class="space-y-6">
                    @csrf
                    
                    <div>
                        <label for="assignment_id" class="block text-sm font-bold text-gray-800">Assign To (Student)</label>
                        <select id="assignment_id" name="assignment_id" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 bg-white text-gray-900 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                            @foreach($assignments as $assignment)
                                <option value="{{ $assignment->id }}">{{ $assignment->student->name }} ({{ $assignment->company->name ?? 'No Company' }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="title" class="block text-sm font-bold text-gray-800">Task Title</label>
                        <input type="text" name="title" id="title" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 bg-white text-gray-900 placeholder-gray-400 rounded-md" placeholder="e.g. Complete Weekly Report">
                    </div>

                    <div>
                        <label for="description" class="block text-sm font-bold text-gray-800">Description</label>
                        <textarea id="description" name="description" rows="4" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 mt-1 block w-full sm:text-sm border-gray-300 bg-white text-gray-900 placeholder-gray-400 rounded-md" placeholder="Detailed instructions..."></textarea>
                    </div>

                    <div>
                        <label for="due_date" class="block text-sm font-bold text-gray-800">Due Date</label>
                        <input type="date" name="due_date" id="due_date" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 bg-white text-gray-900 rounded-md">
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Assign Task
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-supervisor-layout>