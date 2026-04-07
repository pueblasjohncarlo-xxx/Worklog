<x-ojt-adviser-layout>
    <x-slot name="header">
        Attendance Logs: {{ $student->name }}
    </x-slot>

    <div class="space-y-6">
        <div class="flex justify-between items-center">
            <a href="{{ route('ojt_adviser.students') }}" class="text-indigo-400 hover:text-indigo-300 text-sm font-bold flex items-center gap-2 transition-colors">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Back to OJT Students
            </a>
        </div>

        <div class="bg-white/5 border border-white/10 rounded-2xl overflow-hidden shadow-xl backdrop-blur-md">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-black/30">
                        <tr>
                            <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Time In</th>
                            <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Time Out</th>
                            <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Hours</th>
                            <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider text-right">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        @forelse($logs as $log)
                            <tr class="hover:bg-white/5 transition-colors">
                                <td class="px-6 py-4 font-medium text-gray-200">{{ $log->work_date->format('M d, Y') }}</td>
                                <td class="px-6 py-4 text-gray-400">{{ $log->time_in ? $log->time_in->format('h:i A') : '---' }}</td>
                                <td class="px-6 py-4 text-gray-400">{{ $log->time_out ? $log->time_out->format('h:i A') : '---' }}</td>
                                <td class="px-6 py-4 text-indigo-400 font-bold">{{ $log->hours }}h</td>
                                <td class="px-6 py-4 text-right">
                                    @if($log->status === 'approved')
                                        <span class="px-2 py-1 rounded-full bg-emerald-500/10 text-emerald-400 text-[10px] font-bold uppercase tracking-wider border border-emerald-500/20">Approved</span>
                                    @elseif($log->status === 'submitted')
                                        <span class="px-2 py-1 rounded-full bg-blue-500/10 text-blue-400 text-[10px] font-bold uppercase tracking-wider border border-blue-500/20">Pending</span>
                                    @else
                                        <span class="px-2 py-1 rounded-full bg-gray-500/10 text-gray-400 text-[10px] font-bold uppercase tracking-wider border border-gray-500/20">{{ ucfirst($log->status) }}</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-gray-500 italic">No attendance logs found for this student.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($logs->hasPages())
                <div class="p-6 border-t border-white/10 bg-black/20">
                    {{ $logs->links() }}
                </div>
            @endif
        </div>
    </div>
</x-ojt-adviser-layout>
