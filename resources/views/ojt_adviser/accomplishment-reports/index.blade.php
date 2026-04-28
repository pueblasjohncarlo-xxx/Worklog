<x-ojt-adviser-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Accomplishment Reports') }}
        </h2>
    </x-slot>

    @php
        $reportPayload = $workLogs->map(function ($log) {
            $student = $log->assignment?->student;
            $company = $log->assignment?->company;
            $status = strtolower((string) ($log->status ?? 'draft'));
            $statusLabel = match ($status) {
                'approved' => 'Approved',
                'submitted' => 'Pending',
                'rejected' => 'Rejected',
                default => 'Draft',
            };

            $typeValue = strtolower((string) $log->type);

            return [
                'id' => $log->id,
                'student_name' => $student?->name ?? 'N/A',
                'section' => $student?->section ?? 'N/A',
                'company' => $company?->name ?? 'N/A',
                'type' => strtoupper((string) $log->type),
                'type_value' => $typeValue,
                'date' => $log->work_date?->format('M d, Y') ?? 'N/A',
                'status' => $statusLabel,
                'filename' => $log->attachment_path ? basename($log->attachment_path) : 'No file',
                'view_url' => $log->attachment_path ? route('ojt_adviser.worklogs.attachment', $log->id) . '?inline=1' : null,
                'download_url' => $log->attachment_path ? route('ojt_adviser.worklogs.attachment', $log->id) : null,
                'search_blob' => strtolower(implode(' ', array_filter([
                    $student?->name,
                    $student?->section,
                    $company?->name,
                    $log->type,
                    $log->work_date?->format('M d, Y'),
                    $statusLabel,
                    $log->attachment_path ? basename($log->attachment_path) : 'no file',
                    auth()->user()?->name,
                ]))),
            ];
        })->values();
    @endphp

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div
                x-data="adviserAccomplishmentReports(@js($reportPayload))"
                class="bg-white overflow-hidden shadow-sm sm:rounded-lg"
            >
                <div class="p-6 text-gray-900 space-y-6">
                    <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Accomplishment Reports</h3>
                            <p class="mt-1 text-sm text-gray-600">
                                Search instantly by student, section, company, type, date, status, or filename.
                            </p>
                        </div>

                        <div class="w-full lg:w-[28rem]">
                            <label for="adviser-ar-search" class="sr-only">Search accomplishment reports</label>
                            <div class="relative">
                                <svg class="pointer-events-none absolute left-3 top-3.5 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                                <input
                                    id="adviser-ar-search"
                                    x-model.debounce.100ms="searchQuery"
                                    type="search"
                                    placeholder="Search by student, company, type, status, or filename"
                                    class="w-full rounded-xl border border-gray-300 bg-white py-3 pl-10 pr-4 text-sm text-gray-900 shadow-sm outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500"
                                >
                            </div>
                        </div>
                    </div>

                    <div class="grid gap-3 md:grid-cols-4">
                        <template x-for="filter in typeFilters" :key="filter.value">
                            <button
                                type="button"
                                @click="selectedType = filter.value"
                                :class="selectedType === filter.value ? 'bg-indigo-600 text-white border-indigo-600 shadow-sm' : 'bg-white text-gray-700 border-gray-300 hover:border-indigo-400 hover:text-indigo-700'"
                                class="rounded-2xl border px-4 py-3 text-left transition"
                            >
                                <div class="text-xs font-black uppercase tracking-[0.16em]" x-text="filter.label"></div>
                                <div class="mt-2 text-2xl font-black" x-text="typeCount(filter.value)"></div>
                                <div class="mt-1 text-xs font-semibold" x-text="filterDescription(filter.value)"></div>
                            </button>
                        </template>
                    </div>

                    <div class="flex items-center justify-between text-xs font-semibold text-gray-600">
                        <span>Showing <span class="font-black text-gray-900" x-text="filteredReports.length"></span> of <span class="font-black text-gray-900" x-text="reports.length"></span> reports in <span class="font-black text-indigo-700" x-text="activeFilterLabel"></span></span>
                        <button
                            x-show="searchQuery"
                            x-cloak
                            @click="searchQuery = ''"
                            type="button"
                            class="rounded-full border border-gray-300 px-3 py-1 text-gray-700 transition hover:border-indigo-400 hover:text-indigo-700"
                        >
                            Clear Search
                        </button>
                    </div>

                    <div class="overflow-x-auto rounded-2xl border border-gray-200">
                        <table class="min-w-full text-sm">
                            <thead>
                                <tr class="border-b bg-gray-50">
                                    <th class="text-left px-3 py-2 font-bold uppercase tracking-wider text-gray-700">Student</th>
                                    <th class="text-left px-3 py-2 font-bold uppercase tracking-wider text-gray-700">Company</th>
                                    <th class="text-left px-3 py-2 font-bold uppercase tracking-wider text-gray-700">Type</th>
                                    <th class="text-left px-3 py-2 font-bold uppercase tracking-wider text-gray-700">Date</th>
                                    <th class="text-left px-3 py-2 font-bold uppercase tracking-wider text-gray-700">Status</th>
                                    <th class="text-left px-3 py-2 font-bold uppercase tracking-wider text-gray-700">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-for="report in filteredReports" :key="report.id">
                                    <tr class="border-b align-top hover:bg-gray-50/80 transition">
                                        <td class="px-3 py-3">
                                            <div class="font-semibold text-gray-900" x-text="report.student_name"></div>
                                            <div class="text-xs text-gray-500" x-text="report.section"></div>
                                        </td>
                                        <td class="px-3 py-3 text-gray-700" x-text="report.company"></td>
                                        <td class="px-3 py-3">
                                            <span class="inline-flex rounded-full bg-indigo-100 px-2.5 py-1 text-xs font-bold uppercase text-indigo-700" x-text="report.type"></span>
                                        </td>
                                        <td class="px-3 py-3 text-gray-700" x-text="report.date"></td>
                                        <td class="px-3 py-3">
                                            <span :class="statusClass(report.status)" class="inline-flex rounded-full px-2.5 py-1 text-xs font-bold uppercase" x-text="report.status"></span>
                                        </td>
                                        <td class="px-3 py-3">
                                            <template x-if="report.view_url">
                                                <div class="flex flex-wrap items-center gap-2">
                                                    <a :href="report.view_url" target="_blank" class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-1.5 text-xs font-bold text-white transition hover:bg-indigo-700">View File</a>
                                                    <a :href="report.download_url" class="inline-flex items-center rounded-md bg-emerald-600 px-3 py-1.5 text-xs font-bold text-white transition hover:bg-emerald-700">Download</a>
                                                </div>
                                            </template>
                                            <template x-if="!report.view_url">
                                                <span class="inline-flex rounded-full bg-slate-100 px-2.5 py-1 text-xs font-bold text-slate-700">No file</span>
                                            </template>
                                        </td>
                                    </tr>
                                </template>
                                <tr x-show="filteredReports.length === 0">
                                    <td colspan="6" class="px-3 py-8 text-center">
                                        <p class="font-semibold text-gray-900">No accomplishment reports found.</p>
                                        <p class="mt-1 text-sm text-gray-500">Try a different student name, section, company, type, status, or filename.</p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function adviserAccomplishmentReports(reports) {
            return {
                reports: Array.isArray(reports) ? reports : [],
                searchQuery: '',
                selectedType: 'all',
                typeFilters: [
                    { value: 'all', label: 'All' },
                    { value: 'daily', label: 'Daily' },
                    { value: 'weekly', label: 'Weekly' },
                    { value: 'monthly', label: 'Monthly' },
                ],
                get filteredReports() {
                    const query = (this.searchQuery || '').trim().toLowerCase();

                    return this.reports.filter((report) => {
                        const matchesSearch = query === '' || (report.search_blob || '').includes(query);
                        const matchesType = this.selectedType === 'all' || report.type_value === this.selectedType;

                        return matchesSearch && matchesType;
                    });
                },
                typeCount(type) {
                    if (type === 'all') {
                        return this.reports.length;
                    }

                    return this.reports.filter((report) => report.type_value === type).length;
                },
                filterDescription(type) {
                    if (type === 'all') {
                        return 'Every submitted report';
                    }

                    return `Only ${type} accomplishment reports`;
                },
                get activeFilterLabel() {
                    const active = this.typeFilters.find((filter) => filter.value === this.selectedType);

                    return active ? active.label : 'All';
                },
                statusClass(status) {
                    if (status === 'Approved') {
                        return 'bg-emerald-100 text-emerald-700';
                    }

                    if (status === 'Pending') {
                        return 'bg-amber-100 text-amber-800';
                    }

                    if (status === 'Rejected') {
                        return 'bg-rose-100 text-rose-700';
                    }

                    return 'bg-slate-100 text-slate-700';
                },
            };
        }
    </script>
</x-ojt-adviser-layout>
