<x-supervisor-layout>
    <x-slot name="header">
        Create Announcement
    </x-slot>

    <div class="max-w-2xl mx-auto bg-white dark:bg-gray-800 p-8 rounded-lg shadow-md">
        <h2 class="text-2xl font-bold mb-6 text-gray-800 dark:text-gray-200">Compose New Message</h2>

        <form method="POST" action="{{ route('supervisor.announcements.store') }}" class="space-y-6" enctype="multipart/form-data">
            @csrf

            <!-- Title -->
            <div>
                <x-input-label for="title" :value="__('Title')" />
                <x-text-input id="title" class="block mt-1 w-full" type="text" name="title" :value="old('title')" required autofocus />
                <x-input-error :messages="$errors->get('title')" class="mt-2" />
            </div>

            <!-- Type -->
            <div>
                <x-input-label for="type" :value="__('Type')" class="font-semibold" />
                <select id="type" name="type" class="block mt-1 w-full rounded-md shadow-sm border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 text-gray-900 font-medium focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="announcement">Announcement</option>
                    <option value="update">Update</option>
                </select>
                <x-input-error :messages="$errors->get('type')" class="mt-2" />
            </div>

            <!-- Audience (Hidden - Always OJT Students) -->
            <input type="hidden" name="audience" value="students">
            <div>
                <x-input-label for="audience_display" :value="__('Send To')" class="font-semibold" />
                <div class="block mt-1 w-full p-3 bg-gray-100 dark:bg-gray-700 rounded-md text-gray-700 dark:text-gray-100 font-medium border border-gray-300 dark:border-gray-600">
                    Assigned OJT Students
                </div>
            </div>

            <!-- Content -->
            <div>
                <x-input-label for="content" :value="__('Message Content')" class="font-semibold" />
                <textarea id="content" name="content" rows="6" class="block mt-1 w-full rounded-md shadow-sm border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 text-gray-900 font-medium focus:border-indigo-500 focus:ring-indigo-500" required>{{ old('content') }}</textarea>
                <x-input-error :messages="$errors->get('content')" class="mt-2" />
            </div>

            <!-- Attachment -->
            <div>
                <x-input-label for="attachment" :value="__('Attachment (Optional)')" class="font-semibold" />
                <input id="attachment" name="attachment" type="file" class="block w-full text-base font-medium text-gray-700 dark:text-gray-200 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 dark:file:bg-indigo-900 dark:file:text-indigo-300" />
                <p class="mt-2 text-base text-gray-600 dark:text-gray-300 font-medium">Allowed files: PDF, DOC, DOCX, XLS, XLSX, PPT, PPTX, JPG, PNG, ZIP (Max: 10MB)</p>
                <x-input-error :messages="$errors->get('attachment')" class="mt-2" />
            </div>

            <div class="flex items-center justify-end mt-4">
                <a href="{{ route('supervisor.announcements.index') }}" class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 underline mr-4">
                    Cancel
                </a>
                <x-primary-button>
                    {{ __('Post Announcement') }}
                </x-primary-button>
            </div>
        </form>
    </div>
</x-supervisor-layout>
