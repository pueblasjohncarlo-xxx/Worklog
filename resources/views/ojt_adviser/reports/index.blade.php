<x-ojt-adviser-layout>
    <x-slot name="header">
        Reports & Export Center
    </x-slot>

    <div class="space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Attendance Summary -->
            <div class="bg-white/5 border border-white/10 rounded-2xl p-6 shadow-xl backdrop-blur-md">
                <div class="flex items-center gap-4 mb-6">
                    <div class="p-3 bg-indigo-500/20 rounded-xl text-indigo-400">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-white">Attendance Summary</h3>
                </div>
                <p class="text-sm text-gray-400 mb-6">Generate a complete overview of OJT student clock-in/out records, total hours, and status for all your assigned students.</p>
                <form method="GET" action="{{ route('ojt_adviser.reports.attendance.export') }}" class="space-y-3">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Date From (optional)</label>
                            <input type="date" name="date_from" value="{{ request('date_from') }}" class="w-full rounded-xl bg-black/30 border border-white/10 text-gray-100 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/60" />
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Date To (optional)</label>
                            <input type="date" name="date_to" value="{{ request('date_to') }}" class="w-full rounded-xl bg-black/30 border border-white/10 text-gray-100 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/60" />
                        </div>
                    </div>

                    <button type="submit" class="w-full py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl transition-all flex items-center justify-center gap-2">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a2 2 0 002 2h12a2 2 0 002-2v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                        Export Attendance (CSV)
                    </button>
                </form>
            </div>

            <!-- Task & Journal Report -->
            <div class="bg-white/5 border border-white/10 rounded-2xl p-6 shadow-xl backdrop-blur-md">
                <div class="flex items-center gap-4 mb-6">
                    <div class="p-3 bg-emerald-500/20 rounded-xl text-emerald-400">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-white">Journal & Feedback</h3>
                </div>
                <p class="text-sm text-gray-400 mb-6">Download a compilation of OJT student reflections along with supervisor comments and your adviser feedback.</p>
                <button class="w-full py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl transition-all flex items-center justify-center gap-2">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a2 2 0 002 2h12a2 2 0 002-2v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                    Export Journals (Excel)
                </button>
            </div>
        </div>

        <!-- Student Progress Summary -->
        <div class="bg-white/5 border border-white/10 rounded-2xl overflow-hidden shadow-xl backdrop-blur-md">
            <div class="p-6 border-b border-white/10">
                <h3 class="text-lg font-bold text-white">OJT Completion Status</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-black/30">
                        <tr>
                            <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Student</th>
                            <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Company</th>
                            <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Target Hours</th>
                            <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        @foreach($assignments as $assignment)
                            <tr class="hover:bg-white/5 transition-colors">
                                <td class="px-6 py-4 text-gray-200">{{ $assignment->student->name }}</td>
                                <td class="px-6 py-4 text-gray-400">{{ $assignment->company->name }}</td>
                                <td class="px-6 py-4 text-indigo-400 font-bold">{{ $assignment->required_hours }}h</td>
                                <td class="px-6 py-4">
                                    @if($assignment->progressPercentage() >= 100)
                                        <span class="text-emerald-400 font-bold">COMPLETED</span>
                                    @else
                                        <span class="text-amber-400 font-bold">IN PROGRESS ({{ $assignment->progressPercentage() }}%)</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-ojt-adviser-layout>
