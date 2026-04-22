<x-coordinator-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="text-3xl font-extrabold text-white drop-shadow-md tracking-tight">Supervisor Overview</h2>
        </div>
    </x-slot>

    <div class="space-y-6" x-data="{
        supervisors: @js($supervisors),
        panel: @js(request('panel', 'supervisors')),
        searchTerm: @js(request('q', '')),
        selectedCompany: @js(request('company', '')),
        selectedStatus: @js(request('status', '')),
        selectedSupervisor: null,
        showDetailsModal: false,
        
        getFilteredSupervisors() {
            return this.supervisors.filter(s => {
                const matchesSearch = s.name.toLowerCase().includes(this.searchTerm.toLowerCase()) || 
                                    s.email.toLowerCase().includes(this.searchTerm.toLowerCase());
                const matchesCompany = this.selectedCompany === '' || 
                                    s.companies.some(c => String(c.id) == String(this.selectedCompany));
                const matchesStatus = this.selectedStatus === '' || s.status === this.selectedStatus;
                
                return matchesSearch && matchesCompany && matchesStatus;
            });
        },
        
        openDetails(supervisor) {
            this.selectedSupervisor = supervisor;
            this.showDetailsModal = true;
        },
        
        closeDetails() {
            this.showDetailsModal = false;
            this.selectedSupervisor = null;
        },
        
        clearFilters() {
            this.searchTerm = '';
            this.selectedCompany = '';
            this.selectedStatus = '';
        },

        getConnectedCompanies() {
            const map = new Map();
            this.supervisors.forEach(s => {
                (s.companies || []).forEach(c => {
                    const key = String(c.id);
                    if (!map.has(key)) {
                        map.set(key, { id: c.id, name: c.name });
                    }
                });
            });
            return Array.from(map.values()).sort((a, b) => String(a.name).localeCompare(String(b.name)));
        },

        getStudentsSupervised() {
            // Unique students across supervisors; if duplicates exist, prefer an active assignment.
            const map = new Map();

            this.supervisors.forEach(s => {
                (s.students || []).forEach(st => {
                    const key = String(st.id);
                    const entry = {
                        id: st.id,
                        name: st.name,
                        email: st.email,
                        program: st.program,
                        company_id: st.company_id ?? null,
                        company_name: st.company_name ?? null,
                        status: st.status,
                        supervisor_name: s.name,
                    };

                    if (!map.has(key)) {
                        map.set(key, entry);
                        return;
                    }

                    const current = map.get(key);
                    const currentIsActive = String(current.status).toLowerCase() === 'active';
                    const entryIsActive = String(entry.status).toLowerCase() === 'active';
                    if (!currentIsActive && entryIsActive) {
                        map.set(key, entry);
                    }
                });
            });

            return Array.from(map.values()).sort((a, b) => String(a.name).localeCompare(String(b.name)));
        }
    }">
        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Total Supervisors -->
                <a href="{{ route('coordinator.supervisor-overview', ['panel' => 'supervisors']) }}#roster" class="block bg-white dark:bg-gray-800 rounded-lg shadow p-6 transition hover:shadow-md focus:outline-none focus:ring-2 focus:ring-indigo-500 cursor-pointer">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-700 dark:text-gray-200">Total Supervisors</p>
                        <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2">{{ $totalSupervisors }}</p>
                    </div>
                    <div class="p-3 bg-blue-100 dark:bg-blue-900/30 rounded-full">
                        <svg class="h-8 w-8 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 12H9m6 0a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </div>
                </div>
                </a>

            <!-- Active Supervisors -->
                <a href="{{ route('coordinator.supervisor-overview', ['panel' => 'supervisors', 'status' => 'Active']) }}#roster" class="block bg-white dark:bg-gray-800 rounded-lg shadow p-6 transition hover:shadow-md focus:outline-none focus:ring-2 focus:ring-indigo-500 cursor-pointer">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-700 dark:text-gray-200">Active Supervisors</p>
                        <p class="text-3xl font-bold text-green-600 dark:text-green-400 mt-2">{{ $activeSupervisors }}</p>
                        <p class="text-xs text-gray-600 dark:text-gray-300 mt-1">{{ $totalSupervisors > 0 ? round(($activeSupervisors / $totalSupervisors) * 100, 0) : 0 }}%</p>
                    </div>
                    <div class="p-3 bg-green-100 dark:bg-green-900/30 rounded-full">
                        <svg class="h-8 w-8 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
                </a>

            <!-- Total Companies -->
                <a href="{{ route('coordinator.supervisor-overview', ['panel' => 'companies']) }}#companies" class="block bg-white dark:bg-gray-800 rounded-lg shadow p-6 transition hover:shadow-md focus:outline-none focus:ring-2 focus:ring-indigo-500 cursor-pointer">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-700 dark:text-gray-200">Total Companies</p>
                        <p class="text-3xl font-bold text-purple-600 dark:text-purple-400 mt-2">{{ $totalCompanies }}</p>
                    </div>
                    <div class="p-3 bg-purple-100 dark:bg-purple-900/30 rounded-full">
                        <svg class="h-8 w-8 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </div>
                </div>
                </a>

            <!-- Total Students Supervised -->
                <a href="{{ route('coordinator.supervisor-overview', ['panel' => 'students']) }}#students" class="block bg-white dark:bg-gray-800 rounded-lg shadow p-6 transition hover:shadow-md focus:outline-none focus:ring-2 focus:ring-indigo-500 cursor-pointer">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-700 dark:text-gray-200">OJT Students Supervised</p>
                        <p class="text-3xl font-bold text-indigo-600 dark:text-indigo-400 mt-2">{{ $totalStudents }}</p>
                    </div>
                    <div class="p-3 bg-indigo-100 dark:bg-indigo-900/30 rounded-full">
                        <svg class="h-8 w-8 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                        </svg>
                    </div>
                </div>
                </a>
        </div>

            <!-- Result Panels (opened by summary cards) -->
            <template x-if="panel === 'companies'">
                <div id="companies" class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Companies Connected to Supervisors</h3>
                            <p class="text-sm text-gray-700 dark:text-gray-200 mt-1">Showing companies that currently appear under supervisor assignments.</p>
                        </div>
                        <a href="{{ route('coordinator.supervisor-overview', ['panel' => 'supervisors']) }}#roster" class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline font-medium">Back to roster</a>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-[640px] w-full">
                            <thead class="bg-gray-50 dark:bg-gray-900/50 border-b border-gray-200 dark:border-gray-700">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Company</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Supervisors</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Active OJT Students</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                <template x-for="company in getConnectedCompanies()" :key="company.id">
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                        <td class="px-6 py-4">
                                            <p class="text-sm font-medium text-gray-900 dark:text-white" x-text="company.name"></p>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="text-sm text-gray-700 dark:text-gray-200" x-text="supervisors.filter(s => (s.companies || []).some(c => String(c.id) === String(company.id))).length"></span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="text-sm text-gray-700 dark:text-gray-200" x-text="getStudentsSupervised().filter(st => String(st.company_id) === String(company.id) && String(st.status).toLowerCase() === 'active').length"></span>
                                        </td>
                                    </tr>
                                </template>
                                <template x-if="getConnectedCompanies().length === 0">
                                    <tr>
                                        <td colspan="3" class="px-6 py-10 text-center text-sm text-gray-700 dark:text-gray-200">No companies found for supervisor assignments.</td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>
            </template>

            <template x-if="panel === 'students'">
                <div id="students" class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">OJT Students Supervised</h3>
                            <p class="text-sm text-gray-700 dark:text-gray-200 mt-1">Unique OJT students currently associated with supervisors (status shown per assignment).</p>
                        </div>
                        <a href="{{ route('coordinator.supervisor-overview', ['panel' => 'supervisors']) }}#roster" class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline font-medium">Back to roster</a>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-[760px] w-full">
                            <thead class="bg-gray-50 dark:bg-gray-900/50 border-b border-gray-200 dark:border-gray-700">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">OJT Student</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Program</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Company</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Supervisor</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                <template x-for="student in getStudentsSupervised()" :key="student.id">
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                        <td class="px-6 py-4">
                                            <p class="text-sm font-medium text-gray-900 dark:text-white" x-text="student.name"></p>
                                            <p class="text-xs text-gray-700 dark:text-gray-200" x-text="student.email"></p>
                                        </td>
                                        <td class="px-6 py-4">
                                            <p class="text-sm text-gray-700 dark:text-gray-200" x-text="student.program"></p>
                                        </td>
                                        <td class="px-6 py-4">
                                            <p class="text-sm text-gray-700 dark:text-gray-200" x-text="student.company_name || '—'"></p>
                                        </td>
                                        <td class="px-6 py-4">
                                            <p class="text-sm text-gray-700 dark:text-gray-200" x-text="student.supervisor_name"></p>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <template x-if="String(student.status).toLowerCase() === 'active'">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-200">Active</span>
                                            </template>
                                            <template x-if="String(student.status).toLowerCase() !== 'active'">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-200 dark:bg-gray-700 text-gray-900 dark:text-gray-100" x-text="student.status"></span>
                                            </template>
                                        </td>
                                    </tr>
                                </template>
                                <template x-if="getStudentsSupervised().length === 0">
                                    <tr>
                                        <td colspan="5" class="px-6 py-10 text-center text-sm text-gray-700 dark:text-gray-200">No OJT students found under supervisors.</td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>
            </template>

        <!-- Filters Section -->
        <div id="roster" class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Supervisor Roster</h3>
                <div class="flex gap-2">
                    <button @click="clearFilters()" class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline font-medium">Clear Filters</button>
                    <a href="{{ route('coordinator.supervisors.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 transition ease-in-out duration-150">
                        Add Supervisor
                    </a>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <!-- Search -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Search Supervisor</label>
                    <input type="text" x-model="searchTerm" placeholder="Name or email..." class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                </div>

                <!-- Company Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Company</label>
                    <select x-model="selectedCompany" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        <option value="">All Companies</option>
                        @foreach($companies as $company)
                            <option value="{{ $company->id }}">{{ $company->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Status Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
                    <select x-model="selectedStatus" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        <option value="">All Status</option>
                        <option value="Active">Active</option>
                        <option value="Inactive">Inactive</option>
                    </select>
                </div>

                <div class="flex items-end">
                    <p class="text-sm text-gray-700 dark:text-gray-200"><span x-text="getFilteredSupervisors().length"></span> supervisor(s) found</p>
                </div>
            </div>
        </div>

        <!-- Empty State -->
        <template x-if="supervisors.length === 0">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-12 text-center">
                <div class="flex justify-center mb-4">
                    <div class="p-3 bg-indigo-100 dark:bg-indigo-900/30 rounded-full">
                        <svg class="h-12 w-12 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">No Supervisors Found</h3>
                <p class="text-gray-700 dark:text-gray-200 max-w-sm mx-auto mb-6">
                    There are currently no supervisors in the system. Add one to get started.
                </p>
                <a href="{{ route('coordinator.supervisors.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 transition ease-in-out duration-150">
                    Add First Supervisor
                </a>
            </div>
        </template>

        <!-- Supervisors Table -->
        <template x-if="supervisors.length > 0">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
                <template x-if="getFilteredSupervisors().length === 0">
                    <div class="p-8 text-center">
                        <svg class="h-12 w-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                        </svg>
                        <p class="text-gray-700 dark:text-gray-200 text-sm">No supervisors match your filters</p>
                    </div>
                </template>

                <template x-if="getFilteredSupervisors().length > 0">
                    <div class="overflow-x-auto">
                        <table class="min-w-[760px] w-full">
                            <thead class="bg-gray-50 dark:bg-gray-900/50 border-b border-gray-200 dark:border-gray-700">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Supervisor</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Company</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">OJT Students</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Evaluations</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Monitoring</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                <template x-for="supervisor in getFilteredSupervisors()" :key="supervisor.id">
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="h-10 w-10 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-full flex items-center justify-center text-white font-bold text-sm flex-shrink-0">
                                                    <span x-text="supervisor.name.charAt(0).toUpperCase()"></span>
                                                </div>
                                                <div class="ml-3">
                                                    <p class="text-sm font-medium text-gray-900 dark:text-white" x-text="supervisor.name"></p>
                                                    <p class="text-xs text-gray-700 dark:text-gray-200" x-text="supervisor.email"></p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="space-y-1">
                                                <template x-for="company in supervisor.companies" :key="company.id">
                                                    <p class="text-sm text-gray-600 dark:text-gray-300" x-text="company.name"></p>
                                                </template>
                                                <template x-if="supervisor.companies.length === 0">
                                                    <p class="text-xs text-gray-600 dark:text-gray-300 italic">No company assigned</p>
                                                </template>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center space-x-2">
                                                <span class="text-sm font-semibold text-gray-900 dark:text-white" x-text="supervisor.total_students"></span>
                                                <span class="text-xs text-gray-700 dark:text-gray-200" x-text="`(${supervisor.active_students} active)`"></span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center space-x-2">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-200">
                                                    <span x-text="`${supervisor.completed_evaluations} Done`"></span>
                                                </span>
                                                <template x-if="supervisor.pending_evaluations > 0">
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-200">
                                                        <span x-text="`${supervisor.pending_evaluations} Pending`"></span>
                                                    </span>
                                                </template>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-200">
                                                <span x-text="`${supervisor.active_tasks} Tasks`"></span>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <template x-if="supervisor.status === 'Active'">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-200">
                                                    Active
                                                </span>
                                            </template>
                                            <template x-if="supervisor.status !== 'Active'">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-200 dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                                                    Inactive
                                                </span>
                                            </template>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm space-x-2">
                                            <button @click="openDetails(supervisor)" class="text-indigo-600 dark:text-indigo-400 hover:underline font-medium">View Details</button>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </template>
            </div>
        </template>
    <!-- Details Modal -->
    <div x-show="showDetailsModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <!-- Backdrop -->
        <div x-show="showDetailsModal" x-transition class="fixed inset-0 bg-black bg-opacity-50"></div>

        <!-- Modal -->
        <div class="flex min-h-full items-center justify-center p-4">
            <template x-if="selectedSupervisor">
                <div x-show="showDetailsModal" x-transition class="relative bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-2xl w-full p-6 space-y-6">
                    <!-- Close Button -->
                    <button @click="closeDetails()" class="absolute top-4 right-4 text-gray-700 dark:text-gray-200 hover:text-gray-700 dark:hover:text-gray-300">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>

                    <!-- Header -->
                    <div class="border-b border-gray-200 dark:border-gray-700 pb-4">
                        <div class="flex items-center gap-4">
                            <div class="h-14 w-14 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-full flex items-center justify-center text-white font-bold text-lg flex-shrink-0">
                                <span x-text="selectedSupervisor.name.charAt(0).toUpperCase()"></span>
                            </div>
                            <div>
                                <h2 class="text-2xl font-bold text-gray-900 dark:text-white" x-text="selectedSupervisor.name"></h2>
                                <p class="text-gray-700 dark:text-gray-200" x-text="selectedSupervisor.position_title"></p>
                            </div>
                        </div>
                    </div>

                    <!-- Contact & Details -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm font-medium text-gray-700 dark:text-gray-300">Email</p>
                            <p class="text-gray-900 dark:text-white" x-text="selectedSupervisor.email"></p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-700 dark:text-gray-300">Phone</p>
                            <p class="text-gray-900 dark:text-white" x-text="selectedSupervisor.phone !== 'N/A' ? selectedSupervisor.phone : 'Not provided'"></p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-700 dark:text-gray-300">Department</p>
                            <p class="text-gray-900 dark:text-white" x-text="selectedSupervisor.department"></p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-700 dark:text-gray-300">Status</p>
                            <template x-if="selectedSupervisor.status === 'Active'">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-200">Active</span>
                            </template>
                            <template x-if="selectedSupervisor.status !== 'Active'">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-200 dark:bg-gray-700 text-gray-900 dark:text-gray-100">Inactive</span>
                            </template>
                        </div>
                    </div>

                    <!-- Companies -->
                    <div>
                        <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Assigned Companies</p>
                        <template x-if="selectedSupervisor.companies.length > 0">
                            <div class="flex flex-wrap gap-2">
                                <template x-for="company in selectedSupervisor.companies" :key="company.id">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-indigo-100 dark:bg-indigo-900/30 text-indigo-800 dark:text-indigo-200" x-text="company.name"></span>
                                </template>
                            </div>
                        </template>
                        <template x-if="selectedSupervisor.companies.length === 0">
                            <p class="text-gray-700 dark:text-gray-200 text-sm">No company assigned</p>
                        </template>
                    </div>

                    <!-- Metrics -->
                    <div class="grid grid-cols-3 gap-4 p-4 bg-gray-50 dark:bg-gray-900/30 rounded-lg">
                        <div class="text-center">
                            <p class="text-2xl font-bold text-indigo-600 dark:text-indigo-400" x-text="selectedSupervisor.total_students"></p>
                            <p class="text-xs text-gray-700 dark:text-gray-200 mt-1">Total OJT Students</p>
                        </div>
                        <div class="text-center">
                            <p class="text-2xl font-bold text-green-600 dark:text-green-400" x-text="selectedSupervisor.completed_evaluations"></p>
                            <p class="text-xs text-gray-700 dark:text-gray-200 mt-1">Completed Evaluations</p>
                        </div>
                        <div class="text-center">
                            <p class="text-2xl font-bold text-yellow-600 dark:text-yellow-400" x-text="selectedSupervisor.pending_evaluations"></p>
                            <p class="text-xs text-gray-700 dark:text-gray-200 mt-1">Pending Evaluations</p>
                        </div>
                    </div>

                    <!-- Students List -->
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">Supervised OJT Students</h3>
                        <template x-if="selectedSupervisor.students.length > 0">
                            <div class="space-y-2 max-h-64 overflow-y-auto">
                                <template x-for="student in selectedSupervisor.students" :key="student.id">
                                    <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700/50 rounded">
                                        <div>
                                            <p class="text-sm font-medium text-gray-900 dark:text-white" x-text="student.name"></p>
                                            <p class="text-xs text-gray-700 dark:text-gray-200" x-text="student.program"></p>
                                        </div>
                                        <template x-if="student.status === 'active'">
                                            <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-200">Active</span>
                                        </template>
                                        <template x-if="student.status !== 'active'">
                                            <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-gray-200 dark:bg-gray-700 text-gray-900 dark:text-gray-100" x-text="student.status"></span>
                                        </template>
                                    </div>
                                </template>
                            </div>
                        </template>
                        <template x-if="selectedSupervisor.students.length === 0">
                            <p class="text-gray-700 dark:text-gray-200 text-sm">No OJT students assigned</p>
                        </template>
                    </div>

                    <!-- Actions -->
                    <div class="border-t border-gray-200 dark:border-gray-700 pt-4 flex gap-2 justify-end">
                        <button @click="closeDetails()" class="px-4 py-2 text-gray-700 dark:text-gray-300 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                            Close
                        </button>
                        <a href="#" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors text-sm font-medium">
                            Edit Supervisor
                        </a>
                    </div>
                </div>
            </template>
        </div>
    </div>

    </div>
</x-coordinator-layout>
