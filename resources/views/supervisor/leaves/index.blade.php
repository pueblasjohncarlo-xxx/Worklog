<x-supervisor-layout>
    <x-slot name="header">Leave Requests</x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-5">
            @php
                $pendingCount = $leaves->getCollection()->whereIn('status', ['submitted', 'pending'])->count();
            @endphp

            @if($pendingCount > 0)
                <div class="p-4 bg-orange-50 dark:bg-orange-900/20 border-l-4 border-orange-500 rounded-r-lg shadow-md">
                    <div class="flex items-center gap-3">
                        <svg class="h-6 w-6 text-orange-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4v.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <div>
                            <h3 class="font-semibold text-orange-800 dark:text-orange-200">Pending Leave Requests</h3>
                            <p class="text-sm text-orange-700 dark:text-orange-300 mt-1">You have <strong>{{ $pendingCount }}</strong> leave request(s) awaiting your review. Please review and approve or reject them below.</p>
                        </div>
                    </div>
                </div>
            @endif
            
            @if (session('status'))
                <div class="p-3 bg-green-50 text-green-800 rounded border border-green-200 shadow-md">
                    <div class="flex items-center gap-2">
                        <svg class="h-5 w-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                        {{ session('status') }}
                    </div>
                </div>
            @endif

            @if (isset($errors) && $errors->any())
                <div class="p-3 bg-red-50 text-red-800 rounded border border-red-200">
                    {{ $errors->first() }}
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50">
                    <form method="GET" action="{{ route('supervisor.leaves.index') }}" class="space-y-3">
                        <div class="grid grid-cols-1 md:grid-cols-6 gap-3">
                            <input type="text" name="q" value="{{ request('q') }}" placeholder="Search student/type/reason..." class="md:col-span-2 rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 text-gray-900 text-sm shadow-sm">
                            <select name="status" class="rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 text-gray-900 text-sm shadow-sm">
                                <option value="">All status</option>
                                @foreach (['submitted','pending','approved','rejected','draft','cancelled'] as $st)
                                    <option value="{{ $st }}" @selected(request('status') === $st)>{{ ucfirst($st) }}</option>
                                @endforeach
                            </select>
                            <input type="date" name="date_from" value="{{ request('date_from') }}" class="rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 text-gray-900 text-sm shadow-sm" aria-label="Date from">
                            <input type="date" name="date_to" value="{{ request('date_to') }}" class="rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 text-gray-900 text-sm shadow-sm" aria-label="Date to">
                            <div class="flex gap-2">
                                <button type="submit" class="flex-1 px-3 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded text-sm font-semibold shadow-sm transition-colors">Apply</button>
                                <a href="{{ route('supervisor.leaves.index') }}" class="px-3 py-2 bg-gray-200 hover:bg-gray-300 text-gray-900 rounded text-sm font-semibold shadow-sm transition-colors">Reset</a>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="p-6 text-gray-900 dark:text-gray-100 overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="border-b bg-gray-100 dark:bg-gray-800">
                                <th class="text-left px-3 py-3 font-bold text-gray-900 dark:text-gray-100">Student</th>
                                <th class="text-left px-3 py-3 font-bold text-gray-900 dark:text-gray-100">Type</th>
                                <th class="text-left px-3 py-3 font-bold text-gray-900 dark:text-gray-100">Dates</th>
                                <th class="text-left px-3 py-3 font-bold text-gray-900 dark:text-gray-100">Days</th>
                                <th class="text-left px-3 py-3 font-bold text-gray-900 dark:text-gray-100">Reason</th>
                                <th class="text-left px-3 py-3 font-bold text-gray-900 dark:text-gray-100">Status</th>
                                <th class="text-left px-3 py-3 font-bold text-gray-900 dark:text-gray-100">Submitted</th>
                                <th class="text-left px-3 py-3 font-bold text-gray-900 dark:text-gray-100">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($leaves as $leave)
                                <tr class="border-b align-top {{ in_array($leave->status, ['submitted', 'pending']) ? 'bg-orange-50 dark:bg-orange-900/10 hover:bg-orange-100 dark:hover:bg-orange-900/20' : 'hover:bg-gray-50 dark:hover:bg-gray-800' }}">
                                    <td class="px-3 py-2 font-semibold text-gray-900 dark:text-gray-100">
                                        <div class="flex items-center gap-2">
                                            {{ $leave->assignment?->student?->name ?? 'N/A' }}
                                            @if(in_array($leave->status, ['submitted', 'pending'], true))
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold bg-orange-500 text-white">NEW</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-3 py-2 font-semibold text-gray-900 dark:text-gray-100 whitespace-nowrap">{{ $leave->type }}</td>
                                    <td class="px-3 py-2 text-gray-700 dark:text-gray-300 whitespace-nowrap">{{ optional($leave->start_date)->format('M d, Y') }} - {{ optional($leave->end_date)->format('M d, Y') }}</td>
                                    <td class="px-3 py-2 font-bold text-gray-900 dark:text-gray-100 whitespace-nowrap">{{ $leave->number_of_days ?? '-' }}</td>
                                    <td class="px-3 py-2 max-w-[260px] text-gray-700 dark:text-gray-300 whitespace-normal break-words" title="{{ $leave->reason }}">{{ $leave->reason }}</td>
                                    <td class="px-3 py-2">
                                        @php
                                            $statusClasses = [
                                                'approved' => 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300',
                                                'rejected' => 'bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300',
                                                'pending' => 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-300 font-bold',
                                                'submitted' => 'bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300 font-bold',
                                                'draft' => 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300',
                                                'cancelled' => 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300',
                                            ];
                                        @endphp
                                        <span class="px-2 py-1 rounded-full text-xs font-bold {{ $statusClasses[$leave->status] ?? 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300' }}">{{ ucfirst($leave->status) }}</span>
                                        @if($leave->reviewer_remarks)
                                            <div class="mt-2 rounded-md bg-gray-50 dark:bg-gray-700/30 p-2">
                                                <div class="text-[11px] font-semibold text-gray-700 dark:text-gray-300">Remarks</div>
                                                <div class="text-xs text-gray-600 dark:text-gray-400 mt-0.5 break-words">{{ $leave->reviewer_remarks }}</div>
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-3 py-2 text-sm text-gray-600 dark:text-gray-400 whitespace-nowrap">{{ optional($leave->submitted_at)->format('M d, Y h:i A') ?? '-' }}</td>
                                    <td class="px-3 py-2">
                                        <div class="flex flex-col gap-2 min-w-[220px]">
                                            <div class="flex flex-wrap gap-2">
                                                <a href="{{ route('supervisor.leaves.print', $leave->id) }}" target="_blank" class="px-3 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded text-center text-xs sm:text-sm font-semibold shadow-sm transition-colors">
                                                    Details
                                                </a>

                                                @if($leave->attachment_path)
                                                    <a href="{{ Storage::url($leave->attachment_path) }}" target="_blank" class="px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded text-center text-xs sm:text-sm font-semibold shadow-sm transition-colors">
                                                        Attachment
                                                    </a>
                                                @endif
                                            </div>

                                            @if(in_array($leave->status, ['submitted', 'pending'], true))
                                                <details class="rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50/60 dark:bg-gray-900/20 p-2">
                                                    <summary class="cursor-pointer select-none text-xs font-bold text-gray-800 dark:text-gray-200">Review (approve / reject)</summary>
                                                    <div class="mt-2 space-y-2">
                                                        <form method="POST" action="{{ route('supervisor.leaves.approve', $leave->id) }}" onsubmit="return confirm('Approve this leave request?');">
                                                            @csrf
                                                            <textarea name="reviewer_remarks" rows="2" placeholder="Optional remarks (approve)" class="w-full rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 text-gray-900 text-xs p-2 shadow-sm"></textarea>
                                                            <button type="submit" class="mt-2 w-full px-3 py-2 bg-green-600 hover:bg-green-700 text-white rounded text-sm font-semibold shadow-sm transition-colors">Approve</button>
                                                        </form>

                                                        <form method="POST" action="{{ route('supervisor.leaves.reject', $leave->id) }}" onsubmit="return confirm('Reject this leave request? Please provide a reason.');">
                                                            @csrf
                                                            <textarea name="reviewer_remarks" rows="2" placeholder="Required remarks (reject)" class="w-full rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 text-gray-900 text-xs p-2 shadow-sm" required></textarea>
                                                            <button type="submit" class="mt-2 w-full px-3 py-2 bg-red-600 hover:bg-red-700 text-white rounded text-sm font-semibold shadow-sm transition-colors">Reject</button>
                                                        </form>
                                                    </div>
                                                </details>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-3 py-6 text-center text-gray-500">No leave requests found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <div class="mt-4">
                        {{ $leaves->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-supervisor-layout>
