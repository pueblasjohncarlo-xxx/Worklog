<x-admin-layout>
    <x-slot name="header">
        User Management
    </x-slot>

    <div class="space-y-6">
        <!-- Create User Form -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg border border-gray-200 dark:border-gray-700">
            <div class="p-6 text-gray-900 dark:text-gray-100">
                @if ($errors->any())
                    <div class="mb-4 rounded-lg border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-medium text-rose-800 dark:border-rose-900/40 dark:bg-rose-950/40 dark:text-rose-200">
                        {{ $errors->first() }}
                    </div>
                @endif

                @if (session('status'))
                    <div class="mb-4 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800 dark:border-emerald-900/40 dark:bg-emerald-950/40 dark:text-emerald-200">
                        {{ session('status') }}
                    </div>
                @endif

                <div class="mb-2" x-data="{ role: '{{ old('role', 'student') }}' }">
                    <div class="mb-3 space-y-1">
                        <h3 id="create-user" class="text-lg font-bold text-gray-900 dark:text-gray-100">Create New User</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-300">Create admin, adviser, supervisor, coordinator, or student accounts from one form.</p>
                    </div>
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
                            class="inline-flex items-center justify-center px-4 py-2 rounded-md bg-indigo-700 text-white text-sm font-semibold shadow-md transition-colors hover:bg-indigo-600 focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:ring-offset-2 dark:focus:ring-offset-gray-800"
                        >
                            Create
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Administrative Sections -->
        <div class="space-y-4">
            <h3 class="px-1 text-xl font-bold text-gray-900 dark:text-gray-100">Administrative Roles</h3>
            
            <!-- Admins -->
            <div x-data="{ open: true }" class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <button @click="open = !open" class="flex w-full items-center justify-between bg-gray-50 px-6 py-4 transition-colors hover:bg-gray-100 dark:bg-gray-900/50 dark:hover:bg-gray-800">
                    <div class="flex items-center gap-3">
                        <span class="rounded-full bg-purple-100 px-2.5 py-1 text-xs font-bold uppercase tracking-wide text-purple-800 dark:bg-purple-900 dark:text-purple-200">
                            Administrators
                        </span>
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                            {{ $admins->count() }} Users
                        </span>
                    </div>
                    <svg class="h-5 w-5 text-gray-500 transition-transform duration-200 dark:text-gray-300" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
                <div x-show="open" class="overflow-x-auto">
                    <x-user-table :users="$admins" />
                </div>
            </div>

            <!-- Coordinators -->
            <div x-data="{ open: true }" class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <button @click="open = !open" class="flex w-full items-center justify-between bg-gray-50 px-6 py-4 transition-colors hover:bg-gray-100 dark:bg-gray-900/50 dark:hover:bg-gray-800">
                    <div class="flex items-center gap-3">
                        <span class="rounded-full bg-cyan-100 px-2.5 py-1 text-xs font-bold uppercase tracking-wide text-cyan-800 dark:bg-cyan-900 dark:text-cyan-200">
                            Coordinators
                        </span>
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                            {{ $coordinators->count() }} Users
                        </span>
                    </div>
                    <svg class="h-5 w-5 text-gray-500 transition-transform duration-200 dark:text-gray-300" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
                <div x-show="open" class="overflow-x-auto">
                    <x-user-table :users="$coordinators" />
                </div>
            </div>

            <!-- Supervisors -->
            <div x-data="{ open: true }" class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <button @click="open = !open" class="flex w-full items-center justify-between bg-gray-50 px-6 py-4 transition-colors hover:bg-gray-100 dark:bg-gray-900/50 dark:hover:bg-gray-800">
                    <div class="flex items-center gap-3">
                        <span class="rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-bold uppercase tracking-wide text-emerald-800 dark:bg-emerald-900 dark:text-emerald-200">
                            Supervisors
                        </span>
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                            {{ $supervisors->count() }} Users
                        </span>
                    </div>
                    <svg class="h-5 w-5 text-gray-500 transition-transform duration-200 dark:text-gray-300" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
                <div x-show="open" class="overflow-x-auto">
                    <x-user-table :users="$supervisors" />
                </div>
            </div>

            <!-- OJT Advisers -->
            <div x-data="{ open: true }" class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <button @click="open = !open" class="flex w-full items-center justify-between bg-gray-50 px-6 py-4 transition-colors hover:bg-gray-100 dark:bg-gray-900/50 dark:hover:bg-gray-800">
                    <div class="flex items-center gap-3">
                        <span class="rounded-full bg-orange-100 px-2.5 py-1 text-xs font-bold uppercase tracking-wide text-orange-800 dark:bg-orange-900 dark:text-orange-200">
                            OJT Advisers
                        </span>
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                            {{ $ojtAdvisers->count() }} Users
                        </span>
                    </div>
                    <svg class="h-5 w-5 text-gray-500 transition-transform duration-200 dark:text-gray-300" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
                <div x-show="open" class="overflow-x-auto">
                    <x-user-table :users="$ojtAdvisers" />
                </div>
            </div>
        </div>

        <!-- OJT Student Sections -->
        <div class="space-y-4 border-t border-gray-200 pt-4 dark:border-gray-700">
            <div class="flex items-center justify-between px-1">
                <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100">OJT Student Roster</h3>
                <span class="text-sm font-medium text-gray-600 dark:text-gray-300">Grouped by Section</span>
            </div>

            @forelse($studentsBySection as $section => $students)
                <div x-data="{ showModal: false, search: '' }" class="border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden bg-white dark:bg-gray-800 transition-shadow hover:shadow-md">
                    <button @click="showModal = true" class="flex w-full items-center justify-between bg-gray-50 px-6 py-4 transition-colors hover:bg-gray-100 dark:bg-gray-900/50 dark:hover:bg-gray-800 group">
                        <div class="flex items-center gap-3">
                            <span class="rounded-lg bg-indigo-100 px-3 py-1.5 text-sm font-bold uppercase tracking-wide text-indigo-800 transition-transform group-hover:scale-105 dark:bg-indigo-900 dark:text-indigo-200">
                                {{ $section }}
                            </span>
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                {{ $students->count() }} OJT Students
                            </span>
                        </div>
                        <div class="rounded-full bg-white p-2 text-gray-500 shadow-sm transition-colors group-hover:text-indigo-600 dark:bg-gray-800 dark:text-gray-300">
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
                                <div class="flex flex-col items-center justify-between gap-4 border-b border-gray-200 bg-gray-50 px-6 py-4 dark:border-gray-700 dark:bg-gray-900/80 sm:flex-row">
                                    <h3 class="flex items-center gap-2 text-xl font-bold text-gray-900 dark:text-gray-100">
                                        <span class="rounded-full bg-indigo-100 px-2 py-1 text-sm text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200">{{ $section }}</span>
                                        <span>OJT Student Roster</span>
                                    </h3>
                                    
                                    <!-- Search Input -->
                                    <div class="relative w-full sm:w-72">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                            </svg>
                                        </div>
                                        <input x-model="search" type="text" class="block w-full rounded-lg border border-gray-300 bg-white py-2 pl-10 pr-3 leading-5 text-gray-900 transition-shadow placeholder-gray-500 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 sm:text-sm" placeholder="Search by name or email...">
                                    </div>
                                </div>

                                <!-- Content -->
                                <div class="bg-white dark:bg-gray-800 max-h-[70vh] overflow-y-auto">
                                    <table class="min-w-full text-left text-sm">
                                        <thead class="sticky top-0 z-10 bg-gray-50 shadow-sm backdrop-blur-sm dark:bg-gray-900/90">
                                            <tr class="border-b border-gray-200 dark:border-gray-700">
                                                <th class="px-6 py-3 text-xs font-semibold uppercase tracking-wider text-gray-700 dark:text-gray-300">Name</th>
                                                <th class="px-6 py-3 text-xs font-semibold uppercase tracking-wider text-gray-700 dark:text-gray-300">Email</th>
                                                <th class="px-6 py-3 text-xs font-semibold uppercase tracking-wider text-gray-700 dark:text-gray-300">Status</th>
                                                <th class="px-6 py-3 text-xs font-semibold uppercase tracking-wider text-gray-700 dark:text-gray-300">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                            @foreach ($students as $user)
                                                <tr
                                                    class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors"
                                                    x-show="!search || $el.innerText.toLowerCase().includes(search.toLowerCase())"
                                                    x-transition
                                                >
                                                    <td class="px-6 py-4 whitespace-nowrap">
                                                        <div class="font-semibold text-gray-900 dark:text-gray-100">{{ $user->name }}</div>
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-gray-700 dark:text-gray-300">
                                                        {{ $user->email }}
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap">
                                                        @if($user->has_requested_account && !$user->is_approved)
                                                            <span class="inline-flex items-center rounded-full bg-amber-100 px-2.5 py-1 text-xs font-semibold text-amber-800 dark:bg-amber-900 dark:text-amber-200">
                                                                Pending
                                                            </span>
                                                        @elseif(!$user->has_requested_account)
                                                            <span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-800 dark:bg-slate-700 dark:text-slate-200">
                                                                Imported
                                                            </span>
                                                        @else
                                                            <span class="inline-flex items-center rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-semibold text-emerald-800 dark:bg-emerald-900 dark:text-emerald-200">
                                                                Active
                                                            </span>
                                                        @endif
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                        <div class="flex items-center gap-3">
                                                            <a href="{{ route('admin.users.show', $user) }}" class="text-xs font-bold uppercase tracking-wide text-indigo-700 hover:text-indigo-900 dark:text-indigo-300 dark:hover:text-indigo-200">
                                                                View
                                                            </a>
                                                            <form method="POST" action="{{ route('admin.users.destroy', $user) }}" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this user?');">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="text-xs font-bold uppercase tracking-wide text-rose-700 hover:text-rose-900 dark:text-rose-300 dark:hover:text-rose-200">
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
                                    <div
                                        class="p-8 text-center text-gray-600 dark:text-gray-300"
                                        x-show="search && !$el.previousElementSibling.querySelector('tbody tr[x-show]:not([style*=&quot;display: none&quot;])')"
                                        style="display: none;"
                                    >
                                        No OJT students found matching "<span x-text="search" class="font-bold"></span>"
                                    </div>
                                </div>

                                <!-- Footer -->
                                <div class="bg-gray-50 dark:bg-gray-900/80 px-6 py-3 flex justify-end">
                                    <button @click="showModal = false" type="button" class="w-full inline-flex justify-center rounded-lg border border-gray-300 bg-white px-4 py-2 text-base font-semibold text-gray-800 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700 dark:focus:ring-offset-gray-800">
                                        Close
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="rounded-lg border border-gray-200 bg-white py-8 text-center text-gray-600 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300">
                    No OJT students found.
                </div>
            @endforelse
        </div>
    </div>
</x-admin-layout>
