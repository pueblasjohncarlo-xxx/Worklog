<x-supervisor-layout>
    <x-slot name="header">
        Supervisor Dashboard
    </x-slot>

    <div class="space-y-8">
        <!-- Top Action Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Approvals Card -->
            <a href="#approvals" class="bg-white rounded-3xl shadow-lg p-6 flex flex-col items-center justify-center group hover:shadow-xl transition-all duration-300">
                <div class="mb-3 p-3 rounded-full bg-gray-50 group-hover:bg-gray-100 transition-colors">
                    <svg class="h-8 w-8 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <span class="text-lg font-bold text-gray-800">Approvals</span>
            </a>

            <!-- Assign New Task Card -->
            <a href="{{ route('supervisor.tasks.create') }}" class="bg-gradient-to-r from-purple-600 to-indigo-600 rounded-3xl shadow-lg p-6 flex flex-col items-center justify-center group hover:shadow-xl hover:scale-[1.02] transition-all duration-300">
                <div class="mb-3 p-3 rounded-full bg-white/20 group-hover:bg-white/30 transition-colors">
                    <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                    </svg>
                </div>
                <span class="text-lg font-bold text-white">Assign New Task</span>
            </a>

            <!-- Performance Evaluation Card -->
            <a href="{{ route('supervisor.evaluations.index') }}" class="bg-white rounded-3xl shadow-lg p-6 flex flex-col items-center justify-center group hover:shadow-xl transition-all duration-300">
                <div class="mb-3 p-3 rounded-full bg-gray-50 group-hover:bg-gray-100 transition-colors">
                    <svg class="h-8 w-8 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <span class="text-lg font-bold text-gray-800">Performance Evaluation</span>
            </a>
        </div>

        <!-- Charts Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Task Completion Doughnut Chart -->
            <div class="bg-white rounded-xl shadow-md border border-gray-100 p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                    <svg class="h-5 w-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                    </svg>
                    Team Task Status
                </h3>
                <div class="relative h-[250px]">
                    <canvas id="taskCompletionChart"
                        data-labels="{{ json_encode(array_keys($taskStats)) }}"
                        data-values="{{ json_encode(array_values($taskStats)) }}">
                    </canvas>
                </div>
            </div>

            <!-- Weekly Hours Line Chart -->
            <div class="bg-white rounded-xl shadow-md border border-gray-100 p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                    <svg class="h-5 w-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Total Approved Hours (7 Days)
                </h3>
                <div class="relative h-[250px]">
                    <canvas id="weeklyHoursChart"
                        data-labels="{{ json_encode($weeklyHours->pluck('day')) }}"
                        data-values="{{ json_encode($weeklyHours->pluck('total_hours')) }}">
                    </canvas>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-100">
            <div class="bg-white px-6 py-4 border-b border-gray-100">
                <h3 class="font-bold text-gray-800 text-lg">Assigned Student Progress</h3>
            </div>
            <div class="p-6 bg-gray-50/50">
                @if($assignments->isEmpty())
                    <p class="text-gray-500 font-medium">No active assignments.</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm">
                            <thead class="border-b border-gray-200">
                                <tr>
                                    <th class="py-3 font-bold text-gray-600 uppercase tracking-wider">OJT Student</th>
                                    <th class="py-3 font-bold text-gray-600 uppercase tracking-wider">Company</th>
                                    <th class="py-3 font-bold text-gray-600 uppercase tracking-wider">Progress</th>
                                    <th class="py-3 font-bold text-gray-600 uppercase tracking-wider text-right">Hours</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @foreach($assignments as $assignment)
                                    @php
                                        $hoursCompleted = $assignment->totalApprovedHours();
                                        $progress = $assignment->progressPercentage();
                                    @endphp
                                    <tr class="group hover:bg-gray-50 transition-colors">
                                        <td class="py-4 font-bold text-gray-800">{{ $assignment->student->name }}</td>
                                        <td class="py-4 text-gray-600">{{ $assignment->company->name ?? '-' }}</td>
                                        <td class="py-4">
                                            <div class="flex items-center gap-3">
                                                <div class="w-full bg-gray-200 rounded-full h-2 overflow-hidden">
                                                    <div class="bg-indigo-600 h-2 rounded-full" @style(['width: ' . $progress . '%'])></div>
                                                </div>
                                                <div class="text-xs font-bold text-gray-700 whitespace-nowrap">{{ $progress }}%</div>
                                            </div>
                                        </td>
                                        <td class="py-4 text-right">
                                            <span class="font-bold text-gray-800">{{ (int) $hoursCompleted }}</span>
                                            <span class="text-gray-500 text-xs">/ {{ $assignment->required_hours }}</span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>

        <div id="approvals" class="space-y-6">
            <!-- Hours Approvals (White Header) -->
            <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-100">
                <div class="bg-white px-6 py-4 border-b border-gray-100">
                    <h3 class="font-bold text-gray-800 text-lg">Hours Approvals</h3>
                </div>
                <div class="p-6 bg-gray-50/50">
                    @if($pendingHoursLogs->isEmpty())
                        <p class="text-gray-500 font-medium">No pending hours entries.</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-sm">
                                <thead class="border-b border-gray-200">
                                    <tr>
                                        <th class="py-3 font-bold text-gray-600 uppercase tracking-wider">OJT Student</th>
                                        <th class="py-3 font-bold text-gray-600 uppercase tracking-wider">Date</th>
                                        <th class="py-3 font-bold text-gray-600 uppercase tracking-wider">Hours</th>
                                        <th class="py-3 font-bold text-gray-600 uppercase tracking-wider">Description</th>
                                        <th class="py-3 font-bold text-gray-600 uppercase tracking-wider text-right">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    @foreach($pendingHoursLogs as $log)
                                        <tr class="group hover:bg-gray-50 transition-colors">
                                            <td class="py-4 font-bold text-gray-800">
                                                {{ $log->assignment->student->name }}
                                            </td>
                                            <td class="py-4 text-gray-600">{{ $log->work_date->format('M d, Y') }}</td>
                                            <td class="py-4 font-mono text-indigo-600 font-bold">{{ number_format($log->hours, 2) }}</td>
                                            <td class="py-4 text-gray-500 italic truncate max-w-xs">{{ $log->description }}</td>
                                            <td class="py-4 text-right space-x-2">
                                                <form action="{{ route('supervisor.worklogs.approve', $log) }}" method="POST" class="inline">
                                                    @csrf
                                                    <button type="submit" class="inline-flex items-center rounded-lg bg-emerald-700 px-3 py-2 text-xs font-bold text-white shadow-sm transition hover:bg-emerald-800 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2">Approve</button>
                                                </form>
                                                <form action="{{ route('supervisor.worklogs.reject', $log) }}" method="POST" class="inline">
                                                    @csrf
                                                    <button type="submit" class="inline-flex items-center rounded-lg bg-rose-700 px-3 py-2 text-xs font-bold text-white shadow-sm transition hover:bg-rose-800 focus:outline-none focus:ring-2 focus:ring-rose-500 focus:ring-offset-2">Reject</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Attendance Approvals (Amber Header) -->
            <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-100">
                <div class="bg-amber-400 px-6 py-4">
                    <h3 class="font-bold text-white text-lg">Attendance Approvals</h3>
                </div>
                <div class="p-6 bg-white">
                    @if($pendingAttendanceLogs->isEmpty())
                        <p class="text-gray-500 font-medium">No pending attendance approvals.</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-sm">
                                <thead class="border-b border-gray-200">
                                    <tr>
                                        <th class="py-3 font-bold text-gray-600 uppercase tracking-wider">OJT Student</th>
                                        <th class="py-3 font-bold text-gray-600 uppercase tracking-wider">Date</th>
                                        <th class="py-3 font-bold text-gray-600 uppercase tracking-wider">Time In</th>
                                        <th class="py-3 font-bold text-gray-600 uppercase tracking-wider">Time Out</th>
                                        <th class="py-3 font-bold text-gray-600 uppercase tracking-wider">Hours</th>
                                        <th class="py-3 font-bold text-gray-600 uppercase tracking-wider text-right">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    @foreach($pendingAttendanceLogs as $log)
                                        <tr class="group hover:bg-gray-50 transition-colors">
                                            <td class="py-4 font-bold text-gray-800">
                                                {{ $log->assignment->student->name }}
                                            </td>
                                            <td class="py-4 text-gray-600">{{ $log->work_date->format('M d, Y') }}</td>
                                            <td class="py-4 text-gray-600">{{ $log->time_in ? $log->time_in->format('h:i A') : '-' }}</td>
                                            <td class="py-4 text-gray-600">{{ $log->time_out ? $log->time_out->format('h:i A') : '-' }}</td>
                                            <td class="py-4 font-mono text-indigo-600 font-bold">{{ number_format($log->hours, 2) }}</td>
                                            <td class="py-4 text-right space-x-2">
                                                <form action="{{ route('supervisor.worklogs.approve', $log) }}" method="POST" class="inline">
                                                    @csrf
                                                    <button type="submit" class="inline-flex items-center rounded-lg bg-emerald-700 px-3 py-2 text-xs font-bold text-white shadow-sm transition hover:bg-emerald-800 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2">Approve</button>
                                                </form>
                                                <form action="{{ route('supervisor.worklogs.reject', $log) }}" method="POST" class="inline">
                                                    @csrf
                                                    <button type="submit" class="inline-flex items-center rounded-lg bg-rose-700 px-3 py-2 text-xs font-bold text-white shadow-sm transition hover:bg-rose-800 focus:outline-none focus:ring-2 focus:ring-rose-500 focus:ring-offset-2">Reject</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Task Reviews (Blue Header) -->
            <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-100">
                <div class="bg-blue-600 px-6 py-4">
                    <h3 class="font-bold text-white text-lg">Task Reviews</h3>
                </div>
                <div class="p-6 bg-white">
                    @if($pendingTaskReviews->isEmpty())
                        <p class="text-gray-500 font-medium">No tasks pending review.</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-sm">
                                <thead class="border-b border-gray-200">
                                    <tr>
                                        <th class="py-3 font-black text-black uppercase tracking-wider">OJT Student</th>
                                        <th class="py-3 font-black text-black uppercase tracking-wider">Task</th>
                                        <th class="py-3 font-black text-black uppercase tracking-wider">Status</th>
                                        <th class="py-3 font-black text-black uppercase tracking-wider text-right">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    @foreach($pendingTaskReviews as $task)
                                        <tr class="group hover:bg-gray-50 transition-colors">
                                            <td class="py-4 font-bold text-black">
                                                {{ $assignment->student->name }}
                                            </td>
                                            <td class="py-4 text-black font-medium">
                                                <div class="font-bold">{{ $task->title }}</div>
                                                @if($task->submitted_at)
                                                    <div class="text-xs text-blue-800 font-bold mt-1 flex items-center gap-1">
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                        </svg>
                                                        Submitted: {{ $task->submitted_at->format('M d, Y h:i A') }}
                                                    </div>
                                                @endif
                                            </td>
                                            <td class="py-4">
                                                <x-status-badge :status="$task->status" size="sm" />
                                            </td>
                                            <td class="py-4 text-right space-x-2">
                                                <div class="flex items-center justify-end gap-2" x-data="{ showRejectModal: false, showFeedbackModal: false }">
                                                    <form action="{{ route('supervisor.tasks.approve', $task) }}" method="POST" class="flex items-center gap-2">
                                                        @csrf
                                                        <div class="flex items-center gap-1">
                                                            <input type="text" name="grade" list="grades-{{ $task->id }}" placeholder="Score" required
                                                                class="w-24 px-2 py-1 text-xs border border-gray-300 rounded focus:ring-emerald-500 focus:border-emerald-500 text-center font-bold text-gray-900 bg-white placeholder-gray-500" 
                                                                title="Enter Grade/Score">
                                                            <datalist id="grades-{{ $task->id }}">
                                                                <option value="10/10">
                                                                <option value="50/50">
                                                                <option value="100/100">
                                                            </datalist>
                                                            <button type="submit" class="px-3 py-1 bg-emerald-600 text-white border border-emerald-600 rounded text-xs font-bold hover:bg-emerald-700 transition-colors shadow-sm">
                                                                Approve
                                                            </button>
                                                        </div>
                                                    </form>
                                                    
                                                    <!-- Feedback Button (Yellow/Orange) -->
                                                    <button @click="showFeedbackModal = true" type="button" class="px-3 py-2 bg-amber-600 text-white border border-amber-600 rounded text-xs font-bold hover:bg-amber-700 transition-colors shadow-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:ring-offset-2">
                                                        Notes
                                                    </button>

                                                    <!-- Feedback/Notes Modal -->
                                                    <div x-show="showFeedbackModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
                                                        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                                                            <div x-show="showFeedbackModal" @click="showFeedbackModal = false" class="fixed inset-0 transition-opacity" aria-hidden="true">
                                                                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
                                                            </div>
                                                            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                                                            <div x-show="showFeedbackModal" class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                                                                <form action="{{ route('supervisor.tasks.reject', $task) }}" method="POST" enctype="multipart/form-data"> 
                                                                    @csrf
                                                                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                                                                        <div class="sm:flex sm:items-start">
                                                                            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-amber-100 sm:mx-0 sm:h-10 sm:w-10">
                                                                                <svg class="h-6 w-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                                                </svg>
                                                                            </div>
                                                                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                                                                <h3 class="text-lg leading-6 font-medium text-gray-900">
                                                                                    Note for Feedback
                                                                                </h3>
                                                                                <div class="mt-2">
                                                                                    <div class="space-y-4">
                                                                                        <div>
                                                                                            <label class="block text-sm font-bold text-black">Note to Student</label>
                                                                                            <textarea name="note" rows="6" maxlength="1000" class="mt-1 block w-full rounded-md border-gray-400 shadow-sm focus:border-amber-500 focus:ring-amber-500 sm:text-sm text-black placeholder-gray-600 font-medium" placeholder="Explain what needs to be corrected..."></textarea>
                                                                                            <p class="mt-1 text-xs text-black font-bold text-right">Max 1000 char</p>
                                                                                        </div>
                                                                                        <div>
                                                                                            <label class="block text-sm font-bold text-black">Attach File (Optional)</label>
                                                                                            <input type="file" name="attachment" class="mt-1 block w-full text-sm text-gray-800 font-medium file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-amber-100 file:text-amber-800 hover:file:bg-amber-200 cursor-pointer">
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                                                                        <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-amber-600 text-base font-medium text-white hover:bg-amber-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500 sm:ml-3 sm:w-auto sm:text-sm">
                                                                            Send Feedback
                                                                        </button>
                                                                        <button @click="showFeedbackModal = false" type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                                                            Cancel
                                                                        </button>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>

             <!-- Recent Tasks Assigned (White Header) -->
             <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-100">
                <div class="bg-white px-6 py-4 border-b border-gray-100">
                    <h3 class="font-bold text-gray-800 text-lg">Recent Tasks Assigned</h3>
                </div>
                <div class="p-6 bg-white">
                    @if($recentTasks->isEmpty())
                        <p class="text-gray-500 font-medium">No recent tasks assigned.</p>
                    @else
                         <div class="overflow-x-auto">
                            <table class="w-full text-left text-sm">
                                <thead class="border-b border-gray-200">
                                    <tr>
                                        <th class="py-3 font-black text-black uppercase tracking-wider">Title</th>
                                        <th class="py-3 font-black text-black uppercase tracking-wider">Student</th>
                                        <th class="py-3 font-black text-black uppercase tracking-wider">Due Date</th>
                                        <th class="py-3 font-black text-black uppercase tracking-wider">Status</th>
                                        <th class="py-3 font-black text-black uppercase tracking-wider text-right">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    @foreach($recentTasks as $task)
                                        <tr class="group hover:bg-gray-50 transition-colors">
                                            <td class="py-4 font-black text-black">
                                                {{ $task->title }}
                                                @if($task->grade)
                                                    <div class="text-xs text-emerald-800 font-black mt-1">Grade: {{ $task->grade }}</div>
                                                @endif
                                            </td>
                                            <td class="py-4 text-black font-bold">
                                                <div class="flex items-center gap-2">
                                                    <span>{{ $task->assignment->student->name }}</span>
                                                    <x-user-role-badge role="student" />
                                                </div>
                                            </td>
                                            <td class="py-4 text-black font-medium">{{ $task->due_date ? $task->due_date->format('M d, Y') : '-' }}</td>
                                            <td class="py-4">
                                                @php
                                                    $taskStatusLabel = match ($task->status) {
                                                        'in_progress' => 'In Progress',
                                                        'approved' => 'Approved',
                                                        'submitted' => 'Submitted',
                                                        'rejected' => 'Rejected',
                                                        'completed' => 'Completed',
                                                        default => ucwords(str_replace('_', ' ', (string) $task->status)),
                                                    };
                                                @endphp
                                                <div class="inline-flex min-w-[9.5rem] flex-col items-start gap-1 rounded-xl border border-slate-300 bg-slate-50 px-3 py-2 shadow-sm">
                                                    <span class="text-[10px] font-black uppercase tracking-[0.16em] text-slate-600">Task Status</span>
                                                    <x-status-badge :status="$task->status" :label="$taskStatusLabel" size="sm" class="shadow-sm ring-1 ring-slate-400/70" />
                                                </div>
                                            </td>
                                            <td class="py-4 text-right">
                                                @if($task->status === 'approved')
                                                    <form action="{{ route('supervisor.tasks.unapprove', $task) }}" method="POST" class="inline">
                                                        @csrf
                                                        <button type="submit" class="inline-flex items-center rounded-lg bg-rose-700 px-3 py-2 text-xs font-bold uppercase tracking-wide text-white shadow-sm transition hover:bg-rose-800 focus:outline-none focus:ring-2 focus:ring-rose-500 focus:ring-offset-2" onclick="return confirm('Are you sure you want to cancel the approval for this task?');">
                                                            Cancel Approval
                                                        </button>
                                                    </form>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Task Completion Chart
            const taskCanvas = document.getElementById('taskCompletionChart');
            const taskLabels = JSON.parse(taskCanvas.dataset.labels);
            const taskData = JSON.parse(taskCanvas.dataset.values);

            const taskCtx = taskCanvas.getContext('2d');
            new Chart(taskCtx, {
                type: 'doughnut',
                data: {
                    labels: taskLabels,
                    datasets: [{
                        data: taskData,
                        backgroundColor: taskLabels.map(label => window.getWorklogChartColor(label, '#6366f1')),
                        borderWidth: 0,
                        hoverOffset: 10
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                padding: 20,
                                font: { size: 12, weight: 'bold' }
                            }
                        }
                    },
                    cutout: '65%'
                }
            });

            // Weekly Hours Chart
            const hoursCanvas = document.getElementById('weeklyHoursChart');
            const weeklyHoursLabels = JSON.parse(hoursCanvas.dataset.labels);
            const weeklyHoursData = JSON.parse(hoursCanvas.dataset.values);

            const hoursCtx = hoursCanvas.getContext('2d');
            const hoursGradient = hoursCtx.createLinearGradient(0, 0, 0, 250);
            hoursGradient.addColorStop(0, 'rgba(16, 185, 129, 0.2)');
            hoursGradient.addColorStop(1, 'rgba(16, 185, 129, 0)');

            new Chart(hoursCtx, {
                type: 'line',
                data: {
                    labels: weeklyHoursLabels,
                    datasets: [{
                        label: 'Approved Hours',
                        data: weeklyHoursData,
                        borderColor: '#10b981',
                        backgroundColor: hoursGradient,
                        fill: true,
                        tension: 0.4,
                        borderWidth: 3,
                        pointBackgroundColor: '#10b981',
                        pointRadius: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { color: 'rgba(0, 0, 0, 0.05)' }
                        },
                        x: {
                            grid: { display: false }
                        }
                    }
                }
            });
        });
    </script>
</x-supervisor-layout>
