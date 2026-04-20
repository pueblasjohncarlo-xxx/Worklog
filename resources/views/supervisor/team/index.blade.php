<x-supervisor-layout>
    <x-slot name="header">
        Team Overview
    </x-slot>

    <div class="space-y-6 py-6">
        <!-- Team Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white rounded-xl shadow p-6 border-l-4 border-indigo-500">
                <div class="text-gray-500 text-sm font-bold uppercase">Total OJT Students</div>
                <div class="text-3xl font-black text-gray-800 mt-2">{{ $teamMembers->count() }}</div>
            </div>
            <div class="bg-white rounded-xl shadow p-6 border-l-4 border-emerald-500">
                <div class="text-gray-500 text-sm font-bold uppercase">Total Hours Approved</div>
                <div class="text-3xl font-black text-gray-800 mt-2">{{ number_format($teamMembers->sum('total_hours'), 2) }}</div>
            </div>
            <div class="bg-white rounded-xl shadow p-6 border-l-4 border-blue-500">
                <div class="text-gray-500 text-sm font-bold uppercase">Active Tasks</div>
                <div class="text-3xl font-black text-gray-800 mt-2">{{ $teamMembers->sum('active_tasks') }}</div>
            </div>
        </div>

        <!-- Team List -->
        <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-100">
            <div class="bg-gray-50 px-6 py-4 border-b border-gray-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <h3 class="font-bold text-gray-800 text-lg">My OJT Students</h3>
                <div class="w-full sm:w-80">
                    <label for="team-student-search" class="sr-only">Search OJT students</label>
                    <div class="relative">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                            <svg class="h-4 w-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M10.5 18a7.5 7.5 0 100-15 7.5 7.5 0 000 15z" />
                            </svg>
                        </div>
                        <input
                            id="team-student-search"
                            type="text"
                            placeholder="Search name, section, company, status..."
                            class="w-full border border-gray-300 rounded-lg bg-white text-gray-900 placeholder-gray-500 pl-9 pr-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                        >
                    </div>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-3 font-bold text-gray-500 uppercase tracking-wider">Student</th>
                            <th class="px-6 py-3 font-bold text-gray-500 uppercase tracking-wider">Company</th>
                            <th class="px-6 py-3 font-bold text-gray-500 uppercase tracking-wider">Progress</th>
                            <th class="px-6 py-3 font-bold text-gray-500 uppercase tracking-wider">Last Log</th>
                            <th class="px-6 py-3 font-bold text-gray-500 uppercase tracking-wider text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="team-members-body" class="divide-y divide-gray-200 bg-white">
                        @foreach($teamMembers as $member)
                            @php
                                $percentage = min(100, ($member['total_hours'] / ($member['required_hours'] ?: 1)) * 100);
                                $section = $member['student']->section ?? $member['student']->department ?? 'N/A';
                                $status = $percentage >= 100 ? 'Completed' : ($percentage < 5 ? 'Needs Attention' : 'On Track');
                                $searchText = mb_strtolower(trim(implode(' ', [
                                    $member['student']->name ?? '',
                                    $member['student']->email ?? '',
                                    $section,
                                    $member['company']->name ?? 'N/A',
                                    $status,
                                ])));
                            @endphp
                            <tr class="team-member-row hover:bg-gray-50 transition-colors" data-search="{{ $searchText }}">
                                <td class="px-6 py-4 font-medium text-gray-900">
                                    <div class="flex items-center gap-3">
                                        <div class="h-8 w-8 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 font-bold">
                                            {{ substr($member['student']->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <div class="font-bold">{{ $member['student']->name }}</div>
                                            <div class="text-xs text-gray-500">{{ $member['student']->email }}</div>
                                            <div class="text-xs text-gray-500">{{ $section }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-gray-600">{{ $member['company']->name ?? 'N/A' }}</td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2">
                                        <x-progress-bar :value="$percentage" />
                                        <span class="text-xs font-bold text-gray-600">{{ number_format($member['total_hours'], 1) }} / {{ $member['required_hours'] }} hrs</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-gray-500">
                                    {{ $member['last_log'] ? $member['last_log']->work_date->format('M d, Y') : 'Never' }}
                                    <div class="mt-1">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold {{ $status === 'Completed' ? 'bg-cyan-100 text-cyan-700' : ($status === 'On Track' ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700') }}">
                                            {{ $status }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <a href="{{ route('supervisor.team.show', $member['assignment_id']) }}" class="text-indigo-600 hover:text-indigo-900 font-bold text-xs uppercase">View Profile</a>
                                </td>
                            </tr>
                        @endforeach
                        <tr id="team-no-results" class="hidden">
                            <td colspan="5" class="px-6 py-6 text-center text-gray-500 font-medium">No OJT students found.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const input = document.getElementById('team-student-search');
            const rows = Array.from(document.querySelectorAll('.team-member-row'));
            const noResults = document.getElementById('team-no-results');

            if (!input || rows.length === 0 || !noResults) {
                return;
            }

            const filterRows = () => {
                const term = input.value.trim().toLowerCase();
                let visibleCount = 0;

                rows.forEach((row) => {
                    const haystack = (row.dataset.search || '').toLowerCase();
                    const match = term === '' || haystack.includes(term);
                    row.style.display = match ? '' : 'none';
                    if (match) {
                        visibleCount++;
                    }
                });

                noResults.style.display = visibleCount === 0 ? '' : 'none';
            };

            input.addEventListener('input', filterRows);
        });
    </script>
    @endpush
</x-supervisor-layout>