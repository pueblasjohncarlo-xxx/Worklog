<x-coordinator-layout>
    <x-slot name="header">
        Performance Evaluation
    </x-slot>

    <div class="space-y-6">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900 dark:text-gray-100">
                <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
                    <h3 class="text-lg font-semibold">Supervisors by Company</h3>
                    
                    <!-- Filters and Actions -->
                    <div class="flex flex-wrap items-center gap-2">
                        <!-- Search Bar -->
                        <form method="GET" action="{{ route('coordinator.evaluations.index') }}" class="flex items-center gap-2">
                            <div class="relative">
                                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search supervisors..." 
                                       class="text-sm rounded-md border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-700 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 pl-8 w-64"
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
                    </div>
                </div>

                <div class="space-y-4">
                    @forelse($groupedSupervisors as $groupName => $supervisors)
                        <div x-data="{ showModal: false, search: '' }" class="border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden bg-white dark:bg-gray-800 transition-shadow hover:shadow-md">
                            <!-- Header -->
                            <button @click="showModal = true" class="w-full px-6 py-4 bg-gray-50 dark:bg-gray-900/50 flex items-center justify-between hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors group">
                                <div class="flex items-center gap-3">
                                    <span class="px-3 py-1.5 rounded-lg text-sm font-bold bg-indigo-100 text-indigo-700 dark:bg-indigo-900 dark:text-indigo-300 uppercase tracking-wide group-hover:scale-105 transition-transform">
                                        {{ $groupName }}
                                    </span>
                                    <span class="text-sm text-gray-700 dark:text-gray-200 font-medium">
                                        {{ $supervisors->count() }} Supervisors
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
                                                <span>Supervisor List</span>
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
                                        <div class="bg-white dark:bg-gray-800 max-h-[70vh] overflow-y-auto overflow-x-auto">
                                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                                <thead class="sticky top-0 z-10 bg-gray-50 dark:bg-gray-900/90 backdrop-blur-sm shadow-sm">
                                                    <tr>
                                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-200 uppercase tracking-wider">Supervisor Name</th>
                                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-200 uppercase tracking-wider">Department</th>
                                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-200 uppercase tracking-wider">Assigned OJT Students</th>
                                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-200 uppercase tracking-wider">Evaluations</th>
                                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-700 dark:text-gray-200 uppercase tracking-wider">Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                                    @foreach($supervisors as $supervisor)
                                                        @php 
                                                            $assignments = $supervisor->supervisorAssignments->where('status', 'active');
                                                            $evaluationsCount = \App\Models\PerformanceEvaluation::where('supervisor_id', $supervisor->id)->count();
                                                        @endphp
                                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-900/50 transition-colors" x-show="!search || $el.innerText.toLowerCase().includes(search.toLowerCase())">
                                                            <td class="px-6 py-4 whitespace-nowrap">
                                                                <div class="flex items-center gap-3">
                                                                    <div class="h-8 w-8 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 font-bold text-xs">
                                                                        {{ substr($supervisor->name, 0, 1) }}
                                                                    </div>
                                                                    <div>
                                                                        <div class="text-sm font-bold text-gray-900 dark:text-gray-100">
                                                                            {{ $supervisor->name }}
                                                                        </div>
                                                                        <div class="text-xs text-gray-700 dark:text-gray-200">
                                                                            {{ $supervisor->email }}
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </td>
                                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-300">
                                                                {{ $supervisor->supervisorProfile->department ?? 'N/A' }}
                                                            </td>
                                                            <td class="px-6 py-4 whitespace-nowrap">
                                                                <div class="flex -space-x-2 overflow-hidden">
                                                                    @foreach($assignments->take(5) as $assignment)
                                                                        <div class="inline-block h-6 w-6 rounded-full ring-2 ring-white dark:ring-gray-800 bg-gray-200 dark:bg-gray-700 flex items-center justify-center text-[10px] font-bold text-gray-600 dark:text-gray-300" title="{{ $assignment->student->name }}">
                                                                            {{ substr($assignment->student->name, 0, 1) }}
                                                                        </div>
                                                                    @endforeach
                                                                    @if($assignments->count() > 5)
                                                                        <div class="inline-block h-6 w-6 rounded-full ring-2 ring-white dark:ring-gray-800 bg-indigo-100 flex items-center justify-center text-[10px] font-bold text-indigo-600">
                                                                            +{{ $assignments->count() - 5 }}
                                                                        </div>
                                                                    @endif
                                                                </div>
                                                                <span class="text-xs text-gray-700 dark:text-gray-200 mt-1 block">{{ $assignments->count() }} OJT Students Assigned</span>
                                                            </td>
                                                            <td class="px-6 py-4 whitespace-nowrap">
                                                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                                                    {{ $evaluationsCount }} Submitted
                                                                </span>
                                                            </td>
                                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                                <div class="inline-flex items-center gap-2">
                                                                    <a href="{{ route('coordinator.evaluations.supervisor', $supervisor) }}" class="inline-flex items-center px-3 py-1.5 rounded-lg bg-indigo-600 text-white text-xs font-bold hover:bg-indigo-700 transition-colors shadow-sm">
                                                                        View Details
                                                                    </a>
                                                                    <a href="{{ route('coordinator.evaluations.supervisor', ['supervisor' => $supervisor, 'print' => 1]) }}" target="_blank" class="inline-flex items-center px-3 py-1.5 rounded-lg bg-slate-900 text-white text-xs font-bold hover:bg-black transition-colors shadow-sm">
                                                                        Print
                                                                    </a>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                            
                                            <!-- No Results Message -->
                                            <div class="p-8 text-center text-gray-700 dark:text-gray-200" x-show="$el.previousElementSibling.querySelectorAll('tr[x-show]').length === 0 && search !== ''" style="display: none;">
                                                No supervisors found matching "<span x-text="search" class="font-bold"></span>"
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
                            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">No supervisors found</h3>
                            <p class="mt-1 text-sm text-gray-700 dark:text-gray-200">Try adjusting your filters.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-coordinator-layout>
