<x-ojt-adviser-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Accomplishment Reports') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead>
                                <tr class="border-b bg-gray-50">
                                    <th class="text-left px-3 py-2">Student</th>
                                    <th class="text-left px-3 py-2">Company</th>
                                    <th class="text-left px-3 py-2">Type</th>
                                    <th class="text-left px-3 py-2">Date</th>
                                    <th class="text-left px-3 py-2">Status</th>
                                    <th class="text-left px-3 py-2">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($workLogs as $log)
                                    <tr class="border-b">
                                        <td class="px-3 py-2">
                                            {{ $log->assignment?->student?->name ?? 'N/A' }}
                                        </td>
                                        <td class="px-3 py-2">
                                            {{ $log->assignment?->company?->name ?? 'N/A' }}
                                        </td>
                                        <td class="px-3 py-2 uppercase">
                                            {{ $log->type }}
                                        </td>
                                        <td class="px-3 py-2">
                                            {{ $log->work_date?->format('M d, Y') ?? 'N/A' }}
                                        </td>
                                        <td class="px-3 py-2 uppercase">
                                            {{ $log->status }}
                                        </td>
                                        <td class="px-3 py-2">
                                            @if($log->attachment_path)
                                                <a href="{{ route('ojt_adviser.worklogs.attachment', $log->id) }}?inline=1" target="_blank" class="text-indigo-600 hover:underline font-semibold">
                                                    View File
                                                </a>
                                                <span class="mx-2 text-gray-300">|</span>
                                                <a href="{{ route('ojt_adviser.worklogs.attachment', $log->id) }}" class="text-emerald-700 hover:underline font-semibold">
                                                    Download
                                                </a>
                                            @else
                                                <span class="text-gray-500">No file</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-3 py-6 text-center text-gray-500">
                                            No accomplishment reports found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $workLogs->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-ojt-adviser-layout>
