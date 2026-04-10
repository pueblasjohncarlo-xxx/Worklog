<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task Debug - {{ $user->name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold mb-8">🐛 Task System Debug</h1>

        <!-- User Info -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h2 class="text-xl font-semibold mb-4">📝 Your Information</h2>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <p class="text-gray-600 text-sm">User ID:</p>
                    <p class="font-mono font-bold">{{ $user->id }}</p>
                </div>
                <div>
                    <p class="text-gray-600 text-sm">Name:</p>
                    <p class="font-bold">{{ $user->name }}</p>
                </div>
                <div>
                    <p class="text-gray-600 text-sm">Email:</p>
                    <p class="font-mono">{{ $user->email }}</p>
                </div>
                <div>
                    <p class="text-gray-600 text-sm">Role:</p>
                    <p class="font-bold">{{ $user->role }}</p>
                </div>
            </div>
        </div>

        <!-- Assignment Info -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h2 class="text-xl font-semibold mb-4">🤝 Assignment Status</h2>
            @if($assignment)
                <div class="grid grid-cols-2 gap-4 bg-green-50 p-4 rounded-lg border-2 border-green-200">
                    <div>
                        <p class="text-gray-600 text-sm">Assignment ID:</p>
                        <p class="font-mono font-bold">{{ $assignment->id }}</p>
                    </div>
                    <div>
                        <p class="text-gray-600 text-sm">Supervisor:</p>
                        <p class="font-bold">{{ $assignment->supervisor->name ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-600 text-sm">Company:</p>
                        <p class="font-bold">{{ $assignment->company->name ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-600 text-sm">Status:</p>
                        <p class="font-bold">{{ $assignment->status }}</p>
                    </div>
                </div>
            @else
                <div class="bg-red-50 p-4 rounded-lg border-2 border-red-200">
                    <p class="font-bold text-red-700">❌ No Active Assignment Found</p>
                    <p class="text-gray-600 text-sm mt-2">You don't have an assignment set up. Contact your coordinator.</p>
                </div>
            @endif
        </div>

        <!-- Tasks Info -->
        @if($assignment)
            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <h2 class="text-xl font-semibold mb-4">📋 Tasks Created</h2>
                @if($tasks->isEmpty())
                    <div class="bg-yellow-50 p-4 rounded-lg border-2 border-yellow-200">
                        <p class="font-bold text-yellow-700">⚠️ No Tasks Found</p>
                        <p class="text-gray-600 text-sm mt-2">No tasks have been assigned to you yet. Ask your supervisor to create tasks from the "Assign New Task" page.</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="px-4 py-2 text-left text-sm font-semibold">ID</th>
                                    <th class="px-4 py-2 text-left text-sm font-semibold">Title</th>
                                    <th class="px-4 py-2 text-left text-sm font-semibold">Semester</th>
                                    <th class="px-4 py-2 text-left text-sm font-semibold">Status</th>
                                    <th class="px-4 py-2 text-left text-sm font-semibold">Created</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($tasks as $task)
                                    <tr class="border-t">
                                        <td class="px-4 py-3 font-mono text-sm">{{ $task->id }}</td>
                                        <td class="px-4 py-3 font-bold">{{ $task->title }}</td>
                                        <td class="px-4 py-3">
                                            <span class="px-2 py-1 bg-indigo-100 text-indigo-700 rounded text-sm">{{ $task->semester }}</span>
                                        </td>
                                        <td class="px-4 py-3">
                                            <span class="px-2 py-1 bg-blue-100 text-blue-700 rounded text-sm">{{ $task->status }}</span>
                                        </td>
                                        <td class="px-4 py-3 text-sm">{{ $task->created_at->format('Y-m-d H:i') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        @endif

        <!-- Raw Data -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h2 class="text-xl font-semibold mb-4">📊 Raw JSON Data</h2>
            <pre class="bg-gray-100 p-4 rounded overflow-auto text-sm"><code>{{json_encode([
    'user' => [
        'id' => $user->id,
        'name' => $user->name,
        'email' => $user->email,
        'role' => $user->role,
    ],
    'assignment' => $assignment ? [
        'id' => $assignment->id,
        'supervisor_id' => $assignment->supervisor_id,
        'supervisor_name' => $assignment->supervisor->name ?? null,
        'status' => $assignment->status,
    ] : null,
    'tasks_count' => $tasks->count(),
    'tasks' => $tasks->map(function($t) {
        return [
            'id' => $t->id,
            'title' => $t->title,
            'semester' => $t->semester,
            'status' => $t->status,
            'created_at' => $t->created_at->toIso8601String(),
        ];
    })->toArray(),
], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)}}</code></pre>
        </div>

        <!-- Instructions -->
        <div class="bg-blue-50 border-l-4 border-blue-500 p-6 rounded">
            <h3 class="font-bold text-blue-900 mb-2">📌 What to do:</h3>
            <ol class="list-decimal list-inside space-y-2 text-gray-700">
                @if(!$assignment)
                    <li><strong>First:</strong> Ask your OJT Coordinator to create an assignment for you</li>
                @else
                    <li><strong>If you see "No Tasks Found":</strong> Ask your supervisor ({{ $assignment->supervisor->name ?? 'N/A' }}) to assign tasks using the "Assign New Task" page</li>
                    <li><strong>When creating tasks:</strong> Make sure to select your name and a semester (1st or 2nd)</li>
                    <li><strong>After supervisor creates tasks:</strong> Refresh this page and the tasks should appear above</li>
                @endif
                <li><strong>Then:</strong> Go to <a href="{{ route('student.tasks.index') }}" class="underline text-blue-600">My Tasks</a> to see your assigned tasks</li>
            </ol>
        </div>
    </div>
</body>
</html>
