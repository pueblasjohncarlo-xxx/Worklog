<x-supervisor-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Leave Requests') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if (session('status'))
                        <div class="mb-4 p-3 bg-green-50 text-green-800 rounded">
                            {{ session('status') }}
                        </div>
                    @endif

                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead>
                                <tr class="border-b bg-gray-50">
                                    <th class="text-left px-3 py-2">Student</th>
                                    <th class="text-left px-3 py-2">Type</th>
                                    <th class="text-left px-3 py-2">Dates</th>
                                    <th class="text-left px-3 py-2">Reason</th>
                                    <th class="text-left px-3 py-2">Status</th>
                                    <th class="text-left px-3 py-2">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($leaves as $leave)
                                    <tr class="border-b">
                                        <td class="px-3 py-2">
                                            {{ $leave->assignment?->student?->name ?? 'N/A' }}
                                        </td>
                                        <td class="px-3 py-2 uppercase">
                                            {{ $leave->type }}
                                        </td>
                                        <td class="px-3 py-2">
                                            {{ \Carbon\Carbon::parse($leave->start_date)->format('M d, Y') }} - {{ \Carbon\Carbon::parse($leave->end_date)->format('M d, Y') }}
                                        </td>
                                        <td class="px-3 py-2 max-w-md truncate">
                                            {{ $leave->reason }}
                                        </td>
                                        <td class="px-3 py-2 uppercase">
                                            {{ $leave->status }}
                                        </td>
                                        <td class="px-3 py-2">
                                            <div class="flex items-center gap-2">
                                                <a href="{{ route('supervisor.leaves.print', $leave->id) }}" target="_blank" class="px-3 py-1 bg-indigo-600 hover:bg-indigo-700 text-white rounded">
                                                    Print / Doc
                                                </a>
                                                @if($leave->status === 'pending')
                                                    <form method="POST" action="{{ route('supervisor.leaves.approve', $leave->id) }}">
                                                        @csrf
                                                        <button type="submit" class="px-3 py-1 bg-green-600 hover:bg-green-700 text-white rounded">
                                                            Approve
                                                        </button>
                                                    </form>
                                                    <form method="POST" action="{{ route('supervisor.leaves.reject', $leave->id) }}">
                                                        @csrf
                                                        <button type="submit" class="px-3 py-1 bg-red-600 hover:bg-red-700 text-white rounded">
                                                            Reject
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-3 py-6 text-center text-gray-500">
                                            No leave requests found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $leaves->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-supervisor-layout>
