<x-supervisor-layout>
    <x-slot name="header">
        Team Overview
    </x-slot>

    <div class="space-y-6 py-6">
        <!-- Team Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white rounded-xl shadow p-6 border-l-4 border-indigo-500">
                <div class="text-gray-500 text-sm font-bold uppercase">Total Students</div>
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
            <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
                <h3 class="font-bold text-gray-800 text-lg">My Students</h3>
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
                    <tbody class="divide-y divide-gray-200 bg-white">
                        @foreach($teamMembers as $member)
                            @php
                                $percentage = min(100, ($member['total_hours'] / ($member['required_hours'] ?: 1)) * 100);
                                $progressStyle = "width: {$percentage}%";
                            @endphp
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 font-medium text-gray-900">
                                    <div class="flex items-center gap-3">
                                        <div class="h-8 w-8 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 font-bold">
                                            {{ substr($member['student']->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <div class="font-bold">{{ $member['student']->name }}</div>
                                            <div class="text-xs text-gray-500">{{ $member['student']->email }}</div>
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
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <a href="#" class="text-indigo-600 hover:text-indigo-900 font-bold text-xs uppercase">View Profile</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-supervisor-layout>