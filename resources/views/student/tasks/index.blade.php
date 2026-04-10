<x-app-layout>
    <x-slot name="header">
        <h2 class="text-white">My Tasks</h2>
    </x-slot>

    <main class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Main Content Card -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm overflow-hidden">
                <div class="p-6">
                    <!-- Status Bar -->
                    <div class="mb-6 p-4 bg-blue-50 dark:bg-blue-900/20 border-l-4 border-blue-500 rounded">
                        <p class="text-sm text-blue-900 dark:text-blue-300">
                            <strong>Status:</strong> 
                            @if($assignment)
                                ? Assignment Loaded (ID: {{ $assignment->id }})
                            @else
                                ?? No Assignment Found
                            @endif
                            | 
                             <strong>Total Tasks:</strong> {{ count($sem1_tasks ?? []) + count($sem2_tasks ?? []) }}
                        </p>
                    </div>

                    <!-- Content Section -->
                    @if(!$assignment)
                        <div class="text-center py-16">
                            <div class="text-6xl mb-4">??</div>
                            <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">No Active Assignment</h3>
                            <p class="text-gray-600 dark:text-gray-400 mb-4">You don't have an active assignment yet.</p>
                            <p class="text-xs text-gray-500">Please contact your coordinator.</p>
                        </div>

                    @elseif((count($sem1_tasks ?? []) + count($sem2_tasks ?? [])) === 0)
                        <div class="text-center py-16">
                            <div class="text-6xl mb-4">??</div>
                            <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">No Tasks Assigned</h3>
                            <p class="text-gray-600 dark:text-gray-400 mb-4">Your supervisor hasn't assigned any tasks yet.</p>
                        </div>

                    @else
                        <!-- Semester Tabs -->
                        <div class="mb-6 border-b border-gray-200 dark:border-gray-700">
                            <div class="flex gap-6">
                                <button onclick="switchTab('sem1')" id="tab-sem1" class="px-1 py-4 font-semibold border-b-2 border-indigo-600 text-indigo-600 dark:text-indigo-400">?? 1st Sem ({{ count($sem1_tasks) }})</button>
                                <button onclick="switchTab('sem2')" id="tab-sem2" class="px-1 py-4 font-semibold text-gray-600 dark:text-gray-400 border-b-2 border-transparent">?? 2nd Sem ({{ count($sem2_tasks) }})</button>
                            </div>
                        </div>

                        <!-- 1st Semester -->
                        <div id="content-sem1">
                            @if(count($sem1_tasks) > 0)
                                <table class="w-full text-sm">
                                    <thead class="bg-gray-100 dark:bg-gray-700">
                                        <tr>
                                            <th class="px-6 py-3 text-left font-semibold">Task</th>
                                            <th class="px-6 py-3 text-center font-semibold">Due</th>
                                            <th class="px-6 py-3 text-center font-semibold">Status</th>
                                            <th class="px-6 py-3 text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y">
                                        @foreach($sem1_tasks as $task)
                                            <tr>
                                                <td class="px-6 py-4 font-semibold">{{ $task['title'] }}</td>
                                                <td class="px-6 py-4 text-center text-sm">{{ isset($task['due_date']) ? Carbon\Carbon::parse($task['due_date'])->format('M d') : 'N/A' }}</td>
                                                <td class="px-6 py-4 text-center">
                                                    <span class="px-3 py-1 text-xs rounded-full bg-gray-100">{{ $task['status'] ?? 'pending' }}</span>
                                                </td>
                                                <td class="px-6 py-4 text-center"><a href="/student/tasks/{{ $task['id'] }}" class="text-indigo-600 hover:underline">View</a></td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @endif
                        </div>

                        <!-- 2nd Semester -->
                        <div id="content-sem2" class="hidden">
                            @if(count($sem2_tasks) > 0)
                                <table class="w-full text-sm">
                                    <thead class="bg-gray-100 dark:bg-gray-700">
                                        <tr>
                                            <th class="px-6 py-3 text-left font-semibold">Task</th>
                                            <th class="px-6 py-3 text-center font-semibold">Due</th>
                                            <th class="px-6 py-3 text-center font-semibold">Status</th>
                                            <th class="px-6 py-3 text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y">
                                        @foreach($sem2_tasks as $task)
                                            <tr>
                                                <td class="px-6 py-4 font-semibold">{{ $task['title'] }}</td>
                                                <td class="px-6 py-4 text-center text-sm">{{ isset($task['due_date']) ? Carbon\Carbon::parse($task['due_date'])->format('M d') : 'N/A' }}</td>
                                                <td class="px-6 py-4 text-center">
                                                    <span class="px-3 py-1 text-xs rounded-full bg-gray-100">{{ $task['status'] ?? 'pending' }}</span>
                                                </td>
                                                <td class="px-6 py-4 text-center"><a href="/student/tasks/{{ $task['id'] }}" class="text-indigo-600 hover:underline">View</a></td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </main>

    <script>
        function switchTab(sem) {
            document.getElementById('content-sem1').classList.toggle('hidden', sem !== 'sem1');
            document.getElementById('content-sem2').classList.toggle('hidden', sem !== 'sem2');
            document.getElementById('tab-sem1').classList.toggle('border-indigo-600', sem === 'sem1');
            document.getElementById('tab-sem1').classList.toggle('text-indigo-600', sem === 'sem1');
            document.getElementById('tab-sem2').classList.toggle('border-indigo-600', sem === 'sem2');
            document.getElementById('tab-sem2').classList.toggle('text-indigo-600', sem === 'sem2');
        }
    </script>
</x-app-layout>
