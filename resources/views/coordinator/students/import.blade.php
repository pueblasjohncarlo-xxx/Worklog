<x-coordinator-layout>
    <x-slot name="header">
        Import OJT Students
    </x-slot>

    <div class="space-y-6">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900 dark:text-gray-100">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold">Bulk Upload Students</h3>
                    <div class="flex gap-4">
                        <a href="{{ route('coordinator.students.import.template') }}" class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">
                            Download CSV Template
                        </a>
                    </div>
                </div>

                @if (session('status'))
                    <div class="mb-4 text-sm font-medium text-green-600">
                        {{ session('status') }}
                    </div>
                @endif

                @if (session('import_errors'))
                    <div class="mb-4 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-md">
                        <h4 class="text-red-800 dark:text-red-400 font-semibold mb-2">Import Errors:</h4>
                        <ul class="list-disc list-inside text-sm text-red-600 dark:text-red-300">
                            @foreach (session('import_errors') as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="mb-6 p-4 bg-blue-50 dark:bg-blue-900/20 border-l-4 border-blue-500 text-blue-700 dark:text-blue-300 text-sm">
                    <p class="font-bold mb-1">Supported Formats:</p>
                    <p>You can upload <strong>CSV</strong>, <strong>Excel (.xlsx)</strong>, or <strong>Excel 97-2003 (.xls)</strong> files.</p>
                </div>

                <form method="POST" action="{{ route('coordinator.students.import.store') }}" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    <div>
                        <x-input-label for="file" :value="__('Import File (CSV or Excel)')" />
                        <input id="file" name="file" type="file" accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" class="mt-1 block w-full text-sm text-gray-500 dark:text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100" required />
                        <x-input-error :messages="$errors->get('file')" class="mt-2" />
                    </div>

                    <div class="flex items-center gap-4">
                        <x-primary-button>{{ __('Upload & Import') }}</x-primary-button>
                        <a href="{{ route('coordinator.dashboard') }}" class="text-sm text-gray-600 dark:text-gray-400 hover:underline">Cancel</a>
                    </div>
                </form>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900 dark:text-gray-100">
                <h3 class="text-lg font-semibold mb-4">Manual Guide: Adding Students</h3>
                <div class="prose dark:prose-invert max-w-none text-sm space-y-4">
                    <div>
                        <h4 class="font-bold">1. Bulk Import (Recommended)</h4>
                        <p>To add multiple students at once, use the form above. Download the CSV template, fill in the student names, emails, and temporary passwords, then upload the file here. The system will automatically create accounts for all students in the list.</p>
                    </div>
                    <div>
                        <h4 class="font-bold">2. Admin Management</h4>
                        <p>The System Administrator has exclusive authority to create student accounts manually from the Admin panel. User registration is restricted to administrators only to ensure proper account management and security oversight.</p>
                    </div>
                    </div>
                    <div class="p-4 bg-indigo-50 dark:bg-indigo-900/20 border-l-4 border-indigo-500 text-indigo-700 dark:text-indigo-300">
                        <p class="font-bold">Note:</p>
                        <p>After accounts are created (via any method), you must go to the "Manage Assignments" section to link students with their respective supervisors and companies to start their OJT work logs.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-coordinator-layout>
