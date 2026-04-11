<x-supervisor-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Leave Requests') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">
            @if (session('status'))
                <div class="p-3 bg-green-50 text-green-800 rounded">{{ session('status') }}</div>
            @endif

            @if (isset($errors) && $errors->any())
                <div class="p-3 bg-red-50 text-red-800 rounded">
                    {{ $errors->first() }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-4 border-b">
                    <form method="GET" action="{{ route('supervisor.leaves.index') }}" class="grid grid-cols-1 md:grid-cols-5 gap-3">
                        <input type="text" name="q" value="{{ request('q') }}" placeholder="Search student/type/reason" class="rounded border-gray-300 text-sm">
                        <select name="status" class="rounded border-gray-300 text-sm">
                            <option value="">All Status</option>
                            @foreach (['draft','submitted','pending','approved','rejected','cancelled'] as $st)
                                <option value="{{ $st }}" @selected(request('status') === $st)>{{ ucfirst($st) }}</option>
                            @endforeach
                        </select>
                        <input type="date" name="date_from" value="{{ request('date_from') }}" class="rounded border-gray-300 text-sm">
                        <input type="date" name="date_to" value="{{ request('date_to') }}" class="rounded border-gray-300 text-sm">
                        <button type="submit" class="px-3 py-2 bg-indigo-600 text-white rounded text-sm font-semibold">Filter</button>
                    </form>
                </div>

                <div class="p-6 text-gray-900 overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="border-b bg-gray-50">
                                <th class="text-left px-3 py-2">Student</th>
                                <th class="text-left px-3 py-2">Type</th>
                                <th class="text-left px-3 py-2">Dates</th>
                                <th class="text-left px-3 py-2">Days</th>
                                <th class="text-left px-3 py-2">Reason</th>
                                <th class="text-left px-3 py-2">Status</th>
                                <th class="text-left px-3 py-2">Submitted</th>
                                <th class="text-left px-3 py-2">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($leaves as $leave)
                                <tr class="border-b align-top">
                                    <td class="px-3 py-2">{{ $leave->assignment?->student?->name ?? 'N/A' }}</td>
                                    <td class="px-3 py-2 font-semibold">{{ $leave->type }}</td>
                                    <td class="px-3 py-2">{{ optional($leave->start_date)->format('M d, Y') }} - {{ optional($leave->end_date)->format('M d, Y') }}</td>
                                    <td class="px-3 py-2">{{ $leave->number_of_days ?? '-' }}</td>
                                    <td class="px-3 py-2 max-w-[220px] truncate" title="{{ $leave->reason }}">{{ $leave->reason }}</td>
                                    <td class="px-3 py-2">
                                        @php
                                            $statusClasses = [
                                                'approved' => 'bg-green-100 text-green-700',
                                                'rejected' => 'bg-red-100 text-red-700',
                                                'pending' => 'bg-yellow-100 text-yellow-700',
                                                'submitted' => 'bg-blue-100 text-blue-700',
                                                'draft' => 'bg-gray-100 text-gray-700',
                                                'cancelled' => 'bg-slate-100 text-slate-700',
                                            ];
                                        @endphp
                                        <span class="px-2 py-1 rounded-full text-xs font-bold {{ $statusClasses[$leave->status] ?? 'bg-gray-100 text-gray-700' }}">{{ ucfirst($leave->status) }}</span>
                                    </td>
                                    <td class="px-3 py-2">{{ optional($leave->submitted_at)->format('M d, Y h:i A') ?? '-' }}</td>
                                    <td class="px-3 py-2">
                                        <div class="flex flex-col gap-2 min-w-[220px]">
                                            <a href="{{ route('supervisor.leaves.print', $leave->id) }}" target="_blank" class="px-3 py-1 bg-indigo-600 hover:bg-indigo-700 text-white rounded text-center">
                                                View Full Details
                                            </a>

                                            @if($leave->attachment_path)
                                                <a href="{{ Storage::url($leave->attachment_path) }}" target="_blank" class="px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white rounded text-center">
                                                    Open Attachment
                                                </a>
                                            @endif

                                            @if(in_array($leave->status, ['submitted', 'pending'], true))
                                                <form method="POST" action="{{ route('supervisor.leaves.approve', $leave->id) }}" onsubmit="return confirm('Approve this leave request?');" class="space-y-1">
                                                    @csrf
                                                    <textarea name="reviewer_remarks" rows="2" placeholder="Optional approval remarks" class="w-full rounded border-gray-300 text-xs"></textarea>
                                                    <button type="submit" class="w-full px-3 py-1 bg-green-600 hover:bg-green-700 text-white rounded">Approve</button>
                                                </form>

                                                <form method="POST" action="{{ route('supervisor.leaves.reject', $leave->id) }}" onsubmit="return confirm('Reject this leave request?');" class="space-y-1">
                                                    @csrf
                                                    <textarea name="reviewer_remarks" rows="2" placeholder="Required rejection remarks" class="w-full rounded border-gray-300 text-xs" required></textarea>
                                                    <button type="submit" class="w-full px-3 py-1 bg-red-600 hover:bg-red-700 text-white rounded">Reject</button>
                                                </form>
                                            @endif

                                            @if($leave->reviewer_remarks)
                                                <p class="text-xs text-gray-600"><span class="font-semibold">Remarks:</span> {{ $leave->reviewer_remarks }}</p>
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
