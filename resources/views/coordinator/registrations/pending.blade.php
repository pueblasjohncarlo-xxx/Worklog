<x-coordinator-layout>
    <x-slot name="header">
        {{ __('Registration Approvals') }}
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">
            @if (session('status'))
                <div class="p-3 bg-green-50 text-green-800 rounded border border-green-200">
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="p-3 bg-red-50 text-red-800 rounded border border-red-200">
                    {{ $errors->first() }}
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50">
                    <form method="GET" action="{{ route('coordinator.registrations.pending') }}" class="grid grid-cols-1 md:grid-cols-4 gap-3">
                        <input
                            type="text"
                            name="q"
                            value="{{ request('q') }}"
                            placeholder="Search name, email, or role..."
                            class="rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 text-gray-900 text-sm shadow-sm md:col-span-3"
                        >
                        <button type="submit" class="px-3 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded text-sm font-semibold shadow-sm transition-colors">
                            Filter
                        </button>
                    </form>
                </div>

                <div class="p-6 text-gray-900 dark:text-gray-100 overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="border-b bg-gray-100 dark:bg-gray-800">
                                <th class="text-left px-3 py-3 font-bold">Name</th>
                                <th class="text-left px-3 py-3 font-bold">Email</th>
                                <th class="text-left px-3 py-3 font-bold">Role</th>
                                <th class="text-left px-3 py-3 font-bold">Requested</th>
                                <th class="text-left px-3 py-3 font-bold">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($users as $user)
                                <tr class="border-b align-top hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                    <td class="px-3 py-3 font-semibold">{{ $user->name }}</td>
                                    <td class="px-3 py-3">{{ $user->email }}</td>
                                    <td class="px-3 py-3">
                                        @php
                                            $roleBadgeClasses = match ($user->role) {
                                                'student' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-200',
                                                'supervisor' => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-200',
                                                'coordinator' => 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900/40 dark:text-indigo-200',
                                                'ojt_adviser' => 'bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-200',
                                                'admin' => 'bg-rose-100 text-rose-800 dark:bg-rose-900/40 dark:text-rose-200',
                                                default => 'bg-gray-200 text-gray-900 dark:bg-gray-700 dark:text-gray-100',
                                            };
                                        @endphp
                                        <span class="px-2 py-1 rounded-full text-xs font-semibold {{ $roleBadgeClasses }}">
                                            {{ ucfirst(str_replace('_', ' ', $user->role)) }}
                                        </span>
                                    </td>
                                    <td class="px-3 py-3">{{ optional($user->created_at)->format('M d, Y h:i A') }}</td>
                                    <td class="px-3 py-3">
                                        <div class="flex flex-col sm:flex-row gap-2 min-w-[220px]">
                                            <form method="POST" action="{{ route('coordinator.registrations.approve', $user) }}">
                                                @csrf
                                                <button type="submit" class="w-full sm:w-auto px-3 py-2 bg-green-600 hover:bg-green-700 text-white rounded text-xs font-semibold">
                                                    Approve
                                                </button>
                                            </form>

                                            <form method="POST" action="{{ route('coordinator.registrations.reject', $user) }}" class="flex flex-col sm:flex-row gap-2 w-full">
                                                @csrf
                                                <input
                                                    type="text"
                                                    name="reason"
                                                    placeholder="Optional rejection reason"
                                                    class="w-full sm:w-56 rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 text-gray-900 text-xs"
                                                >
                                                <button type="submit" class="px-3 py-2 bg-red-600 hover:bg-red-700 text-white rounded text-xs font-semibold">
                                                    Reject
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-3 py-6 text-center text-gray-700 dark:text-gray-200">No pending registration requests.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <div class="mt-4">
                        {{ $users->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-coordinator-layout>
