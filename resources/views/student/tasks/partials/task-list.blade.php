@if($tasks->isEmpty())
    <div class="text-center py-12">
        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
        </svg>
        <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">No tasks found</h3>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
            @if(isset($semester) && $semester === '2nd')
                Second semester tasks will appear here soon.
            @else
                You don't have any tasks for this semester yet.
            @endif
        </p>
    </div>
@else
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-900/50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Task Title</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Due Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Submit Task</th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                @foreach($tasks as $task)
                    @php
                        $status = strtolower($task->status);
                        if ($status == 'pending' && $task->due_date && $task->due_date->isPast()) {
                            $status = 'missing';
                        }
                        
                        $statusClasses = match($status) {
                            'approved' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
                            'rejected' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
                            'missing' => 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200',
                            'submitted' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
                            default => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300', // Pending
                        };

                        $statusLabel = match($status) {
                            'missing' => 'Missing / Overdue',
                            default => ucfirst($status),
                        };
                    @endphp
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $task->title }}</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">{{ Str::limit($task->description, 50) }}</div>
                            
                            @if($task->status === 'rejected' && ($task->supervisor_note || $task->supervisor_attachment_path))
                                <div class="mt-2 p-2 bg-red-50 dark:bg-red-900/30 rounded border border-red-100 dark:border-red-800">
                                    <p class="text-xs font-bold text-red-600 dark:text-red-400 mb-1">Supervisor Feedback:</p>
                                    @if($task->supervisor_note)
                                        <p class="text-xs text-black dark:text-gray-100 font-medium">{{ $task->supervisor_note }}</p>
                                    @endif
                                    @if($task->supervisor_attachment_path)
                                        <div class="mt-1">
                                            <a href="{{ Storage::url($task->supervisor_attachment_path) }}" target="_blank" class="text-xs text-blue-600 hover:text-blue-800 flex items-center gap-1">
                                                {{ $task->supervisor_original_filename ?? 'View Attachment' }}
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                            @if($task->due_date)
                                <div class="{{ $task->due_date->isPast() && $status !== 'submitted' && $status !== 'approved' ? 'text-red-500 font-bold' : '' }}">
                                    {{ $task->due_date->format('M d, Y') }}
                                </div>
                            @else
                                -
                            @endif

                            @if($task->submitted_at)
                                <div class="text-xs text-blue-600 dark:text-blue-400 mt-1">
                                    <span class="font-bold">Submitted:</span>
                                    {{ $task->submitted_at->format('h:i A') }}
                                </div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusClasses }}">
                                {{ $statusLabel }}
                            </span>
                            @if($task->grade)
                                <div class="text-xs font-bold text-gray-600 dark:text-gray-400 mt-1">
                                    Grade: {{ $task->grade }}
                                </div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <button class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300">View Details</button>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            @if($status === 'pending' || $status === 'in_progress' || $status === 'missing' || $status === 'rejected')
                                <form action="{{ route('student.tasks.submit', $task) }}" method="POST" class="inline" enctype="multipart/form-data">
                                    @csrf
                                    <div class="flex flex-col gap-2" x-data="{ fileName: '' }">
                                        <div class="relative">
                                            <input type="file" name="attachment" id="file-{{ $task->id }}" class="hidden" required 
                                                @change="fileName = $event.target.files.length > 0 ? $event.target.files[0].name : ''">
                                            <label for="file-{{ $task->id }}" class="cursor-pointer inline-flex items-center px-3 py-1 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 rounded text-xs transition-colors">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                                                </svg>
                                                Attach File
                                            </label>
                                            <span x-text="fileName" class="text-[10px] text-gray-500 dark:text-gray-400 ml-2 truncate max-w-[100px] inline-block align-middle"></span>
                                        </div>
                                        <button type="submit" class="text-white bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-xs px-3 py-1.5 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800 transition-colors w-fit">
                                            Submit Task
                                        </button>
                                    </div>
                                </form>
                            @endif

                            @if($status === 'submitted')
                                <div class="flex flex-col gap-2">
                                    @if($task->original_filename)
                                        <div class="text-xs text-gray-500 dark:text-gray-400 flex items-center">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                            {{ Str::limit($task->original_filename, 20) }}
                                        </div>
                                    @endif
                                    <form action="{{ route('student.tasks.unsubmit', $task) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="text-white bg-gray-600 hover:bg-gray-700 focus:ring-4 focus:ring-gray-300 font-medium rounded-lg text-xs px-3 py-1.5 dark:bg-gray-600 dark:hover:bg-gray-700 focus:outline-none dark:focus:ring-gray-800 transition-colors">
                                            Unsubmit
                                        </button>
                                    </form>
                                </div>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif
