<x-coordinator-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="text-3xl font-bold text-gray-900 dark:text-white">OJT Adviser Overview</h2>
        </div>
    </x-slot>

    <div class="space-y-6" x-data='{
        selectedAdviserId: null,
        advisers: @json($advisersData),
        searchTerm: "",
        selectedCompany: "",
        selectedStatus: "",
        selectedEvalStatus: "",
        
        selectedAdviser() {
            if (!this.selectedAdviserId) return null;
            return this.advisers.find(a => String(a.id) === String(this.selectedAdviserId)) || null;
        },
        
        getFilteredStudents() {
            const adviser = this.selectedAdviser();
            if (!adviser) return [];
            
            return adviser.students.filter(s => {
                const matchesSearch = s.name.toLowerCase().includes(this.searchTerm.toLowerCase()) || 
                                    s.email.toLowerCase().includes(this.searchTerm.toLowerCase());
                const matchesCompany = this.selectedCompany === "" || s.company_id == this.selectedCompany;
                const matchesStatus = this.selectedStatus === "" || s.status === this.selectedStatus;
                const matchesEval = this.selectedEvalStatus === "" || s.evaluation_status === this.selectedEvalStatus;
                
                return matchesSearch && matchesCompany && matchesStatus && matchesEval;
            });
        },
        
        clearFilters() {
            this.searchTerm = "";
            this.selectedCompany = "";
            this.selectedStatus = "";
            this.selectedEvalStatus = "";
        }
    }'>
        <!-- Adviser Selector Card -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Select OJT Adviser</h3>
            </div>

            <div class="flex flex-col md:flex-row gap-4">
                <div class="flex-1">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">OJT Adviser</label>
                    <select x-model="selectedAdviserId" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">-- Select an adviser --</option>
                        <template x-for="adviser in advisers" :key="adviser.id">
                            <option :value="adviser.id" x-text="adviser.name"></option>
                        </template>
                    </select>
                </div>
                
                <div class="flex items-end gap-2">
                    <a x-show="selectedAdviserId" x-transition :href="`/messages/${selectedAdviserId}`" class="inline-flex items-center px-4 py-2 bg-gray-900/90 dark:bg-white/10 border border-gray-900/10 dark:border-white/10 rounded-md font-semibold text-xs text-white dark:text-gray-100 uppercase tracking-widest hover:bg-gray-900 dark:hover:bg-white/20 transition ease-in-out duration-150">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                        Message
                    </a>
                </div>
            </div>
        </div>

        <!-- Empty State -->
        <template x-if="!selectedAdviserId">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-12 text-center">
                <div class="flex justify-center mb-4">
                    <div class="p-3 bg-indigo-100 dark:bg-indigo-900/30 rounded-full">
                        <svg class="h-12 w-12 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">No Adviser Selected</h3>
                <p class="text-gray-500 dark:text-gray-400 max-w-sm mx-auto">
                    Select an OJT Adviser from the dropdown above to view their student roster, performance metrics, and monitoring information.
                </p>
            </div>
        </template>

        <!-- Adviser Dashboard -->
        <template x-if="selectedAdviserId && selectedAdviser()">
            <div x-transition>
                <!-- Adviser Header Card -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden">
                    <div class="bg-gradient-to-r from-indigo-600 to-indigo-800 px-6 py-4">
                        <div class="flex items-center gap-4">
                            <div class="h-14 w-14 rounded-full overflow-hidden border-4 border-white bg-gray-900/10 flex items-center justify-center flex-shrink-0">
                                <template x-if="selectedAdviser().photo_url">
                                    <img :src="selectedAdviser().photo_url" alt="" class="h-full w-full object-cover">
                                </template>
                                <template x-if="!selectedAdviser().photo_url">
                                    <div class="text-white font-bold text-lg" x-text="selectedAdviser().name.charAt(0).toUpperCase()"></div>
                                </template>
                            </div>
                            <div class="flex-1 min-w-0">
                                <h2 class="text-2xl font-bold text-white" x-text="selectedAdviser().name"></h2>
                                <p class="text-indigo-100 text-sm" x-text="selectedAdviser().department"></p>
                                <p class="text-indigo-200 text-xs mt-1" x-text="selectedAdviser().email"></p>
                            </div>
                        </div>
                    </div>

                    <!-- Summary Cards -->
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 p-6">
                        <!-- Total Students -->
                        <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4 border border-blue-200 dark:border-blue-800">
                            <p class="text-xs font-semibold text-blue-600 dark:text-blue-300 uppercase tracking-wider">Total Students</p>
                            <p class="text-3xl font-bold text-blue-600 dark:text-blue-400 mt-2" x-text="selectedAdviser().total_students"></p>
                        </div>

                        <!-- Active Students -->
                        <div class="bg-green-50 dark:bg-green-900/20 rounded-lg p-4 border border-green-200 dark:border-green-800">
                            <p class="text-xs font-semibold text-green-600 dark:text-green-300 uppercase tracking-wider">Active Students</p>
                            <p class="text-3xl font-bold text-green-600 dark:text-green-400 mt-2" x-text="selectedAdviser().active_students"></p>
                        </div>

                        <!-- Completed Students -->
                        <div class="bg-purple-50 dark:bg-purple-900/20 rounded-lg p-4 border border-purple-200 dark:border-purple-800">
                            <p class="text-xs font-semibold text-purple-600 dark:text-purple-300 uppercase tracking-wider">Completed</p>
                            <p class="text-3xl font-bold text-purple-600 dark:text-purple-400 mt-2" x-text="selectedAdviser().completed_students"></p>
                        </div>

                        <!-- Pending Evaluations -->
                        <div class="bg-yellow-50 dark:bg-yellow-900/20 rounded-lg p-4 border border-yellow-200 dark:border-yellow-800">
                            <p class="text-xs font-semibold text-yellow-600 dark:text-yellow-300 uppercase tracking-wider">Pending Review</p>
                            <p class="text-3xl font-bold text-yellow-600 dark:text-yellow-400 mt-2" x-text="selectedAdviser().pending_evaluations"></p>
                        </div>
                    </div>
                </div>

                <!-- Filters Section -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Student Roster</h3>
                        <button @click="clearFilters()" class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline font-medium">Clear All Filters</button>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <!-- Search -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Search Student</label>
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
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">OJT Status</label>
                            <select x-model="selectedStatus" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                <option value="">All Status</option>
                                <option value="active">Active</option>
                                <option value="completed">Completed</option>
                                <option value="on_hold">On Hold</option>
                            </select>
                        </div>

                        <!-- Evaluation Status Filter -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Evaluation Status</label>
                            <select x-model="selectedEvalStatus" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                <option value="">All Status</option>
                                <option value="Pending">Pending Review</option>
                                <option value="Evaluated">Evaluated</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Student Roster Table -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden">
                    <template x-if="getFilteredStudents().length === 0">
                        <div class="p-8 text-center">
                            <svg class="h-12 w-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                            </svg>
                            <p class="text-gray-500 dark:text-gray-400 text-sm">No students match the selected filters</p>
                        </div>
                    </template>

                    <template x-if="getFilteredStudents().length > 0">
                        <div class="overflow-x-auto">
                            <table class="min-w-[760px] w-full">
                                <thead class="bg-gray-50 dark:bg-gray-900/50 border-b border-gray-200 dark:border-gray-700">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Student</th>
                                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Program</th>
                                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Company</th>
                                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Supervisor</th>
                                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Hours</th>
                                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Tasks</th>
                                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Status</th>
                                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                    <template x-for="student in getFilteredStudents()" :key="student.id">
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    <div class="h-9 w-9 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-full flex items-center justify-center text-white font-bold text-sm flex-shrink-0">
                                                        <span x-text="student.name.charAt(0).toUpperCase()"></span>
                                                    </div>
                                                    <div class="ml-3">
                                                        <p class="text-sm font-medium text-gray-900 dark:text-white" x-text="student.name"></p>
                                                        <p class="text-xs text-gray-500 dark:text-gray-400" x-text="student.email"></p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-300" x-text="student.program"></td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-300" x-text="student.company"></td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-300" x-text="student.supervisor"></td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center space-x-2">
                                                    <div class="w-16">
                                                        <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                                            <div class="bg-purple-600 h-2 rounded-full" :style="`width: ${Math.min(student.hours_percentage, 100)}%`"></div>
                                                        </div>
                                                    </div>
                                                    <span class="text-xs font-medium text-gray-600 dark:text-gray-300 whitespace-nowrap" x-text="`${student.hours_percentage}%`"></span>
                                                </div>
                                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1" x-text="`${student.completed_hours}/${student.required_hours} hrs`"></p>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="text-sm font-medium text-gray-900 dark:text-white" x-text="`${student.submitted_tasks}/${student.total_tasks}`"></span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <template x-if="student.status === 'active'">
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-200">
                                                        Active
                                                    </span>
                                                </template>
                                                <template x-if="student.status === 'completed'">
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-200">
                                                        Completed
                                                    </span>
                                                </template>
                                                <template x-if="student.status !== 'active' && student.status !== 'completed'">
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 dark:bg-gray-900/30 text-gray-800 dark:text-gray-200" x-text="student.status"></span>
                                                </template>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                <a href="#" class="text-indigo-600 dark:text-indigo-400 hover:underline font-medium">View Details</a>
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                    </template>
                </div>

                <!-- Summary Stats -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Completion Rate -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
                        <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-4">Average Hours Completion</h4>
                        <template x-if="selectedAdviser().students.length > 0">
                            <div>
                                <p class="text-3xl font-bold text-indigo-600 dark:text-indigo-400" x-text="`${Math.round(selectedAdviser().students.reduce((sum, s) => sum + s.hours_percentage, 0) / selectedAdviser().students.length)}%`"></p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">Across all students</p>
                            </div>
                        </template>
                    </div>

                    <!-- Incomplete Requirements -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
                        <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-4">Incomplete Requirements</h4>
                        <p class="text-3xl font-bold text-red-600 dark:text-red-400" x-text="selectedAdviser().students.filter(s => s.hours_percentage < 100 || s.tasks_percentage < 100).length"></p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">Students needing follow-up</p>
                    </div>

                    <!-- Evaluation Follow-up -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
                        <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-4">Evaluation Follow-up</h4>
                        <p class="text-3xl font-bold text-yellow-600 dark:text-yellow-400" x-text="selectedAdviser().pending_evaluations"></p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">Pending evaluations</p>
                    </div>
                </div>
            </div>
        </template>
    </div>
</x-coordinator-layout>
