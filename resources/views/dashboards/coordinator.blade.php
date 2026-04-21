<x-coordinator-layout>
    <x-slot name="header">
        Coordinator dashboard
    </x-slot>

    <div class="space-y-6">
        <div class="grid gap-4 grid-cols-1 sm:grid-cols-2 lg:grid-cols-4">
            <a href="{{ route('coordinator.student-overview') }}" class="block bg-indigo-600/20 backdrop-blur-md border border-indigo-500/30 rounded-xl shadow-[0_8px_32px_0_rgba(79,70,229,0.1)] overflow-hidden cursor-pointer hover:scale-105 transition-all duration-300 hover:shadow-indigo-500/20 hover:bg-indigo-600/30 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-400 group">
                <div class="p-4 text-gray-100">
                    <div class="text-sm text-indigo-200 font-bold uppercase tracking-wider group-hover:text-indigo-100 transition-colors">
                        OJT Students
                    </div>
                    <div class="mt-1 text-2xl font-black text-white group-hover:text-indigo-50 transition-colors">
                        {{ $totalStudents }}
                    </div>
                </div>
            </a>
            <a href="{{ route('coordinator.adviser-overview') }}" class="block bg-emerald-600/20 backdrop-blur-md border border-emerald-500/30 rounded-xl shadow-[0_8px_32px_0_rgba(5,150,105,0.1)] overflow-hidden cursor-pointer hover:scale-105 transition-all duration-300 hover:shadow-emerald-500/20 hover:bg-emerald-600/30 focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-400 group">
                <div class="p-4 text-gray-100">
                    <div class="text-sm text-emerald-200 font-bold uppercase tracking-wider group-hover:text-emerald-100 transition-colors">
                        OJT Advisory
                    </div>
                    <div class="mt-1 text-2xl font-black text-white group-hover:text-emerald-50 transition-colors">
                        {{ $advisersCount }}
                    </div>
                </div>
            </a>
            <a href="{{ route('coordinator.companies.index') }}" class="block bg-amber-600/20 backdrop-blur-md border border-amber-500/30 rounded-xl shadow-[0_8px_32px_0_rgba(217,119,6,0.1)] overflow-hidden cursor-pointer hover:scale-105 transition-all duration-300 hover:shadow-amber-500/20 hover:bg-amber-600/30 focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-400 group">
                <div class="p-4 text-gray-100">
                    <div class="text-sm text-amber-200 font-bold uppercase tracking-wider group-hover:text-amber-100 transition-colors">
                        Industry
                    </div>
                    <div class="mt-1 text-2xl font-black text-white group-hover:text-amber-50 transition-colors">
                        {{ $companiesCount }}
                    </div>
                </div>
            </a>
            <a href="{{ route('coordinator.deployment.index') }}" class="block bg-rose-600/20 backdrop-blur-md border border-rose-500/30 rounded-xl shadow-[0_8px_32px_0_rgba(225,29,72,0.1)] overflow-hidden cursor-pointer hover:scale-105 transition-all duration-300 hover:shadow-rose-500/20 hover:bg-rose-600/30 focus:outline-none focus-visible:ring-2 focus-visible:ring-rose-400 group">
                <div class="p-4 text-gray-100">
                    <div class="text-sm text-rose-200 font-bold uppercase tracking-wider group-hover:text-rose-100 transition-colors">
                        Status
                    </div>
                    <div class="mt-1 text-2xl font-black text-white group-hover:text-rose-50 transition-colors">
                        {{ $activeAssignmentsCount }}
                    </div>
                </div>
            </a>
        </div>

        <!-- Charts Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Section Progress Bar Chart -->
            <div class="bg-black/40 backdrop-blur-md border border-indigo-500/20 rounded-2xl p-6 shadow-xl">
                <h3 class="text-lg font-bold text-white mb-4 flex items-center gap-2">
                    <svg class="h-5 w-5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                    OJT Students
                </h3>
                <div class="relative h-[220px]">
                    <canvas id="studentProgressBarChart"
                        data-labels="{{ json_encode($sectionProgress->pluck('section')) }}"
                        data-values="{{ json_encode($sectionProgress->pluck('avg_progress')) }}">
                    </canvas>
                </div>
            </div>

            <!-- Attendance Trend Line Chart -->
            <div class="bg-black/40 backdrop-blur-md border border-indigo-500/20 rounded-2xl p-6 shadow-xl">
                <h3 class="text-lg font-bold text-white mb-4 flex items-center gap-2">
                    <svg class="h-5 w-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                    </svg>
                    Daily Attendance Trends
                </h3>
                <div class="relative h-[220px]">
                    <canvas id="attendanceTrendChart"
                        data-labels="{{ json_encode($attendanceTrend->pluck('day')) }}"
                        data-total="{{ json_encode($attendanceTrend->pluck('total')) }}"
                        data-late="{{ json_encode($attendanceTrend->pluck('late')) }}">
                    </canvas>
                </div>
            </div>
        </div>

        <div class="bg-black/40 backdrop-blur-md border border-indigo-500/20 rounded-2xl p-6 shadow-xl" x-data='{
            advisers: @json($ojtAdvisers),
            expandedId: null,
            toggle(id) {
                this.expandedId = this.expandedId === id ? null : id;
            }
        }'>
            <h3 class="text-lg font-bold text-white mb-6">
                OJT Advisory
            </h3>

            <template x-if="advisers.length === 0">
                <div class="text-sm text-gray-300">
                    No OJT advisers found.
                </div>
            </template>

            <template x-if="advisers.length > 0">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <template x-for="adviser in advisers" :key="adviser.id">
                        <div class="flex flex-col gap-4">
                            <!-- Adviser Card -->
                            <div @click="toggle(adviser.id)" 
                                 class="border border-white/10 rounded-xl p-4 bg-white/5 hover:bg-white/10 transition-all cursor-pointer group relative overflow-hidden"
                                 :class="expandedId === adviser.id ? 'ring-2 ring-indigo-500/60 bg-white/10' : ''">
                                <div class="flex items-center gap-4">
                                    <div class="h-14 w-14 rounded-full overflow-hidden border border-white/10 bg-black/20 flex items-center justify-center shrink-0">
                                        <template x-if="adviser.photo_url">
                                            <img :src="adviser.photo_url" :data-avatar-user-id="adviser.id" alt="" class="h-full w-full object-cover">
                                        </template>
                                        <template x-if="!adviser.photo_url">
                                            <div class="text-white font-black text-xl" x-text="adviser.name.charAt(0).toUpperCase()"></div>
                                        </template>
                                    </div>
                                    <div class="min-w-0">
                                        <div class="text-base font-black text-white group-hover:text-indigo-200 transition-colors truncate" x-text="adviser.name"></div>
                                        <div class="text-xs text-gray-400 truncate" x-text="adviser.email"></div>
                                        <div class="mt-1 flex items-center gap-2">
                                            <span class="text-[10px] font-bold text-indigo-300 uppercase tracking-widest bg-indigo-500/10 px-2 py-0.5 rounded-full border border-indigo-500/20">
                                                <span x-text="adviser.active_assignments_count"></span> Students
                                            </span>
                                        </div>
                                    </div>
                                    <div class="ml-auto text-gray-500 group-hover:text-white transition-colors">
                                        <svg class="h-5 w-5 transform transition-transform duration-200" :class="expandedId === adviser.id ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </div>
                                </div>
                            </div>

                            <!-- Expanded Details -->
                            <div x-show="expandedId === adviser.id" 
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="opacity-0 -translate-y-2"
                                 x-transition:enter-end="opacity-100 translate-y-0"
                                 x-cloak
                                 class="space-y-4">
                                
                                <!-- Student Counts -->
                                <div class="grid grid-cols-2 gap-2 text-center">
                                    <div class="border border-white/10 rounded-lg px-2 py-1.5 bg-white/5">
                                        <div class="text-xs font-bold uppercase tracking-widest text-gray-400">Comptech</div>
                                        <div class="text-lg font-black text-white" x-text="adviser.comptech_students"></div>
                                    </div>
                                    <div class="border border-white/10 rounded-lg px-2 py-1.5 bg-white/5">
                                        <div class="text-xs font-bold uppercase tracking-widest text-gray-400">Electronics</div>
                                        <div class="text-lg font-black text-white" x-text="adviser.electronics_students"></div>
                                    </div>
                                </div>

                                <!-- Contact Info -->
                                <div class="grid grid-cols-1 gap-2 text-sm text-gray-300">
                                    <div class="border border-white/10 rounded-lg px-3 py-2 bg-white/5">
                                        <div class="text-[10px] font-bold uppercase tracking-widest text-gray-400">Department</div>
                                        <div class="truncate text-white font-medium" x-text="adviser.department || '-'"></div>
                                    </div>
                                    <div class="border border-white/10 rounded-lg px-3 py-2 bg-white/5">
                                        <div class="text-[10px] font-bold uppercase tracking-widest text-gray-400">Contact Number</div>
                                        <div class="truncate text-white font-medium" x-text="adviser.phone || '-'"></div>
                                    </div>
                                    <div class="border border-white/10 rounded-lg px-3 py-2 bg-white/5">
                                        <div class="text-[10px] font-bold uppercase tracking-widest text-gray-400">Address</div>
                                        <div class="truncate text-white font-medium" x-text="adviser.address || '-'"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </template>
        </div>

        <div x-data='{
            departments: @json($departmentsData),
            selectedSections: {},
            init() {
                this.departments.forEach(d => {
                    const options = d.section_options || [];
                    this.selectedSections[d.name] = options.length ? options[0] : "";
                });
            },
            sectionsFor(dept) {
                return dept.section_options || [];
            },
            studentsFor(dept) {
                const section = this.selectedSections[dept.name];
                return (dept.students_by_section && dept.students_by_section[section]) ? dept.students_by_section[section] : [];
            },
            setSection(deptName, section) {
                this.selectedSections[deptName] = section;
            }
        }' x-init="init()">
            <div class="bg-black/40 backdrop-blur-md border border-indigo-500/20 rounded-2xl p-6 shadow-xl">
                <h3 class="text-lg font-bold text-white mb-4">
                    OJT Students
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <template x-for="dept in departments" :key="dept.name">
                        <div class="border border-white/10 rounded-xl p-4 bg-white/5">
                            <div class="flex items-center justify-between gap-4">
                                <div class="text-sm font-bold text-white" x-text="dept.name"></div>
                                <select class="rounded-lg border-gray-300 bg-white text-gray-900 dark:bg-gray-900/60 dark:text-gray-100 dark:border-gray-700 text-sm"
                                    x-model="selectedSections[dept.name]">
                                    <template x-for="section in sectionsFor(dept)" :key="section">
                                        <option :value="section" x-text="section"></option>
                                    </template>
                                </select>
                            </div>

                            <div class="mt-4 space-y-2">
                                <template x-if="studentsFor(dept).length === 0">
                                    <div class="text-sm text-gray-300">
                                        No OJT students found.
                                    </div>
                                </template>
                                <template x-for="student in studentsFor(dept)" :key="student.id">
                                    <div class="flex items-center justify-between gap-3 border rounded-lg px-3 py-2 bg-black/20 transition-all" :class="student.has_assignment ? 'border-indigo-500/50 hover:border-indigo-400 hover:bg-indigo-900/20' : 'border-white/10 hover:bg-white/5'">
                                        <div class="min-w-0 flex-1">
                                            <div class="flex items-center gap-2">
                                                <div class="text-sm font-bold text-white truncate" x-text="student.name"></div>
                                                <template x-if="student.has_assignment">
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-emerald-500/30 text-emerald-300 border border-emerald-500/50">
                                                        ✓ Active
                                                    </span>
                                                </template>
                                            </div>
                                            <div class="text-xs text-gray-300 truncate" x-text="student.email"></div>
                                        </div>
                                        <div class="text-xs font-bold text-indigo-200 uppercase tracking-wider whitespace-nowrap" x-text="student.section"></div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

        </div>

        <div class="space-y-6">
            <div class="bg-black/40 backdrop-blur-md border border-indigo-500/20 rounded-2xl p-6 shadow-xl" x-data='{
                arMetrics: @json($arMetrics),
                selectedAr: null,
                init() {
                    this.selectedAr = this.arMetrics.length ? this.arMetrics[0] : null;
                    if (this.selectedAr) {
                        window.dispatchEvent(new CustomEvent("coordinator-ar-metric", { detail: this.selectedAr }));
                    }
                },
                selectAr(metric) {
                    this.selectedAr = metric;
                    window.dispatchEvent(new CustomEvent("coordinator-ar-metric", { detail: metric }));
                }
            }' x-init="init()">
                <h3 class="text-lg font-bold text-white mb-4">
                    Accomplishment Report
                </h3>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
                    <template x-for="metric in arMetrics" :key="metric.key">
                        <button type="button"
                            class="text-left border border-white/10 rounded-lg px-3 py-2 bg-white/5 hover:bg-white/10 transition-colors"
                            :class="selectedAr && selectedAr.key === metric.key ? 'ring-2 ring-indigo-500/60' : ''"
                            @click="selectAr(metric)">
                            <div class="text-xs font-bold text-gray-200" x-text="metric.label"></div>
                            <div class="text-lg font-black text-white">
                                <span x-text="metric.percent"></span>%
                            </div>
                        </button>
                    </template>
                </div>

                <div class="mt-4 border border-white/10 rounded-xl p-4 bg-black/20">
                    <div class="text-sm font-bold text-white" x-text="selectedAr ? selectedAr.label : ''"></div>
                    <div class="mt-3 h-[220px]">
                        <canvas id="arMetricsChart" data-initial='@json($arMetrics->first())'></canvas>
                    </div>
                </div>
            </div>

            <div class="bg-black/40 backdrop-blur-md border border-indigo-500/20 rounded-2xl p-6 shadow-xl">
                <h3 class="text-lg font-bold text-white mb-4">
                    OJT Attendance (OJT Meeting)
                </h3>
                <div class="border border-white/10 rounded-xl p-4 bg-black/20">
                    <div class="text-2xl font-black text-white">
                        {{ $trackingBoxes['attendance_meeting']['count'] }}
                        <span class="text-sm text-gray-300 font-bold">/ {{ $trackingBoxes['attendance_meeting']['total'] }}</span>
                    </div>
                    <div class="text-xs text-gray-300 font-semibold mt-1">
                        {{ $trackingBoxes['attendance_meeting']['period'] }}
                    </div>
                </div>
                <div class="mt-4 border border-white/10 rounded-xl p-4 bg-black/20">
                    <div class="text-sm font-bold text-white">
                        Attendance Trend
                    </div>
                    <div class="mt-3 h-[220px]">
                        <canvas id="attendanceMiniChart"
                            data-labels="{{ json_encode($attendanceTrend->pluck('day')) }}"
                            data-values="{{ json_encode($attendanceTrend->pluck('total')) }}"></canvas>
                    </div>
                </div>
            </div>

            <div class="bg-black/40 backdrop-blur-md border border-indigo-500/20 rounded-2xl p-6 shadow-xl">
                <h3 class="text-lg font-bold text-white mb-4">
                    Mapping
                </h3>
                <div class="border border-white/10 rounded-xl p-4 bg-black/20">
                    <div class="text-2xl font-black text-white">
                        {{ $trackingBoxes['mapping']['count'] }}
                    </div>
                    <div class="text-xs text-gray-300 font-semibold mt-1">
                        {{ $trackingBoxes['mapping']['period'] }}
                    </div>
                </div>
            </div>

            <div class="bg-black/40 backdrop-blur-md border border-indigo-500/20 rounded-2xl p-6 shadow-xl">
                <h3 class="text-lg font-bold text-white mb-4">
                    Journals
                </h3>
                <div class="border border-white/10 rounded-xl p-4 bg-black/20">
                    <div class="text-2xl font-black text-white">
                        {{ $trackingBoxes['journals']['count'] }}
                        <span class="text-sm text-gray-300 font-bold">/ {{ $trackingBoxes['journals']['total'] }}</span>
                    </div>
                    <div class="text-xs text-gray-300 font-semibold mt-1">
                        {{ $trackingBoxes['journals']['period'] }}
                    </div>
                </div>
                <div class="mt-4 border border-white/10 rounded-xl p-4 bg-black/20">
                    <div class="text-sm font-bold text-white">
                        Journals Trend
                    </div>
                    <div class="mt-3 h-[220px]">
                        <canvas id="journalsMiniChart"
                            data-labels="{{ json_encode($journalsTrend->pluck('day')) }}"
                            data-values="{{ json_encode($journalsTrend->pluck('total')) }}"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid gap-6 grid-cols-1 lg:grid-cols-3" x-data="{ expandedSections: { '4A': true }, searchQuery: '' }">
            <div class="lg:col-span-2 glass-panel overflow-hidden">
                <div class="p-6 text-gray-100 space-y-3">
                    <div class="flex items-center justify-between gap-4 mb-4">
                        <h3 class="font-bold text-lg text-white">
                            Student Progress Overview
                        </h3>
                        <div class="relative flex-1 max-w-xs">
                            <input 
                                type="text" 
                                x-model="searchQuery" 
                                placeholder="Search students, section, supervisor..." 
                                class="w-full pl-4 pr-10 py-2 rounded-lg bg-black/40 border border-indigo-500/30 text-white placeholder-gray-400 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all"
                            >
                            <svg class="absolute right-3 top-2.5 w-5 h-5 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                    </div>
                    @forelse ($studentProgress as $section => $progressList)
                        @php
                            $sectionName = $section === 'blank' ? 'NO SECTION' : $section . ' (COT)';
                        @endphp
                        <div class="border border-indigo-500/30 rounded-lg overflow-hidden bg-black/20">
                            <!-- Section Header -->
                            <button
                                @click="expandedSections['{{ $section }}'] = !expandedSections['{{ $section }}']"
                                class="w-full px-4 py-3 flex items-center justify-between hover:bg-indigo-600/20 transition-colors group">
                                <div class="flex items-center gap-3">
                                    <svg class="w-5 h-5 text-indigo-400 transition-transform" :class="expandedSections['{{ $section }}'] ? 'rotate-90' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                    </svg>
                                    <span class="text-sm font-bold text-white uppercase tracking-wider">{{ $sectionName }}</span>
                                </div>
                                <span class="text-xs font-semibold text-indigo-200 bg-indigo-900/40 px-3 py-1 rounded-full">{{ $progressList->count() }} Student{{ $progressList->count() !== 1 ? 's' : '' }}</span>
                            </button>

                            <!-- Section Content -->
                            <div x-show="expandedSections['{{ $section }}']" x-transition class="border-t border-indigo-500/20">
                                <div class="overflow-x-auto">
                                    <table class="min-w-full text-left text-sm">
                                        <thead>
                                            <tr class="bg-black/30 border-b border-indigo-500/20">
                                                <th class="px-4 py-2.5 text-indigo-200 font-semibold uppercase tracking-wider text-xs">Student</th>
                                                <th class="px-4 py-2.5 text-indigo-200 font-semibold uppercase tracking-wider text-xs">Industry</th>
                                                <th class="px-4 py-2.5 text-indigo-200 font-semibold uppercase tracking-wider text-xs">Supervisor</th>
                                                <th class="px-4 py-2.5 w-1/3 text-indigo-200 font-semibold uppercase tracking-wider text-xs">Progress</th>
                                                <th class="px-4 py-2.5 text-right text-indigo-200 font-semibold uppercase tracking-wider text-xs">Hours</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        @foreach ($progressList as $progress)
                                            @php
                                                $studentLower = strtolower($progress['student_name']);
                                                $industryLower = strtolower($progress['industry_name']);
                                                $supervisorLower = strtolower($progress['supervisor_name'] ?? '');
                                            @endphp
                                            <tr class="border-b border-indigo-500/10 hover:bg-indigo-900/20 transition-colors"
                                                x-show="!searchQuery || '{{ $studentLower }}'.includes(searchQuery.toLowerCase()) || '{{ $industryLower }}'.includes(searchQuery.toLowerCase()) || '{{ $supervisorLower }}'.includes(searchQuery.toLowerCase())">
                                                <td class="px-4 py-3 font-bold text-white">
                                                    {{ $progress['student_name'] }}
                                                </td>
                                                <td class="px-4 py-3 text-gray-100">{{ $progress['industry_name'] }}</td>
                                                <td class="px-4 py-3 text-gray-100">{{ $progress['supervisor_name'] ?? '-' }}</td>
                                                <td class="px-4 py-3">
                                                    <div class="flex items-center gap-2">
                                                        <div class="w-full bg-white/10 rounded-full h-2.5">
                                                            <div class="bg-indigo-500 h-2.5 rounded-full shadow-[0_0_10px_rgba(99,102,241,0.5)]" style="width: {{ $progress['progress'] }}%"></div>
                                                        </div>
                                                        <span class="text-xs text-white font-bold whitespace-nowrap">{{ $progress['progress'] }}%</span>
                                                    </div>
                                                </td>
                                                <td class="px-4 py-3 text-right">
                                                    <span class="font-bold text-white">{{ $progress['hours_completed'] }}</span>
                                                    <span class="text-indigo-200 text-xs">/ {{ $progress['required_hours'] }}</span>
                                                </td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8 text-gray-100 font-semibold">
                            No active assignments found.
                        </div>
                    @endforelse
                </div>
            </div>
                <div class="p-6 text-gray-100 space-y-4">
                    <h3 class="font-bold text-lg text-white">
                        Recent Activity
                    </h3>
                    @if(($recentActivities ?? collect())->isNotEmpty())
                        <ul class="space-y-3 text-sm">
                            @foreach($recentActivities as $activity)
                                <li class="border border-white/10 rounded-lg p-3 bg-white/5">
                                    <div class="font-bold text-white">{{ $activity['action'] ?? 'Activity' }}</div>
                                    <div class="text-xs text-gray-300">{{ $activity['time'] ?? '' }}</div>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <div class="text-sm text-gray-400">
                            No recent activity.
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="glass-panel overflow-hidden">
            <div class="p-6 text-gray-100 space-y-4" x-data="{ 
                openDropdown: null, 
                searchStudents: '', 
                searchSupervisors: '', 
                searchCompanies: '',
                selectedItem: null,
                selectedType: null
            }">
                <h3 class="font-bold text-lg text-white mb-4">
                    Monitoring Alerts
                </h3>

                <!-- Students Dropdown -->
                <div class="space-y-2">
                    <button @click="openDropdown = openDropdown === 'students' ? null : 'students'" 
                        class="w-full flex items-center justify-between p-4 bg-rose-900/20 border border-rose-500/30 rounded-lg hover:bg-rose-900/30 transition-colors cursor-pointer">
                        <div class="flex items-center gap-2">
                            <h4 class="text-sm font-bold text-rose-200 uppercase tracking-wider">Student Alerts by Section</h4>
                            <span class="text-xs bg-rose-500/30 px-2 py-1 rounded text-rose-200 font-bold">{{ count($studentsAlerts ?? []) }}</span>
                        </div>
                        <svg class="w-5 h-5 text-rose-200 transition-transform" :class="openDropdown === 'students' ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
                        </svg>
                    </button>
                    
                    <div x-show="openDropdown === 'students'" x-transition class="bg-rose-900/10 border border-rose-500/20 rounded-lg p-4 space-y-3">
                        <input type="text" x-model="searchStudents" placeholder="Search student alerts..." 
                            class="w-full px-3 py-2 rounded-lg bg-rose-900/20 border border-rose-500/30 text-white placeholder-rose-300/50 focus:outline-none focus:border-rose-500">
                        
                        @if(!empty($studentsAlerts))
                            @php $studentsBySection = collect($studentsAlerts)->groupBy('section'); @endphp
                            @foreach($studentsBySection as $section => $students)
                                <div class="border border-rose-500/30 rounded-lg bg-rose-900/10 overflow-hidden">
                                    <div class="px-4 py-2 bg-rose-900/20">
                                        <h5 class="text-xs font-bold text-rose-200 uppercase">{{ $section === 'blank' ? 'NO SECTION' : $section . ' (COT)' }}</h5>
                                    </div>
                                    <ul class="space-y-2 p-3">
                                        @foreach($students as $alert)
                                            @php
                                                $studentLower = strtolower($alert['student']);
                                            @endphp
                                            <li class="text-sm cursor-pointer hover:bg-rose-900/20 p-2 rounded transition-colors" 
                                                x-show="'{{ $studentLower }}'.includes(searchStudents.toLowerCase()) || !searchStudents"
                                                @click="selectedItem = { student: '{{ $alert['student'] }}', company: '{{ $alert['company'] }}', supervisor: '{{ $alert['supervisor'] ?? 'Not Assigned' }}', reasons: '{{ implode(', ', $alert['reasons']) }}' }; selectedType = 'student'; $refs.alertPanel.scrollIntoView({behavior: 'smooth'})">
                                                <div class="font-bold text-white">{{ $alert['student'] }}</div>
                                                <div class="text-gray-300 text-xs">{{ $alert['company'] }}</div>
                                                <div class="text-[10px] text-rose-200 font-bold uppercase mt-1">{{ implode(' • ', $alert['reasons']) }}</div>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endforeach
                        @else
                            <p class="text-gray-400 text-sm">No student alerts.</p>
                        @endif
                    </div>
                </div>

                <!-- Supervisors Dropdown -->
                <div class="space-y-2">
                    <button @click="openDropdown = openDropdown === 'supervisors' ? null : 'supervisors'" 
                        class="w-full flex items-center justify-between p-4 bg-amber-900/20 border border-amber-500/30 rounded-lg hover:bg-amber-900/30 transition-colors cursor-pointer">
                        <div class="flex items-center gap-2">
                            <h4 class="text-sm font-bold text-amber-200 uppercase tracking-wider">Supervisors</h4>
                            <span class="text-xs bg-amber-500/30 px-2 py-1 rounded text-amber-200 font-bold">{{ count($supervisorsAlerts ?? []) }}</span>
                        </div>
                        <svg class="w-5 h-5 text-amber-200 transition-transform" :class="openDropdown === 'supervisors' ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
                        </svg>
                    </button>
                    
                    <div x-show="openDropdown === 'supervisors'" x-transition class="bg-amber-900/10 border border-amber-500/20 rounded-lg p-4 space-y-3">
                        <input type="text" x-model="searchSupervisors" placeholder="Search supervisors..." 
                            class="w-full px-3 py-2 rounded-lg bg-amber-900/20 border border-amber-500/30 text-white placeholder-amber-300/50 focus:outline-none focus:border-amber-500">
                        
                        @if(!empty($supervisorsAlerts))
                            <ul class="space-y-2 text-sm">
                                @foreach($supervisorsAlerts as $alert)
                                    @php
                                        $supervisorLower = strtolower($alert['supervisor']);
                                    @endphp
                                    <li class="bg-amber-900/20 border border-amber-500/30 rounded-lg p-3 cursor-pointer hover:bg-amber-900/30 transition-colors"
                                        x-show="'{{ $supervisorLower }}'.includes(searchSupervisors.toLowerCase()) || !searchSupervisors"
                                        @click="selectedItem = { supervisor: '{{ $alert['supervisor'] }}', pending: {{ $alert['pending'] }} }; selectedType = 'supervisor'; $refs.alertPanel.scrollIntoView({behavior: 'smooth'})">
                                        <div class="font-bold text-white">{{ $alert['supervisor'] }}</div>
                                        <div class="text-[11px] text-amber-200 font-bold uppercase">📋 {{ $alert['pending'] }} Pending Approval{{ $alert['pending'] != 1 ? 's' : '' }}</div>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <p class="text-gray-400 text-sm">No supervisor alerts.</p>
                        @endif
                    </div>
                </div>

                <!-- Companies Dropdown -->
                <div class="space-y-2">
                    <button @click="openDropdown = openDropdown === 'companies' ? null : 'companies'" 
                        class="w-full flex items-center justify-between p-4 bg-indigo-900/20 border border-indigo-500/30 rounded-lg hover:bg-indigo-900/30 transition-colors cursor-pointer">
                        <div class="flex items-center gap-2">
                            <h4 class="text-sm font-bold text-indigo-200 uppercase tracking-wider">Companies</h4>
                            <span class="text-xs bg-indigo-500/30 px-2 py-1 rounded text-indigo-200 font-bold">{{ count($companiesAlerts ?? []) }}</span>
                        </div>
                        <svg class="w-5 h-5 text-indigo-200 transition-transform" :class="openDropdown === 'companies' ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
                        </svg>
                    </button>
                    
                    <div x-show="openDropdown === 'companies'" x-transition class="bg-indigo-900/10 border border-indigo-500/20 rounded-lg p-4 space-y-3">
                        <input type="text" x-model="searchCompanies" placeholder="Search companies..." 
                            class="w-full px-3 py-2 rounded-lg bg-indigo-900/20 border border-indigo-500/30 text-white placeholder-indigo-300/50 focus:outline-none focus:border-indigo-500">
                        
                        @if(!empty($companiesAlerts))
                            <ul class="space-y-2 text-sm">
                                @foreach($companiesAlerts as $alert)
                                    @php
                                        $activeStudents = $alert['active_students'] ?? ($alert['flagged_students'] ?? 'N/A');
                                        $companyLower = strtolower($alert['company']);
                                    @endphp
                                    <li class="bg-indigo-900/20 border border-indigo-500/30 rounded-lg p-3 cursor-pointer hover:bg-indigo-900/30 transition-colors"
                                        x-show="'{{ $companyLower }}'.includes(searchCompanies.toLowerCase()) || !searchCompanies"
                                        @click="selectedItem = { company: '{{ $alert['company'] }}', active_students: '{{ $activeStudents }}' }; selectedType = 'company'; $refs.alertPanel.scrollIntoView({behavior: 'smooth'})">
                                        <div class="font-bold text-white">{{ $alert['company'] }}</div>
                                        <div class="text-[11px] text-indigo-200 font-bold uppercase">👥 {{ $activeStudents }} Active Student{{ $activeStudents != 1 && $activeStudents != 'N/A' ? 's' : '' }}</div>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <p class="text-gray-400 text-sm">No company data.</p>
                        @endif
                    </div>
                </div>

                <!-- Details Panel -->
                <div x-show="selectedItem" x-transition x-ref="alertPanel" class="mt-6 p-4 border border-indigo-500/30 rounded-lg bg-indigo-900/10">
                    <div class="flex items-center justify-between mb-4">
                        <h4 class="text-lg font-bold text-white" x-text="selectedType === 'supervisor' ? 'Supervisor Details' : selectedType === 'company' ? 'Company Details' : 'Student Details'"></h4>
                        <button @click="selectedItem = null; selectedType = null" class="text-gray-400 hover:text-white transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                    
                    <!-- Supervisor Details -->
                    <div x-show="selectedType === 'supervisor'" class="space-y-3">
                        <div class="p-3 bg-amber-900/20 border border-amber-500/30 rounded-lg">
                            <p class="text-amber-200 text-xs font-bold uppercase mb-1">Supervisor Name:</p>
                            <p class="text-white font-bold text-lg" x-text="selectedItem.supervisor"></p>
                        </div>
                        <div class="p-3 bg-amber-900/20 border border-amber-500/30 rounded-lg">
                            <p class="text-amber-200 text-xs font-bold uppercase mb-1">Pending Approvals:</p>
                            <p class="text-white font-bold text-lg" x-text="selectedItem.pending"></p>
                        </div>
                        <button @click="selectedItem = null; selectedType = null" class="w-full mt-4 px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white rounded-lg transition-colors">
                            Close
                        </button>
                    </div>

                    <!-- Company Details -->
                    <div x-show="selectedType === 'company'" class="space-y-3">
                        <div class="p-3 bg-indigo-900/20 border border-indigo-500/30 rounded-lg">
                            <p class="text-indigo-200 text-xs font-bold uppercase mb-1">Company Name:</p>
                            <p class="text-white font-bold text-lg" x-text="selectedItem.company"></p>
                        </div>
                        <div class="p-3 bg-indigo-900/20 border border-indigo-500/30 rounded-lg">
                            <p class="text-indigo-200 text-xs font-bold uppercase mb-1">Active Students:</p>
                            <p class="text-white font-bold text-lg" x-text="selectedItem.active_students"></p>
                        </div>
                        <button @click="selectedItem = null; selectedType = null" class="w-full mt-4 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition-colors">
                            Close
                        </button>
                    </div>

                    <!-- Student Details -->
                    <div x-show="selectedType === 'student'" class="space-y-3">
                        <div class="p-3 bg-rose-900/20 border border-rose-500/30 rounded-lg">
                            <p class="text-rose-200 text-xs font-bold uppercase mb-1">Student Name:</p>
                            <p class="text-white font-bold text-lg" x-text="selectedItem.student"></p>
                        </div>
                        <div class="p-3 bg-rose-900/20 border border-rose-500/30 rounded-lg">
                            <p class="text-rose-200 text-xs font-bold uppercase mb-1">Company:</p>
                            <p class="text-white font-bold" x-text="selectedItem.company"></p>
                        </div>
                        <div class="p-3 bg-rose-900/20 border border-rose-500/30 rounded-lg">
                            <p class="text-rose-200 text-xs font-bold uppercase mb-1">Supervisor:</p>
                            <p class="text-white font-bold" x-text="selectedItem.supervisor"></p>
                        </div>
                        <div class="p-3 bg-rose-900/20 border border-rose-500/30 rounded-lg">
                            <p class="text-rose-200 text-xs font-bold uppercase mb-1">Alerts:</p>
                            <p class="text-rose-300 text-sm" x-text="selectedItem.reasons"></p>
                        </div>
                        <button @click="selectedItem = null; selectedType = null" class="w-full mt-4 px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white rounded-lg transition-colors">
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>



    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Student Progress Bar Chart
            const progressCanvas = document.getElementById('studentProgressBarChart');
            const studentLabels = JSON.parse(progressCanvas.dataset.labels);
            const studentProgressData = JSON.parse(progressCanvas.dataset.values);

            const progressCtx = progressCanvas.getContext('2d');
            new Chart(progressCtx, {
                type: 'bar',
                data: {
                    labels: studentLabels,
                    datasets: [{
                        label: 'Avg Progress %',
                        data: studentProgressData,
                        backgroundColor: '#6366f1',
                        borderRadius: 8,
                        barThickness: 15,
                    }]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        x: {
                            max: 100,
                            grid: { color: 'rgba(255, 255, 255, 0.05)' },
                            ticks: { color: '#9ca3af', font: { size: 10 } }
                        },
                        y: {
                            grid: { display: false },
                            ticks: { color: '#9ca3af', font: { size: 10 } }
                        }
                    }
                }
            });

            // Attendance Trend Chart
            const attendanceCanvas = document.getElementById('attendanceTrendChart');
            const attendanceLabels = JSON.parse(attendanceCanvas.dataset.labels);
            const attendanceTotal = JSON.parse(attendanceCanvas.dataset.total);
            const attendanceLate = JSON.parse(attendanceCanvas.dataset.late);

            const attendanceCtx = attendanceCanvas.getContext('2d');
            
            new Chart(attendanceCtx, {
                type: 'line',
                data: {
                    labels: attendanceLabels,
                    datasets: [
                        {
                            label: 'Total Clock-ins',
                            data: attendanceTotal,
                            borderColor: '#10b981',
                            backgroundColor: 'rgba(16, 185, 129, 0.1)',
                            fill: true,
                            tension: 0.4,
                            borderWidth: 2,
                            pointRadius: 3
                        },
                        {
                            label: 'Late',
                            data: attendanceLate,
                            borderColor: '#f43f5e',
                            backgroundColor: 'rgba(244, 63, 94, 0.1)',
                            fill: true,
                            tension: 0.4,
                            borderWidth: 2,
                            pointRadius: 3
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { 
                            position: 'top',
                            labels: { color: '#9ca3af', font: { size: 10 }, boxWidth: 10 }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { color: 'rgba(255, 255, 255, 0.05)' },
                            ticks: { color: '#9ca3af', font: { size: 10 }, stepSize: 1 }
                        },
                        x: {
                            grid: { display: false },
                            ticks: { color: '#9ca3af', font: { size: 10 } }
                        }
                    }
                }
            });

            const arCanvas = document.getElementById('arMetricsChart');
            if (arCanvas) {
                const initial = JSON.parse(arCanvas.dataset.initial || 'null');
                const initialLabel = initial?.label || 'Accomplishment Report';
                const initialPercent = Number(initial?.percent ?? 0);

                const arCtx = arCanvas.getContext('2d');
                const arChart = new Chart(arCtx, {
                    type: 'bar',
                    data: {
                        labels: [initialLabel],
                        datasets: [
                            {
                                data: [initialPercent],
                                backgroundColor: ['rgba(99, 102, 241, 0.85)'],
                                borderColor: ['rgba(99, 102, 241, 1)'],
                                borderWidth: 1,
                                borderRadius: 8,
                                barThickness: 28,
                                maxBarThickness: 36,
                            },
                        ],
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                max: 100,
                                grid: { color: 'rgba(255, 255, 255, 0.05)' },
                                ticks: {
                                    color: '#9ca3af',
                                    font: { size: 10 },
                                    callback: function (value) {
                                        return value + '%';
                                    },
                                },
                            },
                            x: {
                                grid: { display: false },
                                ticks: { color: '#9ca3af', font: { size: 10 } },
                            },
                        },
                    },
                });

                window.addEventListener('coordinator-ar-metric', function (e) {
                    const label = e.detail?.label || 'Accomplishment Report';
                    const percent = Number(e.detail?.percent ?? 0);
                    arChart.data.labels = [label];
                    arChart.data.datasets[0].data = [percent];
                    arChart.update();
                });
            }

            const miniLineOptions = {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: 'rgba(255, 255, 255, 0.05)' },
                        ticks: { color: '#9ca3af', font: { size: 10 }, stepSize: 1 },
                    },
                    x: {
                        grid: { display: false },
                        ticks: { color: '#9ca3af', font: { size: 10 } },
                    },
                },
            };

            const attendanceMiniCanvas = document.getElementById('attendanceMiniChart');
            if (attendanceMiniCanvas) {
                const labels = JSON.parse(attendanceMiniCanvas.dataset.labels || '[]');
                const values = JSON.parse(attendanceMiniCanvas.dataset.values || '[]');
                const ctx = attendanceMiniCanvas.getContext('2d');
                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels,
                        datasets: [
                            {
                                label: 'Attendance',
                                data: values,
                                borderColor: '#10b981',
                                backgroundColor: 'rgba(16, 185, 129, 0.12)',
                                fill: true,
                                tension: 0.35,
                                borderWidth: 2,
                                pointRadius: 2,
                            },
                        ],
                    },
                    options: miniLineOptions,
                });
            }

            const journalsMiniCanvas = document.getElementById('journalsMiniChart');
            if (journalsMiniCanvas) {
                const labels = JSON.parse(journalsMiniCanvas.dataset.labels || '[]');
                const values = JSON.parse(journalsMiniCanvas.dataset.values || '[]');
                const ctx = journalsMiniCanvas.getContext('2d');
                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels,
                        datasets: [
                            {
                                label: 'Journals',
                                data: values,
                                borderColor: '#60a5fa',
                                backgroundColor: 'rgba(96, 165, 250, 0.12)',
                                fill: true,
                                tension: 0.35,
                                borderWidth: 2,
                                pointRadius: 2,
                            },
                        ],
                    },
                    options: miniLineOptions,
                });
            }
        });
    </script>
</x-coordinator-layout>
