<x-coordinator-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Accomplishment Reports') }}
        </h2>
    </x-slot>

    <div
        x-data="coordinatorArMonitor(@js($studentReports), @js($summary), @js($sectionSummary))"
        class="space-y-6"
    >
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900 dark:text-gray-100 space-y-6">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                    <div>
                        <h3 class="text-lg font-semibold">Accomplishment Reports Monitoring</h3>
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-300">
                            Track submitted and missing daily, weekly, and monthly accomplishment reports for all active OJT students.
                        </p>
                    </div>

                    <div class="w-full lg:w-[28rem]">
                        <label for="ar-search" class="sr-only">Search accomplishment reports</label>
                        <div class="relative">
                            <svg class="pointer-events-none absolute left-3 top-3.5 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                            <input
                                id="ar-search"
                                x-model.debounce.150ms="searchQuery"
                                type="text"
                                placeholder="Search by student, section, company, report type, or status..."
                                class="w-full rounded-xl border border-gray-300 bg-white py-3 pl-10 pr-4 text-sm text-gray-900 shadow-sm outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100"
                            >
                        </div>
                    </div>
                </div>

                <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-5">
                    <div class="rounded-2xl border border-indigo-200 bg-gradient-to-br from-indigo-50 to-white p-4 dark:border-indigo-900/40 dark:from-indigo-950/40 dark:to-gray-900">
                        <p class="text-xs font-bold uppercase tracking-[0.2em] text-indigo-600 dark:text-indigo-300">Students</p>
                        <p class="mt-3 text-3xl font-black text-gray-900 dark:text-white" x-text="filteredRows.length"></p>
                        <p class="mt-1 text-xs text-gray-600 dark:text-gray-300">Visible with current filters</p>
                    </div>
                    <div class="rounded-2xl border border-emerald-200 bg-gradient-to-br from-emerald-50 to-white p-4 dark:border-emerald-900/40 dark:from-emerald-950/40 dark:to-gray-900">
                        <p class="text-xs font-bold uppercase tracking-[0.2em] text-emerald-600 dark:text-emerald-300">Submitted</p>
                        <p class="mt-3 text-3xl font-black text-gray-900 dark:text-white" x-text="statusCounts.submitted"></p>
                        <p class="mt-1 text-xs text-gray-600 dark:text-gray-300">Complied in current view</p>
                    </div>
                    <div class="rounded-2xl border border-amber-200 bg-gradient-to-br from-amber-50 to-white p-4 dark:border-amber-900/40 dark:from-amber-950/40 dark:to-gray-900">
                        <p class="text-xs font-bold uppercase tracking-[0.2em] text-amber-600 dark:text-amber-300">Pending</p>
                        <p class="mt-3 text-3xl font-black text-gray-900 dark:text-white" x-text="statusCounts.pending"></p>
                        <p class="mt-1 text-xs text-gray-600 dark:text-gray-300">Saved but not fully complied</p>
                    </div>
                    <div class="rounded-2xl border border-rose-200 bg-gradient-to-br from-rose-50 to-white p-4 dark:border-rose-900/40 dark:from-rose-950/40 dark:to-gray-900">
                        <p class="text-xs font-bold uppercase tracking-[0.2em] text-rose-600 dark:text-rose-300">Not Submitted</p>
                        <p class="mt-3 text-3xl font-black text-gray-900 dark:text-white" x-text="statusCounts.notSubmitted"></p>
                        <p class="mt-1 text-xs text-gray-600 dark:text-gray-300">No recent report found</p>
                    </div>
                    <div class="rounded-2xl border border-fuchsia-200 bg-gradient-to-br from-fuchsia-50 to-white p-4 dark:border-fuchsia-900/40 dark:from-fuchsia-950/40 dark:to-gray-900">
                        <p class="text-xs font-bold uppercase tracking-[0.2em] text-fuchsia-600 dark:text-fuchsia-300">Incomplete</p>
                        <p class="mt-3 text-3xl font-black text-gray-900 dark:text-white" x-text="statusCounts.incomplete"></p>
                        <p class="mt-1 text-xs text-gray-600 dark:text-gray-300">Needs follow-up or resubmission</p>
                    </div>
                </div>

                <div class="flex flex-wrap items-center gap-2">
                    <template x-for="filter in typeFilters" :key="filter.value">
                        <button
                            @click="selectedType = filter.value"
                            type="button"
                            :class="selectedType === filter.value ? 'bg-indigo-600 text-white border-indigo-600 shadow-sm' : 'bg-white text-gray-700 border-gray-300 hover:border-indigo-400 hover:text-indigo-700 dark:bg-gray-900 dark:text-gray-200 dark:border-gray-700'"
                            class="rounded-full border px-4 py-2 text-sm font-semibold transition"
                        >
                            <span x-text="filter.label"></span>
                        </button>
                    </template>
                </div>

                <div class="grid gap-3 md:grid-cols-2 xl:grid-cols-4">
                    <template x-for="section in sectionCards" :key="section.section">
                        <div class="rounded-2xl border border-gray-200 bg-gray-50/80 p-4 dark:border-gray-700 dark:bg-gray-900/50">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <p class="text-sm font-bold text-gray-900 dark:text-white" x-text="section.section"></p>
                                    <p class="mt-1 text-xs text-gray-600 dark:text-gray-300">
                                        <span x-text="`${section.total_students} students`"></span>
                                    </p>
                                </div>
                                <span class="rounded-full bg-indigo-100 px-2.5 py-1 text-xs font-bold text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-300" x-text="section.submitted"></span>
                            </div>
                            <div class="mt-4 flex flex-wrap gap-2 text-xs font-semibold">
                                <span class="rounded-full bg-emerald-100 px-2.5 py-1 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300" x-text="`Submitted ${section.submitted}`"></span>
                                <span class="rounded-full bg-amber-100 px-2.5 py-1 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300" x-text="`Pending ${section.pending}`"></span>
                                <span class="rounded-full bg-rose-100 px-2.5 py-1 text-rose-700 dark:bg-rose-900/30 dark:text-rose-300" x-text="`Follow-up ${section.needs_follow_up}`"></span>
                            </div>
                        </div>
                    </template>
                </div>

                <div class="overflow-hidden rounded-2xl border border-gray-200 dark:border-gray-700">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 text-sm dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-900/50">
                                <tr class="text-left">
                                    <th class="px-4 py-3 font-bold uppercase tracking-wider text-gray-600 dark:text-gray-300">Student</th>
                                    <th class="px-4 py-3 font-bold uppercase tracking-wider text-gray-600 dark:text-gray-300">Section</th>
                                    <th class="px-4 py-3 font-bold uppercase tracking-wider text-gray-600 dark:text-gray-300">Company</th>
                                    <th class="px-4 py-3 font-bold uppercase tracking-wider text-gray-600 dark:text-gray-300">Daily</th>
                                    <th class="px-4 py-3 font-bold uppercase tracking-wider text-gray-600 dark:text-gray-300">Weekly</th>
                                    <th class="px-4 py-3 font-bold uppercase tracking-wider text-gray-600 dark:text-gray-300">Monthly</th>
                                    <th class="px-4 py-3 font-bold uppercase tracking-wider text-gray-600 dark:text-gray-300">Current Status</th>
                                    <th class="px-4 py-3 font-bold uppercase tracking-wider text-gray-600 dark:text-gray-300">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white dark:divide-gray-700 dark:bg-gray-800">
                                <template x-for="row in filteredRows" :key="row.assignment_id">
                                    <tr class="align-top hover:bg-gray-50 dark:hover:bg-gray-900/30">
                                        <td class="px-4 py-4">
                                            <div class="flex items-start gap-3">
                                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-indigo-600 text-sm font-black text-white" x-text="initials(row.student_name)"></div>
                                                <div>
                                                    <p class="font-semibold text-gray-900 dark:text-white" x-text="row.student_name"></p>
                                                    <p class="text-xs text-gray-500 dark:text-gray-400" x-text="row.student_email"></p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-4 py-4">
                                            <p class="font-medium text-gray-900 dark:text-white" x-text="row.section"></p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400" x-text="row.department"></p>
                                        </td>
                                        <td class="px-4 py-4">
                                            <p class="font-medium text-gray-900 dark:text-white" x-text="row.company"></p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400" x-text="`${row.report_count} report${row.report_count === 1 ? '' : 's'}`"></p>
                                        </td>
                                        <td class="px-4 py-4">
                                            <span :class="badgeClass(row.type_statuses.daily.label)" class="inline-flex rounded-full px-2.5 py-1 text-xs font-bold" x-text="row.type_statuses.daily.label"></span>
                                        </td>
                                        <td class="px-4 py-4">
                                            <span :class="badgeClass(row.type_statuses.weekly.label)" class="inline-flex rounded-full px-2.5 py-1 text-xs font-bold" x-text="row.type_statuses.weekly.label"></span>
                                        </td>
                                        <td class="px-4 py-4">
                                            <span :class="badgeClass(row.type_statuses.monthly.label)" class="inline-flex rounded-full px-2.5 py-1 text-xs font-bold" x-text="row.type_statuses.monthly.label"></span>
                                        </td>
                                        <td class="px-4 py-4">
                                            <div class="space-y-1">
                                                <span :class="badgeClass(currentStatus(row))" class="inline-flex rounded-full px-2.5 py-1 text-xs font-bold" x-text="currentStatus(row)"></span>
                                                <p class="text-xs text-gray-500 dark:text-gray-400" x-text="currentStatusMeta(row)"></p>
                                            </div>
                                        </td>
                                        <td class="px-4 py-4">
                                            <button
                                                @click="openStudent(row)"
                                                type="button"
                                                class="inline-flex items-center rounded-lg bg-indigo-600 px-3 py-2 text-xs font-bold text-white transition hover:bg-indigo-700"
                                            >
                                                View Reports
                                            </button>
                                        </td>
                                    </tr>
                                </template>
                                <tr x-show="filteredRows.length === 0">
                                    <td colspan="8" class="px-4 py-12 text-center">
                                        <p class="text-sm font-semibold text-gray-700 dark:text-gray-200">No accomplishment reports matched your current search.</p>
                                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Try a different student name, section, company, report type, or status.</p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div
            x-show="showModal"
            x-transition.opacity
            style="display: none;"
            class="fixed inset-0 z-50 overflow-y-auto"
            @click.self="showModal = false"
        >
            <div class="fixed inset-0 bg-black/50 backdrop-blur-sm"></div>

            <div class="relative flex min-h-screen items-center justify-center p-4">
                <div class="relative w-full max-w-5xl overflow-hidden rounded-2xl bg-white shadow-2xl dark:bg-gray-800">
                    <div class="border-b border-gray-200 bg-gradient-to-r from-indigo-50 to-white px-6 py-5 dark:border-gray-700 dark:from-indigo-950/40 dark:to-gray-900">
                        <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                            <div>
                                <h3 class="text-xl font-bold text-gray-900 dark:text-white" x-text="activeStudent?.student_name || 'Student reports'"></h3>
                                <p class="mt-1 text-sm text-gray-600 dark:text-gray-300">
                                    <span x-text="activeStudent?.section_label || ''"></span>
                                    <span class="mx-1">|</span>
                                    <span x-text="activeStudent?.company || ''"></span>
                                </p>
                            </div>
                            <div class="flex flex-wrap gap-2">
                                <template x-for="filter in typeFilters" :key="`modal-${filter.value}`">
                                    <button
                                        @click="modalType = filter.value"
                                        type="button"
                                        :class="modalType === filter.value ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-white text-gray-700 border-gray-300 dark:bg-gray-900 dark:text-gray-200 dark:border-gray-700'"
                                        class="rounded-full border px-3 py-1.5 text-xs font-bold transition"
                                    >
                                        <span x-text="filter.label"></span>
                                    </button>
                                </template>
                            </div>
                        </div>
                    </div>

                    <div class="max-h-[70vh] overflow-y-auto px-6 py-6">
                        <div class="grid gap-3 md:grid-cols-3">
                            <template x-if="activeStudent">
                                <div class="rounded-xl border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-900/50">
                                    <p class="text-xs font-bold uppercase tracking-[0.2em] text-gray-500 dark:text-gray-400">Daily</p>
                                    <span :class="badgeClass(activeStudent.type_statuses.daily.label)" class="mt-3 inline-flex rounded-full px-2.5 py-1 text-xs font-bold" x-text="activeStudent.type_statuses.daily.label"></span>
                                    <p class="mt-2 text-xs text-gray-500 dark:text-gray-400" x-text="activeStudent.type_statuses.daily.last_date ? `Latest: ${activeStudent.type_statuses.daily.last_date}` : 'No recent daily report'"></p>
                                </div>
                            </template>
                            <template x-if="activeStudent">
                                <div class="rounded-xl border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-900/50">
                                    <p class="text-xs font-bold uppercase tracking-[0.2em] text-gray-500 dark:text-gray-400">Weekly</p>
                                    <span :class="badgeClass(activeStudent.type_statuses.weekly.label)" class="mt-3 inline-flex rounded-full px-2.5 py-1 text-xs font-bold" x-text="activeStudent.type_statuses.weekly.label"></span>
                                    <p class="mt-2 text-xs text-gray-500 dark:text-gray-400" x-text="activeStudent.type_statuses.weekly.last_date ? `Latest: ${activeStudent.type_statuses.weekly.last_date}` : 'No recent weekly report'"></p>
                                </div>
                            </template>
                            <template x-if="activeStudent">
                                <div class="rounded-xl border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-900/50">
                                    <p class="text-xs font-bold uppercase tracking-[0.2em] text-gray-500 dark:text-gray-400">Monthly</p>
                                    <span :class="badgeClass(activeStudent.type_statuses.monthly.label)" class="mt-3 inline-flex rounded-full px-2.5 py-1 text-xs font-bold" x-text="activeStudent.type_statuses.monthly.label"></span>
                                    <p class="mt-2 text-xs text-gray-500 dark:text-gray-400" x-text="activeStudent.type_statuses.monthly.last_date ? `Latest: ${activeStudent.type_statuses.monthly.last_date}` : 'No recent monthly report'"></p>
                                </div>
                            </template>
                        </div>

                        <div class="mt-6 space-y-3">
                            <template x-for="report in modalReports" :key="report.id">
                                <div class="flex flex-col gap-4 rounded-2xl border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-900/40 lg:flex-row lg:items-center lg:justify-between">
                                    <div class="space-y-2">
                                        <div class="flex flex-wrap items-center gap-2">
                                            <span class="rounded-full bg-indigo-100 px-2.5 py-1 text-xs font-bold uppercase text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-300" x-text="report.type"></span>
                                            <span class="text-sm font-semibold text-gray-900 dark:text-white" x-text="report.date"></span>
                                        </div>
                                        <span :class="badgeClass(report.status)" class="inline-flex rounded-full px-2.5 py-1 text-xs font-bold" x-text="report.status"></span>
                                    </div>
                                    <div class="flex flex-wrap gap-2">
                                        <a
                                            :href="report.print_url"
                                            target="_blank"
                                            class="inline-flex items-center rounded-lg bg-indigo-600 px-3 py-2 text-xs font-bold text-white transition hover:bg-indigo-700"
                                        >
                                            Print / Download
                                        </a>
                                        <a
                                            x-show="report.attachment_url"
                                            :href="report.attachment_url"
                                            class="inline-flex items-center rounded-lg bg-emerald-600 px-3 py-2 text-xs font-bold text-white transition hover:bg-emerald-700"
                                        >
                                            Download File
                                        </a>
                                    </div>
                                </div>
                            </template>

                            <div x-show="modalReports.length === 0" class="rounded-2xl border border-dashed border-gray-300 px-6 py-12 text-center dark:border-gray-600">
                                <p class="text-sm font-semibold text-gray-700 dark:text-gray-200">No reports found for this filter.</p>
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">This student has not submitted a report in the selected category.</p>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end border-t border-gray-200 bg-gray-50 px-6 py-4 dark:border-gray-700 dark:bg-gray-900/50">
                        <button
                            @click="showModal = false"
                            type="button"
                            class="rounded-lg bg-gray-300 px-4 py-2 text-sm font-semibold text-gray-800 transition hover:bg-gray-400 dark:bg-gray-600 dark:text-gray-100 dark:hover:bg-gray-500"
                        >
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function coordinatorArMonitor(rows, summary, sections) {
            return {
                rows,
                summary,
                sections,
                searchQuery: '',
                selectedType: 'all',
                modalType: 'all',
                showModal: false,
                activeStudent: null,
                typeFilters: [
                    { value: 'all', label: 'All Reports' },
                    { value: 'daily', label: 'Daily' },
                    { value: 'weekly', label: 'Weekly' },
                    { value: 'monthly', label: 'Monthly' },
                ],
                get filteredRows() {
                    const query = this.searchQuery.trim().toLowerCase();

                    return this.rows.filter((row) => {
                        const currentStatus = this.currentStatus(row).toLowerCase();
                        const haystack = [
                            row.student_name,
                            row.student_email,
                            row.section,
                            row.department,
                            row.section_label,
                            row.company,
                            row.overall_status,
                            `daily ${row.type_statuses.daily.label}`,
                            `weekly ${row.type_statuses.weekly.label}`,
                            `monthly ${row.type_statuses.monthly.label}`,
                            this.selectedType === 'all' ? 'all reports' : `${this.selectedType} report`,
                            currentStatus,
                        ].join(' ').toLowerCase();

                        return query === '' || haystack.includes(query);
                    });
                },
                get statusCounts() {
                    return this.filteredRows.reduce((counts, row) => {
                        const status = this.currentStatus(row);

                        if (status === 'Submitted') {
                            counts.submitted += 1;
                        } else if (status === 'Pending') {
                            counts.pending += 1;
                        } else if (status === 'Incomplete') {
                            counts.incomplete += 1;
                        } else {
                            counts.notSubmitted += 1;
                        }

                        return counts;
                    }, { submitted: 0, pending: 0, incomplete: 0, notSubmitted: 0 });
                },
                get sectionCards() {
                    if (this.searchQuery.trim() === '' && this.selectedType === 'all') {
                        return this.sections;
                    }

                    const grouped = {};

                    this.filteredRows.forEach((row) => {
                        if (!grouped[row.section_label]) {
                            grouped[row.section_label] = {
                                section: row.section_label,
                                total_students: 0,
                                submitted: 0,
                                pending: 0,
                                needs_follow_up: 0,
                            };
                        }

                        const card = grouped[row.section_label];
                        const status = this.currentStatus(row);

                        card.total_students += 1;
                        if (status === 'Submitted') {
                            card.submitted += 1;
                        } else if (status === 'Pending') {
                            card.pending += 1;
                        } else {
                            card.needs_follow_up += 1;
                        }
                    });

                    return Object.values(grouped);
                },
                get modalReports() {
                    if (!this.activeStudent) {
                        return [];
                    }

                    return this.activeStudent.reports.filter((report) => {
                        return this.modalType === 'all' || report.type === this.modalType;
                    });
                },
                currentStatus(row) {
                    if (this.selectedType === 'all') {
                        return row.overall_status;
                    }

                    return row.type_statuses[this.selectedType]?.label || 'Not Submitted';
                },
                currentStatusMeta(row) {
                    if (this.selectedType === 'all') {
                        return `${row.approved_hours.toFixed(1)} approved hours`;
                    }

                    const typeStatus = row.type_statuses[this.selectedType];

                    if (typeStatus?.last_date) {
                        return `Latest ${this.selectedType}: ${typeStatus.last_date}`;
                    }

                    return `No recent ${this.selectedType} report found`;
                },
                badgeClass(status) {
                    if (status === 'Submitted') {
                        return 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300';
                    }

                    if (status === 'Pending') {
                        return 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300';
                    }

                    if (status === 'Incomplete') {
                        return 'bg-fuchsia-100 text-fuchsia-700 dark:bg-fuchsia-900/30 dark:text-fuchsia-300';
                    }

                    return 'bg-rose-100 text-rose-700 dark:bg-rose-900/30 dark:text-rose-300';
                },
                initials(name) {
                    return String(name || '')
                        .split(' ')
                        .filter(Boolean)
                        .slice(0, 2)
                        .map((part) => part.charAt(0).toUpperCase())
                        .join('') || 'ST';
                },
                openStudent(row) {
                    this.activeStudent = row;
                    this.modalType = this.selectedType === 'all' ? 'all' : this.selectedType;
                    this.showModal = true;
                },
            };
        }
    </script>
</x-coordinator-layout>
