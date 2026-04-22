<x-coordinator-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Accomplishment Reports') }}
        </h2>
    </x-slot>

    <div class="space-y-6">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900 dark:text-gray-100">
                <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
                    <h3 class="text-lg font-semibold">Accomplishment Reports</h3>
                </div>

                <div class="space-y-4">
                    @forelse($groupedReports as $groupName => $departmentLogs)
                        <div x-data="{ 
                            showModal: false,
                            searchQuery: '',
                            expandedStudent: null,
                            get filteredStudents() {
                                const query = this.searchQuery.toLowerCase();
                                return this.allStudents.filter(student => 
                                    student.name.toLowerCase().includes(query) || 
                                    student.company.toLowerCase().includes(query)
                                );
                            },
                            allStudents: {{ json_encode($departmentLogs->groupBy(function($log) { return $log->assignment?->student?->id ?? 'unknown'; })->map(function($studentLogs) { $student = $studentLogs->first()->assignment?->student; $assignment = $studentLogs->first()->assignment; $studentName = $student?->name ?? 'N/A'; $companyName = $studentLogs->first()->assignment?->company?->name ?? 'N/A'; $course = $student?->section ?? 'N/A'; $department = $student?->department ?? 'N/A'; $hours = $assignment ? ($assignment->totalApprovedHours() ?? 0) : 0; $status = $assignment?->status === 'active' ? 'Active' : ($assignment?->status === 'completed' ? 'Completed' : 'Inactive'); return ['id' => $student?->id, 'name' => $studentName, 'company' => $companyName, 'course' => $course, 'department' => $department, 'hours' => $hours, 'status' => $status, 'reportCount' => $studentLogs->count(), 'logs' => $studentLogs->map(fn($log) => ['id' => $log->id, 'type' => $log->type, 'date' => $log->work_date?->format('M d, Y'), 'status' => ucfirst($log->status === 'rejected' ? 'declined' : $log->status), 'updatedAt' => optional($log->updated_at)->timestamp, 'attachmentUrl' => $log->attachment_path ? route('coordinator.worklogs.attachment', $log->id) : null, 'printUrl' => route('coordinator.worklogs.print', $log->id)])->values()->all()]; })->values()) }}
                        }" class="border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden bg-white dark:bg-gray-800 transition-shadow hover:shadow-md">
                            <!-- Header -->
                            <button @click="showModal = true" class="w-full px-6 py-4 bg-gray-50 dark:bg-gray-900/50 flex items-center justify-between hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors group">
                                <div class="flex items-center gap-3">
                                    <span class="px-3 py-1.5 rounded-lg text-sm font-bold bg-indigo-100 text-indigo-700 dark:bg-indigo-900 dark:text-indigo-300 uppercase tracking-wide group-hover:scale-105 transition-transform">
                                        {{ $groupName }}
                                    </span>
                                    <span class="text-sm text-gray-700 dark:text-gray-200 font-medium">
                                        {{ $departmentLogs->groupBy(fn($log) => $log->assignment?->student?->id)->count() }} OJT Student{{ $departmentLogs->groupBy(fn($log) => $log->assignment?->student?->id)->count() !== 1 ? 's' : '' }}
                                    </span>
                                </div>
                                <div class="p-2 rounded-full bg-white dark:bg-gray-800 text-gray-400 group-hover:text-indigo-500 shadow-sm transition-colors">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4" />
                                    </svg>
                                </div>
                            </button>

                            <!-- Modal - List of OJT Students -->
                            <div x-show="showModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
                                <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                                    <!-- Overlay -->
                                    <div class="fixed inset-0 bg-gray-900/75 transition-opacity backdrop-blur-sm" @click="showModal = false" aria-hidden="true" x-transition.opacity></div>

                                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                                    <!-- Modal Panel -->
                                    <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-5xl sm:w-full border border-gray-200 dark:border-gray-700" x-transition>
                                        <!-- Header -->
                                        <div class="bg-gradient-to-r from-indigo-50 to-indigo-100 dark:from-indigo-900/20 dark:to-indigo-900/40 px-6 py-5 border-b border-indigo-200 dark:border-indigo-800">
                                            <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100 flex items-center gap-2 mb-4">
                                                <span class="px-3 py-1 rounded text-sm bg-indigo-600 text-white font-bold">{{ $groupName }}</span>
                                                <span>OJT Students</span>
                                            </h3>
                                            
                                            <!-- Search Bar -->
                                            <div class="relative">
                                                <svg class="absolute left-3 top-3 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                                </svg>
                                                <input x-model="searchQuery" type="text" placeholder="Search by name or company..." class="w-full pl-10 pr-4 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent outline-none transition-all">
                                            </div>
                                        </div>

                                        <!-- Content - List of OJT Students with Details -->
                                        <div class="bg-white dark:bg-gray-800 max-h-[65vh] overflow-y-auto">
                                            <!-- Table Header -->
                                            <div class="sticky top-0 bg-gradient-to-r from-gray-100 to-gray-50 dark:from-gray-900/50 dark:to-gray-900/30 border-b border-gray-200 dark:border-gray-700">
                                                <div class="grid grid-cols-12 gap-4 px-6 py-3 text-xs font-bold uppercase text-gray-700 dark:text-gray-200 tracking-wider">
                                                    <div class="col-span-3">OJT Student Name</div>
                                                    <div class="col-span-2">Course</div>
                                                    <div class="col-span-3">Company</div>
                                                    <div class="col-span-1 text-center">Hours</div>
                                                    <div class="col-span-2">Status</div>
                                                    <div class="col-span-1 text-center">Actions</div>
                                                </div>
                                            </div>

                                            <!-- Student Rows -->
                                            <div class="divide-y divide-gray-200 dark:divide-gray-700">
                                                <template x-for="student in filteredStudents" :key="student.id">
                                                    <div class="group">
                                                        <!-- Main Row -->
                                                        <div class="grid grid-cols-12 gap-4 px-6 py-4 hover:bg-indigo-50 dark:hover:bg-indigo-900/10 transition-colors items-center">
                                                            <!-- Student Name with Avatar -->
                                                            <div class="col-span-3 flex items-center gap-3">
                                                                <div class="h-10 w-10 rounded-full bg-gradient-to-br from-indigo-400 to-indigo-600 flex items-center justify-center text-white font-bold text-lg font-mono flex-shrink-0">
                                                                    <span x-text="student.name.charAt(0).toUpperCase()"></span>
                                                                </div>
                                                                <div class="flex-1 min-w-0">
                                                                    <p class="font-bold text-gray-900 dark:text-gray-100 truncate" x-text="student.name"></p>
                                                                    <p class="text-xs text-gray-700 dark:text-gray-200 mt-0.5" x-text="student.name.toLowerCase().replace(/\s+/g, '').substring(0, 8) + '@cksc.edu.ph'"></p>
                                                                </div>
                                                            </div>

                                                            <!-- Course -->
                                                            <div class="col-span-2">
                                                                <span class="px-3 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400" x-text="student.course"></span>
                                                            </div>

                                                            <!-- Company -->
                                                            <div class="col-span-3">
                                                                <p class="text-sm font-medium text-gray-900 dark:text-gray-100" x-text="student.company"></p>
                                                            </div>

                                                            <!-- Hours -->
                                                            <div class="col-span-1 text-center">
                                                                <p class="text-sm font-bold text-gray-900 dark:text-gray-100" x-text="`${student.hours.toFixed(1)}h`"></p>
                                                            </div>

                                                            <!-- Status -->
                                                            <div class="col-span-2">
                                                                <span :class="{
                                                                    'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400': student.status === 'Active',
                                                                    'bg-gray-200 text-gray-900 dark:bg-gray-700 dark:text-gray-100': student.status !== 'Active'
                                                                }" class="px-3 py-1 rounded-full text-xs font-bold inline-flex items-center gap-1" x-text="student.status">
                                                                </span>
                                                            </div>

                                                            <!-- Actions -->
                                                            <div class="col-span-1 text-center">
                                                                <button @click.stop="
                                                                    const logs = student.logs.map(log => ({
                                                                        id: log.id,
                                                                        type: log.type,
                                                                        date: log.date,
                                                                        status: log.status
                                                                    }));
                                                                    $dispatch('open-reports-modal', {
                                                                        studentId: student.id,
                                                                        studentName: student.name,
                                                                        companyName: student.company,
                                                                        department: student.department,
                                                                        logs: logs
                                                                    });
                                                                    showModal = false;
                                                                " class="inline-flex items-center gap-1 px-3 py-1.5 bg-indigo-600 text-white text-xs font-bold rounded-lg hover:bg-indigo-700 transition-colors shadow-md">
                                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                                                    <span x-text="`${student.reportCount} Report${student.reportCount !== 1 ? 's' : ''}`"></span>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </template>

                                                <!-- No OJT Students Found -->
                                                <template x-if="filteredStudents.length === 0">
                                                    <div class="px-6 py-12 text-center col-span-full">
                                                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                                        </svg>
                                                        <p class="text-gray-700 dark:text-gray-200 text-sm font-medium mt-2">No OJT students found</p>
                                                        <p class="text-gray-600 dark:text-gray-300 text-xs mt-1" x-text="`Try searching for "${searchQuery}"`"></p>
                                                    </div>
                                                </template>
                                            </div>
                                        </div>

                                        <!-- Footer -->
                                        <div class="bg-gray-50 dark:bg-gray-900/50 px-6 py-4 border-t border-gray-200 dark:border-gray-700 flex justify-between items-center">
                                            <span class="text-sm text-gray-700 dark:text-gray-200" x-text="`${filteredStudents.length} of ${allStudents.length} OJT students`"></span>
                                            <button @click="showModal = false" class="px-4 py-2 bg-gray-300 dark:bg-gray-600 text-gray-800 dark:text-gray-200 rounded-lg font-semibold hover:bg-gray-400 dark:hover:bg-gray-500 transition-colors">
                                                Close
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-8 text-center">
                            <p class="text-gray-700 dark:text-gray-200">No accomplishment reports found.</p>
                        </div>
                    @endforelse
                </div>

                <!-- Student Reports Modal -->
                <div @show-student-reports.window="
                    $dispatch('open-reports-modal', $event.detail);
                "></div>

                <div x-data="{ 
                    showModal: false, 
                    student: null,
                    reports: [],
                    selectedType: 'all'
                }" 
                     @open-reports-modal.window="
                        student = $event.detail;
                        reports = $event.detail.logs || [];
                        selectedType = 'all';
                        showModal = true;
                        console.log('Reports Modal Opened:', {student: student?.studentName, reportCount: reports.length});
                     ">
                    <!-- Modal -->
                    <div x-show="showModal" style="display: none" class="fixed inset-0 z-50 overflow-y-auto" @click.self="showModal = false">
                        <!-- Overlay -->
                        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm"></div>

                        <!-- Modal Content -->
                        <div class="relative flex items-center justify-center min-h-screen">
                            <div class="relative bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-4xl w-full mx-4" x-transition>
                                <!-- Header -->
                                <div class="bg-gradient-to-r from-indigo-50 to-indigo-100 dark:from-indigo-900/20 dark:to-indigo-900/40 px-8 py-6 border-b border-gray-200 dark:border-gray-700">
                                    <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-2">
                                        <span x-text="student?.studentName || 'OJT Student'"></span> - Accomplishment Reports
                                    </h2>
                                    <p class="text-sm text-gray-700 dark:text-gray-200 flex items-center gap-2 flex-wrap">
                                        <span class="px-2 py-1 rounded bg-indigo-100 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-400 font-bold text-xs" x-text="student?.department || 'Department'"></span>
                                        <span class="mx-1">•</span>
                                        <span x-text="student?.companyName || 'Company'"></span>
                                        <span class="mx-1">•</span>
                                        <span x-text="`${reports.length} Total Reports`"></span>
                                    </p>
                                </div>

                                <!-- Tabs -->
                                <div class="border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/30 px-8 py-4 flex gap-2 flex-wrap">
                                    <button @click="selectedType = 'all'" :class="selectedType === 'all' ? 'bg-indigo-600 text-white' : 'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-700'" class="px-4 py-2 rounded-lg font-semibold text-sm transition">
                                        All (<span x-text="reports.length"></span>)
                                    </button>
                                    <button @click="selectedType = 'daily'" :class="selectedType === 'daily' ? 'bg-green-600 text-white' : 'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-700'" class="px-4 py-2 rounded-lg font-semibold text-sm transition">
                                        Daily (<span x-text="reports.filter(r => r.type === 'daily').length"></span>)
                                    </button>
                                    <button @click="selectedType = 'weekly'" :class="selectedType === 'weekly' ? 'bg-blue-600 text-white' : 'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-700'" class="px-4 py-2 rounded-lg font-semibold text-sm transition">
                                        Weekly (<span x-text="reports.filter(r => r.type === 'weekly').length"></span>)
                                    </button>
                                    <button @click="selectedType = 'monthly'" :class="selectedType === 'monthly' ? 'bg-purple-600 text-white' : 'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-700'" class="px-4 py-2 rounded-lg font-semibold text-sm transition">
                                        Monthly (<span x-text="reports.filter(r => r.type === 'monthly').length"></span>)
                                    </button>
                                </div>

                                <!-- Content -->
                                <div class="px-8 py-6 max-h-96 overflow-y-auto">
                                    <template x-if="reports.filter(r => selectedType === 'all' || r.type === selectedType).length > 0">
                                        <div class="space-y-3">
                                            <template x-for="report in reports.filter(r => selectedType === 'all' || r.type === selectedType)" :key="report.id">
                                                <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-900/30 rounded-lg border border-gray-200 dark:border-gray-700">
                                                    <div class="flex-1">
                                                        <div class="flex items-center gap-2 mb-2">
                                                            <span :class="{
                                                                'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400': report.type === 'daily',
                                                                'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400': report.type === 'weekly',
                                                                'bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400': report.type === 'monthly'
                                                            }" class="px-3 py-1 rounded-full text-xs font-bold uppercase" x-text="report.type"></span>
                                                            <span class="text-sm font-bold text-gray-900 dark:text-gray-100" x-text="report.date"></span>
                                                        </div>
                                                        <span :class="{
                                                            'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400': report.status === 'Approved',
                                                            'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400': report.status === 'Draft',
                                                            'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400': report.status === 'Declined',
                                                            'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400': report.status === 'Submitted'
                                                        }" class="px-3 py-1 rounded text-xs font-bold" x-text="report.status"></span>
                                                    </div>
                                                    <div class="flex gap-2 ml-4">
                                                        <template x-if="report.attachmentUrl">
                                                            <div class="flex gap-2">
                                                                <a :href="report.attachmentUrl + '?inline=1&v=' + (report.updatedAt || report.id)" target="_blank" class="px-3 py-2 bg-indigo-600 text-white text-xs font-bold rounded hover:bg-indigo-700">
                                                                    View File
                                                                </a>
                                                                <a :href="report.attachmentUrl + '?v=' + (report.updatedAt || report.id)" class="px-3 py-2 bg-emerald-600 text-white text-xs font-bold rounded hover:bg-emerald-700">
                                                                    Download
                                                                </a>
                                                            </div>
                                                        </template>
                                                        <template x-if="!report.attachmentUrl">
                                                            <a :href="report.printUrl" target="_blank" class="px-3 py-2 bg-indigo-600 text-white text-xs font-bold rounded hover:bg-indigo-700">
                                                                Print
                                                            </a>
                                                        </template>
                                                    </div>
                                                </div>
                                            </template>
                                        </div>
                                    </template>
                                    <template x-if="reports.filter(r => selectedType === 'all' || r.type === selectedType).length === 0">
                                        <div class="text-center py-12">
                                            <p class="text-gray-700 dark:text-gray-200">No reports found in this category</p>
                                        </div>
                                    </template>
                                </div>

                                <!-- Footer -->
                                <div class="bg-gray-50 dark:bg-gray-900/50 px-8 py-4 border-t border-gray-200 dark:border-gray-700 flex justify-end">
                                    <button @click="showModal = false" class="px-4 py-2 bg-gray-300 dark:bg-gray-600 text-gray-800 dark:text-gray-200 rounded-lg font-semibold hover:bg-gray-400 dark:hover:bg-gray-500">
                                        Close
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-coordinator-layout>
