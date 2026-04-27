<x-coordinator-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="text-3xl font-extrabold text-white drop-shadow-md tracking-tight">Deployment Management</h2>
        </div>
    </x-slot>

    @php
        $supervisorsForJs = $supervisors->map(function ($s) {
            return [
                'id' => $s->id,
                'name' => $s->name,
                'company_id' => optional($s->supervisorProfile)->company_id,
                'company_name' => optional(optional($s->supervisorProfile)->company)->name,
            ];
        })->values();

        $advisersForJs = $ojtAdvisers->map(function ($a) {
            return [
                'id' => $a->id,
                'name' => $a->name,
            ];
        })->values();

        $companiesForJs = $companies->map(function ($c) {
            return [
                'id' => $c->id,
                'name' => $c->name,
            ];
        })->values();
    @endphp

    <script id="deployment-data" type="application/json">@json($deploymentData->values())</script>
    <script id="deployment-supervisors" type="application/json">@json($supervisorsForJs)</script>
    <script id="deployment-advisers" type="application/json">@json($advisersForJs)</script>
    <script id="deployment-companies" type="application/json">@json($companiesForJs)</script>

    <div class="space-y-6" x-data="coordinatorDeploymentManager()">
        <!-- Status Messages -->
        @if ($errors->any())
            <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4">
                <div class="flex items-start">
                    <svg class="h-5 w-5 text-red-400 dark:text-red-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4v.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-800 dark:text-red-300">Deployment Error</h3>
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

        @php
            $summaryCards = [
                ['label' => 'OJT Students', 'value' => $topSummary['totalStudents'] ?? 0, 'href' => route('coordinator.student-overview'), 'tone' => 'indigo'],
                ['label' => 'Active OJT', 'value' => $topSummary['activeOJTs'] ?? 0, 'href' => route('coordinator.deployment.index'), 'tone' => 'sky'],
                ['label' => 'OJT Advisers', 'value' => $topSummary['advisersCount'] ?? 0, 'href' => route('coordinator.adviser-overview'), 'tone' => 'emerald'],
                ['label' => 'Supervisors', 'value' => $topSummary['supervisorsCount'] ?? 0, 'href' => route('coordinator.supervisor-overview'), 'tone' => 'cyan'],
                ['label' => 'Industry', 'value' => $topSummary['totalCompanies'] ?? 0, 'href' => route('coordinator.companies.index'), 'tone' => 'amber'],
                ['label' => 'Pending Approvals', 'value' => $topSummary['pendingApprovals'] ?? 0, 'href' => route('coordinator.registrations.pending'), 'tone' => 'fuchsia'],
                ['label' => 'Pending AR', 'value' => $topSummary['pendingAccomplishmentReports'] ?? 0, 'href' => route('coordinator.accomplishment-reports'), 'tone' => 'rose'],
                ['label' => 'Needs Attention', 'value' => $topSummary['studentsNeedingAttention'] ?? 0, 'href' => route('coordinator.compliance-overview'), 'tone' => 'orange'],
            ];
        @endphp

        <x-coordinator.summary-cards :cards="$summaryCards" />

        <!-- Create New Deployment Form -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Create New Deployment</h3>
            
            <form id="deploymentForm" method="POST" action="{{ route('coordinator.deployment.store') }}" class="space-y-4">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <!-- OJT Students Search -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">OJT Student(s)</label>
                        @if($groupedStudents->isNotEmpty())
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
                                                {{ $student->display_name_last_first }}
                                            </option>
                                        @endforeach
                                    </optgroup>
                                @endforeach
                            </select>
                        @else
                            <div class="w-full rounded-lg border border-dashed border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 px-3 py-3 text-sm text-gray-600 dark:text-gray-300">
                                No eligible students are available for deployment.
                            </div>
                        @endif
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
                                <option
                                    value="{{ $supervisor->id }}"
                                    data-company-id="{{ $supervisor->supervisorProfile?->company_id ?? '' }}"
                                    data-company-name="{{ $supervisor->supervisorProfile?->company?->name ?? '' }}"
                                >{{ $supervisor->name }}</option>
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
                            id="company_id_display"
                            class="w-full rounded-lg border-gray-300 dark:border-gray-700 bg-gray-100 dark:bg-gray-600 text-gray-900 dark:text-white focus:border-indigo-500 focus:ring-indigo-500 cursor-not-allowed"
                            disabled
                        >
                            <option value="">Select company</option>
                            @foreach ($companies as $company)
                                <option value="{{ $company->id }}">{{ $company->name }}</option>
                            @endforeach
                        </select>
                        <input type="hidden" id="company_id" name="company_id" value="{{ old('company_id') }}" required>
                        <p id="supervisor-company-status" class="mt-1 text-xs text-gray-600 dark:text-gray-300">Select a supervisor to view assigned company.</p>
                        <p id="supervisor-company-validation" class="mt-1 text-xs text-red-600 dark:text-red-400 hidden"></p>
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
                    @if($groupedStudents->isNotEmpty())
                        <button
                            type="button"
                            onclick="confirmDeployment()"
                            class="inline-flex items-center px-4 py-2 rounded-lg bg-indigo-600 text-sm font-semibold uppercase tracking-wide text-white hover:bg-indigo-700 transition-colors"
                        >
                            <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            Create Deployment
                        </button>
                    @else
                        <button
                            type="button"
                            class="inline-flex items-center px-4 py-2 rounded-lg bg-gray-400 text-sm font-semibold uppercase tracking-wide text-white cursor-not-allowed"
                            disabled
                        >
                            No Eligible Students
                        </button>
                    @endif
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
                            <h3 class="ml-4 text-lg font-semibold text-gray-900 dark:text-white">Review Deployment</h3>
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
                            Confirm & Deploy
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Edit Deployment Modal -->
        <div id="editDeploymentModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 z-0 bg-black bg-opacity-50 transition-opacity pointer-events-none"></div>
                <form x-show="editingDeployment" method="POST" :action="editFormAction" class="relative z-10 inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full">
                    @csrf
                    @method('PATCH')
                    <div class="bg-white dark:bg-gray-800 px-6 pt-5 pb-4 sm:p-6">
                        <div class="flex items-center mb-4">
                            <div class="flex items-center justify-center h-12 w-12 rounded-full bg-indigo-100 dark:bg-indigo-900/30">
                                <svg class="h-6 w-6 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                            </div>
                            <h3 class="ml-4 text-lg font-semibold text-gray-900 dark:text-white">Edit Deployment</h3>
                        </div>
                        <template x-if="editingDeployment">
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Student</label>
                                    <p class="px-3 py-2 bg-gray-100 dark:bg-gray-700 rounded-lg text-gray-900 dark:text-white text-sm" x-text="editingDeployment.student_name"></p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Supervisor *</label>
                                    <select name="supervisor_id" x-model="editSupervisorId" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:border-indigo-500 focus:ring-indigo-500">
                                        <option value="">Select supervisor</option>
                                        <template x-for="supervisor in supervisors" :key="supervisor.id">
                                            <option :value="supervisor.id" x-text="supervisor.name"></option>
                                        </template>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">OJT Adviser</label>
                                    <select name="ojt_adviser_id" x-model="editAdviserId" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:border-indigo-500 focus:ring-indigo-500">
                                        <option value="">Select adviser</option>
                                        <template x-for="adviser in advisers" :key="adviser.id">
                                            <option :value="adviser.id" x-text="adviser.name"></option>
                                        </template>
                                    </select>
                                </div>
                                <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-3 mt-4">
                                    <p class="text-xs text-blue-800 dark:text-blue-200">
                                        <strong>Completion Status:</strong> This deployment will be marked as <span x-text="(editSupervisorId && editAdviserId) ? '✓ Complete' : ((editSupervisorId || editAdviserId) ? '⚠ Incomplete' : 'Unassigned')" class="font-bold"></span>
                                    </p>
                                </div>
                            </div>
                        </template>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-700 px-6 py-3 flex gap-2 justify-end">
                        <button type="button" @click="closeEditModal()" class="px-4 py-2 text-gray-700 dark:text-gray-300 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors text-sm font-medium">
                            Cancel
                        </button>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors text-sm font-medium">
                            Save Changes
                        </button>
                    </div>
                </form>
                </div>
            </div>
        </div>

        <!-- Filters Section -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Deployment Records</h3>
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
                        @foreach($filterCompanies as $company)
                            <option value="{{ $company['id'] }}">{{ $company['name'] }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- OJT Status Filter -->
                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">OJT Status</label>
                    <select x-model="selectedStatus" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        <option value="">All Status</option>
                        @foreach($statusOptions as $statusOption)
                            <option value="{{ $statusOption }}">{{ ucfirst($statusOption) }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Deployment Status Filter -->
                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Deployment Status</label>
                    <select x-model="selectedDeploymentStatus" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:border-indigo-500 focus:ring-indigo-500 text-sm">
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
                        @foreach($filterSupervisors as $supervisor)
                            <option value="{{ $supervisor['id'] }}">{{ $supervisor['name'] }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Adviser Filter -->
                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Adviser</label>
                    <select x-model="selectedAdviser" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        <option value="">All Advisers</option>
                        @foreach($filterAdvisers as $adviser)
                            <option value="{{ $adviser['id'] }}">{{ $adviser['name'] }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <!-- Deployments Table -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
            @if($deploymentData->isEmpty())
                <div class="p-12 text-center">
                    <svg class="h-12 w-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <p class="text-gray-500 dark:text-gray-400 text-sm">No active deployments found.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-[980px] w-full">
                        <thead class="bg-gray-50 dark:bg-gray-900/50 border-b border-gray-200 dark:border-gray-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Student</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Supervisor</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">OJT Adviser</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Company</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Duration</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Hours</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($deploymentData as $deployment)
                                @php
                                    $rowClass = $deployment['deployment_status'] === 'complete'
                                        ? 'bg-white dark:bg-gray-800'
                                        : ($deployment['deployment_status'] === 'incomplete'
                                            ? 'bg-yellow-50/50 dark:bg-yellow-900/10'
                                            : 'bg-red-50/50 dark:bg-red-900/10');
                                @endphp
                                <tr
                                    class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors {{ $rowClass }}"
                                    x-bind:class="deployments.length > 0 && !isDeploymentVisible({{ $deployment['id'] }}) ? 'hidden' : ''"
                                >
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($deployment['deployment_status'] === 'complete')
                                            <x-status-badge status="complete" label="Complete" size="sm" />
                                        @elseif($deployment['deployment_status'] === 'incomplete')
                                            <x-status-badge status="incomplete" label="Incomplete" size="sm" />
                                        @else
                                            <x-status-badge status="unassigned" label="Unassigned" size="sm" />
                                        @endif

                                        <div class="mt-1 text-[11px] text-gray-500 dark:text-gray-400">
                                            {{ ucfirst((string) ($deployment['status'] ?? '')) }}
                                        </div>
                                    </td>

                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="h-9 w-9 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-full flex items-center justify-center text-white font-bold text-sm flex-shrink-0">
                                                {{ strtoupper(substr((string) $deployment['student_name'], 0, 1)) }}
                                            </div>
                                            <div class="ml-3">
                                                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $deployment['student_name'] }}</p>
                                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $deployment['student_program'] }}</p>
                                            </div>
                                        </div>
                                    </td>

                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($deployment['supervisor_name'] !== 'Not Assigned')
                                            <span class="wl-status-badge wl-status-info px-2.5 py-1 text-[11px] normal-case tracking-normal">{{ $deployment['supervisor_name'] }}</span>
                                        @else
                                            <x-status-badge status="unassigned" label="Not Assigned" size="sm" />
                                        @endif
                                    </td>

                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($deployment['adviser_name'] !== 'Not Assigned')
                                            <span class="wl-status-badge wl-status-info px-2.5 py-1 text-[11px] normal-case tracking-normal">{{ $deployment['adviser_name'] }}</span>
                                        @else
                                            <x-status-badge status="unassigned" label="Not Assigned" size="sm" />
                                        @endif
                                    </td>

                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">{{ $deployment['company_name'] }}</td>

                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-300">
                                        @if($deployment['start_date'] && $deployment['end_date'])
                                            {{ $deployment['start_date'] }} to {{ $deployment['end_date'] }}
                                        @else
                                            <span class="text-gray-400 italic">Not specified</span>
                                        @endif
                                    </td>

                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="wl-status-badge wl-status-info px-2.5 py-1 text-[11px] normal-case tracking-normal">{{ $deployment['required_hours'] }} hrs</span>
                                    </td>

                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        @if($deployment['deployment_status'] !== 'complete')
                                            <button @click="openEditModal(@js($deployment))" class="inline-flex items-center px-3 py-1.5 rounded-lg bg-indigo-600 text-white text-xs font-medium hover:bg-indigo-700 transition-colors">
                                                <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                                Edit
                                            </button>
                                        @else
                                            <span class="text-gray-400 text-xs">Completed</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div x-show="deployments.length > 0 && getFilteredDeployments().length === 0" class="p-12 text-center border-t border-gray-200 dark:border-gray-700">
                    <svg class="h-12 w-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                    <p class="text-gray-500 dark:text-gray-400 text-sm">No deployments match your filters</p>
                </div>
            @endif
        </div>
    </div>

    @push('scripts')
    <script>
        function readDeploymentJson(id, fallback = []) {
            const element = document.getElementById(id);

            if (!element) {
                return fallback;
            }

            try {
                return JSON.parse(element.textContent || 'null') ?? fallback;
            } catch (error) {
                console.error(`Unable to parse deployment payload for ${id}`, error);
                return fallback;
            }
        }

        function coordinatorDeploymentManager() {
            return {
                deployments: readDeploymentJson('deployment-data', []),
                supervisors: readDeploymentJson('deployment-supervisors', []),
                advisers: readDeploymentJson('deployment-advisers', []),
                companies: readDeploymentJson('deployment-companies', []),
                searchTerm: "",
                selectedCompany: "",
                selectedStatus: "",
                selectedDeploymentStatus: "",
                selectedSupervisor: "",
                selectedAdviser: "",
                editingDeployment: null,
                editFormAction: "",
                editSupervisorId: "",
                editAdviserId: "",

                normalizeId(value) {
                    if (value === null || value === undefined || value === "") return "";
                    return String(value);
                },

                normalizeText(value) {
                    return String(value === null || value === undefined ? "" : value).trim().toLowerCase();
                },

                isDeploymentVisible(id) {
                    const targetId = this.normalizeId(id);
                    return this.getFilteredDeployments().some(a => this.normalizeId(a && a.id) === targetId);
                },

                getFilteredDeployments() {
                    const search = this.normalizeText(this.searchTerm);

                    return (this.deployments || []).filter(a => {
                        const studentName = this.normalizeText(a && a.student_name);
                        const studentEmail = this.normalizeText(a && a.student_email);
                        const supervisorName = this.normalizeText(a && a.supervisor_name);
                        const adviserName = this.normalizeText(a && a.adviser_name);
                        const companyName = this.normalizeText(a && a.company_name);
                        const studentProgram = this.normalizeText(a && a.student_program);
                        const statusLabel = this.normalizeText(a && a.status);
                        const deploymentStatus = this.normalizeText(a && a.deployment_status);

                        const matchesSearch =
                            search === "" ||
                            studentName.includes(search) ||
                            studentEmail.includes(search) ||
                            supervisorName.includes(search) ||
                            adviserName.includes(search) ||
                            companyName.includes(search) ||
                            studentProgram.includes(search) ||
                            statusLabel.includes(search) ||
                            deploymentStatus.includes(search);
                        const matchesCompany = this.selectedCompany === "" || this.normalizeId(a && a.company_id) === this.normalizeId(this.selectedCompany);
                        const matchesStatus = this.selectedStatus === "" || statusLabel === this.normalizeText(this.selectedStatus);
                        const matchesDeployStatus = this.selectedDeploymentStatus === "" || deploymentStatus === this.normalizeText(this.selectedDeploymentStatus);
                        const matchesSupervisor = this.selectedSupervisor === "" || this.normalizeId(a && a.supervisor_id) === this.normalizeId(this.selectedSupervisor);
                        const matchesAdviser = this.selectedAdviser === "" || this.normalizeId(a && a.adviser_id) === this.normalizeId(this.selectedAdviser);

                        return matchesSearch && matchesCompany && matchesStatus && matchesDeployStatus && matchesSupervisor && matchesAdviser;
                    });
                },

                clearFilters() {
                    this.searchTerm = "";
                    this.selectedCompany = "";
                    this.selectedStatus = "";
                    this.selectedDeploymentStatus = "";
                    this.selectedSupervisor = "";
                    this.selectedAdviser = "";
                },

                openEditModal(deployment) {
                    this.editingDeployment = { ...deployment };
                    this.editFormAction = `/coordinator/deployment-management/${deployment.id}`;
                    this.editSupervisorId = deployment.supervisor_id || "";
                    this.editAdviserId = deployment.adviser_id || "";
                    document.getElementById("editDeploymentModal").classList.remove("hidden");
                },

                closeEditModal() {
                    this.editingDeployment = null;
                    this.editFormAction = "";
                    this.editSupervisorId = "";
                    this.editAdviserId = "";
                    document.getElementById("editDeploymentModal").classList.add("hidden");
                },

                saveDeployment() {
                    if (!this.editingDeployment) return;

                    const formData = new FormData();
                    formData.append("supervisor_id", this.editSupervisorId);
                    formData.append("ojt_adviser_id", this.editAdviserId);
                    formData.append("_method", "PATCH");
                    formData.append("_token", document.querySelector("meta[name=csrf-token]").getAttribute("content"));

                    fetch(`/coordinator/deployment-management/${this.editingDeployment.id}`, {
                        method: "POST",
                        body: formData
                    })
                    .then(response => {
                        if (response.ok) {
                            const index = this.deployments.findIndex(d => this.normalizeId(d && d.id) === this.normalizeId(this.editingDeployment && this.editingDeployment.id));
                            if (index !== -1) {
                                const selectedSupervisor = this.supervisors.find(s => String(s.id) === String(this.editSupervisorId));
                                this.deployments[index].supervisor_id = this.editSupervisorId;
                                this.deployments[index].adviser_id = this.editAdviserId;
                                this.deployments[index].supervisor_name = this.supervisors.find(s => s.id == this.editSupervisorId)?.name || "Not Assigned";
                                this.deployments[index].adviser_name = this.advisers.find(a => a.id == this.editAdviserId)?.name || "Not Assigned";
                                if (selectedSupervisor?.company_id) {
                                    this.deployments[index].company_id = selectedSupervisor.company_id;
                                    this.deployments[index].company_name = selectedSupervisor.company_name || this.deployments[index].company_name;
                                }
                                this.deployments[index].deployment_status = (this.editSupervisorId && this.editAdviserId) ? "complete" : ((this.editSupervisorId || this.editAdviserId) ? "incomplete" : "unassigned");
                            }
                            this.closeEditModal();
                            const successMsg = document.createElement("div");
                            successMsg.className = "bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4 mb-4";
                            successMsg.innerHTML = `<div class="flex items-start"><svg class="h-5 w-5 text-green-400 dark:text-green-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg><div class="ml-3"><h3 class="text-sm font-medium text-green-800 dark:text-green-300">Success</h3><p class="text-sm text-green-700 dark:text-green-400 mt-1">Deployment updated successfully.</p></div></div>`;
                            document.querySelector(".space-y-6").prepend(successMsg);
                            setTimeout(() => successMsg.remove(), 5000);
                        }
                    })
                    .catch(error => console.error("Error:", error));
                }
            };
        }

        $(document).ready(function() {
            if ($('#student_ids').length) {
                $('#student_ids').select2({
                    placeholder: "Search by Name or Email...",
                    allowClear: true,
                    width: '100%',
                    closeOnSelect: false,
                });
            }
            $('#supervisor_id').select2({ width: '100%' });
            $('#ojt_adviser_id').select2({ width: '100%' });
            $('#company_id_display').select2({ width: '100%' });

            $('#supervisor_id').on('change', syncCompanyFromSupervisor);
            syncCompanyFromSupervisor();
        });

        function normalizeSupervisorIds(rawValue) {
            if (Array.isArray(rawValue)) {
                return rawValue.filter(Boolean);
            }

            if (rawValue) {
                return [rawValue];
            }

            return [];
        }

        function setSupervisorValidationMessage(message) {
            const messageElement = $('#supervisor-company-validation');
            if (!message) {
                messageElement.text('');
                messageElement.addClass('hidden');
                return;
            }

            messageElement.text(message);
            messageElement.removeClass('hidden');
        }

        function setSupervisorCompanyStatus(message, tone = 'neutral') {
            const statusElement = $('#supervisor-company-status');
            statusElement.removeClass('text-gray-600 text-gray-300 text-green-700 text-green-300 text-amber-700 text-amber-300');

            if (tone === 'success') {
                statusElement.addClass('text-green-700 dark:text-green-300');
            } else if (tone === 'warning') {
                statusElement.addClass('text-amber-700 dark:text-amber-300');
            } else {
                statusElement.addClass('text-gray-600 dark:text-gray-300');
            }

            statusElement.text(message);
        }

        function setCompanyValue(companyId) {
            const value = companyId ? String(companyId) : '';
            $('#company_id').val(value);
            $('#company_id_display').val(value).trigger('change.select2');
        }

        function syncCompanyFromSupervisor() {
            const selectedSupervisorIds = normalizeSupervisorIds($('#supervisor_id').val());

            if (selectedSupervisorIds.length === 0) {
                setCompanyValue('');
                setSupervisorValidationMessage('Select a supervisor to auto-fill company.');
                setSupervisorCompanyStatus('Select a supervisor to view assigned company.', 'neutral');
                return false;
            }

            const selectedSupervisorOptions = selectedSupervisorIds.map((supervisorId) =>
                $(`#supervisor_id option[value="${supervisorId}"]`)
            );

            const selectedCompanyIds = [...new Set(selectedSupervisorOptions.map((option) => {
                return option.data('company-id') ? String(option.data('company-id')) : '';
            }))];

            if (selectedCompanyIds.includes('')) {
                setCompanyValue('');
                setSupervisorValidationMessage('No company assigned to this supervisor.');
                setSupervisorCompanyStatus('No company assigned to this supervisor.', 'warning');
                return false;
            }

            if (selectedCompanyIds.length !== 1) {
                setCompanyValue('');
                setSupervisorValidationMessage('Selected supervisors belong to different companies. Please choose supervisors from the same company.');
                setSupervisorCompanyStatus('Selected supervisors map to different companies.', 'warning');
                return false;
            }

            const resolvedCompanyName = String(selectedSupervisorOptions[0].data('company-name') || '').trim();

            setCompanyValue(selectedCompanyIds[0]);
            setSupervisorValidationMessage('');
            setSupervisorCompanyStatus(
                resolvedCompanyName ? `Assigned company: ${resolvedCompanyName}` : 'Assigned company loaded from supervisor profile.',
                'success'
            );
            return true;
        }

        function confirmDeployment() {
            if (!$('#student_ids').length) {
                alert('No eligible students are available for deployment.');
                return;
            }

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
            if (!syncCompanyFromSupervisor()) {
                alert('Please fix supervisor and company mapping before creating deployment.');
                return;
            }
            if (!companyId) {
                alert('Company is auto-filled from the selected supervisor. Please select a valid supervisor with an assigned company.');
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
            $('#confirm-company').text($('#company_id_display option:selected').text().trim());
            
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
                $('#confirm-warning').text(`Note: This will create ${studentIds.length} separate deployments.`);
            } else {
                $('#confirm-warning').text('');
            }

            $('#confirmationModal').removeClass('hidden');
        }

        function closeModal() {
            $('#confirmationModal').addClass('hidden');
        }

        function submitForm() {
            $('#deploymentForm').submit();
        }
    </script>
    @endpush
</x-coordinator-layout>
