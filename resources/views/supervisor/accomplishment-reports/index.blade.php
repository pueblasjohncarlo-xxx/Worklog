<x-supervisor-layout>
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
                    
                    <!-- Filters and Actions -->
                    <div class="flex flex-wrap items-center gap-2">
                        <!-- Type Filter -->
                        <div class="flex items-center gap-2">
                            @foreach(['daily','weekly','monthly'] as $cat)
                                <a href="{{ route('supervisor.accomplishment-reports', array_merge(request()->query(), ['type' => $cat])) }}"
                                   class="px-3 py-1 rounded-full uppercase text-xs font-bold border transition-colors {{ ($type=== $cat) ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-white dark:bg-gray-700 text-gray-600 dark:text-gray-300 border-gray-300 dark:border-gray-600 hover:bg-indigo-50 dark:hover:bg-gray-600' }}">
                                    {{ ucfirst($cat) }}
                                </a>
                            @endforeach
                            <a href="{{ route('supervisor.accomplishment-reports', request()->except('type')) }}" class="px-3 py-1 rounded-full uppercase text-xs font-bold border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600">All</a>
                        </div>

                        <!-- Status Filter -->
                        <select name="status" onchange="window.location=window.location.pathname + '?' + new URLSearchParams({...Object.fromEntries(new URLSearchParams(window.location.search)), status: this.value}).toString()" class="rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 px-2 py-1 text-xs">
                            <option value="">All Status</option>
                            @foreach(['approved'=>'Approved','draft'=>'Draft','rejected'=>'Declined','submitted'=>'Submitted'] as $key => $label)
                                <option value="{{ $key }}" @selected($status === $key)>{{ $label }}</option>
                            @endforeach
                        </select>

                        <!-- Date Filter -->
                        <input type="date" name="sent_date" value="{{ $sentDate }}" onchange="window.location=window.location.pathname + '?' + new URLSearchParams({...Object.fromEntries(new URLSearchParams(window.location.search)), sent_date: this.value}).toString()" class="rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 px-2 py-1 text-xs">

                        <!-- Reset Button -->
                        <a href="{{ route('supervisor.accomplishment-reports') }}" class="px-3 py-1 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-md text-xs font-semibold hover:bg-gray-200 dark:hover:bg-gray-600">Reset</a>
                    </div>
                </div>

                <div class="space-y-4">
                    @php
                        $groupedByStudent = $workLogs->groupBy(function($log) {
                            return $log->assignment?->student?->name ?? 'Unknown Student';
                        });
                    @endphp
                    
                    @forelse($groupedByStudent as $studentName => $studentLogs)
                        <div x-data="{ showModal: false, search: '' }" class="border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden bg-white dark:bg-gray-800 transition-shadow hover:shadow-md">
                            <!-- Header -->
                            <button @click="showModal = true" class="w-full px-6 py-4 bg-gray-50 dark:bg-gray-900/50 flex items-center justify-between hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors group">
                                <div class="flex items-center gap-3">
                                    <span class="px-3 py-1.5 rounded-lg text-sm font-bold bg-indigo-100 text-indigo-700 dark:bg-indigo-900 dark:text-indigo-300 uppercase tracking-wide group-hover:scale-105 transition-transform">
                                        {{ $studentName }}
                                    </span>
                                    <span class="text-sm text-gray-600 dark:text-gray-400 font-medium">
                                        {{ $studentLogs->count() }} Report{{ $studentLogs->count() !== 1 ? 's' : '' }}
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
                                    <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full border border-gray-200 dark:border-gray-700" x-transition>
                                        <!-- Header -->
                                        <div class="bg-gray-50 dark:bg-gray-900/80 px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex flex-col sm:flex-row justify-between items-center gap-4">
                                            <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100 flex items-center gap-2">
                                                <span class="px-2 py-1 rounded text-sm bg-indigo-100 text-indigo-700 dark:bg-indigo-900 dark:text-indigo-300">{{ $studentName }}</span>
                                                <span>Accomplishment Reports</span>
                                            </h3>
                                            
                                            <!-- Search Input -->
                                            <div class="relative w-full sm:w-72">
                                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                                    </svg>
                                                </div>
                                                <input x-model="search" type="text" class="block w-full pl-10 pr-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg leading-5 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm transition-shadow" placeholder="Search by type or status...">
                                            </div>
                                        </div>

                                        <!-- Content -->
                                        <div class="bg-white dark:bg-gray-800 max-h-[70vh] overflow-y-auto">
                                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                                <thead class="sticky top-0 z-10 bg-gray-50 dark:bg-gray-900/90 backdrop-blur-sm shadow-sm">
                                                    <tr>
                                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Type</th>
                                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Date</th>
                                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                                    @foreach($studentLogs as $log)
                                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-900/50 transition-colors" 
                                                            x-show="!search || $el.innerText.toLowerCase().includes(search.toLowerCase())">
                                                            <td class="px-6 py-4 whitespace-nowrap">
                                                                <span class="px-2 py-1 rounded-full text-xs font-bold uppercase {{ $log->type === 'daily' ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300' : ($log->type === 'weekly' ? 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300' : 'bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-300') }}">
                                                                    {{ $log->type }}
                                                                </span>
                                                            </td>
                                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-300">
                                                                {{ $log->work_date?->format('M d, Y') ?? 'N/A' }}
                                                            </td>
                                                            <td class="px-6 py-4 whitespace-nowrap">
                                                                @php
                                                                    $statusDisplay = ucfirst($log->status === 'rejected' ? 'declined' : $log->status);
                                                                    $statusBadge = match($log->status) {
                                                                        'approved' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300',
                                                                        'draft' => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-300',
                                                                        'rejected' => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300',
                                                                        'submitted' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300',
                                                                        default => 'bg-gray-100 text-gray-700 dark:bg-gray-900/30 dark:text-gray-300'
                                                                    };
                                                                @endphp
                                                                <span class="px-2 py-1 rounded text-xs font-bold uppercase {{ $statusBadge }}">
                                                                    {{ $statusDisplay }}
                                                                </span>
                                                            </td>
                                                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                                <a href="{{ route('supervisor.worklogs.print', $log->id) }}" target="_blank" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 font-semibold hover:underline">
                                                                    Print
                                                                </a>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>

                                        <!-- Close Button -->
                                        <div class="bg-gray-50 dark:bg-gray-900/50 px-6 py-4 border-t border-gray-200 dark:border-gray-700 flex justify-end">
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
                            <p class="text-gray-500 dark:text-gray-400">No accomplishment reports found.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-supervisor-layout>
