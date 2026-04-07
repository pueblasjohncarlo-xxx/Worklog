<x-supervisor-layout>
    <x-slot name="header">
        My Generated Reports
    </x-slot>

    <div class="max-w-4xl mx-auto py-6">
        <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-100">
            <div class="p-12 text-center">
                <div class="mx-auto h-16 w-16 bg-indigo-100 rounded-full flex items-center justify-center mb-4">
                    <svg class="h-8 w-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">No Reports Saved</h3>
                <p class="text-gray-500 mb-6">You haven't generated and saved any reports yet.</p>
                <a href="{{ route('supervisor.reports.create') }}" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                    Generate New Report
                </a>
            </div>
        </div>
    </div>
</x-supervisor-layout>