<x-supervisor-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Accomplishment Reports') }}
        </h2>
    </x-slot>

    @php
        $groupedByStudent = $workLogs->groupBy(function ($log) {
            return $log->assignment?->student?->name ?? 'Unknown Student';
        });

        $studentGroups = $groupedByStudent->map(function ($studentLogs, $studentName) {
            $firstLog = $studentLogs->first();
            $student = $firstLog?->assignment?->student;
            $company = $firstLog?->assignment?->company;

            $logs = $studentLogs->map(function ($log) {
                $rawStatus = (string) ($log->status ?? 'draft');
                $statusLabel = match ($rawStatus) {
                    'approved' => 'Approved',
                    'submitted' => 'Submitted',
                    'rejected' => 'Declined',
                    default => 'Draft',
                };
                $typeValue = strtolower((string) $log->type);

                return [
                    'id' => $log->id,
                    'type' => ucfirst((string) $log->type),
                    'type_value' => $typeValue,
                    'date' => $log->work_date?->format('M d, Y') ?? 'N/A',
                    'status' => $statusLabel,
                    'filename' => $log->attachment_path ? basename($log->attachment_path) : 'Generated report',
                    'view_url' => $log->attachment_path ? route('supervisor.worklogs.attachment', $log->id) . '?inline=1' : null,
                    'download_url' => $log->attachment_path ? route('supervisor.worklogs.attachment', $log->id) : null,
                    'print_url' => route('supervisor.worklogs.print', $log->id),
                    'search_blob' => strtolower(implode(' ', array_filter([
                        $log->type,
                        $log->work_date?->format('M d, Y'),
                        $statusLabel,
                        $log->attachment_path ? basename($log->attachment_path) : 'generated report',
                    ]))),
                ];
            })->values();

            return [
                'student_name' => $studentName,
                'student_section' => $student?->section ?? 'N/A',
                'company' => $company?->name ?? 'N/A',
                'supervisor_name' => auth()->user()?->name ?? 'Supervisor',
                'report_count' => $logs->count(),
                'search_blob' => strtolower(implode(' ', array_filter([
                    $studentName,
                    $student?->section,
                    $company?->name,
                    auth()->user()?->name,
                    $logs->pluck('search_blob')->implode(' '),
                ]))),
                'logs' => $logs->all(),
            ];
        })->values();
    @endphp

    <div
        x-data="supervisorAccomplishmentReports(@js($studentGroups))"
        class="space-y-6"
    >
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900 dark:text-gray-100">
                <div class="flex flex-col md:flex-row justify-between items-start mb-6 gap-4">
                    <div class="space-y-3">
                        <h3 class="text-lg font-semibold">Accomplishment Reports</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-300">Search instantly by student, type, date, status, company, supervisor, or filename while keeping the current filters active.</p>
                    </div>

                    <div class="w-full md:max-w-md">
                        <label for="supervisor-ar-search" class="sr-only">Search accomplishment reports</label>
                        <div class="relative">
                            <svg class="pointer-events-none absolute left-3 top-3.5 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                            <input
                                id="supervisor-ar-search"
                                x-model.debounce.100ms="searchQuery"
                                type="search"
                                placeholder="Search by student, type, status, company, or filename"
                                class="w-full rounded-xl border border-gray-300 bg-white py-3 pl-10 pr-4 text-sm text-gray-900 shadow-sm outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100"
                            >
                        </div>
                    </div>
                </div>

                <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
                    <div class="flex flex-wrap items-center gap-2">
                        <div class="flex items-center gap-2">
                            @foreach(['daily','weekly','monthly'] as $cat)
                                <a href="{{ route('supervisor.accomplishment-reports', array_merge(request()->query(), ['type' => $cat])) }}"
                                   class="px-3 py-1 rounded-full uppercase text-xs font-bold border transition-colors {{ ($type=== $cat) ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-white dark:bg-gray-700 text-gray-600 dark:text-gray-300 border-gray-300 dark:border-gray-600 hover:bg-indigo-50 dark:hover:bg-gray-600' }}">
                                    {{ ucfirst($cat) }}
                                </a>
                            @endforeach
                            <a href="{{ route('supervisor.accomplishment-reports', request()->except('type')) }}" class="px-3 py-1 rounded-full uppercase text-xs font-bold border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600">All</a>
                        </div>

                        <select name="status" onchange="window.location=window.location.pathname + '?' + new URLSearchParams({...Object.fromEntries(new URLSearchParams(window.location.search)), status: this.value}).toString()" class="rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 px-2 py-1 text-xs">
                            <option value="">All Status</option>
                            @foreach(['approved'=>'Approved','draft'=>'Draft','rejected'=>'Declined','submitted'=>'Submitted'] as $key => $label)
                                <option value="{{ $key }}" @selected($status === $key)>{{ $label }}</option>
                            @endforeach
                        </select>

                        <input type="date" name="sent_date" value="{{ $sentDate }}" onchange="window.location=window.location.pathname + '?' + new URLSearchParams({...Object.fromEntries(new URLSearchParams(window.location.search)), sent_date: this.value}).toString()" class="rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 px-2 py-1 text-xs">

                        <a href="{{ route('supervisor.accomplishment-reports') }}" class="px-3 py-1 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-md text-xs font-semibold hover:bg-gray-200 dark:hover:bg-gray-600">Reset</a>
                    </div>

                    <div class="text-xs font-semibold text-gray-600 dark:text-gray-300">
                        Showing <span class="font-black text-gray-900 dark:text-white" x-text="filteredGroups.length"></span> of <span class="font-black text-gray-900 dark:text-white" x-text="groups.length"></span> students
                    </div>
                </div>

                <div class="space-y-4">
                    <template x-for="group in filteredGroups" :key="group.student_name">
                        <div class="border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden bg-white dark:bg-gray-800 transition-shadow hover:shadow-md">
                            <button @click="openGroup(group)" class="w-full px-6 py-4 bg-gray-50 dark:bg-gray-900/50 flex items-center justify-between hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors group text-left">
                                <div class="flex items-center gap-3">
                                    <span class="px-3 py-1.5 rounded-lg text-sm font-bold bg-indigo-100 text-indigo-700 dark:bg-indigo-900 dark:text-indigo-300 uppercase tracking-wide group-hover:scale-105 transition-transform" x-text="group.student_name"></span>
                                    <span class="text-sm text-gray-600 dark:text-gray-400 font-medium" x-text="`${group.report_count} Report${group.report_count !== 1 ? 's' : ''}`"></span>
                                </div>
                                <div class="p-2 rounded-full bg-white dark:bg-gray-800 text-gray-400 group-hover:text-indigo-500 shadow-sm transition-colors">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4" />
                                    </svg>
                                </div>
                            </button>
                        </div>
                    </template>

                    <div x-show="filteredGroups.length === 0" class="border border-gray-200 dark:border-gray-700 rounded-lg p-8 text-center">
                        <p class="font-semibold text-gray-900 dark:text-white">No accomplishment reports found.</p>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Try a different student name, report type, date, status, company, supervisor, or filename.</p>
                    </div>
                </div>
            </div>
        </div>

        <div x-show="showModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-900/75 transition-opacity backdrop-blur-sm" @click="showModal = false" aria-hidden="true" x-transition.opacity></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full border border-gray-200 dark:border-gray-700" x-transition>
                    <div class="bg-gray-50 dark:bg-gray-900/80 px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex flex-col sm:flex-row justify-between items-center gap-4">
                        <div class="w-full space-y-4">
                            <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                                <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100 flex items-center gap-2">
                                    <span class="px-2 py-1 rounded text-sm bg-indigo-100 text-indigo-700 dark:bg-indigo-900 dark:text-indigo-300" x-text="activeGroup?.student_name || ''"></span>
                                    <span>Accomplishment Reports</span>
                                </h3>

                                <div class="relative w-full sm:w-72">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                        </svg>
                                    </div>
                                    <input x-model="modalSearch" type="text" class="block w-full pl-10 pr-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg leading-5 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm transition-shadow" placeholder="Search type, date, status, filename">
                                </div>
                            </div>

                            <div class="flex flex-wrap gap-2">
                                <button type="button" @click="modalType = 'all'" :class="modalType === 'all' ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-white text-gray-700 border-gray-300 dark:bg-gray-900 dark:text-gray-200 dark:border-gray-700'" class="rounded-full border px-3 py-1.5 text-xs font-black uppercase tracking-[0.14em] transition">All</button>
                                <button type="button" @click="modalType = 'daily'" :class="modalType === 'daily' ? 'bg-emerald-600 text-white border-emerald-600' : 'bg-white text-gray-700 border-gray-300 dark:bg-gray-900 dark:text-gray-200 dark:border-gray-700'" class="rounded-full border px-3 py-1.5 text-xs font-black uppercase tracking-[0.14em] transition">Daily</button>
                                <button type="button" @click="modalType = 'weekly'" :class="modalType === 'weekly' ? 'bg-sky-600 text-white border-sky-600' : 'bg-white text-gray-700 border-gray-300 dark:bg-gray-900 dark:text-gray-200 dark:border-gray-700'" class="rounded-full border px-3 py-1.5 text-xs font-black uppercase tracking-[0.14em] transition">Weekly</button>
                                <button type="button" @click="modalType = 'monthly'" :class="modalType === 'monthly' ? 'bg-violet-600 text-white border-violet-600' : 'bg-white text-gray-700 border-gray-300 dark:bg-gray-900 dark:text-gray-200 dark:border-gray-700'" class="rounded-full border px-3 py-1.5 text-xs font-black uppercase tracking-[0.14em] transition">Monthly</button>
                            </div>
                        </div>
                    </div>

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
                                <template x-for="log in filteredModalLogs" :key="log.id">
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-900/50 transition-colors">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 py-1 rounded-full text-xs font-bold uppercase" :class="typeClass(log.type)" x-text="log.type"></span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-300" x-text="log.date"></td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 py-1 rounded text-xs font-bold uppercase" :class="statusClass(log.status)" x-text="log.status"></span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            <template x-if="log.view_url">
                                                <div>
                                                    <a :href="log.view_url" target="_blank" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 font-semibold hover:underline">View File</a>
                                                    <span class="mx-2 text-gray-300">|</span>
                                                    <a :href="log.download_url" class="text-emerald-700 dark:text-emerald-300 hover:underline font-semibold">Download</a>
                                                </div>
                                            </template>
                                            <template x-if="!log.view_url">
                                                <a :href="log.print_url" target="_blank" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 font-semibold hover:underline">Print</a>
                                            </template>
                                        </td>
                                    </tr>
                                </template>
                                <tr x-show="filteredModalLogs.length === 0">
                                    <td colspan="4" class="px-6 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                                        No accomplishment reports found.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-900/50 px-6 py-4 border-t border-gray-200 dark:border-gray-700 flex justify-end">
                        <button @click="showModal = false" class="px-4 py-2 bg-gray-300 dark:bg-gray-600 text-gray-800 dark:text-gray-200 rounded-lg font-semibold hover:bg-gray-400 dark:hover:bg-gray-500 transition-colors">
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function supervisorAccomplishmentReports(groups) {
            return {
                groups: Array.isArray(groups) ? groups : [],
                searchQuery: '',
                showModal: false,
                activeGroup: null,
                modalSearch: '',
                modalType: 'all',
                get filteredGroups() {
                    const query = (this.searchQuery || '').trim().toLowerCase();

                    return this.groups.filter((group) => {
                        return query === '' || (group.search_blob || '').includes(query);
                    });
                },
                get filteredModalLogs() {
                    const query = (this.modalSearch || '').trim().toLowerCase();
                    const logs = Array.isArray(this.activeGroup?.logs) ? this.activeGroup.logs : [];

                    return logs.filter((log) => {
                        const matchesSearch = query === '' || (log.search_blob || '').includes(query);
                        const matchesType = this.modalType === 'all' || log.type_value === this.modalType;

                        return matchesSearch && matchesType;
                    });
                },
                openGroup(group) {
                    this.activeGroup = group;
                    this.modalSearch = '';
                    this.modalType = 'all';
                    this.showModal = true;
                },
                statusClass(status) {
                    if (status === 'Approved') {
                        return 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300';
                    }

                    if (status === 'Submitted') {
                        return 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300';
                    }

                    if (status === 'Declined') {
                        return 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300';
                    }

                    return 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-300';
                },
                typeClass(type) {
                    if (type === 'Daily') {
                        return 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300';
                    }

                    if (type === 'Weekly') {
                        return 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300';
                    }

                    return 'bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-300';
                },
            };
        }
    </script>
</x-supervisor-layout>
