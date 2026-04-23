@props(['users'])

<table class="min-w-full text-left text-sm">
    <thead>
        <tr class="border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50">
            <th class="px-6 py-3 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Name</th>
            <th class="px-6 py-3 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Email</th>
            <th class="px-6 py-3 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Role</th>
            <th class="px-6 py-3 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
            <th class="px-6 py-3 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
        </tr>
    </thead>
    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
        @foreach ($users as $user)
            @php
                $displayRole = match ($user->role) {
                    'staff' => 'admin',
                    default => $user->role,
                };

                $displayRoleLabel = match ($user->role) {
                    'ojt_adviser' => 'OJT Adviser',
                    'staff' => 'Admin',
                    default => ucfirst(str_replace('_', ' ', $displayRole)),
                };
            @endphp
            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="font-medium text-gray-900 dark:text-gray-100">{{ $user->name }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-gray-500 dark:text-gray-400">
                    {{ $user->email }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                        {{ match($user->role) {
                            'admin' => 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200',
                            'staff' => 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200',
                            'coordinator' => 'bg-cyan-100 text-cyan-800 dark:bg-cyan-900 dark:text-cyan-200',
                            'supervisor' => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900 dark:text-emerald-200',
                            'ojt_adviser' => 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200',
                            'student' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
                            default => 'bg-gray-100 text-gray-800'
                        } }}">
                        {{ $displayRoleLabel }}
                    </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    @if($user->role === 'student')
                        @if($user->has_requested_account && !$user->is_approved)
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                                Pending
                            </span>
                        @elseif(!$user->has_requested_account)
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                                Imported
                            </span>
                        @else
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                Active
                            </span>
                        @endif
                    @else
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                            Active
                        </span>
                    @endif
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                    <div class="flex items-center gap-3">
                        <a href="{{ route('admin.users.show', $user) }}" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 font-bold text-xs uppercase tracking-wide">
                            View
                        </a>
                        
                        <form method="POST" action="{{ route('admin.users.update-role', $user) }}" class="inline-flex items-center">
                            @csrf
                            <select name="role" onchange="this.form.submit()" class="rounded-md border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-700 dark:text-gray-300 text-xs py-1 pl-2 pr-6 focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="admin" @selected(in_array($user->role, ['admin', 'staff'], true))>Admin</option>
                                <option value="coordinator" @selected($user->role === 'coordinator')>Coordinator</option>
                                <option value="supervisor" @selected($user->role === 'supervisor')>Supervisor</option>
                                <option value="ojt_adviser" @selected($user->role === 'ojt_adviser')>OJT Adviser</option>
                                <option value="student" @selected($user->role === 'student')>Student</option>
                            </select>
                        </form>

                        <form method="POST" action="{{ route('admin.users.destroy', $user) }}" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this user?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300 font-bold text-xs uppercase tracking-wide">
                                Delete
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
