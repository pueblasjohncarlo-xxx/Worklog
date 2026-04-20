<x-coordinator-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="text-3xl font-extrabold text-white drop-shadow-md tracking-tight">Assignments</h2>
        </div>
    </x-slot>

    <div class="space-y-6" x-data='{
        assignments: @json($assignmentData),
        supervisors: @json($supervisors->map(fn($s) => ["id" => $s->id, "name" => $s->name])),
        advisers: @json($ojtAdvisers->map(fn($a) => ["id" => $a->id, "name" => $a->name])),
        companies: @json($companies->map(fn($c) => ["id" => $c->id, "name" => $c->name])),
        searchTerm: "",
        selectedCompany: "",
        selectedStatus: "",
        selectedAssignmentStatus: "",
        selectedSupervisor: "",
        selectedAdviser: "",
        
        getFilteredAssignments() {
            return this.assignments.filter(a => {
                const matchesSearch = a.student_name.toLowerCase().includes(this.searchTerm.toLowerCase()) || 
                                    a.student_email.toLowerCase().includes(this.searchTerm.toLowerCase());
                const matchesCompany = this.selectedCompany === "" || String(a.company_id) === String(this.selectedCompany);
                const matchesStatus = this.selectedStatus === "" || a.status === this.selectedStatus;
                const matchesAssignStatus = this.selectedAssignmentStatus === "" || a.assignment_status === this.selectedAssignmentStatus;
                const matchesSupervisor = this.selectedSupervisor === "" || a.supervisor_id == this.selectedSupervisor;
                const matchesAdviser = this.selectedAdviser === "" || a.adviser_id == this.selectedAdviser;
                
                return matchesSearch && matchesCompany && matchesStatus && matchesAssignStatus && matchesSupervisor && matchesAdviser;
            });
        },
        
        clearFilters() {
            this.searchTerm = "";
            this.selectedCompany = "";
            this.selectedStatus = "";
            this.selectedAssignmentStatus = "";
            this.selectedSupervisor = "";
            this.selectedAdviser = "";
        }
    }'>
        <!-- Status Messages -->
        @if ($errors->any())
            <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4">
                <div class="flex items-start">
                    <svg class="h-5 w-5 text-red-400 dark:text-red-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4v.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-800 dark:text-red-300">Assignment Error</h3>
                        <p class="text-sm text-red-700 dark:text-red-400 mt-1">{{ $errors->first() }}</p>
                    </div>
                </div>
            </div>
        @endif

        @if (session('status'))
            <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4">
                <div class="flex items-start">
                    <svg class="h-5 w-5 text-green-400 dark:text-green-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-green-800 dark:text-green-300">Success</h3>
                        <p class="text-sm text-green-700 dark:text-green-400 mt-1">{{ session('status') }}</p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Assignments</p>
                        <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2">{{ $totalAssigned }}</p>
                    </div>
                    <div class="p-3 bg-blue-100 dark:bg-blue-900/30 rounded-full">
                        <svg class="h-8 w-8 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Fully Assigned</p>
                        <p class="text-3xl font-bold text-green-600 dark:text-green-400 mt-2">{{ $fullyAssigned }}</p>
                    </div>
                    <div class="p-3 bg-green-100 dark:bg-green-900/30 rounded-full">
                        <svg class="h-8 w-8 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Incomplete</p>
                        <p class="text-3xl font-bold text-yellow-600 dark:text-yellow-400 mt-2">{{ $incomplete }}</p>
                    </div>
                    <div class="p-3 bg-yellow-100 dark:bg-yellow-900/30 rounded-full">
                        <svg class="h-8 w-8 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4v.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Active</p>
                        <p class="text-3xl font-bold text-indigo-600 dark:text-indigo-400 mt-2">{{ $active }}</p>
                    </div>
                    <div class="p-3 bg-indigo-100 dark:bg-indigo-900/30 rounded-full">
                        <svg class="h-8 w-8 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Supervisor Only</p>
                        <p class="text-3xl font-bold text-purple-600 dark:text-purple-400 mt-2">{{ $supervisorOnly }}</p>
                    </div>
                    <div class="p-3 bg-purple-100 dark:bg-purple-900/30 rounded-full">
                        <svg class="h-8 w-8 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 12H9m6 0a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Create New Assignment Form -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Create New Assignment</h3>
            
            <form id="assignmentForm" method="POST" action="{{ route('coordinator.assignments.store') }}" class="space-y-4">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <!-- Students Search -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">OJT Student(s)</label>
                        <select
                            id="student_ids"
                            name="student_ids[]"
                            class="w-full rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:border-indigo-500 focus:ring-indigo-500"
                            multiple="multiple"
                            required
                        >
                            @foreach ($groupedStudents as $group => $students)
                                <optgroup label="{{ $group }}">
                                    @foreach ($students as $student)
                                        <option value="{{ $student->id }}" data-email="{{ $student->email }}">
                                            {{ $student->lastname }}, {{ $student->firstname }}
                                        </option>
                                    @endforeach
                                </optgroup>
                            @endforeach
                        </select>
                    </div>

                    <!-- Supervisor -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Supervisor *</label>
                        <select
                            id="supervisor_id"
                            name="supervisor_id"
                            class="w-full rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:border-indigo-500 focus:ring-indigo-500"
                            required
                        >
                            <option value="">Select supervisor</option>
                            @foreach ($supervisors as $supervisor)
                                <option value="{{ $supervisor->id }}">{{ $supervisor->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- OJT Adviser -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">OJT Adviser</label>
                        <select
                            id="ojt_adviser_id"
                            name="ojt_adviser_id"
                            class="w-full rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:border-indigo-500 focus:ring-indigo-500"
                        >
                            <option value="">Select adviser (optional)</option>
                            @foreach ($ojtAdvisers as $adviser)
                                <option value="{{ $adviser->id }}">{{ $adviser->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Company -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Company *</label>
                        <select
                            id="company_id"
                            name="company_id"
                            class="w-full rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:border-indigo-500 focus:ring-indigo-500"
                            required
                        >
                            <option value="">Select company</option>
                            @foreach ($companies as $company)
                                <option value="{{ $company->id }}">{{ $company->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Start Date</label>
                        <input
                            id="start_date"
                            name="start_date"
                            type="date"
                            class="w-full rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:border-indigo-500 focus:ring-indigo-500"
                        >
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">End Date</label>
                        <input
                            id="end_date"
                            name="end_date"
                            type="date"
                            class="w-full rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:border-indigo-500 focus:ring-indigo-500"
                        >
                    </div>
                </div>

                <div class="flex justify-end gap-2 pt-2">
                    <button
                        type="button"
                        onclick="confirmAssignment()"
                        class="inline-flex items-center px-4 py-2 rounded-lg bg-indigo-600 text-sm font-semibold uppercase tracking-wide text-white hover:bg-indigo-700 transition-colors"
                    >
                        <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Create Assignment
                    </button>
                </div>
            </form>
        </div>

        <!-- Confirmation Modal -->
        <div id="confirmationModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity"></div>
                <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <div class="bg-white dark:bg-gray-800 px-6 pt-5 pb-4 sm:p-6">
                        <div class="flex items-center mb-4">
                            <div class="flex items-center justify-center h-12 w-12 rounded-full bg-indigo-100 dark:bg-indigo-900/30">
                                <svg class="h-6 w-6 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <h3 class="ml-4 text-lg font-semibold text-gray-900 dark:text-white">Review Assignment</h3>
                        </div>
                        <div class="space-y-3 text-sm">
                            <div class="bg-gray-50 dark:bg-gray-700/50 p-4 rounded-lg space-y-2">
                                <p class="font-medium text-gray-700 dark:text-gray-300">OJT Students: <span id="confirm-count" class="text-indigo-600 dark:text-indigo-400 font-bold">0</span></p>
                                <div id="confirm-students-list" class="text-xs text-gray-600 dark:text-gray-300 max-h-32 overflow-y-auto pl-4 border-l-2 border-indigo-500 space-y-1"></div>
                                <p class="font-medium text-gray-700 dark:text-gray-300 pt-2">Supervisor: <span id="confirm-supervisor" class="text-indigo-600 dark:text-indigo-400 font-bold">-</span></p>
                                <p class="font-medium text-gray-700 dark:text-gray-300">OJT Adviser: <span id="confirm-adviser" class="text-indigo-600 dark:text-indigo-400 font-bold">-</span></p>
                                <p class="font-medium text-gray-700 dark:text-gray-300">Company: <span id="confirm-company" class="text-indigo-600 dark:text-indigo-400 font-bold">-</span></p>
                                <p class="font-medium text-gray-700 dark:text-gray-300">Duration: <span id="confirm-duration" class="text-indigo-600 dark:text-indigo-400 font-bold">-</span></p>
                            </div>
                            <p class="text-xs italic text-red-600 dark:text-red-400" id="confirm-warning"></p>
                        </div>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-700 px-6 py-3 flex gap-2 justify-end">
                        <button type="button" onclick="closeModal()" class="px-4 py-2 text-gray-700 dark:text-gray-300 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors text-sm font-medium">
                            Cancel
                        </button>
                        <button type="button" onclick="submitForm()" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors text-sm font-medium">
                            Confirm & Create
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters Section -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Existing Assignments</h3>
                <button @click="clearFilters()" class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline font-medium">Clear Filters</button>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-3">
                <!-- Search -->
                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Search Student</label>
                    <input type="text" x-model="searchTerm" placeholder="Name or email..." class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                </div>

                <!-- Company Filter -->
                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Company</label>
                    <select x-model="selectedCompany" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        <option value="">All Companies</option>
                        @foreach($companies as $company)
                            <option value="{{ $company->id }}">{{ $company->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- OJT Status Filter -->
                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">OJT Status</label>
                    <select x-model="selectedStatus" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        <option value="">All Status</option>
                        <option value="active">Active</option>
                        <option value="completed">Completed</option>
                    </select>
                </div>

                <!-- Assignment Status Filter -->
                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Assignment Status</label>
                    <select x-model="selectedAssignmentStatus" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        <option value="">All</option>
                        <option value="complete">Complete</option>
                        <option value="incomplete">Incomplete</option>
                        <option value="unassigned">Unassigned</option>
                    </select>
                </div>

                <!-- Supervisor Filter -->
                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Supervisor</label>
                    <select x-model="selectedSupervisor" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        <option value="">All Supervisors</option>
                        @foreach($supervisors as $supervisor)
                            <option value="{{ $supervisor->id }}">{{ $supervisor->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Adviser Filter -->
                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Adviser</label>
                    <select x-model="selectedAdviser" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        <option value="">All Advisers</option>
                        @foreach($ojtAdvisers as $adviser)
                            <option value="{{ $adviser->id }}">{{ $adviser->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <!-- Assignments Table -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
            <template x-if="assignments.length === 0">
                <div class="p-12 text-center">
                    <svg class="h-12 w-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <p class="text-gray-500 dark:text-gray-400 text-sm">No assignments yet. Create one above!</p>
                </div>
            </template>

            <template x-if="assignments.length > 0 && getFilteredAssignments().length === 0">
                <div class="p-12 text-center">
                    <svg class="h-12 w-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                    <p class="text-gray-500 dark:text-gray-400 text-sm">No assignments match your filters</p>
                </div>
            </template>

            <template x-if="getFilteredAssignments().length > 0">
                <div class="overflow-x-auto">
                    <table class="min-w-[900px] w-full">
                        <thead class="bg-gray-50 dark:bg-gray-900/50 border-b border-gray-200 dark:border-gray-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Student</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Supervisor</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">OJT Adviser</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Company</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Duration</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Hours</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            <template x-for="assignment in getFilteredAssignments()" :key="assignment.id">
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors" :class="assignment.assignment_status === 'complete' ? 'bg-white dark:bg-gray-800' : assignment.assignment_status === 'incomplete' ? 'bg-yellow-50/50 dark:bg-yellow-900/10' : 'bg-red-50/50 dark:bg-red-900/10'">
                                    <!-- Status Indicator -->
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <template x-if="assignment.assignment_status === 'complete'">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-200">
                                                ✓ Complete
                                            </span>
                                        </template>
                                        <template x-if="assignment.assignment_status === 'incomplete'">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-200">
                                                ⚠ Incomplete
                                            </span>
                                        </template>
                                        <template x-if="assignment.assignment_status === 'unassigned'">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-200">
                                                ✕ Unassigned
                                            </span>
                                        </template>
                                    </td>

                                    <!-- Student -->
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="h-9 w-9 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-full flex items-center justify-center text-white font-bold text-sm flex-shrink-0">
                                                <span x-text="assignment.student_name.charAt(0).toUpperCase()"></span>
                                            </div>
                                            <div class="ml-3">
                                                <p class="text-sm font-medium text-gray-900 dark:text-white" x-text="assignment.student_name"></p>
                                                <p class="text-xs text-gray-500 dark:text-gray-400" x-text="assignment.student_program"></p>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Supervisor -->
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <template x-if="assignment.supervisor_name !== 'Not Assigned'">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-200" x-text="assignment.supervisor_name"></span>
                                        </template>
                                        <template x-if="assignment.supervisor_name === 'Not Assigned'">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 dark:bg-gray-900/30 text-gray-600 dark:text-gray-400">Not Assigned</span>
                                        </template>
                                    </td>

                                    <!-- OJT Adviser -->
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <template x-if="assignment.adviser_name !== 'Not Assigned'">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 dark:bg-purple-900/30 text-purple-800 dark:text-purple-200" x-text="assignment.adviser_name"></span>
                                        </template>
                                        <template x-if="assignment.adviser_name === 'Not Assigned'">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 dark:bg-gray-900/30 text-gray-600 dark:text-gray-400">Not Assigned</span>
                                        </template>
                                    </td>

                                    <!-- Company -->
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white" x-text="assignment.company_name"></td>

                                    <!-- Duration -->
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-300">
                                        <template x-if="assignment.start_date && assignment.end_date">
                                            <span x-text="`${assignment.start_date} to ${assignment.end_date}`"></span>
                                        </template>
                                        <template x-if="!assignment.start_date || !assignment.end_date">
                                            <span class="text-gray-400 italic">Not specified</span>
                                        </template>
                                    </td>

                                    <!-- Hours -->
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-indigo-100 dark:bg-indigo-900/30 text-indigo-800 dark:text-indigo-200" x-text="`${assignment.required_hours} hrs`"></span>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </template>
        </div>
    </div>

    @push('scripts')
    <script>
        $(document).ready(function() {
            $('#student_ids').select2({
                placeholder: "Search by Name or Email...",
                allowClear: true,
                width: '100%',
                closeOnSelect: false,
            });
            $('#supervisor_id').select2({ width: '100%' });
            $('#ojt_adviser_id').select2({ width: '100%' });
            $('#company_id').select2({ width: '100%' });
        });

        function confirmAssignment() {
            const studentIds = $('#student_ids').val();
            const supervisorId = $('#supervisor_id').val();
            const companyId = $('#company_id').val();

            if (!studentIds || studentIds.length === 0) {
                alert('Please select at least one student.');
                return;
            }
            if (!supervisorId) {
                alert('Please select a supervisor.');
                return;
            }
            if (!companyId) {
                alert('Please select a company.');
                return;
            }

            $('#confirm-count').text(studentIds.length);
            
            let studentList = '';
            $('#student_ids option:selected').each(function() {
                studentList += `<div>• ${$(this).text().trim()}</div>`;
            });
            $('#confirm-students-list').html(studentList);

            $('#confirm-supervisor').text($('#supervisor_id option:selected').text().trim());
            const adviserText = $('#ojt_adviser_id').val() ? $('#ojt_adviser_id option:selected').text().trim() : 'None';
            $('#confirm-adviser').text(adviserText);
            $('#confirm-company').text($('#company_id option:selected').text().trim());
            
            const startDate = $('#start_date').val();
            const endDate = $('#end_date').val();
            let duration = 'Not specified';
            if (startDate && endDate) {
                duration = `${startDate} to ${endDate}`;
            } else if (startDate) {
                duration = `Starts ${startDate}`;
            }
            $('#confirm-duration').text(duration);

            if (studentIds.length > 1) {
                $('#confirm-warning').text(`Note: This will create ${studentIds.length} separate assignments.`);
            } else {
                $('#confirm-warning').text('');
            }

            $('#confirmationModal').removeClass('hidden');
        }

        function closeModal() {
            $('#confirmationModal').addClass('hidden');
        }

        function submitForm() {
            $('#assignmentForm').submit();
        }
    </script>
    @endpush
</x-coordinator-layout>
