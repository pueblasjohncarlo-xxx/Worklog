<x-coordinator-layout>
    <x-slot name="header">
        OJT Student Overview
    </x-slot>

    <div class="space-y-6">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900 dark:text-gray-100">
                <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
                    <h3 class="text-lg font-semibold">OJT Student Roster</h3>
                    
                    <!-- Filters and Actions -->
                    <div class="flex flex-wrap items-center gap-2">
                        <!-- Search Bar -->
                        <form method="GET" action="{{ route('coordinator.student-overview') }}" class="flex items-center gap-2">
                            <div class="relative">
                                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search OJT students..." 
                                        class="text-sm rounded-md border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-700 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 pl-8 w-full sm:w-64"
                                       oninput="this.form.submit()">
                                <div class="absolute inset-y-0 left-0 pl-2.5 flex items-center pointer-events-none">
                                    <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                </div>
                            </div>
                            
                            <select name="company_id" onchange="this.form.submit()" class="text-sm rounded-md border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-700 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">All Companies</option>
                                @foreach($companies as $company)
                                    <option value="{{ $company->id }}" {{ request('company_id') == $company->id ? 'selected' : '' }}>
                                        {{ $company->name }}
                                    </option>
                                @endforeach
                            </select>
                        </form>

                        <a href="{{ route('coordinator.students.import') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                            Bulk Import
                        </a>
                    </div>
                </div>

                <div class="space-y-4">
                    @forelse($groupedStudents as $groupName => $students)
                        <div x-data="{ showModal: false, search: '' }" class="border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden bg-white dark:bg-gray-800 transition-shadow hover:shadow-md">
                            <!-- Header -->
                            <button @click="showModal = true" class="w-full px-6 py-4 bg-gray-50 dark:bg-gray-900/50 flex items-center justify-between hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors group">
                                <div class="flex items-center gap-3">
                                    <span class="px-3 py-1.5 rounded-lg text-sm font-bold bg-indigo-100 text-indigo-700 dark:bg-indigo-900 dark:text-indigo-300 uppercase tracking-wide group-hover:scale-105 transition-transform">
                                        {{ $groupName }}
                                    </span>
                                    <span class="text-sm text-gray-700 dark:text-gray-200 font-medium">
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
                                    <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-6xl sm:w-full border border-gray-200 dark:border-gray-700" x-transition>
                                        <!-- Header -->
                                        <div class="bg-gray-50 dark:bg-gray-900/80 px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex flex-col sm:flex-row justify-between items-center gap-4">
                                            <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100 flex items-center gap-2">
                                                <span class="px-2 py-1 rounded text-sm bg-indigo-100 text-indigo-700 dark:bg-indigo-900 dark:text-indigo-300">{{ $groupName }}</span>
                                                <span>OJT Student Roster</span>
                                            </h3>
                                            
                                            <!-- Search Input -->
                                            <div class="relative w-full sm:w-72">
                                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                                    </svg>
                                                </div>
                                                <input x-model="search" type="text" class="block w-full pl-10 pr-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg leading-5 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm transition-shadow" placeholder="Search by name or company...">
                                            </div>
                                        </div>

                                        <!-- Content -->
                                        <div class="bg-white dark:bg-gray-800 max-h-[70vh] overflow-y-auto overflow-x-auto">
                                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                                <thead class="sticky top-0 z-10 bg-gray-50 dark:bg-gray-900/90 backdrop-blur-sm shadow-sm">
                                                    <tr>
                                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-200 uppercase tracking-wider">Student Name</th>
                                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-200 uppercase tracking-wider">Course</th>
                                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-200 uppercase tracking-wider">Assigned Company</th>
                                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-200 uppercase tracking-wider">Hours Rendered</th>
                                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-200 uppercase tracking-wider">Status</th>
                                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-200 uppercase tracking-wider">Last Login</th>
                                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-200 uppercase tracking-wider">Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                                    @foreach($students as $student)
                                                        @php 
                                                            $assignment = $student->studentAssignments->where('status', 'active')->first(); 
                                                            $totalHours = $assignment ? $assignment->totalApprovedHours() : 0;
                                                            $requiredHours = $assignment->required_hours ?? 0;
                                                            $percentage = $requiredHours > 0 ? min(100, ($totalHours / $requiredHours) * 100) : 0;
                                                        @endphp
                                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-900/50 transition-colors" x-show="!search || $el.innerText.toLowerCase().includes(search.toLowerCase())"
                                                            x-data="{ showDetails: false }">
                                                            <td class="px-6 py-4 whitespace-nowrap">
                                                                <div class="flex items-center gap-3">
                                                                    <div class="h-8 w-8 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 font-bold text-xs">
                                                                        {{ substr($student->name, 0, 1) }}
                                                                    </div>
                                                                    <div>
                                                                        <div class="text-sm font-bold text-gray-900 dark:text-gray-100">
                                                                            {{ $student->name }}
                                                                        </div>
                                                                        <div class="text-xs text-gray-700 dark:text-gray-200">
                                                                            {{ $student->email }}
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </td>
                                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-300">
                                                                {{ $student->department ?? 'N/A' }}
                                                            </td>
                                                            <td class="px-6 py-4 whitespace-nowrap">
                                                                @if($assignment)
                                                                    <div class="flex items-center gap-2">
                                                                        <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                                                        </svg>
                                                                        <span class="text-sm font-semibold text-indigo-600 dark:text-indigo-400">{{ $assignment->company->name }}</span>
                                                                    </div>
                                                                @else
                                                                    <span class="text-xs text-red-500 italic">Not Assigned</span>
                                                                @endif
                                                            </td>
                                                            <td class="px-6 py-4 whitespace-nowrap">
                                                                @if($assignment)
                                                                    <div class="flex items-center gap-3">
                                                                        <div class="w-24 bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                                                            <div class="bg-emerald-500 h-2 rounded-full" @style(["width: {$percentage}%"])></div>
                                                                        </div>
                                                                        <span class="text-sm font-bold text-gray-700 dark:text-gray-200">{{ number_format($totalHours, 1) }}h</span>
                                                                    </div>
                                                                @else
                                                                    -
                                                                @endif
                                                            </td>
                                                            <td class="px-6 py-4 whitespace-nowrap">
                                                                @if($student->last_login_at)
                                                                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                                                        Active
                                                                    </span>
                                                                @else
                                                                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-200 text-gray-900 dark:bg-gray-600 dark:text-gray-100">
                                                                        Inactive
                                                                    </span>
                                                                @endif
                                                            </td>
                                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-200">
                                                                {{ $student->last_login_at ? $student->last_login_at->diffForHumans() : 'Never' }}
                                                            </td>
                                                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                                <button class="view-details-btn inline-flex items-center px-3 py-1.5 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors font-medium text-xs uppercase tracking-wider"
                                                                    data-student="{{ json_encode($student) }}"
                                                                    data-assignment="{{ json_encode($assignment) }}"
                                                                    data-total-hours="{{ $totalHours }}"
                                                                    data-required-hours="{{ $requiredHours }}"
                                                                    data-percentage="{{ $percentage }}">
                                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                                    </svg>
                                                                    View Details
                                                                </button>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                            
                                            <!-- No Results Message -->
                                            <div class="p-8 text-center text-gray-700 dark:text-gray-200" x-show="$el.previousElementSibling.querySelectorAll('tr[x-show]').length === 0 && search !== ''" style="display: none;">
                                                No OJT students found matching "<span x-text="search" class="font-bold"></span>"
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
                        <div class="text-center py-12 bg-white dark:bg-gray-800 rounded-lg border border-dashed border-gray-300 dark:border-gray-700">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">No OJT students found</h3>
                            <p class="mt-1 text-sm text-gray-700 dark:text-gray-200">Try adjusting your filters or import new OJT students.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Global Student Details Modal - Pure JavaScript -->
        <div id="studentDetailsModal" class="hidden fixed inset-0 overflow-y-auto bg-black/50 backdrop-blur-sm flex items-center justify-center p-4" style="z-index: 99999;">
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-3xl max-h-[90vh] overflow-auto">
                <!-- Header -->
                <div id="modalHeader" class="sticky top-0 bg-gradient-to-r from-indigo-600 to-indigo-800 px-8 py-6 flex items-center justify-between rounded-t-2xl">
                    <div class="flex items-center gap-4">
                        <div id="modalAvatar" class="h-12 w-12 rounded-full bg-white flex items-center justify-center text-indigo-600 font-bold text-lg">S</div>
                        <div>
                            <h3 id="modalStudentName" class="text-2xl font-bold text-white">OJT Student</h3>
                            <p id="modalStudentEmail" class="text-indigo-100 text-sm">email@example.com</p>
                        </div>
                    </div>
                    <button onclick="document.getElementById('studentDetailsModal').classList.add('hidden')" class="text-white hover:bg-indigo-700 rounded-full p-2 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <!-- Content -->
                <div class="px-8 py-6 space-y-6">
                    <!-- Personal Information -->
                    <div>
                        <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5 text-indigo-600" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                            </svg>
                            Personal Information
                        </h4>
                        <div class="grid grid-cols-2 gap-6 bg-gray-50 dark:bg-gray-900/30 p-4 rounded-lg">
                            <div>
                                <p class="text-sm text-gray-700 dark:text-gray-200 font-medium mb-1">Email</p>
                                <p id="modalDetailEmail" class="text-gray-900 dark:text-gray-100 font-semibold">N/A</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-700 dark:text-gray-200 font-medium mb-1">Department</p>
                                <p id="modalDetailDept" class="text-gray-900 dark:text-gray-100 font-semibold">N/A</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-700 dark:text-gray-200 font-medium mb-1">Section</p>
                                <p id="modalDetailSection" class="text-gray-900 dark:text-gray-100 font-semibold">N/A</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-700 dark:text-gray-200 font-medium mb-1">Joined</p>
                                <p id="modalDetailJoined" class="text-gray-900 dark:text-gray-100 font-semibold">N/A</p>
                            </div>
                        </div>
                    </div>

                    <!-- Assignment Information -->
                    <div id="assignmentSection" style="display: none;">
                        <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5 text-indigo-600" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                            Current Assignment
                        </h4>
                        <div class="bg-indigo-50 dark:bg-indigo-900/30 rounded-lg p-6 space-y-4">
                            <div class="grid grid-cols-2 gap-6">
                                <div>
                                    <p class="text-sm text-gray-700 dark:text-gray-200 font-medium mb-1">Company</p>
                                    <p id="modalCompanyName" class="text-gray-900 dark:text-gray-100 font-semibold text-lg">N/A</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-700 dark:text-gray-200 font-medium mb-1">Industry</p>
                                    <p id="modalIndustry" class="text-gray-900 dark:text-gray-100 font-semibold text-lg">N/A</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-700 dark:text-gray-200 font-medium mb-1">Required Hours</p>
                                    <p id="modalRequiredHours" class="text-gray-900 dark:text-gray-100 font-semibold text-lg">0 hrs</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-700 dark:text-gray-200 font-medium mb-1">Completed Hours</p>
                                    <p id="modalCompletedHours" class="text-gray-900 dark:text-gray-100 font-semibold text-lg">0 hrs</p>
                                </div>
                            </div>
                            <div>
                                <p class="text-sm text-gray-700 dark:text-gray-200 font-medium mb-3">Progress</p>
                                <div class="flex items-center gap-4">
                                    <div class="flex-1 bg-gray-200 dark:bg-gray-700 rounded-full h-4 overflow-hidden">
                                        <div id="modalProgressBar" class="bg-gradient-to-r from-emerald-500 to-green-500 h-4 rounded-full transition-all" style="width: 0%"></div>
                                    </div>
                                    <span id="modalProgressText" class="text-lg font-bold text-gray-700 dark:text-gray-200 min-w-fit">0%</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- No Assignment Alert -->
                    <div id="noAssignmentSection" style="display: none;" class="bg-amber-50 dark:bg-amber-900/30 border border-amber-200 dark:border-amber-700 rounded-lg p-4 flex items-start gap-3">
                        <svg class="w-6 h-6 text-amber-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/>
                        </svg>
                        <div>
                            <p class="text-sm font-semibold text-amber-800 dark:text-amber-200">No Active Assignment</p>
                            <p class="text-xs text-amber-700 dark:text-amber-300 mt-1">This student doesn't have an active assignment yet.</p>
                        </div>
                    </div>

                    <!-- Status Information -->
                    <div>
                        <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5 text-indigo-600" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                            </svg>
                            Account Status
                        </h4>
                        <div class="grid grid-cols-2 gap-6 bg-gray-50 dark:bg-gray-900/30 p-4 rounded-lg">
                            <div>
                                <p class="text-sm text-gray-700 dark:text-gray-200 font-medium mb-2">Status</p>
                                <span id="modalStatusBadge" class="px-3 py-1.5 inline-flex text-sm leading-5 font-semibold rounded-full bg-gray-200 text-gray-900 dark:bg-gray-600 dark:text-gray-100">Inactive</span>
                            </div>
                            <div>
                                <p class="text-sm text-gray-700 dark:text-gray-200 font-medium mb-2">Last Login</p>
                                <p id="modalLastLogin" class="text-gray-900 dark:text-gray-100 font-semibold">Never</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="bg-gray-50 dark:bg-gray-900/50 px-8 py-4 flex justify-end gap-3 rounded-b-2xl border-t border-gray-200 dark:border-gray-700">
                    <button onclick="document.getElementById('studentDetailsModal').classList.add('hidden')" class="px-6 py-2 text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 font-medium transition-colors">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Event delegation for View Details buttons
        document.addEventListener('click', function(e) {
            if (e.target.closest('.view-details-btn')) {
                const btn = e.target.closest('.view-details-btn');
                try {
                    const student = JSON.parse(btn.dataset.student);
                    const assignment = btn.dataset.assignment ? JSON.parse(btn.dataset.assignment) : null;
                    const totalHours = parseFloat(btn.dataset.totalHours) || 0;
                    const requiredHours = parseFloat(btn.dataset.requiredHours) || 0;
                    const percentage = parseFloat(btn.dataset.percentage) || 0;
                    
                    window.showStudentModal(student, assignment, totalHours, requiredHours, percentage);
                } catch (err) {
                    console.error('Error opening modal:', err);
                }
            }
        });

        // Show student modal function - PURE JAVASCRIPT
        window.showStudentModal = function(student, assignment, totalHours, requiredHours, percentage) {
            const modal = document.getElementById('studentDetailsModal');
            
            if (!modal) {
                console.error('Modal element not found');
                return;
            }
            
            // Update header
            document.getElementById('modalAvatar').textContent = (student?.name?.charAt(0) || 'S').toUpperCase();
            document.getElementById('modalStudentName').textContent = student?.name || 'Student';
            document.getElementById('modalStudentEmail').textContent = student?.email || '';
            
            // Update personal info
            document.getElementById('modalDetailEmail').textContent = student?.email || 'N/A';
            document.getElementById('modalDetailDept').textContent = student?.department || 'N/A';
            document.getElementById('modalDetailSection').textContent = student?.section || 'N/A';
            document.getElementById('modalDetailJoined').textContent = window.formatDate(student?.created_at) || 'N/A';
            
            // Update assignment section
            if (assignment) {
                document.getElementById('assignmentSection').style.display = 'block';
                document.getElementById('noAssignmentSection').style.display = 'none';
                document.getElementById('modalCompanyName').textContent = assignment?.company?.name || 'N/A';
                document.getElementById('modalIndustry').textContent = assignment?.company?.industry || 'N/A';
                document.getElementById('modalRequiredHours').textContent = (requiredHours || 0) + ' hrs';
                document.getElementById('modalCompletedHours').textContent = (totalHours || 0).toFixed(1) + ' hrs';
                document.getElementById('modalProgressBar').style.width = (percentage || 0) + '%';
                document.getElementById('modalProgressText').textContent = Math.round(percentage || 0) + '%';
            } else {
                document.getElementById('assignmentSection').style.display = 'none';
                document.getElementById('noAssignmentSection').style.display = 'flex';
            }
            
            // Update status
            const statusBadge = document.getElementById('modalStatusBadge');
            const lastLogin = document.getElementById('modalLastLogin');
            if (student?.last_login_at) {
                statusBadge.textContent = 'Active';
                statusBadge.className = 'px-3 py-1.5 inline-flex text-sm leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200';
                lastLogin.textContent = window.formatRelativeDate(student.last_login_at) || 'Never';
            } else {
                statusBadge.textContent = 'Inactive';
                statusBadge.className = 'px-3 py-1.5 inline-flex text-sm leading-5 font-semibold rounded-full bg-gray-200 text-gray-900 dark:bg-gray-600 dark:text-gray-100';
                lastLogin.textContent = 'Never';
            }
            
            // Show modal with proper z-index
            modal.classList.remove('hidden');
            modal.style.zIndex = '99999';
            document.body.style.overflow = 'hidden';
            
            // Close modal when clicking outside
            const closeHandler = function(e) {
                if (e.target === modal) {
                    modal.classList.add('hidden');
                    document.body.style.overflow = 'auto';
                    modal.removeEventListener('click', closeHandler);
                }
            };
            modal.addEventListener('click', closeHandler);
        };

        // Format date helper
        window.formatDate = function(dateString) {
            if (!dateString) return 'N/A';
            try {
                const date = new Date(dateString);
                return date.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
            } catch (e) {
                return dateString;
            }
        };

        // Format relative date helper
        window.formatRelativeDate = function(dateString) {
            if (!dateString) return 'Never';
            try {
                const date = new Date(dateString);
                const now = new Date();
                const seconds = Math.floor((now - date) / 1000);
                
                if (seconds < 60) return 'Just now';
                if (seconds < 3600) return Math.floor(seconds / 60) + ' minutes ago';
                if (seconds < 86400) return Math.floor(seconds / 3600) + ' hours ago';
                if (seconds < 604800) return Math.floor(seconds / 86400) + ' days ago';
                if (seconds < 2592000) return Math.floor(seconds / 604800) + ' weeks ago';
                return Math.floor(seconds / 2592000) + ' months ago';
            } catch (e) {
                return dateString;
            }
        };
    </script>
</x-coordinator-layout>
