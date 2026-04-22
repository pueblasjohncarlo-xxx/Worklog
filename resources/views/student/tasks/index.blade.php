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

        <div class="max-w-7xl mx-auto rounded-2xl border border-white/10 bg-white/95 p-3 text-slate-900 shadow-[0_14px_36px_rgba(15,23,42,0.18)] sm:p-4 md:p-5">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3 sm:gap-4">
                <div>
                    <h3 class="text-sm font-black tracking-tight text-slate-900 sm:text-base md:text-lg">Task Workflow</h3>
                    <p class="mt-1 text-xs font-medium leading-6 text-slate-700 md:text-sm">Review each task, complete required outputs, then open the task details page to submit progress on time.</p>
                    @if ($activeFilter && $activeFilter !== 'all')
                        <p class="mt-2 text-xs font-semibold text-slate-700">
                            Showing: {{ ucfirst($activeFilter) }}
                            <a href="{{ request()->fullUrlWithQuery(['filter' => 'all']) }}" class="ml-2 font-bold text-indigo-700 hover:text-indigo-800 hover:underline">Clear</a>
                        </p>
                    @endif
                    <div class="mt-2 flex flex-wrap gap-1 sm:gap-2 text-xs">
                        <span class="rounded-full bg-slate-100 px-2 py-1 text-[11px] font-bold text-slate-800 sm:px-2.5 sm:text-xs">1. Review</span>
                        <span class="rounded-full bg-slate-100 px-2 py-1 text-[11px] font-bold text-slate-800 sm:px-2.5 sm:text-xs">2. Complete</span>
                        <span class="rounded-full bg-slate-100 px-2 py-1 text-[11px] font-bold text-slate-800 sm:px-2.5 sm:text-xs">3. Update</span>
                    </div>
                </div>
                <div class="grid grid-cols-4 gap-1 sm:gap-2 md:gap-3 w-full lg:w-auto">
                    <a href="{{ request()->fullUrlWithQuery(['filter' => 'all']) }}" class="min-w-[60px] rounded-md bg-slate-50 px-2 py-2 text-center shadow-sm ring-1 ring-slate-200 transition hover:ring-2 hover:ring-slate-300 sm:min-w-[90px] sm:px-3">
                        <p class="text-[8px] font-black uppercase tracking-[0.14em] text-slate-700 sm:text-[10px]">Total</p>
                        <p class="text-base font-extrabold text-slate-900 sm:text-lg">{{ $totalCount }}</p>
                    </a>
                    <a href="{{ request()->fullUrlWithQuery(['filter' => 'done']) }}" class="min-w-[60px] rounded-md bg-emerald-50 px-2 py-2 text-center shadow-sm ring-1 ring-emerald-200 transition hover:ring-2 hover:ring-emerald-300 sm:min-w-[90px] sm:px-3">
                        <p class="text-[8px] font-black uppercase tracking-[0.14em] text-emerald-800 sm:text-[10px]">Done</p>
                        <p class="text-base font-extrabold text-emerald-800 sm:text-lg">{{ $doneCount }}</p>
                    </a>
                    <a href="{{ request()->fullUrlWithQuery(['filter' => 'pending']) }}" class="min-w-[60px] rounded-md bg-amber-50 px-2 py-2 text-center shadow-sm ring-1 ring-amber-200 transition hover:ring-2 hover:ring-amber-300 sm:min-w-[90px] sm:px-3">
                        <p class="text-[8px] font-black uppercase tracking-[0.14em] text-amber-800 sm:text-[10px]">Pending</p>
                        <p class="text-base font-extrabold text-amber-800 sm:text-lg">{{ $pendingCount }}</p>
                    </a>
                    <a href="{{ request()->fullUrlWithQuery(['filter' => 'rejected']) }}" class="min-w-[60px] rounded-md bg-rose-50 px-2 py-2 text-center shadow-sm ring-1 ring-rose-200 transition hover:ring-2 hover:ring-rose-300 sm:min-w-[90px] sm:px-3">
                        <p class="text-[8px] font-black uppercase tracking-[0.14em] text-rose-800 sm:text-[10px]">Rejected</p>
                        <p class="text-base font-extrabold text-rose-800 sm:text-lg">{{ $rejectedCount }}</p>
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
                        <a href="{{ request()->fullUrlWithQuery(['filter' => 'all']) }}" class="block rounded-2xl border border-slate-200 bg-white p-3 text-slate-900 shadow-sm">
                            <p class="text-[11px] font-black uppercase tracking-[0.16em] text-slate-600">Tasks</p>
                            <p class="text-2xl font-black text-slate-900">{{ $totalCount }}</p>
                        </a>
                        <a href="{{ request()->fullUrlWithQuery(['filter' => 'done']) }}" class="block rounded-2xl border border-emerald-200 bg-emerald-50 p-3 text-emerald-900 shadow-sm">
                            <p class="text-[11px] font-black uppercase tracking-[0.16em] text-emerald-800">Done</p>
                            <p class="text-2xl font-black text-emerald-800">{{ $doneCount }}</p>
                        </a>
                        <a href="{{ request()->fullUrlWithQuery(['filter' => 'pending']) }}" class="block rounded-2xl border border-amber-200 bg-amber-50 p-3 text-amber-900 shadow-sm">
                            <p class="text-[11px] font-black uppercase tracking-[0.16em] text-amber-800">Pending</p>
                            <p class="text-2xl font-black text-amber-800">{{ $pendingCount }}</p>
                        </a>
                        <a href="{{ request()->fullUrlWithQuery(['filter' => 'rejected']) }}" class="block rounded-2xl border border-rose-200 bg-rose-50 p-3 text-rose-900 shadow-sm">
                            <p class="text-[11px] font-black uppercase tracking-[0.16em] text-rose-800">Rejected</p>
                            <p class="text-2xl font-black text-rose-800">{{ $rejectedCount }}</p>
                        </a>
                    </div>

                    @if((count($sem1_tasks ?? []) + count($sem2_tasks ?? [])) === 0)
                        <div class="bg-gray-100 p-6 text-center rounded">
                            <p class="text-sm text-gray-600">No tasks assigned</p>
                        </div>
                    @else
                        <div class="space-y-3">
                            @foreach($sem1_tasks as $task)
                                <div class="rounded-2xl border border-slate-200 bg-white p-3 text-slate-900 shadow-sm">
                                    <h4 class="mb-1 text-sm font-black tracking-tight text-slate-900">{{ Str::limit($task['title'], 35) }}</h4>
                                    <p class="mb-2 text-xs font-semibold text-slate-700">{{ $task['assigned_by'] ?? 'Supervisor' }}</p>
                                    @php
                                        $rawStatus = strtolower($task['status'] ?? 'pending');
                                        $uiStatus = in_array($rawStatus, ['approved', 'completed'], true) ? 'Done' : ($rawStatus === 'rejected' ? 'Rejected' : 'Pending');
                                        $uiClass = $uiStatus === 'Done' ? 'bg-emerald-100 text-emerald-700' : ($uiStatus === 'Rejected' ? 'bg-rose-100 text-rose-700' : 'bg-amber-100 text-amber-700');
                                    @endphp
                                    <span class="mb-2 inline-block rounded px-2 py-1 text-xs font-bold {{ $uiClass }}">{{ $uiStatus }}</span>
                                    <a href="{{ route('student.tasks.show', $task['id']) }}" class="mt-2 block rounded bg-indigo-600 px-3 py-2 text-center text-xs font-bold text-white shadow-sm hover:bg-indigo-700">View</a>
                                </div>
                            @endforeach
                            @foreach($sem2_tasks as $task)
                                <div class="rounded-2xl border border-slate-200 bg-white p-3 text-slate-900 shadow-sm">
                                    <h4 class="mb-1 text-sm font-black tracking-tight text-slate-900">{{ Str::limit($task['title'], 35) }}</h4>
                                    <p class="mb-2 text-xs font-semibold text-slate-700">{{ $task['assigned_by'] ?? 'Supervisor' }}</p>
                                    @php
                                        $rawStatus = strtolower($task['status'] ?? 'pending');
                                        $uiStatus = in_array($rawStatus, ['approved', 'completed'], true) ? 'Done' : ($rawStatus === 'rejected' ? 'Rejected' : 'Pending');
                                        $uiClass = $uiStatus === 'Done' ? 'bg-emerald-100 text-emerald-700' : ($uiStatus === 'Rejected' ? 'bg-rose-100 text-rose-700' : 'bg-amber-100 text-amber-700');
                                    @endphp
                                    <span class="mb-2 inline-block rounded px-2 py-1 text-xs font-bold {{ $uiClass }}">{{ $uiStatus }}</span>
                                    <a href="{{ route('student.tasks.show', $task['id']) }}" class="mt-2 block rounded bg-indigo-600 px-3 py-2 text-center text-xs font-bold text-white shadow-sm hover:bg-indigo-700">View</a>
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
                    <div class="mb-8 grid grid-cols-4 gap-5">
                        <a href="{{ request()->fullUrlWithQuery(['filter' => 'all']) }}" class="block rounded-2xl border border-slate-200 bg-white p-5 text-slate-900 shadow-[0_12px_28px_rgba(15,23,42,0.12)] transition hover:-translate-y-0.5 hover:ring-2 hover:ring-slate-200">
                            <p class="text-[11px] font-black uppercase tracking-[0.18em] text-slate-500">Total Tasks</p>
                            <p class="mt-3 text-4xl font-black tracking-tight text-slate-900">{{ $totalCount }}</p>
                        </a>
                        <a href="{{ request()->fullUrlWithQuery(['filter' => 'done']) }}" class="block rounded-2xl border border-emerald-200 bg-emerald-50 p-5 text-emerald-900 shadow-[0_12px_28px_rgba(16,185,129,0.10)] transition hover:-translate-y-0.5 hover:ring-2 hover:ring-emerald-200">
                            <p class="text-[11px] font-black uppercase tracking-[0.18em] text-emerald-700">Completed</p>
                            <p class="mt-3 text-4xl font-black tracking-tight text-emerald-800">{{ $doneCount }}</p>
                        </a>
                        <a href="{{ request()->fullUrlWithQuery(['filter' => 'pending']) }}" class="block rounded-2xl border border-amber-200 bg-amber-50 p-5 text-amber-900 shadow-[0_12px_28px_rgba(245,158,11,0.10)] transition hover:-translate-y-0.5 hover:ring-2 hover:ring-amber-200">
                            <p class="text-[11px] font-black uppercase tracking-[0.18em] text-amber-700">Pending</p>
                            <p class="mt-3 text-4xl font-black tracking-tight text-amber-800">{{ $pendingCount }}</p>
                        </a>
                        <a href="{{ request()->fullUrlWithQuery(['filter' => 'rejected']) }}" class="block rounded-2xl border border-rose-200 bg-rose-50 p-5 text-rose-900 shadow-[0_12px_28px_rgba(244,63,94,0.10)] transition hover:-translate-y-0.5 hover:ring-2 hover:ring-rose-200">
                            <p class="text-[11px] font-black uppercase tracking-[0.18em] text-rose-700">Rejected</p>
                            <p class="mt-3 text-4xl font-black tracking-tight text-rose-800">{{ $rejectedCount }}</p>
                        </a>
                    </div>

                    @if((count($sem1_tasks ?? []) + count($sem2_tasks ?? [])) === 0)
                        <div class="bg-white rounded shadow p-12 text-center">
                            <h3 class="text-2xl font-bold mb-2">No Tasks Assigned Yet</h3>
                            <p class="text-gray-600">Your supervisor will assign tasks soon</p>
                        </div>
                    @else
                        <div class="mb-6 rounded-2xl border border-white/10 bg-black/10 px-5 pt-4 shadow-[0_10px_24px_rgba(0,0,0,0.18)]">
                            <div class="flex gap-6 border-b border-white/20">
                                <button onclick="showTab('sem1')" id="tab-sem1" class="inline-flex items-center gap-2 rounded-t-xl border-b-2 border-indigo-400 bg-white/10 px-4 py-3 text-sm font-black tracking-wide text-white transition-all duration-150">Semester 1 ({{ count($sem1_tasks) }})</button>
                                <button onclick="showTab('sem2')" id="tab-sem2" class="inline-flex items-center gap-2 rounded-t-xl border-b-2 border-transparent px-4 py-3 text-sm font-black tracking-wide text-slate-200 transition-all duration-150 hover:border-slate-300/60 hover:text-white">Semester 2 ({{ count($sem2_tasks) }})</button>
                            </div>
                        </div>

                        <div id="tab-sem1-content" class="grid grid-cols-3 gap-6">
                            @foreach($sem1_tasks as $task)
                                <div class="rounded-2xl border border-slate-200 bg-white p-6 text-slate-900 shadow-[0_12px_30px_rgba(15,23,42,0.10)] transition-all duration-150 hover:-translate-y-0.5 hover:shadow-[0_18px_36px_rgba(15,23,42,0.16)]">
                                    <div class="mb-5 border-b border-slate-100 pb-4">
                                        <h3 class="text-lg font-black tracking-tight text-slate-900">{{ $task['title'] }}</h3>
                                    </div>
                                    <div class="mb-4 space-y-1">
                                        <p class="text-[11px] font-black uppercase tracking-[0.16em] text-slate-500">Supervisor</p>
                                        <p class="text-sm font-semibold text-slate-800">{{ $task['assigned_by'] ?? 'Supervisor' }}</p>
                                    </div>
                                    @php
                                        $rawStatus = strtolower($task['status'] ?? 'pending');
                                        $uiStatus = in_array($rawStatus, ['approved', 'completed'], true) ? 'Done' : ($rawStatus === 'rejected' ? 'Rejected' : 'Pending');
                                        $uiClass = $uiStatus === 'Done' ? 'text-emerald-800' : ($uiStatus === 'Rejected' ? 'text-rose-800' : 'text-amber-800');
                                    @endphp
                                    <div class="mb-4 rounded-xl bg-slate-50 px-3 py-2">
                                        <p class="text-xs font-bold text-slate-700">Status: <span class="{{ $uiClass }} font-black">{{ $uiStatus }}</span></p>
                                    </div>
                                    @if(!empty($task['description']))
                                        <p class="mb-5 text-sm font-medium leading-6 text-slate-700">{{ Str::limit($task['description'], 100) }}</p>
                                    @endif
                                    @if(isset($task['due_date']))
                                        <p class="mb-5 text-xs font-semibold text-slate-700">Due: <span class="font-bold text-slate-900">{{ \Carbon\Carbon::parse($task['due_date'])->format('M d, Y') }}</span></p>
                                    @endif
                                    <a href="{{ route('student.tasks.show', $task['id']) }}" class="block w-full rounded bg-indigo-600 px-4 py-2 text-center font-bold text-white shadow-sm transition hover:bg-indigo-700">View Details</a>
                                </div>
                            @endforeach
                        </div>

                        <div id="tab-sem2-content" class="hidden grid grid-cols-3 gap-6">
                            @foreach($sem2_tasks as $task)
                                <div class="rounded-2xl border border-slate-200 bg-white p-6 text-slate-900 shadow-[0_12px_30px_rgba(15,23,42,0.10)] transition-all duration-150 hover:-translate-y-0.5 hover:shadow-[0_18px_36px_rgba(15,23,42,0.16)]">
                                    <div class="mb-5 border-b border-slate-100 pb-4">
                                        <h3 class="text-lg font-black tracking-tight text-slate-900">{{ $task['title'] }}</h3>
                                    </div>
                                    <div class="mb-4 space-y-1">
                                        <p class="text-[11px] font-black uppercase tracking-[0.16em] text-slate-500">Supervisor</p>
                                        <p class="text-sm font-semibold text-slate-800">{{ $task['assigned_by'] ?? 'Supervisor' }}</p>
                                    </div>
                                    @php
                                        $rawStatus = strtolower($task['status'] ?? 'pending');
                                        $uiStatus = in_array($rawStatus, ['approved', 'completed'], true) ? 'Done' : ($rawStatus === 'rejected' ? 'Rejected' : 'Pending');
                                        $uiClass = $uiStatus === 'Done' ? 'text-emerald-800' : ($uiStatus === 'Rejected' ? 'text-rose-800' : 'text-amber-800');
                                    @endphp
                                    <div class="mb-4 rounded-xl bg-slate-50 px-3 py-2">
                                        <p class="text-xs font-bold text-slate-700">Status: <span class="{{ $uiClass }} font-black">{{ $uiStatus }}</span></p>
                                    </div>
                                    @if(!empty($task['description']))
                                        <p class="mb-5 text-sm font-medium leading-6 text-slate-700">{{ Str::limit($task['description'], 100) }}</p>
                                    @endif
                                    @if(isset($task['due_date']))
                                        <p class="mb-5 text-xs font-semibold text-slate-700">Due: <span class="font-bold text-slate-900">{{ \Carbon\Carbon::parse($task['due_date'])->format('M d, Y') }}</span></p>
                                    @endif
                                    <a href="{{ route('student.tasks.show', $task['id']) }}" class="block w-full rounded bg-indigo-600 px-4 py-2 text-center font-bold text-white shadow-sm transition hover:bg-indigo-700">View Details</a>
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
            document.getElementById('tab-sem1').className = 'inline-flex items-center gap-2 rounded-t-xl border-b-2 border-transparent px-4 py-3 text-sm font-black tracking-wide text-slate-200 transition-all duration-150 hover:border-slate-300/60 hover:text-white';
            document.getElementById('tab-sem2').className = 'inline-flex items-center gap-2 rounded-t-xl border-b-2 border-transparent px-4 py-3 text-sm font-black tracking-wide text-slate-200 transition-all duration-150 hover:border-slate-300/60 hover:text-white';

            if (tab === 'sem1') {
                document.getElementById('tab-sem1-content').classList.remove('hidden');
                document.getElementById('tab-sem1').className = 'inline-flex items-center gap-2 rounded-t-xl border-b-2 border-indigo-400 bg-white/10 px-4 py-3 text-sm font-black tracking-wide text-white shadow-[inset_0_-1px_0_rgba(129,140,248,0.2)] transition-all duration-150';
            } else {
                document.getElementById('tab-sem2-content').classList.remove('hidden');
                document.getElementById('tab-sem2').className = 'inline-flex items-center gap-2 rounded-t-xl border-b-2 border-indigo-400 bg-white/10 px-4 py-3 text-sm font-black tracking-wide text-white shadow-[inset_0_-1px_0_rgba(129,140,248,0.2)] transition-all duration-150';
            }
        }
    </script>
</x-student-layout>
