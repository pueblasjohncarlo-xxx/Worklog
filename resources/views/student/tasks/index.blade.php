<x-student-layout>
    <x-slot name="header">
        <h2 class="text-white">My Tasks</h2>
    </x-slot>

    @php
        $allTasks = collect($sem1_tasks ?? [])->merge($sem2_tasks ?? []);
        $activeFilter = $activeTaskFilter ?? (string) request('filter', 'all');
        $totalCount = $totalTasksCount ?? $allTasks->count();
        $doneCount = $completedTasksCount ?? $allTasks->where('status', 'approved')->count();
        $pendingCount = $pendingTasksCount ?? $allTasks->where('status', 'pending')->count();
        $rejectedCount = $rejectedTasksCount ?? $allTasks->where('status', 'rejected')->count();
    @endphp

    <div class="px-3 md:px-6 lg:px-8 py-3 sm:py-4">
        @if (session('error'))
            <div class="max-w-7xl mx-auto mb-3 rounded-lg border border-rose-300 bg-rose-50 px-4 py-3 text-sm font-medium text-rose-800">
                {{ session('error') }}
            </div>
        @endif

        @if (session('status'))
            <div class="max-w-7xl mx-auto mb-3 rounded-lg border border-emerald-300 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800">
                {{ session('status') }}
            </div>
        @endif

        <div class="max-w-7xl mx-auto bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-100 dark:border-gray-700 p-3 sm:p-4 md:p-5">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3 sm:gap-4">
                <div>
                    <h3 class="text-sm sm:text-base md:text-lg font-bold text-gray-900 dark:text-white">Task Workflow</h3>
                    <p class="text-xs md:text-sm text-gray-600 dark:text-gray-300 mt-1">Review each task, complete required outputs, then open the task details page to submit progress on time.</p>
                    @if ($activeFilter && $activeFilter !== 'all')
                        <p class="mt-2 text-xs font-semibold text-gray-500 dark:text-gray-300">
                            Showing: {{ ucfirst($activeFilter) }}
                            <a href="{{ request()->fullUrlWithQuery(['filter' => 'all']) }}" class="ml-2 text-indigo-600 dark:text-indigo-300 hover:underline">Clear</a>
                        </p>
                    @endif
                    <div class="mt-2 flex flex-wrap gap-1 sm:gap-2 text-xs">
                        <span class="px-2 sm:px-2.5 py-1 rounded-full bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 font-semibold text-[11px] sm:text-xs">1. Review</span>
                        <span class="px-2 sm:px-2.5 py-1 rounded-full bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 font-semibold text-[11px] sm:text-xs">2. Complete</span>
                        <span class="px-2 sm:px-2.5 py-1 rounded-full bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 font-semibold text-[11px] sm:text-xs">3. Update</span>
                    </div>
                </div>
                <div class="grid grid-cols-4 gap-1 sm:gap-2 md:gap-3 w-full lg:w-auto">
                    <a href="{{ request()->fullUrlWithQuery(['filter' => 'all']) }}" class="rounded-md bg-slate-50 dark:bg-slate-900/30 px-2 sm:px-3 py-2 text-center min-w-[60px] sm:min-w-[90px] hover:ring-2 hover:ring-slate-300 dark:hover:ring-slate-600 transition">
                        <p class="text-[8px] sm:text-[10px] uppercase font-bold text-slate-700 dark:text-slate-300">Total</p>
                        <p class="text-base sm:text-lg font-extrabold text-slate-700 dark:text-slate-200">{{ $totalCount }}</p>
                    </a>
                    <a href="{{ request()->fullUrlWithQuery(['filter' => 'done']) }}" class="rounded-md bg-emerald-50 dark:bg-emerald-900/30 px-2 sm:px-3 py-2 text-center min-w-[60px] sm:min-w-[90px] hover:ring-2 hover:ring-emerald-300 dark:hover:ring-emerald-600 transition">
                        <p class="text-[8px] sm:text-[10px] uppercase font-bold text-emerald-700 dark:text-emerald-300">Done</p>
                        <p class="text-base sm:text-lg font-extrabold text-emerald-700 dark:text-emerald-200">{{ $doneCount }}</p>
                    </a>
                    <a href="{{ request()->fullUrlWithQuery(['filter' => 'pending']) }}" class="rounded-md bg-amber-50 dark:bg-amber-900/30 px-2 sm:px-3 py-2 text-center min-w-[60px] sm:min-w-[90px] hover:ring-2 hover:ring-amber-300 dark:hover:ring-amber-600 transition">
                        <p class="text-[8px] sm:text-[10px] uppercase font-bold text-amber-700 dark:text-amber-300">Pending</p>
                        <p class="text-base sm:text-lg font-extrabold text-amber-700 dark:text-amber-200">{{ $pendingCount }}</p>
                    </a>
                    <a href="{{ request()->fullUrlWithQuery(['filter' => 'rejected']) }}" class="rounded-md bg-rose-50 dark:bg-rose-900/30 px-2 sm:px-3 py-2 text-center min-w-[60px] sm:min-w-[90px] hover:ring-2 hover:ring-rose-300 dark:hover:ring-rose-600 transition">
                        <p class="text-[8px] sm:text-[10px] uppercase font-bold text-rose-700 dark:text-rose-300">Rejected</p>
                        <p class="text-base sm:text-lg font-extrabold text-rose-700 dark:text-rose-200">{{ $rejectedCount }}</p>
                    </a>
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
                        <a href="{{ request()->fullUrlWithQuery(['filter' => 'all']) }}" class="bg-slate-50 p-3 rounded block">
                            <p class="text-xs font-bold">Tasks</p>
                            <p class="text-2xl font-bold">{{ $totalCount }}</p>
                        </a>
                        <a href="{{ request()->fullUrlWithQuery(['filter' => 'done']) }}" class="bg-emerald-50 p-3 rounded block">
                            <p class="text-xs font-bold">Done</p>
                            <p class="text-2xl font-bold">{{ $doneCount }}</p>
                        </a>
                        <a href="{{ request()->fullUrlWithQuery(['filter' => 'pending']) }}" class="bg-amber-50 p-3 rounded block">
                            <p class="text-xs font-bold">Pending</p>
                            <p class="text-2xl font-bold">{{ $pendingCount }}</p>
                        </a>
                        <a href="{{ request()->fullUrlWithQuery(['filter' => 'rejected']) }}" class="bg-rose-50 p-3 rounded block">
                            <p class="text-xs font-bold">Rejected</p>
                            <p class="text-2xl font-bold">{{ $rejectedCount }}</p>
                        </a>
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
                                    @php
                                        $rawStatus = strtolower($task['status'] ?? 'pending');
                                        $uiStatus = in_array($rawStatus, ['approved', 'completed'], true) ? 'Done' : ($rawStatus === 'rejected' ? 'Rejected' : 'Pending');
                                        $uiClass = $uiStatus === 'Done' ? 'bg-emerald-100 text-emerald-700' : ($uiStatus === 'Rejected' ? 'bg-rose-100 text-rose-700' : 'bg-amber-100 text-amber-700');
                                    @endphp
                                    <span class="text-xs inline-block px-2 py-1 rounded mb-2 font-semibold {{ $uiClass }}">{{ $uiStatus }}</span>
                                    <a href="{{ route('student.tasks.show', $task['id']) }}" class="block px-3 py-2 bg-indigo-600 text-white text-xs text-center rounded font-semibold mt-2">View</a>
                                </div>
                            @endforeach
                            @foreach($sem2_tasks as $task)
                                <div class="bg-white border rounded p-3">
                                    <h4 class="font-bold text-sm mb-1">{{ Str::limit($task['title'], 35) }}</h4>
                                    <p class="text-xs text-gray-600 mb-2">{{ $task['assigned_by'] ?? 'Supervisor' }}</p>
                                    @php
                                        $rawStatus = strtolower($task['status'] ?? 'pending');
                                        $uiStatus = in_array($rawStatus, ['approved', 'completed'], true) ? 'Done' : ($rawStatus === 'rejected' ? 'Rejected' : 'Pending');
                                        $uiClass = $uiStatus === 'Done' ? 'bg-emerald-100 text-emerald-700' : ($uiStatus === 'Rejected' ? 'bg-rose-100 text-rose-700' : 'bg-amber-100 text-amber-700');
                                    @endphp
                                    <span class="text-xs inline-block px-2 py-1 rounded mb-2 font-semibold {{ $uiClass }}">{{ $uiStatus }}</span>
                                    <a href="{{ route('student.tasks.show', $task['id']) }}" class="block px-3 py-2 bg-indigo-600 text-white text-xs text-center rounded font-semibold mt-2">View</a>
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
                        <a href="{{ request()->fullUrlWithQuery(['filter' => 'all']) }}" class="bg-slate-50 border border-slate-200 rounded-lg p-6 block hover:ring-2 hover:ring-slate-200 transition">
                            <p class="text-xs font-bold text-blue-700 uppercase">Total Tasks</p>
                            <p class="text-4xl font-bold text-slate-700 mt-2">{{ $totalCount }}</p>
                        </a>
                        <a href="{{ request()->fullUrlWithQuery(['filter' => 'done']) }}" class="bg-emerald-50 border border-emerald-200 rounded-lg p-6 block hover:ring-2 hover:ring-emerald-200 transition">
                            <p class="text-xs font-bold text-green-700 uppercase">Completed</p>
                            <p class="text-4xl font-bold text-emerald-700 mt-2">{{ $doneCount }}</p>
                        </a>
                        <a href="{{ request()->fullUrlWithQuery(['filter' => 'pending']) }}" class="bg-amber-50 border border-amber-200 rounded-lg p-6 block hover:ring-2 hover:ring-amber-200 transition">
                            <p class="text-xs font-bold text-yellow-700 uppercase">Pending</p>
                            <p class="text-4xl font-bold text-amber-700 mt-2">{{ $pendingCount }}</p>
                        </a>
                        <a href="{{ request()->fullUrlWithQuery(['filter' => 'rejected']) }}" class="bg-rose-50 border border-rose-200 rounded-lg p-6 block hover:ring-2 hover:ring-rose-200 transition">
                            <p class="text-xs font-bold text-red-700 uppercase">Rejected</p>
                            <p class="text-4xl font-bold text-rose-700 mt-2">{{ $rejectedCount }}</p>
                        </a>
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
                                    @php
                                        $rawStatus = strtolower($task['status'] ?? 'pending');
                                        $uiStatus = in_array($rawStatus, ['approved', 'completed'], true) ? 'Done' : ($rawStatus === 'rejected' ? 'Rejected' : 'Pending');
                                        $uiClass = $uiStatus === 'Done' ? 'text-emerald-700' : ($uiStatus === 'Rejected' ? 'text-rose-700' : 'text-amber-700');
                                    @endphp
                                    <p class="text-xs font-semibold mb-3">Status: <span class="{{ $uiClass }}">{{ $uiStatus }}</span></p>
                                    @if(!empty($task['description']))
                                        <p class="text-sm text-gray-700 mb-4">{{ Str::limit($task['description'], 100) }}</p>
                                    @endif
                                    @if(isset($task['due_date']))
                                        <p class="text-xs text-gray-500 mb-4">Due: {{ \Carbon\Carbon::parse($task['due_date'])->format('M d, Y') }}</p>
                                    @endif
                                    <a href="{{ route('student.tasks.show', $task['id']) }}" class="block w-full px-4 py-2 bg-indigo-600 text-white text-center font-semibold rounded">View Details</a>
                                </div>
                            @endforeach
                        </div>

                        <div id="tab-sem2-content" class="hidden grid grid-cols-3 gap-6">
                            @foreach($sem2_tasks as $task)
                                <div class="bg-white border rounded-lg shadow p-6 hover:shadow-lg">
                                    <h3 class="font-bold text-lg mb-2">{{ $task['title'] }}</h3>
                                    <p class="text-sm text-gray-600 mb-3">{{ $task['assigned_by'] ?? 'Supervisor' }}</p>
                                    @php
                                        $rawStatus = strtolower($task['status'] ?? 'pending');
                                        $uiStatus = in_array($rawStatus, ['approved', 'completed'], true) ? 'Done' : ($rawStatus === 'rejected' ? 'Rejected' : 'Pending');
                                        $uiClass = $uiStatus === 'Done' ? 'text-emerald-700' : ($uiStatus === 'Rejected' ? 'text-rose-700' : 'text-amber-700');
                                    @endphp
                                    <p class="text-xs font-semibold mb-3">Status: <span class="{{ $uiClass }}">{{ $uiStatus }}</span></p>
                                    @if(!empty($task['description']))
                                        <p class="text-sm text-gray-700 mb-4">{{ Str::limit($task['description'], 100) }}</p>
                                    @endif
                                    @if(isset($task['due_date']))
                                        <p class="text-xs text-gray-500 mb-4">Due: {{ \Carbon\Carbon::parse($task['due_date'])->format('M d, Y') }}</p>
                                    @endif
                                    <a href="{{ route('student.tasks.show', $task['id']) }}" class="block w-full px-4 py-2 bg-indigo-600 text-white text-center font-semibold rounded">View Details</a>
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