<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">My Tasks</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div id="status" class="mb-4 p-4 bg-blue-50 dark:bg-blue-900/20 rounded text-sm">Loading...</div>
                    <div id="content"></div>
                </div>
            </div>
        </div>
    </div>

    <script type="text/javascript">
        const data = @json(['s1' => $sem1_tasks ?? [], 's2' => $sem2_tasks ?? [], 'a' => $assignment ?? null]);
        document.addEventListener('DOMContentLoaded', start);

        function start() {
            const s1 = data.s1 || [];
            const s2 = data.s2 || [];
            const a = data.a;
            document.getElementById('status').innerHTML = '<strong>Tasks:</strong> ' + (s1.length + s2.length);
            let html = '';
            if (!a) html = '<div class="text-center text-gray-500 py-16">No assignment</div>';
            else if (s1.length + s2.length === 0) html = '<div class="text-center text-gray-500 py-16">No tasks</div>';
            else {
                html = '<div><div class="flex gap-2 border-b pb-3">';
                html += '<button onclick="sw(1)" id="b1" class="px-4 py-2 font-bold border-b-2 border-indigo-600 text-indigo-600">1st (' + s1.length + ')</button>';
                html += '<button onclick="sw(2)" id="b2" class="px-4 py-2 font-bold text-gray-600">2nd (' + s2.length + ')</button>';
                html += '</div><div id="t1">' + gt(s1) + '</div><div id="t2" class="hidden">' + gt(s2) + '</div></div>';
            }
            document.getElementById('content').innerHTML = html;
        }

        function gt(ts) {
            if (!ts || ts.length === 0) return '<p class="text-gray-500">No tasks</p>';
            let h = '<table class="w-full text-sm"><tr class="bg-gray-100"><th class="px-3 py-2 text-left">Task</th><th class="px-3 py-2">Due</th><th class="px-3 py-2">Status</th><th class="px-3 py-2">Action</th></tr>';
            for (let i = 0; i < ts.length; i++) {
                const o = ts[i];
                const d = o.due_date ? new Date(o.due_date).toLocaleDateString() : 'N/A';
                h += '<tr><td class="px-3 py-2">' + o.title + '</td><td class="px-3 py-2">' + d + '</td><td class="px-3 py-2">' + o.status + '</td><td class="px-3 py-2"><a href="/student/tasks/' + o.id + '" class="text-blue-600">View</a></td></tr>';
            }
            return h + '</table>';
        }

        function sw(n) {
            document.getElementById('t1').className = n === 1 ? '' : 'hidden';
            document.getElementById('t2').className = n === 2 ? '' : 'hidden';
            document.getElementById('b1').className = n === 1 ? 'px-4 py-2 font-bold border-b-2 border-indigo-600 text-indigo-600' : 'px-4 py-2 font-bold text-gray-600';
            document.getElementById('b2').className = n === 2 ? 'px-4 py-2 font-bold border-b-2 border-indigo-600 text-indigo-600' : 'px-4 py-2 font-bold text-gray-600';
        }
    </script>
</x-app-layout>
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">My Tasks</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div id="status" class="mb-4 p-4 bg-blue-50 dark:bg-blue-900/20 rounded text-sm">Loading...</div>
                    <div id="content"></div>
                </div>
            </div>
        </div>
    </div>

    <script type="text/javascript">
        const taskData = @json(['s1' => $sem1_tasks ?? [], 's2' => $sem2_tasks ?? [], 'a' => $assignment ?? null]);
        document.addEventListener('DOMContentLoaded', render);
        function render() {
            const s1 = taskData.s1 || [];
            const s2 = taskData.s2 || [];
            const a = taskData.a;
            const t = s1.length + s2.length;
            document.getElementById('status').innerHTML = '<strong>Assignment:</strong> ' + (a ? 'Loaded' : 'None') + ' | <strong>Tasks:</strong> ' + t;
            let html = '';
            if (!a) { html = '<div class="text-center text-gray-500 py-16">No assignment</div>'; }
            else if (t === 0) { html = '<div class="text-center text-gray-500 py-16">No tasks</div>'; }
            else {
                html = '<div class="space-y-4"><div class="flex gap-2 border-b pb-3"><button onclick="tab(1)" id="b1" class="px-4 py-2 font-bold border-b-2 border-indigo-600 text-indigo-600">1st (' + s1.length + ')</button>';
                html += '<button onclick="tab(2)" id="b2" class="px-4 py-2 font-bold text-gray-600">2nd (' + s2.length + ')</button></div>';
                html += '<div id="t1">' + tbl(s1) + '</div><div id="t2" class="hidden">' + tbl(s2) + '</div></div>';
            }
            document.getElementById('content').innerHTML = html;
        }
        function tbl(tasks) {
            if (!tasks || tasks.length === 0) return '<p class="text-gray-500">No tasks</p>';
            let h = '<table class="w-full text-sm border"><tr class="bg-gray-100"><th class="px-3 py-2">Task</th><th class="px-3 py-2">Due</th><th class="px-3 py-2">Status</th><th class="px-3 py-2">Action</th></tr>';
            for (let i = 0; i < tasks.length; i++) {
                const x = tasks[i];
                const d = x.due_date ? new Date(x.due_date).toLocaleDateString() : 'N/A';
                h += '<tr class="border-t"><td class="px-3 py-2">' + x.title + '</td><td class="px-3 py-2">' + d + '</td><td class="px-3 py-2">' + x.status + '</td><td class="px-3 py-2"><a href="/student/tasks/' + x.id + '" class="text-blue-600">View</a></td></tr>';
            }
            h += '</table>';
            return h;
        }
        function tab(n) {
            document.getElementById('t1').className = n === 1 ? '' : 'hidden';
            document.getElementById('t2').className = n === 2 ? '' : 'hidden';
            document.getElementById('b1').className = n === 1 ? 'px-4 py-2 font-bold border-b-2 border-indigo-600 text-indigo-600' : 'px-4 py-2 font-bold text-gray-600';
            document.getElementById('b2').className = n === 2 ? 'px-4 py-2 font-bold border-b-2 border-indigo-600 text-indigo-600' : 'px-4 py-2 font-bold text-gray-600';
        }
    </script>
</x-app-layout>
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">My Tasks</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div id="status" class="mb-4 p-4 bg-blue-50 dark:bg-blue-900/20 rounded text-sm">Loading...</div>
                    <div id="content"></div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const taskData = @json(['sem1' => $sem1_tasks ?? [], 'sem2' => $sem2_tasks ?? [], 'assign' => $assignment ?? null]);
        
        document.addEventListener('DOMContentLoaded', function() {
            const s1 = taskData.sem1 || [];
            const s2 = taskData.sem2 || [];
            const assign = taskData.assign;
            const total = s1.length + s2.length;

            document.getElementById('status').innerHTML = '<strong>Assignment:</strong> ' + (assign ? 'Loaded' : 'None') + ' | <strong>Tasks:</strong> ' + total;

            let html = '';
            if (!assign) {
                html = '<div class="text-center text-gray-500 py-16">No active assignment</div>';
            } else if (total === 0) {
                html = '<div class="text-center text-gray-500 py-16">No tasks assigned</div>';
            } else {
                html = '<div class="space-y-4">';
                html += '<div class="flex gap-2 border-b pb-3">';
                html += '<button onclick="showTab(1)" id="btn1" class="px-4 py-2 font-bold border-b-2 border-indigo-600 text-indigo-600">1st (' + s1.length + ')</button>';
                html += '<button onclick="showTab(2)" id="btn2" class="px-4 py-2 font-bold text-gray-600">2nd (' + s2.length + ')</button>';
                html += '</div>';
                html += '<div id="tab1">' + makeTable(s1) + '</div>';
                html += '<div id="tab2" class="hidden">' + makeTable(s2) + '</div>';
                html += '</div>';
            }
            document.getElementById('content').innerHTML = html;
        });

        function makeTable(tasks) {
            if (!tasks || tasks.length === 0) return '<p class="text-gray-500">No tasks</p>';
            let h = '<div class="overflow-x-auto"><table class="w-full text-sm border"><tr class="bg-gray-100"><th class="px-3 py-2 text-left">Task</th><th class="px-3 py-2">Due</th><th class="px-3 py-2">Status</th><th class="px-3 py-2">Action</th></tr>';
            tasks.forEach(t => {
                const d = t.due_date ? new Date(t.due_date).toLocaleDateString() : 'N/A';
                h += '<tr class="border-t"><td class="px-3 py-2">' + t.title + '</td><td class="px-3 py-2">' + d + '</td><td class="px-3 py-2">' + (t.status || 'pending') + '</td><td class="px-3 py-2"><a href="/student/tasks/' + t.id + '" class="text-blue-600">View</a></td></tr>';
            });
            h += '</table></div>';
            return h;
        }

        function showTab(n) {
            document.getElementById('tab1').classList.add('hidden');
            document.getElementById('tab2').classList.add('hidden');
            document.getElementById('btn1').classList.remove('border-b-2', 'border-indigo-600', 'text-indigo-600');
            document.getElementById('btn2').classList.remove('border-b-2', 'border-indigo-600', 'text-indigo-600');
            document.getElementById('btn1').classList.add('text-gray-600');
            document.getElementById('btn2').classList.add('text-gray-600');
            if (n === 1) {
                document.getElementById('tab1').classList.remove('hidden');
                document.getElementById('btn1').classList.add('border-b-2', 'border-indigo-600', 'text-indigo-600');
                document.getElementById('btn1').classList.remove('text-gray-600');
            } else {
                document.getElementById('tab2').classList.remove('hidden');
                document.getElementById('btn2').classList.add('border-b-2', 'border-indigo-600', 'text-indigo-600');
                document.getElementById('btn2').classList.remove('text-gray-600');
            }
        }
    </script>
</x-app-layout>
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            📋 My Tasks
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    
                    <!-- Status Display -->
                    <div class="mb-4 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800 text-sm">
                        <p class="text-blue-800 dark:text-blue-300"><strong>Status:</strong> <span id="assignmentStatus">Loading...</span> | <strong>Tasks:</strong> <span id="tasksCount">0</span></p>
                    </div>

                    <!-- Content Area -->
                    <div id="tasksContent"></div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            try {
                const sem1_tasks = @json($sem1_tasks ?? []);
                const sem2_tasks = @json($sem2_tasks ?? []);
                const assignment = @json($assignment ?? null);

                const totalTasks = sem1_tasks.length + sem2_tasks.length;

                // Update status
                if (assignment) {
                    document.getElementById('assignmentStatus').textContent = 'Assignment Loaded';
                } else {
                    document.getElementById('assignmentStatus').textContent = 'No Assignment';
                }
                document.getElementById('tasksCount').textContent = totalTasks;

                const contentDiv = document.getElementById('tasksContent');
                
                if (!assignment) {
                    contentDiv.innerHTML = `<div class="text-center py-16 text-gray-500"><p>No active assignment</p></div>`;
                    return;
                }

                if (totalTasks === 0) {
                    contentDiv.innerHTML = `<div class="text-center py-16 text-gray-500"><p>No tasks assigned</p></div>`;
                    return;
                }

                // Build HTML for tasks
                let html = `
                    <div class="space-y-6">
                        <div class="flex gap-4 border-b border-gray-200 dark:border-gray-700">
                            <button onclick="showSemester('1st')" class="px-4 py-3 font-medium border-b-2 border-indigo-600 text-indigo-600 dark:text-indigo-400">
                                1st Semester (${sem1_tasks.length})
                            </button>
                            <button onclick="showSemester('2nd')" class="px-4 py-3 font-medium text-gray-600 dark:text-gray-400">
                                2nd Semester (${sem2_tasks.length})
                            </button>
                        </div>
                        <div id="sem1Container">${renderTasks(sem1_tasks)}</div>
                        <div id="sem2Container" class="hidden">${renderTasks(sem2_tasks)}</div>
                    </div>
                `;
                contentDiv.innerHTML = html;

                console.log('Page loaded:', { assignment: true, sem1: sem1_tasks.length, sem2: sem2_tasks.length });
            } catch (error) {
                console.error('Error:', error);
                document.getElementById('tasksContent').innerHTML = '<div class="text-red-600">Error loading tasks</div>';
            }
        });

        function renderTasks(tasks) {
            if (!tasks || tasks.length === 0) {
                return '<div class="text-center py-8 text-gray-500">No tasks</div>';
            }

            let html = '<div class="overflow-x-auto"><table class="w-full text-sm"><thead class="bg-gray-100 dark:bg-gray-700"><tr><th class="px-4 py-3 text-left">Task</th><th class="px-4 py-3 text-left">Due Date</th><th class="px-4 py-3 text-left">Status</th><th class="px-4 py-3 text-left">Action</th></tr></thead><tbody>';
            
            tasks.forEach(task => {
                const dueDate = task.due_date ? new Date(task.due_date).toLocaleDateString() : 'N/A';
                const status = task.status === 'pending' ? '⏳ Pending' : task.status;
                html += `<tr class="border-t"><td class="px-4 py-3">${task.title}</td><td class="px-4 py-3">${dueDate}</td><td class="px-4 py-3">${status}</td><td class="px-4 py-3"><a href="/student/tasks/${task.id}" class="text-indigo-600">View</a></td></tr>`;
            });

            html += '</tbody></table></div>';
            return html;
        }

        function showSemester(sem) {
            const s1 = document.getElementById('sem1Container');
            const s2 = document.getElementById('sem2Container');
            const b = document.querySelectorAll('button[onclick^="showSemester"]');
            
            if (sem === '1st') {
                s1.classList.remove('hidden');
                s2.classList.add('hidden');
                b[0].classList.add('border-b-2', 'border-indigo-600', 'text-indigo-600', 'dark:text-indigo-400');
                b[0].classList.remove('text-gray-600', 'dark:text-gray-400');
                b[1].classList.remove('border-b-2', 'border-indigo-600', 'text-indigo-600', 'dark:text-indigo-400');
                b[1].classList.add('text-gray-600', 'dark:text-gray-400');
            } else {
                s1.classList.add('hidden');
                s2.classList.remove('hidden');
                b[1].classList.add('border-b-2', 'border-indigo-600', 'text-indigo-600', 'dark:text-indigo-400');
                b[1].classList.remove('text-gray-600', 'dark:text-gray-400');
                b[0].classList.remove('border-b-2', 'border-indigo-600', 'text-indigo-600', 'dark:text-indigo-400');
                b[0].classList.add('text-gray-600', 'dark:text-gray-400');
            }
        }
    </script>
</x-app-layout>
<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                📋 My Tasks
            </h2>
            <div class="flex items-center gap-4">
                <button onclick="location.reload()" class="p-2 text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-200 transition-colors" title="Refresh page">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                </button>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    
                    <!-- Debug Display -->
                    <div class="mb-4 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800 text-sm">
                        <p class="text-blue-800 dark:text-blue-300"><strong>Assignment:</strong> <span id="assignmentStatus">Loading...</span> | <strong>Tasks:</strong> <span id="tasksCount">0</span></p>
                    </div>

                    <!-- Content Wrapper -->
                    <div id="tasksContent"></div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            try {
                const data = {
                    assignment: {{ $assignment ? 'true' : 'false' }},
                    sem1_tasks: @json($sem1_tasks ?? []),
                    sem2_tasks: @json($sem2_tasks ?? [])
                };

                @if($assignment)
                const assignment = {
                    id: {{ $assignment->id }},
                    student_name: '{{ $assignment->student->name ?? 'Unknown' }}',
                    company_name: '{{ $assignment->company->name ?? 'No Company' }}',
                    supervisor_name: '{{ $assignment->supervisor->name ?? 'Unknown Supervisor' }}'
                };
                @else
                const assignment = null;
                @endif

                const sem1Tasks = data.sem1_tasks;
                const sem2Tasks = data.sem2_tasks;
                const totalTasks = sem1Tasks.length + sem2Tasks.length;

                // Update status display
                document.getElementById('assignmentStatus').textContent = assignment ? assignment.supervisor_name + ' ✓' : 'None';
                document.getElementById('tasksCount').textContent = totalTasks;

                // Render content
                const contentDiv = document.getElementById('tasksContent');
                
                if (!assignment) {
                    contentDiv.innerHTML = `
                        <div class="text-center py-16">
                            <div class="mx-auto w-16 h-16 bg-red-100 dark:bg-red-900/30 rounded-full flex items-center justify-center mb-4">
                                <svg class="w-8 h-8 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4v.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">No Active Assignment</h3>
                            <p class="text-gray-600 dark:text-gray-400">You don't have an active assignment yet.</p>
                        </div>
                    `;
                } else if (totalTasks === 0) {
                    contentDiv.innerHTML = `
                        <div class="text-center py-16">
                            <div class="mx-auto w-16 h-16 bg-indigo-100 dark:bg-indigo-900/30 rounded-full flex items-center justify-center mb-4">
                                <svg class="w-8 h-8 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">No Tasks Assigned</h3>
                            <p class="text-gray-600 dark:text-gray-400">Your supervisor hasn't assigned any tasks yet.</p>
                        </div>
                    `;
                } else {
                    // Render tasks
                    let html = `
                        <div class="space-y-6">
                            <div class="flex gap-4 border-b border-gray-200 dark:border-gray-700">
                                <button onclick="showSemester('1st')" class="px-4 py-3 font-medium border-b-2 border-indigo-600 text-indigo-600 dark:text-indigo-400">
                                    📅 1st Semester (${sem1Tasks.length})
                                </button>
                                <button onclick="showSemester('2nd')" class="px-4 py-3 font-medium text-gray-600 dark:text-gray-400">
                                    📅 2nd Semester (${sem2Tasks.length})
                                </button>
                            </div>
                            <div id="sem1Container">${renderTasks(sem1Tasks)}</div>
                            <div id="sem2Container" class="hidden">${renderTasks(sem2Tasks)}</div>
                        </div>
                    `;
                    contentDiv.innerHTML = html;
                }

                console.log('Tasks View Loaded:', { assignment: !!assignment, sem1: sem1Tasks.length, sem2: sem2Tasks.length });
            } catch (error) {
                console.error('Error loading tasks:', error);
                document.getElementById('tasksContent').innerHTML = '<div class="text-red-600">Error loading tasks</div>';
            }
        });

        function renderTasks(tasks) {
            if (tasks.length === 0) {
                return '<div class="text-center py-8 text-gray-500">No tasks for this semester</div>';
            }

            let html = '<div class="overflow-x-auto"><table class="w-full text-sm"><thead class="bg-gray-100 dark:bg-gray-700"><tr><th class="px-4 py-3 text-left font-semibold">Task</th><th class="px-4 py-3 text-left font-semibold">Due Date</th><th class="px-4 py-3 text-left font-semibold">Status</th><th class="px-4 py-3 text-left font-semibold">Action</th></tr></thead><tbody class="divide-y divide-gray-200 dark:divide-gray-700">';
            
            tasks.forEach(task => {
                const dueDate = task.due_date ? new Date(task.due_date).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' }) : 'N/A';
                const statusBadge = getStatusBadge(task.status);
                html += `
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                        <td class="px-4 py-4">
                            <p class="font-semibold text-gray-900 dark:text-white">${task.title}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">${(task.description || '').substring(0, 60)}${(task.description || '').length > 60 ? '...' : ''}</p>
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap"><span class="font-medium">${dueDate}</span></td>
                        <td class="px-4 py-4">${statusBadge}</td>
                        <td class="px-4 py-4"><a href="/student/tasks/${task.id}" class="text-indigo-600 dark:text-indigo-400 hover:underline font-semibold text-sm">View →</a></td>
                    </tr>
                `;
            });

            html += '</tbody></table></div>';
            return html;
        }

        function getStatusBadge(status) {
            const badges = {
                'pending': '<span class="inline-block px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 dark:bg-gray-600 text-gray-800 dark:text-gray-300">⏳ Pending</span>',
                'submitted': '<span class="inline-block px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300">📤 Submitted</span>',
                'approved': '<span class="inline-block px-3 py-1 rounded-full text-xs font-semibold bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300">✓ Approved</span>',
                'rejected': '<span class="inline-block px-3 py-1 rounded-full text-xs font-semibold bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300">✗ Rejected</span>',
            };
            return badges[status] || badges['pending'];
        }

        function showSemester(sem) {
            const container1 = document.getElementById('sem1Container');
            const container2 = document.getElementById('sem2Container');
            const btns = document.querySelectorAll('button[onclick^="showSemester"]');

            if (sem === '1st') {
                container1.classList.remove('hidden');
                container2.classList.add('hidden');
                btns[0].classList.add('border-b-2', 'border-indigo-600', 'text-indigo-600', 'dark:text-indigo-400');
                btns[0].classList.remove('text-gray-600', 'dark:text-gray-400');
                btns[1].classList.remove('border-b-2', 'border-indigo-600', 'text-indigo-600', 'dark:text-indigo-400');
                btns[1].classList.add('text-gray-600', 'dark:text-gray-400');
            } else {
                container1.classList.add('hidden');
                container2.classList.remove('hidden');
                btns[1].classList.add('border-b-2', 'border-indigo-600', 'text-indigo-600', 'dark:text-indigo-400');
                btns[1].classList.remove('text-gray-600', 'dark:text-gray-400');
                btns[0].classList.remove('border-b-2', 'border-indigo-600', 'text-indigo-600', 'dark:text-indigo-400');
                btns[0].classList.add('text-gray-600', 'dark:text-gray-400');
            }
        }
    </script>
</x-app-layout>
<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                📋 My Tasks
            </h2>
            <div class="flex items-center gap-4">
                <button onclick="location.reload()" class="p-2 text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-200 transition-colors" title="Refresh page">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                </button>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    
                    <!-- Debug Display -->
                    <div class="mb-4 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800 text-sm">
                        <p class="text-blue-800 dark:text-blue-300"><strong>Assignment:</strong> <span id="assignmentStatus">Loading...</span> | <strong>Tasks:</strong> <span id="tasksCount">0</span></p>
                    </div>

                    <!-- Content Wrapper -->
                    <div id="tasksContent"></div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            try {
                const data = @json(['assignment' => $assignment, 'sem1_tasks' => $sem1_tasks, 'sem2_tasks' => $sem2_tasks]);
                
                // Parse assignment
                let assignment = null;
                if (data.assignment) {
                    assignment = {
                        id: data.assignment.id,
                        student_name: data.assignment.student?.name || 'Unknown',
                        company_name: data.assignment.company?.name || 'No Company',
                        supervisor_name: data.assignment.supervisor?.name || 'Unknown Supervisor',
                    };
                }

                // Parse tasks
                const sem1Tasks = Array.isArray(data.sem1_tasks) ? data.sem1_tasks : [];
                const sem2Tasks = Array.isArray(data.sem2_tasks) ? data.sem2_tasks : [];
                const totalTasks = sem1Tasks.length + sem2Tasks.length;

                // Update status display
                document.getElementById('assignmentStatus').textContent = assignment ? `Loaded (${assignment.supervisor_name})` : 'None';
                document.getElementById('tasksCount').textContent = totalTasks;

                // Render content
                const contentDiv = document.getElementById('tasksContent');
                
                if (!assignment) {
                    contentDiv.innerHTML = `
                        <div class="text-center py-16">
                            <div class="mx-auto w-16 h-16 bg-red-100 dark:bg-red-900/30 rounded-full flex items-center justify-center mb-4">
                                <svg class="w-8 h-8 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4v.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">No Active Assignment</h3>
                            <p class="text-gray-600 dark:text-gray-400">You don't have an active assignment yet.</p>
                        </div>
                    `;
                } else if (totalTasks === 0) {
                    contentDiv.innerHTML = `
                        <div class="text-center py-16">
                            <div class="mx-auto w-16 h-16 bg-indigo-100 dark:bg-indigo-900/30 rounded-full flex items-center justify-center mb-4">
                                <svg class="w-8 h-8 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">No Tasks Assigned</h3>
                            <p class="text-gray-600 dark:text-gray-400">Your supervisor hasn't assigned any tasks yet.</p>
                        </div>
                    `;
                } else {
                    // Render tasks
                    let html = `
                        <div class="space-y-6">
                            <div class="flex gap-4 border-b border-gray-200 dark:border-gray-700">
                                <button onclick="showSemester('1st')" class="px-4 py-3 font-medium border-b-2 border-indigo-600 text-indigo-600 dark:text-indigo-400">
                                    📅 1st Semester (${sem1Tasks.length})
                                </button>
                                <button onclick="showSemester('2nd')" class="px-4 py-3 font-medium text-gray-600 dark:text-gray-400">
                                    📅 2nd Semester (${sem2Tasks.length})
                                </button>
                            </div>
                            <div id="sem1Container">${renderTasks(sem1Tasks)}</div>
                            <div id="sem2Container" class="hidden">${renderTasks(sem2Tasks)}</div>
                        </div>
                    `;
                    contentDiv.innerHTML = html;
                }

                console.log('Tasks View Loaded:', { assignment: !!assignment, sem1: sem1Tasks.length, sem2: sem2Tasks.length });
            } catch (error) {
                console.error('Error loading tasks:', error);
                document.getElementById('tasksContent').innerHTML = '<div class="text-red-600">Error loading tasks</div>';
            }
        });

        function renderTasks(tasks) {
            if (tasks.length === 0) {
                return '<div class="text-center py-8 text-gray-500">No tasks for this semester</div>';
            }

            let html = '<div class="overflow-x-auto"><table class="w-full text-sm"><thead class="bg-gray-100 dark:bg-gray-700"><tr><th class="px-4 py-3 text-left font-semibold">Task</th><th class="px-4 py-3 text-left font-semibold">Due Date</th><th class="px-4 py-3 text-left font-semibold">Status</th><th class="px-4 py-3 text-left font-semibold">Action</th></tr></thead><tbody class="divide-y divide-gray-200 dark:divide-gray-700">';
            
            tasks.forEach(task => {
                const dueDate = task.due_date ? new Date(task.due_date).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' }) : 'N/A';
                const statusBadge = getStatusBadge(task.status);
                html += `
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                        <td class="px-4 py-4">
                            <p class="font-semibold text-gray-900 dark:text-white">${task.title}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">${(task.description || '').substring(0, 60)}${(task.description || '').length > 60 ? '...' : ''}</p>
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap"><span class="font-medium">${dueDate}</span></td>
                        <td class="px-4 py-4">${statusBadge}</td>
                        <td class="px-4 py-4"><a href="/student/tasks/${task.id}" class="text-indigo-600 dark:text-indigo-400 hover:underline font-semibold text-sm">View →</a></td>
                    </tr>
                `;
            });

            html += '</tbody></table></div>';
            return html;
        }

        function getStatusBadge(status) {
            const badges = {
                'pending': '<span class="inline-block px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 dark:bg-gray-600 text-gray-800 dark:text-gray-300">⏳ Pending</span>',
                'submitted': '<span class="inline-block px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300">📤 Submitted</span>',
                'approved': '<span class="inline-block px-3 py-1 rounded-full text-xs font-semibold bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300">✓ Approved</span>',
                'rejected': '<span class="inline-block px-3 py-1 rounded-full text-xs font-semibold bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300">✗ Rejected</span>',
            };
            return badges[status] || badges['pending'];
        }

        function showSemester(sem) {
            const container1 = document.getElementById('sem1Container');
            const container2 = document.getElementById('sem2Container');
            const btn1 = document.querySelectorAll('button')[0];
            const btn2 = document.querySelectorAll('button')[1];

            if (sem === '1st') {
                container1.classList.remove('hidden');
                container2.classList.add('hidden');
                btn1.classList.add('border-b-2', 'border-indigo-600', 'text-indigo-600', 'dark:text-indigo-400');
                btn1.classList.remove('text-gray-600', 'dark:text-gray-400');
                btn2.classList.remove('border-b-2', 'border-indigo-600', 'text-indigo-600', 'dark:text-indigo-400');
                btn2.classList.add('text-gray-600', 'dark:text-gray-400');
            } else {
                container1.classList.add('hidden');
                container2.classList.remove('hidden');
                btn2.classList.add('border-b-2', 'border-indigo-600', 'text-indigo-600', 'dark:text-indigo-400');
                btn2.classList.remove('text-gray-600', 'dark:text-gray-400');
                btn1.classList.remove('border-b-2', 'border-indigo-600', 'text-indigo-600', 'dark:text-indigo-400');
                btn1.classList.add('text-gray-600', 'dark:text-gray-400');
            }
        }
    </script>
    @endpush
</x-app-layout>
<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                📋 My Tasks
            </h2>
            
            <div class="flex items-center gap-4">
                <!-- Refresh Button -->
                <button onclick="location.reload()" class="p-2 text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-200 transition-colors" title="Refresh page">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                </button>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8" x-data="initTaskManager()" x-init="initialize()">>
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    
                    <!-- Debug Info -->
                    <div class="mb-4 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800 text-sm">
                        <p class="text-blue-800 dark:text-blue-300"><strong>Status:</strong> Assignment: <span x-text="assignment ? 'Loaded ✓' : 'None'"></span> | Total Tasks: <span x-text="totalTasks"></span></p>
                    </div>
                    
                    <!-- No Assignment State -->
                    <template x-if="!assignment">
                        <div class="text-center py-16">
                            <div class="mx-auto w-16 h-16 bg-red-100 dark:bg-red-900/30 rounded-full flex items-center justify-center mb-4">
                                <svg class="w-8 h-8 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4v.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">No Active Assignment</h3>
                            <p class="text-gray-600 dark:text-gray-400 mb-2">You don't have an active assignment set up yet.</p>
                        </div>
                    </template>

                    <!-- No Tasks State -->
                    <template x-if="assignment && totalTasks === 0">
                        <div class="text-center py-16">
                            <div class="mx-auto w-16 h-16 bg-indigo-100 dark:bg-indigo-900/30 rounded-full flex items-center justify-center mb-4">
                                <svg class="w-8 h-8 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">No Tasks Assigned Yet</h3>
                            <p class="text-gray-600 dark:text-gray-400 mb-1">Your supervisor hasn't assigned any tasks yet.</p>
                        </div>
                    </template>

                    <!-- Tasks Found - Show Semester Tabs -->
                    <template x-if="assignment && totalTasks > 0">
                        <div class="space-y-6">
                            <!-- Semester Toggle Tabs -->
                            <div class="flex bg-gray-200 dark:bg-gray-700 rounded-lg p-1">
                                <button @click="currentTab = '1st'" 
                                        :class="{ 'bg-white dark:bg-gray-600 shadow text-gray-900 dark:text-white': currentTab === '1st', 'text-gray-500 dark:text-gray-400': currentTab !== '1st' }" 
                                        class="flex-1 px-4 py-2 rounded-md text-sm font-medium transition-all">
                                    📅 1st Semester
                                </button>
                                <button @click="currentTab = '2nd'" 
                                        :class="{ 'bg-white dark:bg-gray-600 shadow text-gray-900 dark:text-white': currentTab === '2nd', 'text-gray-500 dark:text-gray-400': currentTab !== '2nd' }" 
                                        class="flex-1 px-4 py-2 rounded-md text-sm font-medium transition-all">
                                    📅 2nd Semester
                                </button>
                            </div>

                            <!-- 1st Semester Content -->
                            <div x-show="currentTab === '1st'" class="space-y-4">
                                <template x-if="sem1Tasks.length === 0">
                                    <div class="text-center py-12 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                                        <p class="text-gray-500 dark:text-gray-400">✓ No tasks for 1st semester</p>
                                    </div>
                                </template>
                                <template x-if="sem1Tasks.length > 0">
                                    <div class="overflow-x-auto">
                                        <table class="w-full text-sm">
                                            <thead class="bg-gray-50 dark:bg-gray-700/50 border-b border-gray-200 dark:border-gray-600">
                                                <tr>
                                                    <th class="px-4 py-3 text-left font-semibold">Task</th>
                                                    <th class="px-4 py-3 text-left font-semibold">Due Date</th>
                                                    <th class="px-4 py-3 text-left font-semibold">Status</th>
                                                    <th class="px-4 py-3 text-left font-semibold">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                                <template x-for="task in sem1Tasks" :key="task.id">
                                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                                        <td class="px-4 py-4">
                                                            <p class="font-semibold" x-text="task.title"></p>
                                                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1" x-text="task.description ? task.description.substring(0, 60) + '...' : ''"></p>
                                                        </td>
                                                        <td class="px-4 py-4 whitespace-nowrap">
                                                            <span x-text="task.due_date ? new Date(task.due_date).toLocaleDateString() : 'N/A'"></span>
                                                        </td>
                                                        <td class="px-4 py-4">
                                                            <span x-show="task.status === 'pending'" class="inline-block px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 dark:bg-gray-600 text-gray-800 dark:text-gray-300">⏳ Pending</span>
                                                            <span x-show="task.status === 'submitted'" class="inline-block px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300">📤 Submitted</span>
                                                            <span x-show="task.status === 'approved'" class="inline-block px-3 py-1 rounded-full text-xs font-semibold bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300">✓ Approved</span>
                                                            <span x-show="task.status === 'rejected'" class="inline-block px-3 py-1 rounded-full text-xs font-semibold bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300">✗ Rejected</span>
                                                        </td>
                                                        <td class="px-4 py-4">
                                                            <a :href="'/student/tasks/' + task.id" class="text-indigo-600 dark:text-indigo-400 hover:underline font-semibold text-sm">View →</a>
                                                        </td>
                                                    </tr>
                                                </template>
                                            </tbody>
                                        </table>
                                    </div>
                                </template>
                            </div>

                            <!-- 2nd Semester Content -->
                            <div x-show="currentTab === '2nd'" class="space-y-4">
                                <template x-if="sem2Tasks.length === 0">
                                    <div class="text-center py-12 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                                        <p class="text-gray-500 dark:text-gray-400">✓ No tasks for 2nd semester</p>
                                    </div>
                                </template>
                                <template x-if="sem2Tasks.length > 0">
                                    <div class="overflow-x-auto">
                                        <table class="w-full text-sm">
                                            <thead class="bg-gray-50 dark:bg-gray-700/50 border-b border-gray-200 dark:border-gray-600">
                                                <tr>
                                                    <th class="px-4 py-3 text-left font-semibold">Task</th>
                                                    <th class="px-4 py-3 text-left font-semibold">Due Date</th>
                                                    <th class="px-4 py-3 text-left font-semibold">Status</th>
                                                    <th class="px-4 py-3 text-left font-semibold">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                                <template x-for="task in sem2Tasks" :key="task.id">
                                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                                        <td class="px-4 py-4">
                                                            <p class="font-semibold" x-text="task.title"></p>
                                                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1" x-text="task.description ? task.description.substring(0, 60) + '...' : ''"></p>
                                                        </td>
                                                        <td class="px-4 py-4 whitespace-nowrap">
                                                            <span x-text="task.due_date ? new Date(task.due_date).toLocaleDateString() : 'N/A'"></span>
                                                        </td>
                                                        <td class="px-4 py-4">
                                                            <span x-show="task.status === 'pending'" class="inline-block px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 dark:bg-gray-600 text-gray-800 dark:text-gray-300">⏳ Pending</span>
                                                            <span x-show="task.status === 'submitted'" class="inline-block px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300">📤 Submitted</span>
                                                            <span x-show="task.status === 'approved'" class="inline-block px-3 py-1 rounded-full text-xs font-semibold bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300">✓ Approved</span>
                                                            <span x-show="task.status === 'rejected'" class="inline-block px-3 py-1 rounded-full text-xs font-semibold bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300">✗ Rejected</span>
                                                        </td>
                                                        <td class="px-4 py-4">
                                                            <a :href="'/student/tasks/' + task.id" class="text-indigo-600 dark:text-indigo-400 hover:underline font-semibold text-sm">View →</a>
                                                        </td>
                                                    </tr>
                                                </template>
                                            </tbody>
                                        </table>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function initTaskManager() {
            const rawData = @json(['assignment' => $assignment, 'sem1_tasks' => $sem1_tasks, 'sem2_tasks' => $sem2_tasks]);
            
            return {
                currentTab: '1st',
                assignment: null,
                sem1Tasks: [],
                sem2Tasks: [],
                totalTasks: 0,

                initialize() {
                    if (rawData && rawData.assignment) {
                        this.assignment = {
                            id: rawData.assignment.id,
                            student_name: rawData.assignment.student?.name || 'Unknown',
                            company_name: rawData.assignment.company?.name || 'No Company',
                            supervisor_name: rawData.assignment.supervisor?.name || 'Unknown Supervisor',
                        };
                    }

                    if (rawData && Array.isArray(rawData.sem1_tasks)) {
                        this.sem1Tasks = rawData.sem1_tasks.map(task => ({
                            id: task.id,
                            title: task.title || 'Untitled Task',
                            description: task.description || '',
                            due_date: task.due_date,
                            status: task.status || 'pending',
                            attachment_path: task.attachment_path,
                            original_filename: task.original_filename,
                        }));
                    }

                    if (rawData && Array.isArray(rawData.sem2_tasks)) {
                        this.sem2Tasks = rawData.sem2_tasks.map(task => ({
                            id: task.id,
                            title: task.title || 'Untitled Task',
                            description: task.description || '',
                            due_date: task.due_date,
                            status: task.status || 'pending',
                            attachment_path: task.attachment_path,
                            original_filename: task.original_filename,
                        }));
                    }

                    this.totalTasks = this.sem1Tasks.length + this.sem2Tasks.length;
                    console.log('Tasks loaded:', { sem1: this.sem1Tasks.length, sem2: this.sem2Tasks.length, assignment: !!this.assignment });
                }
            };
        }
        
        // Initialize when DOM is ready
        document.addEventListener('DOMContentLoaded', function() {
            const manager = document.querySelector('[x-data]')?.__alpineUnobservedData;
            if (manager && manager.initialize) {
                manager.initialize();
            }
        });
    </script>
    @endpush
</x-app-layout>
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    
                    <!-- Debug Info (Remove in production) -->
                    <div class="mb-4 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800 text-sm">
                        <p class="text-blue-800 dark:text-blue-300"><strong>Status:</strong> Assignment: <span x-text="assignment ? 'Loaded' : 'Not found'"></span> | Tasks: <span x-text="totalTasks"></span></p>
                    </div>
                    
                    <!-- No Assignment State -->
                    <template x-if="!assignment">
                        <div class="text-center py-16">
                            <div class="mx-auto w-16 h-16 bg-red-100 dark:bg-red-900/30 rounded-full flex items-center justify-center mb-4">
                                <svg class="w-8 h-8 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4v.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">No Active Assignment</h3>
                            <p class="text-gray-600 dark:text-gray-400 mb-2">You don't have an active assignment set up yet.</p>
                            <p class="text-sm text-gray-500 dark:text-gray-500">Please contact your OJT Coordinator or Administrator to create an assignment.</p>
                        </div>
                    </template>

                    <!-- No Tasks State -->
                    <template x-if="assignment && totalTasks === 0">
                        <div class="text-center py-16">
                            <div class="mx-auto w-16 h-16 bg-indigo-100 dark:bg-indigo-900/30 rounded-full flex items-center justify-center mb-4">
                                <svg class="w-8 h-8 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">No Tasks Assigned Yet</h3>
                            <p class="text-gray-600 dark:text-gray-400 mb-1">Your supervisor hasn't assigned any tasks yet.</p>
                            <p class="text-sm text-gray-500 dark:text-gray-500 mb-6">Tasks will appear here once your supervisor creates them. Check back soon!</p>
                            <div class="mt-6 pt-6 border-t border-gray-300 dark:border-gray-600">
                                <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">👤 <span class="font-semibold">Supervisor:</span> <span x-text="assignment.supervisor_name || 'Not assigned'"></span></p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">🏢 <span class="font-semibold">Company:</span> <span x-text="assignment.company_name || 'Not assigned'"></span></p>
                            </div>
                        </div>
                    </template>

                    <!-- Tasks Found - Show Semester Tabs -->
                    <template x-if="assignment && totalTasks > 0">
                        <div class="space-y-6">
                            <!-- Semester Toggle Tabs -->
                            <div class="flex bg-gray-200 dark:bg-gray-700 rounded-lg p-1">
                                <button @click="currentTab = '1st'" 
                                        :class="{ 'bg-white dark:bg-gray-600 shadow text-gray-900 dark:text-white': currentTab === '1st', 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300': currentTab !== '1st' }" 
                                        class="flex-1 px-4 py-2 rounded-md text-sm font-medium transition-all duration-200">
                                    📅 1st Semester
                                </button>
                                <button @click="currentTab = '2nd'" 
                                        :class="{ 'bg-white dark:bg-gray-600 shadow text-gray-900 dark:text-white': currentTab === '2nd', 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300': currentTab !== '2nd' }" 
                                        class="flex-1 px-4 py-2 rounded-md text-sm font-medium transition-all duration-200">
                                    📅 2nd Semester
                                </button>
                            </div>

                            <!-- 1st Semester Content -->
                            <div x-show="currentTab === '1st'" 
                                 x-transition:enter="transition ease-out duration-300" 
                                 x-transition:enter-start="opacity-0 translate-y-2" 
                                 x-transition:enter-end="opacity-100 translate-y-0">
                                <template x-if="sem1Tasks.length === 0">
                                    <div class="text-center py-12 bg-gray-50 dark:bg-gray-700/50 rounded-lg border border-dashed border-gray-300 dark:border-gray-600">
                                        <p class="text-gray-500 dark:text-gray-400">✓ No tasks assigned for 1st semester</p>
                                    </div>
                                </template>
                                <template x-if="sem1Tasks.length > 0">
                                    <div class="overflow-x-auto">
                                        <table class="w-full text-sm">
                                            <thead class="bg-gray-50 dark:bg-gray-700/50 border-b border-gray-200 dark:border-gray-600">
                                                <tr>
                                                    <th class="px-4 py-3 text-left font-semibold text-gray-700 dark:text-gray-300">Task</th>
                                                    <th class="px-4 py-3 text-left font-semibold text-gray-700 dark:text-gray-300">Due Date</th>
                                                    <th class="px-4 py-3 text-left font-semibold text-gray-700 dark:text-gray-300">Status</th>
                                                    <th class="px-4 py-3 text-left font-semibold text-gray-700 dark:text-gray-300">Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                                <template x-for="task in sem1Tasks" :key="task.id">
                                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                                                        <td class="px-4 py-4">
                                                            <div>
                                                                <p class="font-semibold text-gray-900 dark:text-white" x-text="task.title"></p>
                                                                <p class="text-gray-600 dark:text-gray-400 text-xs mt-1" x-text="truncate(task.description, 80)"></p>
                                                                <template x-if="task.attachment_path">
                                                                    <a :href="'/storage/' + task.attachment_path" target="_blank" class="text-indigo-600 dark:text-indigo-400 text-xs mt-1 inline-flex items-center gap-1 hover:underline">
                                                                        📎 <span x-text="task.original_filename"></span>
                                                                    </a>
                                                                </template>
                                                            </div>
                                                        </td>
                                                        <td class="px-4 py-4 whitespace-nowrap">
                                                            <span class="text-gray-900 dark:text-white font-medium" x-text="formatDate(task.due_date)"></span>
                                                            <template x-if="isOverdue(task.due_date, task.status)">
                                                                <p class="text-red-600 dark:text-red-400 text-xs font-semibold">⚠️ Overdue</p>
                                                            </template>
                                                        </td>
                                                        <td class="px-4 py-4">
                                                            <template x-if="task.status === 'approved'">
                                                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300">✓ Approved</span>
                                                            </template>
                                                            <template x-if="task.status === 'rejected'">
                                                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300">✗ Rejected</span>
                                                            </template>
                                                            <template x-if="task.status === 'submitted'">
                                                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300">📤 Submitted</span>
                                                            </template>
                                                            <template x-if="task.status === 'pending'">
                                                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 dark:bg-gray-600 text-gray-800 dark:text-gray-300">⏳ Pending</span>
                                                            </template>
                                                        </td>
                                                        <td class="px-4 py-4">
                                                            <a :href="'/student/tasks/' + task.id" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300 font-semibold text-sm">View →</a>
                                                        </td>
                                                    </tr>
                                                </template>
                                            </tbody>
                                        </table>
                                    </div>
                                </template>
                            </div>

                            <!-- 2nd Semester Content -->
                            <div x-show="currentTab === '2nd'" 
                                 x-transition:enter="transition ease-out duration-300" 
                                 x-transition:enter-start="opacity-0 translate-y-2" 
                                 x-transition:enter-end="opacity-100 translate-y-0">
                                <template x-if="sem2Tasks.length === 0">
                                    <div class="text-center py-12 bg-gray-50 dark:bg-gray-700/50 rounded-lg border border-dashed border-gray-300 dark:border-gray-600">
                                        <p class="text-gray-500 dark:text-gray-400">✓ No tasks assigned for 2nd semester</p>
                                    </div>
                                </template>
                                <template x-if="sem2Tasks.length > 0">
                                    <div class="overflow-x-auto">
                                        <table class="w-full text-sm">
                                            <thead class="bg-gray-50 dark:bg-gray-700/50 border-b border-gray-200 dark:border-gray-600">
                                                <tr>
                                                    <th class="px-4 py-3 text-left font-semibold text-gray-700 dark:text-gray-300">Task</th>
                                                    <th class="px-4 py-3 text-left font-semibold text-gray-700 dark:text-gray-300">Due Date</th>
                                                    <th class="px-4 py-3 text-left font-semibold text-gray-700 dark:text-gray-300">Status</th>
                                                    <th class="px-4 py-3 text-left font-semibold text-gray-700 dark:text-gray-300">Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                                <template x-for="task in sem2Tasks" :key="task.id">
                                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                                                        <td class="px-4 py-4">
                                                            <div>
                                                                <p class="font-semibold text-gray-900 dark:text-white" x-text="task.title"></p>
                                                                <p class="text-gray-600 dark:text-gray-400 text-xs mt-1" x-text="truncate(task.description, 80)"></p>
                                                                <template x-if="task.attachment_path">
                                                                    <a :href="'/storage/' + task.attachment_path" target="_blank" class="text-indigo-600 dark:text-indigo-400 text-xs mt-1 inline-flex items-center gap-1 hover:underline">
                                                                        📎 <span x-text="task.original_filename"></span>
                                                                    </a>
                                                                </template>
                                                            </div>
                                                        </td>
                                                        <td class="px-4 py-4 whitespace-nowrap">
                                                            <span class="text-gray-900 dark:text-white font-medium" x-text="formatDate(task.due_date)"></span>
                                                            <template x-if="isOverdue(task.due_date, task.status)">
                                                                <p class="text-red-600 dark:text-red-400 text-xs font-semibold">⚠️ Overdue</p>
                                                            </template>
                                                        </td>
                                                        <td class="px-4 py-4">
                                                            <template x-if="task.status === 'approved'">
                                                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300">✓ Approved</span>
                                                            </template>
                                                            <template x-if="task.status === 'rejected'">
                                                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300">✗ Rejected</span>
                                                            </template>
                                                            <template x-if="task.status === 'submitted'">
                                                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300">📤 Submitted</span>
                                                            </template>
                                                            <template x-if="task.status === 'pending'">
                                                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 dark:bg-gray-600 text-gray-800 dark:text-gray-300">⏳ Pending</span>
                                                            </template>
                                                        </td>
                                                        <td class="px-4 py-4">
                                                            <a :href="'/student/tasks/' + task.id" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300 font-semibold text-sm">View →</a>
                                                        </td>
                                                    </tr>
                                                </template>
                                            </tbody>
                                        </table>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function taskManager() {
            return {
                currentTab: '1st',
                assignment: null,
                sem1Tasks: [],
                sem2Tasks: [],
                totalTasks: 0,
                
                init() {
                    try {
                        const data = @json(['assignment' => $assignment, 'sem1_tasks' => $sem1_tasks, 'sem2_tasks' => $sem2_tasks]);

                        // Process assignment data
                        if (data && data.assignment && typeof data.assignment === 'object') {
                            this.assignment = {
                                id: data.assignment.id || null,
                                student_name: (data.assignment.student && data.assignment.student.name) ? data.assignment.student.name : 'Unknown',
                                company_name: (data.assignment.company && data.assignment.company.name) ? data.assignment.company.name : 'No Company',
                                supervisor_name: (data.assignment.supervisor && data.assignment.supervisor.name) ? data.assignment.supervisor.name : 'Unknown Supervisor',
                            };
                        }

                        // Process semester 1 tasks
                        if (data && Array.isArray(data.sem1_tasks)) {
                            this.sem1Tasks = data.sem1_tasks.map(task => ({
                                id: task.id,
                                title: task.title || 'Untitled',
                                description: task.description || '',
                                due_date: task.due_date || null,
                                status: task.status || 'pending',
                                attachment_path: task.attachment_path || null,
                                original_filename: task.original_filename || null,
                            }));
                        }

                        // Process semester 2 tasks
                        if (data && Array.isArray(data.sem2_tasks)) {
                            this.sem2Tasks = data.sem2_tasks.map(task => ({
                                id: task.id,
                                title: task.title || 'Untitled',
                                description: task.description || '',
                                due_date: task.due_date || null,
                                status: task.status || 'pending',
                                attachment_path: task.attachment_path || null,
                                original_filename: task.original_filename || null,
                            }));
                        }

                        this.totalTasks = this.sem1Tasks.length + this.sem2Tasks.length;
                        
                        console.log('Task Manager initialized:', {
                            assignment: this.assignment,
                            sem1Count: this.sem1Tasks.length,
                            sem2Count: this.sem2Tasks.length,
                            totalTasks: this.totalTasks
                        });
                    } catch (error) {
                        console.error('Error initializing task manager:', error);
                    }
                },

                truncate(text, length) {
                    if (!text) return '';
                    return text.length > length ? text.substring(0, length) + '...' : text;
                },

                formatDate(dateString) {
                    if (!dateString) return 'N/A';
                    const date = new Date(dateString);
                    return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
                },

                isOverdue(dueDate, status) {
                    if (!dueDate || status === 'approved') return false;
                    const due = new Date(dueDate);
                    const today = new Date();
                    today.setHours(0, 0, 0, 0);
                    return due < today;
                }
            };
        }
    </script>
    @endpush
</x-app-layout>
