<x-ojt-adviser-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Leave Requests') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">
            @php
                $usesStaged = $usesStagedLeaveApproval ?? false;
                $pendingCount = $leaves->getCollection()
                    ->filter(function ($leave) use ($usesStaged) {
                        if (! in_array($leave->status, ['submitted', 'pending'], true)) {
                            return false;
                        }

                        if ($usesStaged) {
                            return ($leave->supervisor_decision ?? null) === 'approved';
                        }

                        return true;
                    })
                    ->count();
            @endphp

            @if($pendingCount > 0)
                <div class="p-4 bg-orange-50 dark:bg-orange-900/20 border-l-4 border-orange-500 rounded-r-lg shadow-md">
                    <div class="flex items-center gap-3">
                        <svg class="h-6 w-6 text-orange-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4v.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <div>
                            <h3 class="font-semibold text-orange-800 dark:text-orange-200">Pending Leave Requests</h3>
                            <p class="text-sm text-orange-700 dark:text-orange-300 mt-1">You have <strong>{{ $pendingCount }}</strong> leave request(s) awaiting your review.</p>
                        </div>
                    </div>
                </div>
            @endif

            @if (session('status'))
                <div class="p-3 bg-green-50 text-green-800 rounded border border-green-200 shadow-md">
                    {{ session('status') }}
                </div>
            @endif

            @if (isset($errors) && $errors->any())
                <div class="p-3 bg-red-50 text-red-800 rounded border border-red-200">
                    {{ $errors->first() }}
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50">
                    <form method="GET" action="{{ route('ojt_adviser.leaves.index') }}" class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-5 gap-3">
                            <input type="text" name="q" value="{{ request('q') }}" placeholder="Search student name/type/reason..." class="rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 text-gray-900 text-sm shadow-sm">
                            <select name="status" class="rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 text-gray-900 text-sm shadow-sm">
                                <option value="">All Status</option>
                                @foreach (['submitted','pending','approved','rejected','draft','cancelled'] as $st)
                                    <option value="{{ $st }}" @selected(request('status') === $st)>{{ ucfirst($st) }}</option>
                                @endforeach
                            </select>
                            <input type="date" name="date_from" value="{{ request('date_from') }}" class="rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 text-gray-900 text-sm shadow-sm">
                            <input type="date" name="date_to" value="{{ request('date_to') }}" class="rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 text-gray-900 text-sm shadow-sm">
                            <button type="submit" class="px-3 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded text-sm font-semibold shadow-sm transition-colors">Filter</button>
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
                                @php
                                    $usesStaged = $usesStagedLeaveApproval ?? false;
                                    $supervisorDecision = $leave->supervisor_decision ?? null;
                                    $isAwaitingSupervisor = $usesStaged && in_array($leave->status, ['submitted', 'pending'], true) && $supervisorDecision !== 'approved';
                                    $canAdviserReview = in_array($leave->status, ['submitted', 'pending'], true) && (! $usesStaged || $supervisorDecision === 'approved');

                                    $statusLabel = ucfirst($leave->status);
                                    if ($usesStaged && $leave->status === 'pending' && $supervisorDecision === 'approved') {
                                        $statusLabel = 'Pending (Supervisor Approved)';
                                    }
                                @endphp
                                <tr class="border-b align-top {{ in_array($leave->status, ['submitted', 'pending']) ? 'bg-orange-50 dark:bg-orange-900/10 hover:bg-orange-100 dark:hover:bg-orange-900/20' : 'hover:bg-gray-50 dark:hover:bg-gray-800' }}">
                                    <td class="px-3 py-2 font-semibold text-gray-900 dark:text-gray-100">{{ $leave->assignment?->student?->name ?? 'N/A' }}</td>
                                    <td class="px-3 py-2 font-semibold text-gray-900 dark:text-gray-100">{{ $leave->type }}</td>
                                    <td class="px-3 py-2 text-gray-700 dark:text-gray-300">{{ optional($leave->start_date)->format('M d, Y') }} - {{ optional($leave->end_date)->format('M d, Y') }}</td>
                                    <td class="px-3 py-2 font-bold text-gray-900 dark:text-gray-100">{{ $leave->number_of_days ?? '-' }}</td>
                                    <td class="px-3 py-2 max-w-[220px] truncate text-gray-700 dark:text-gray-300" title="{{ $leave->reason }}">{{ $leave->reason }}</td>
                                    <td class="px-3 py-2">
                                        <span class="px-2 py-1 rounded-full text-xs font-bold {{ $leave->status === 'approved' ? 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300' : ($leave->status === 'rejected' ? 'bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300' : 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-300') }}">{{ $statusLabel }}</span>
                                        @if ($leave->supervisor_reviewer_remarks)
                                            <div class="mt-2 rounded-md bg-gray-50 dark:bg-gray-700/30 p-2">
                                                <div class="text-[11px] font-semibold text-gray-700 dark:text-gray-300">Supervisor Remarks</div>
                                                <div class="text-xs text-gray-600 dark:text-gray-400 mt-0.5 break-words">{{ $leave->supervisor_reviewer_remarks }}</div>
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-3 py-2 text-sm text-gray-600 dark:text-gray-400">{{ optional($leave->submitted_at)->format('M d, Y h:i A') ?? '-' }}</td>
                                    <td class="px-3 py-2">
                                        <div class="flex flex-col gap-2 min-w-[260px]">
                                            <a href="{{ route('ojt_adviser.leaves.print', $leave->id) }}" target="_blank" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded text-center text-sm font-semibold shadow-sm transition-colors">View Full Details</a>

                                            @if($isAwaitingSupervisor)
                                                <div class="rounded-lg border border-amber-200 bg-amber-50 p-3 text-xs font-semibold text-amber-900">
                                                    Awaiting supervisor approval before adviser review.
                                                </div>
                                            @endif

                                            @if($canAdviserReview)
                                                <form method="POST" action="{{ route('ojt_adviser.leaves.approve', $leave->id) }}" onsubmit="return confirm('Approve this leave request?');">
                                                    @csrf
                                                    <textarea name="reviewer_remarks" rows="2" placeholder="Optional approval remarks..." class="w-full rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 text-gray-900 text-xs p-2 shadow-sm"></textarea>
                                                    <button type="submit" class="w-full px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded text-sm font-semibold shadow-sm transition-colors">Approve</button>
                                                </form>

                                                <form method="POST" action="{{ route('ojt_adviser.leaves.reject', $leave->id) }}" onsubmit="return confirm('Reject this leave request?');">
                                                    @csrf
                                                    <textarea name="reviewer_remarks" rows="2" placeholder="Required rejection remarks..." class="w-full rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 text-gray-900 text-xs p-2 shadow-sm" required></textarea>
                                                    <button type="submit" class="w-full px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded text-sm font-semibold shadow-sm transition-colors">Reject</button>
                                                </form>
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
</x-ojt-adviser-layout>
