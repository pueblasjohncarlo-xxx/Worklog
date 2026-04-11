<x-student-layout>
    <x-slot name="header">
        <h2 class="text-white">My Tasks</h2>
    </x-slot>

    @php
        $allTasks = collect($sem1_tasks ?? [])->merge($sem2_tasks ?? []);
    @endphp

    <div class="px-3 md:px-6 lg:px-8 py-3 sm:py-4">
        <div class="max-w-7xl mx-auto bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-100 dark:border-gray-700 p-3 sm:p-4 md:p-5">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3 sm:gap-4">
                <div>
                    <h3 class="text-sm sm:text-base md:text-lg font-bold text-gray-900 dark:text-white">Task Workflow</h3>
                    <p class="text-xs md:text-sm text-gray-600 dark:text-gray-300 mt-1">Review each task, complete required outputs, then open the task details page to submit progress on time.</p>
                    <div class="mt-2 flex flex-wrap gap-1 sm:gap-2 text-xs">
                        <span class="px-2 sm:px-2.5 py-1 rounded-full bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 font-semibold text-[11px] sm:text-xs">1. Review</span>
                        <span class="px-2 sm:px-2.5 py-1 rounded-full bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 font-semibold text-[11px] sm:text-xs">2. Complete</span>
                        <span class="px-2 sm:px-2.5 py-1 rounded-full bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 font-semibold text-[11px] sm:text-xs">3. Update</span>
                    </div>
                </div>
                <div class="grid grid-cols-3 gap-1 sm:gap-2 md:gap-3 w-full lg:w-auto">
                    <div class="rounded-md bg-indigo-50 dark:bg-indigo-900/30 px-2 sm:px-3 py-2 text-center min-w-[60px] sm:min-w-[90px]">
                        <p class="text-[8px] sm:text-[10px] uppercase font-bold text-indigo-700 dark:text-indigo-300">Total</p>
                        <p class="text-base sm:text-lg font-extrabold text-indigo-600 dark:text-indigo-300">{{ $allTasks->count() }}</p>
                    </div>
                    <div class="rounded-md bg-green-50 dark:bg-green-900/30 px-2 sm:px-3 py-2 text-center min-w-[60px] sm:min-w-[90px]">
                        <p class="text-[8px] sm:text-[10px] uppercase font-bold text-green-700 dark:text-green-300">Done</p>
                        <p class="text-base sm:text-lg font-extrabold text-green-600 dark:text-green-300">{{ $allTasks->where('status', 'approved')->count() }}</p>
                    </div>
                    <div class="rounded-md bg-yellow-50 dark:bg-yellow-900/30 px-2 sm:px-3 py-2 text-center min-w-[60px] sm:min-w-[90px]">
                        <p class="text-[8px] sm:text-[10px] uppercase font-bold text-yellow-700 dark:text-yellow-300">Pending</p>
                        <p class="text-base sm:text-lg font-extrabold text-yellow-600 dark:text-yellow-300">{{ $allTasks->where('status', 'pending')->count() }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Mobile View -->
    <div class="block md:hidden">
        <main class="py-4 px-4">
            @if(!$assignment)
                <div class="bg-yellow-100 p-4 rounded text-center">
                    <p class="text-sm font-semibold">No Active Assignment</p>
                </div>
            @else
                <div class="space-y-4">
                    <div class="grid grid-cols-2 gap-3">
                        <div class="bg-blue-50 p-3 rounded">
                            <p class="text-xs font-bold">Tasks</p>
                            <p class="text-2xl font-bold">{{ count($sem1_tasks ?? []) + count($sem2_tasks ?? []) }}</p>
                        </div>
                        <div class="bg-green-50 p-3 rounded">
                            <p class="text-xs font-bold">Done</p>
                            <p class="text-2xl font-bold">{{ collect($sem1_tasks ?? [])->merge($sem2_tasks ?? [])->where('status', 'approved')->count() }}</p>
                        </div>
                    </div>

                    @if((count($sem1_tasks ?? []) + count($sem2_tasks ?? [])) === 0)
                        <div class="bg-gray-100 p-6 text-center rounded">
                            <p class="text-sm text-gray-600">No tasks assigned</p>
                        </div>
                    @else
                        <div class="space-y-3">
                            @foreach($sem1_tasks as $task)
                                <div class="bg-white border rounded p-3">
                                    <h4 class="font-bold text-sm mb-1">{{ Str::limit($task['title'], 35) }}</h4>
                                    <p class="text-xs text-gray-600 mb-2">{{ $task['assigned_by'] ?? 'Supervisor' }}</p>
                                    <span class="text-xs inline-block bg-indigo-100 px-2 py-1 rounded mb-2">{{ ucfirst($task['status'] ?? 'pending') }}</span>
                                    <a href="/student/tasks/{{ $task['id'] }}" class="block px-3 py-2 bg-indigo-600 text-white text-xs text-center rounded font-semibold mt-2">View</a>
                                </div>
                            @endforeach
                            @foreach($sem2_tasks as $task)
                                <div class="bg-white border rounded p-3">
                                    <h4 class="font-bold text-sm mb-1">{{ Str::limit($task['title'], 35) }}</h4>
                                    <p class="text-xs text-gray-600 mb-2">{{ $task['assigned_by'] ?? 'Supervisor' }}</p>
                                    <span class="text-xs inline-block bg-indigo-100 px-2 py-1 rounded mb-2">{{ ucfirst($task['status'] ?? 'pending') }}</span>
                                    <a href="/student/tasks/{{ $task['id'] }}" class="block px-3 py-2 bg-indigo-600 text-white text-xs text-center rounded font-semibold mt-2">View</a>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            @endif
        </main>
    </div>

    <!-- Desktop View -->
    <div class="hidden md:block">
        <main class="py-12 px-6">
            <div class="max-w-7xl mx-auto">
                @if(!$assignment)
                    <div class="bg-white dark:bg-gray-800 rounded shadow p-12 text-center">
                        <h3 class="text-2xl font-bold mb-2 text-gray-800 dark:text-white">No Active Assignment</h3>
                        <p class="text-gray-600 dark:text-gray-400">Contact your coordinator to get started</p>
                    </div>
                @else
                    <div class="mb-8 grid grid-cols-4 gap-6">
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
                            <p class="text-xs font-bold text-blue-700 uppercase">Total Tasks</p>
                            <p class="text-4xl font-bold text-blue-600 mt-2">{{ count($sem1_tasks ?? []) + count($sem2_tasks ?? []) }}</p>
                        </div>
                        <div class="bg-green-50 border border-green-200 rounded-lg p-6">
                            <p class="text-xs font-bold text-green-700 uppercase">Completed</p>
                            <p class="text-4xl font-bold text-green-600 mt-2">{{ collect($sem1_tasks ?? [])->merge($sem2_tasks ?? [])->where('status', 'approved')->count() }}</p>
                        </div>
                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6">
                            <p class="text-xs font-bold text-yellow-700 uppercase">Pending</p>
                            <p class="text-4xl font-bold text-yellow-600 mt-2">{{ collect($sem1_tasks ?? [])->merge($sem2_tasks ?? [])->where('status', 'pending')->count() }}</p>
                        </div>
                        <div class="bg-red-50 border border-red-200 rounded-lg p-6">
                            <p class="text-xs font-bold text-red-700 uppercase">Rejected</p>
                            <p class="text-4xl font-bold text-red-600 mt-2">{{ collect($sem1_tasks ?? [])->merge($sem2_tasks ?? [])->where('status', 'rejected')->count() }}</p>
                        </div>
                    </div>

                    @if((count($sem1_tasks ?? []) + count($sem2_tasks ?? [])) === 0)
                        <div class="bg-white rounded shadow p-12 text-center">
                            <h3 class="text-2xl font-bold mb-2">No Tasks Assigned Yet</h3>
                            <p class="text-gray-600">Your supervisor will assign tasks soon</p>
                        </div>
                    @else
                        <div class="mb-6 border-b border-gray-300">
                            <div class="flex gap-8">
                                <button onclick="showTab('sem1')"  id="tab-sem1" class="px-4 py-3 font-bold border-b-2 border-indigo-600 text-indigo-600">Semester 1 ({{ count($sem1_tasks) }})</button>
                                <button onclick="showTab('sem2')"  id="tab-sem2" class="px-4 py-3 font-bold text-gray-600 border-b-2 border-transparent">Semester 2 ({{ count($sem2_tasks) }})</button>
                            </div>
                        </div>

                        <div id="tab-sem1-content" class="grid grid-cols-3 gap-6">
                            @foreach($sem1_tasks as $task)
                                <div class="bg-white border rounded-lg shadow p-6 hover:shadow-lg">
                                    <h3 class="font-bold text-lg mb-2">{{ $task['title'] }}</h3>
                                    <p class="text-sm text-gray-600 mb-3">{{ $task['assigned_by'] ?? 'Supervisor' }}</p>
                                    <p class="text-xs font-semibold mb-3">Status: <span class="text-indigo-600">{{ ucfirst($task['status'] ?? 'pending') }}</span></p>
                                    @if(!empty($task['description']))
                                        <p class="text-sm text-gray-700 mb-4">{{ Str::limit($task['description'], 100) }}</p>
                                    @endif
                                    @if(isset($task['due_date']))
                                        <p class="text-xs text-gray-500 mb-4">Due: {{ \Carbon\Carbon::parse($task['due_date'])->format('M d, Y') }}</p>
                                    @endif
                                    <a href="/student/tasks/{{ $task['id'] }}" class="block w-full px-4 py-2 bg-indigo-600 text-white text-center font-semibold rounded">View Details</a>
                                </div>
                            @endforeach
                        </div>

                        <div id="tab-sem2-content" class="hidden grid grid-cols-3 gap-6">
                            @foreach($sem2_tasks as $task)
                                <div class="bg-white border rounded-lg shadow p-6 hover:shadow-lg">
                                    <h3 class="font-bold text-lg mb-2">{{ $task['title'] }}</h3>
                                    <p class="text-sm text-gray-600 mb-3">{{ $task['assigned_by'] ?? 'Supervisor' }}</p>
                                    <p class="text-xs font-semibold mb-3">Status: <span class="text-indigo-600">{{ ucfirst($task['status'] ?? 'pending') }}</span></p>
                                    @if(!empty($task['description']))
                                        <p class="text-sm text-gray-700 mb-4">{{ Str::limit($task['description'], 100) }}</p>
                                    @endif
                                    @if(isset($task['due_date']))
                                        <p class="text-xs text-gray-500 mb-4">Due: {{ \Carbon\Carbon::parse($task['due_date'])->format('M d, Y') }}</p>
                                    @endif
                                    <a href="/student/tasks/{{ $task['id'] }}" class="block w-full px-4 py-2 bg-indigo-600 text-white text-center font-semibold rounded">View Details</a>
                                </div>
                            @endforeach
                        </div>
                    @endif
                @endif
            </div>
        </main>
    </div>

    <script>
        function showTab(tab) {
            document.getElementById('tab-sem1-content').classList.add('hidden');
            document.getElementById('tab-sem2-content').classList.add('hidden');
            document.getElementById('tab-sem1').classList.remove('border-indigo-600', 'text-indigo-600');
            document.getElementById('tab-sem2').classList.remove('border-indigo-600', 'text-indigo-600');
            document.getElementById('tab-sem1').classList.add('text-gray-600', 'border-transparent');
            document.getElementById('tab-sem2').classList.add('text-gray-600', 'border-transparent');

            if (tab === 'sem1') {
                document.getElementById('tab-sem1-content').classList.remove('hidden');
                document.getElementById('tab-sem1').classList.add('border-indigo-600', 'text-indigo-600');
                document.getElementById('tab-sem1').classList.remove('text-gray-600', 'border-transparent');
            } else {
                document.getElementById('tab-sem2-content').classList.remove('hidden');
                document.getElementById('tab-sem2').classList.add('border-indigo-600', 'text-indigo-600');
                document.getElementById('tab-sem2').classList.remove('text-gray-600', 'border-transparent');
            }
        }
    </script>
</x-student-layout>