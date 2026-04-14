<x-admin-layout>
    <x-slot name="header">
        User Management
    </x-slot>

    <div class="space-y-6">
        <!-- Create User Form -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900 dark:text-gray-100">
                @if ($errors->any())
                    <div class="mb-4 text-sm text-red-600 dark:text-red-400">
                        {{ $errors->first() }}
                    </div>
                @endif

                @if (session('status'))
                    <div class="mb-4 text-sm text-green-600 dark:text-green-400">
                        {{ session('status') }}
                    </div>
                @endif

                <div class="mb-2" x-data="{ role: '{{ old('role', 'student') }}' }">
                    <h3 id="create-user" class="font-semibold mb-3 text-lg">Create New User</h3>
                    <form method="POST" action="{{ route('admin.users.store') }}" class="grid grid-cols-1 sm:grid-cols-5 gap-3">
                        @csrf
                        <input
                            type="text"
                            name="name"
                            placeholder="Full name"
                            class="rounded-md border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500 px-3 py-2"
                            required
                        >
                        <input
                            type="email"
                            name="email"
                            placeholder="Email"
                            class="rounded-md border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500 px-3 py-2"
                            required
                        >
                        <input
                            type="password"
                            name="password"
                            placeholder="Password"
                            class="rounded-md border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500 px-3 py-2"
                            required
                        >
                        <select
                            name="role"
                            x-model="role"
                            class="rounded-md border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500 px-3 py-2"
                            required
                        >
                            <option value="admin">Admin</option>
                            <option value="staff">Staff</option>
                            <option value="coordinator">Coordinator</option>
                            <option value="supervisor">Supervisor</option>
                            <option value="ojt_adviser">OJT Adviser</option>
                            <option value="student" selected>OJT Student</option>
                        </select>

                        <template x-if="role === 'supervisor'">
                            <select
                                name="company_id"
                                class="rounded-md border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500 px-3 py-2"
                                required
                            >
                                <option value="">Select company for supervisor</option>
                                @forelse($companies as $company)
                                    <option value="{{ $company->id }}" @selected((string) old('company_id') === (string) $company->id)>{{ $company->name }}</option>
                                @empty
                                    <option value="" disabled>No companies available</option>
                                @endforelse
                            </select>
                        </template>

                        <template x-if="role === 'ojt_adviser'">
                            <input
                                type="text"
                                name="department"
                                placeholder="Dept / Program"
                                class="rounded-md border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500 px-3 py-2"
                            >
                        </template>
                        <template x-if="role === 'ojt_adviser'">
                            <input
                                type="text"
                                name="phone"
                                placeholder="Contact Number"
                                class="rounded-md border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500 px-3 py-2"
                            >
                        </template>

                        <button
                            type="submit"
                            class="inline-flex items-center justify-center px-4 py-2 rounded-md bg-indigo-600 text-white text-sm font-semibold hover:bg-indigo-700 shadow-md transition-colors"
                        >
                            Create
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Staff Sections (Admins, Coordinators, Supervisors) -->
        <div class="space-y-4">
            <h3 class="text-xl font-bold text-gray-800 dark:text-gray-200 px-1">Staff Members</h3>
            
            <!-- Admins -->
            <div x-data="{ open: true }" class="border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden bg-white dark:bg-gray-800">
                <button @click="open = !open" class="w-full px-6 py-4 bg-gray-50 dark:bg-gray-900/50 flex items-center justify-between hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                    <div class="flex items-center gap-3">
                        <span class="px-2 py-1 rounded text-xs font-bold bg-purple-100 text-purple-700 dark:bg-purple-900 dark:text-purple-300 uppercase">
                            Administrators
                        </span>
                        <span class="text-sm text-gray-500 dark:text-gray-400">
                            {{ $admins->count() }} Users
                        </span>
                    </div>
                    <svg class="h-5 w-5 text-gray-400 transform transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
                <div x-show="open" class="overflow-x-auto">
                    <x-user-table :users="$admins" />
                </div>
            </div>

            <!-- Staff -->
            <div x-data="{ open: true }" class="border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden bg-white dark:bg-gray-800">
                <button @click="open = !open" class="w-full px-6 py-4 bg-gray-50 dark:bg-gray-900/50 flex items-center justify-between hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                    <div class="flex items-center gap-3">
                        <span class="px-2 py-1 rounded text-xs font-bold bg-fuchsia-100 text-fuchsia-700 dark:bg-fuchsia-900 dark:text-fuchsia-300 uppercase">
                            Staff
                        </span>
                        <span class="text-sm text-gray-500 dark:text-gray-400">
                            {{ $staff->count() }} Users
                        </span>
                    </div>
                    <svg class="h-5 w-5 text-gray-400 transform transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
                <div x-show="open" class="overflow-x-auto">
                    <x-user-table :users="$staff" />
                </div>
            </div>

            <!-- Coordinators -->
            <div x-data="{ open: true }" class="border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden bg-white dark:bg-gray-800">
                <button @click="open = !open" class="w-full px-6 py-4 bg-gray-50 dark:bg-gray-900/50 flex items-center justify-between hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                    <div class="flex items-center gap-3">
                        <span class="px-2 py-1 rounded text-xs font-bold bg-cyan-100 text-cyan-700 dark:bg-cyan-900 dark:text-cyan-300 uppercase">
                            Coordinators
                        </span>
                        <span class="text-sm text-gray-500 dark:text-gray-400">
                            {{ $coordinators->count() }} Users
                        </span>
                    </div>
                    <svg class="h-5 w-5 text-gray-400 transform transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
                <div x-show="open" class="overflow-x-auto">
                    <x-user-table :users="$coordinators" />
                </div>
            </div>

            <!-- Supervisors -->
            <div x-data="{ open: true }" class="border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden bg-white dark:bg-gray-800">
                <button @click="open = !open" class="w-full px-6 py-4 bg-gray-50 dark:bg-gray-900/50 flex items-center justify-between hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                    <div class="flex items-center gap-3">
                        <span class="px-2 py-1 rounded text-xs font-bold bg-emerald-100 text-emerald-700 dark:bg-emerald-900 dark:text-emerald-300 uppercase">
                            Supervisors
                        </span>
                        <span class="text-sm text-gray-500 dark:text-gray-400">
                            {{ $supervisors->count() }} Users
                        </span>
                    </div>
                    <svg class="h-5 w-5 text-gray-400 transform transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
                <div x-show="open" class="overflow-x-auto">
                    <x-user-table :users="$supervisors" />
                </div>
            </div>

            <!-- OJT Advisers -->
            <div x-data="{ open: true }" class="border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden bg-white dark:bg-gray-800">
                <button @click="open = !open" class="w-full px-6 py-4 bg-gray-50 dark:bg-gray-900/50 flex items-center justify-between hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                    <div class="flex items-center gap-3">
                        <span class="px-2 py-1 rounded text-xs font-bold bg-orange-100 text-orange-700 dark:bg-orange-900 dark:text-orange-300 uppercase">
                            OJT Advisers
                        </span>
                        <span class="text-sm text-gray-500 dark:text-gray-400">
                            {{ $ojtAdvisers->count() }} Users
                        </span>
                    </div>
                    <svg class="h-5 w-5 text-gray-400 transform transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
                <div x-show="open" class="overflow-x-auto">
                    <x-user-table :users="$ojtAdvisers" />
                </div>
            </div>
        </div>

        <!-- OJT Student Sections -->
        <div class="space-y-4 pt-4 border-t border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between px-1">
                <h3 class="text-xl font-bold text-gray-800 dark:text-gray-200">OJT Student Roster</h3>
                <span class="text-sm text-gray-500">Grouped by Section</span>
            </div>

            @forelse($studentsBySection as $section => $students)
                <div x-data="{ showModal: false, search: '' }" class="border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden bg-white dark:bg-gray-800 transition-shadow hover:shadow-md">
                    <button @click="showModal = true" class="w-full px-6 py-4 bg-gray-50 dark:bg-gray-900/50 flex items-center justify-between hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors group">
                        <div class="flex items-center gap-3">
                            <span class="px-3 py-1.5 rounded-lg text-sm font-bold bg-indigo-100 text-indigo-700 dark:bg-indigo-900 dark:text-indigo-300 uppercase tracking-wide group-hover:scale-105 transition-transform">
                                {{ $section }}
                            </span>
                            <span class="text-sm text-gray-600 dark:text-gray-400 font-medium">
                                {{ $students->count() }} OJT Students
                            </span>
                        </div>
                        <div class="p-2 rounded-full bg-white dark:bg-gray-800 text-gray-400 group-hover:text-indigo-500 shadow-sm transition-colors">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4" />
                            </svg>
                        </div>
                    </button>

                    <!-- Modal -->
                    <div x-show="showModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                            <!-- Overlay -->
                            <div class="fixed inset-0 bg-gray-900/75 transition-opacity backdrop-blur-sm" @click="showModal = false" aria-hidden="true" x-transition.opacity></div>

                            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                            <!-- Modal Panel -->
                            <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-5xl sm:w-full border border-gray-200 dark:border-gray-700" x-transition>
                                <!-- Header -->
                                <div class="bg-gray-50 dark:bg-gray-900/80 px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex flex-col sm:flex-row justify-between items-center gap-4">
                                    <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100 flex items-center gap-2">
                                        <span class="px-2 py-1 rounded text-sm bg-indigo-100 text-indigo-700 dark:bg-indigo-900 dark:text-indigo-300">{{ $section }}</span>
                                        <span>OJT Student Roster</span>
                                    </h3>
                                    
                                    <!-- Search Input -->
                                    <div class="relative w-full sm:w-72">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                            </svg>
                                        </div>
                                        <input x-model="search" type="text" class="block w-full pl-10 pr-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg leading-5 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm transition-shadow" placeholder="Search by name or email...">
                                    </div>
                                </div>

                                <!-- Content -->
                                <div class="bg-white dark:bg-gray-800 max-h-[70vh] overflow-y-auto">
                                    <table class="min-w-full text-left text-sm">
                                        <thead class="sticky top-0 z-10 bg-gray-50 dark:bg-gray-900/90 backdrop-blur-sm shadow-sm">
                                            <tr class="border-b border-gray-200 dark:border-gray-700">
                                                <th class="px-6 py-3 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Name</th>
                                                <th class="px-6 py-3 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Email</th>
                                                <th class="px-6 py-3 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                                                <th class="px-6 py-3 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                            @foreach ($students as $user)
                                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors" x-show="!search || $el.innerText.toLowerCase().includes(search.toLowerCase())">
                                                    <td class="px-6 py-4 whitespace-nowrap">
                                                        <div class="font-medium text-gray-900 dark:text-gray-100">{{ $user->name }}</div>
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-gray-500 dark:text-gray-400">
                                                        {{ $user->email }}
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap">
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
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                        <div class="flex items-center gap-3">
                                                            <a href="{{ route('admin.users.show', $user) }}" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 font-bold text-xs uppercase tracking-wide">
                                                                View
                                                            </a>
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
                                    
                                    <!-- No Results Message -->
                                    <div class="p-8 text-center text-gray-500 dark:text-gray-400" x-show="$el.previousElementSibling.querySelectorAll('tr[x-show]').length === 0 && search !== ''" style="display: none;">
                                        No students found matching "<span x-text="search" class="font-bold"></span>"
                                    </div>
                                </div>

                                <!-- Footer -->
                                <div class="bg-gray-50 dark:bg-gray-900/80 px-6 py-3 flex justify-end">
                                    <button @click="showModal = false" type="button" class="w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700">
                                        Close
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-8 text-gray-500 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
                    No students found.
                </div>
            @endforelse
        </div>
    </div>
</x-admin-layout>