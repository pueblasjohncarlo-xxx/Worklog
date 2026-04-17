<x-supervisor-layout>
    <x-slot name="header">
        Concerns & Incidents
    </x-slot>

    <div class="py-6 space-y-6">
        @if (session('status'))
            <div class="rounded-lg border border-emerald-300 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800">
                {{ session('status') }}
            </div>
        @endif

        <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-100">
            <div class="bg-gray-50 px-6 py-4 border-b border-gray-200 flex items-center justify-between gap-3">
                <h3 class="font-bold text-gray-800 text-lg">Reports</h3>
                <a href="{{ route('supervisor.concerns.create') }}" class="inline-flex items-center justify-center px-4 py-2 rounded-lg bg-indigo-600 text-white font-semibold hover:bg-indigo-700">
                    New Report
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-3 font-bold text-gray-500 uppercase tracking-wider">Student</th>
                            <th class="px-6 py-3 font-bold text-gray-500 uppercase tracking-wider">Type</th>
                            <th class="px-6 py-3 font-bold text-gray-500 uppercase tracking-wider">Title</th>
                            <th class="px-6 py-3 font-bold text-gray-500 uppercase tracking-wider">Occurred</th>
                            <th class="px-6 py-3 font-bold text-gray-500 uppercase tracking-wider">Created</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                        @forelse ($concerns as $concern)
                            @php
                                $badge = ($concern->type ?? 'concern') === 'incident'
                                    ? 'bg-rose-100 text-rose-700'
                                    : 'bg-amber-100 text-amber-700';
                            @endphp
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="font-bold text-gray-900">{{ $concern->student?->name ?? 'N/A' }}</div>
                                    <div class="text-xs text-gray-500">{{ $concern->assignment?->company?->name ?? 'N/A' }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold {{ $badge }}">
                                        {{ ucfirst($concern->type ?? 'concern') }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="font-semibold text-gray-900">{{ $concern->title }}</div>
                                    <div class="text-xs text-gray-500 mt-1 line-clamp-2">{{ $concern->details }}</div>
                                </td>
                                <td class="px-6 py-4 text-gray-700">
                                    {{ $concern->occurred_on ? $concern->occurred_on->format('M d, Y') : '—' }}
                                </td>
                                <td class="px-6 py-4 text-gray-700">
                                    {{ $concern->created_at ? $concern->created_at->format('M d, Y g:i A') : '—' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-10 text-center text-gray-500 font-medium">
                                    No reports yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="p-4 border-t border-gray-200">
                {{ $concerns->links() }}
            </div>
        </div>
    </div>
</x-supervisor-layout>
