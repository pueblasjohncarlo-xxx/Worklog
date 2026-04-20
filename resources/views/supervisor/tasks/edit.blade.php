<x-supervisor-layout>
    <x-slot name="header">
        Edit Task
    </x-slot>

    <div class="max-w-4xl mx-auto py-6">
        <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-100">
            <div class="bg-gradient-to-r from-purple-600 to-indigo-600 px-6 py-4">
                <h3 class="font-bold text-white text-lg">✓ Update Task</h3>
                <p class="text-purple-100 text-sm mt-1">Edit task details before it is submitted</p>
            </div>

            <div class="p-8">
                @if ($errors->any())
                    <div class="mb-6 rounded-lg border border-red-200 bg-red-50 p-3 text-sm text-red-800">
                        {{ $errors->first() }}
                    </div>
                @endif

                <form method="POST" action="{{ route('supervisor.tasks.update', $task) }}" class="space-y-6" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">👤 Assigned To</label>
                        <div class="block w-full border border-gray-200 rounded-lg px-4 py-3 bg-gray-50 text-gray-800">
                            {{ $task->assignment?->student?->name ?? 'Student' }}
                            <span class="text-gray-500">—</span>
                            {{ $task->assignment?->company?->name ?? 'N/A' }}
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-3">📅 Semester</label>
                        <div class="grid grid-cols-2 gap-3">
                            <label class="flex items-center p-3 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-indigo-400 hover:bg-indigo-50 transition" id="sem1-label">
                                <input type="radio" name="semester" value="1st" class="w-4 h-4 text-indigo-600 rounded" required @checked(old('semester', $task->semester) === '1st')>
                                <span class="ml-2 font-medium text-gray-900">1st Semester</span>
                            </label>
                            <label class="flex items-center p-3 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-indigo-400 hover:bg-indigo-50 transition" id="sem2-label">
                                <input type="radio" name="semester" value="2nd" class="w-4 h-4 text-indigo-600 rounded" required @checked(old('semester', $task->semester) === '2nd')>
                                <span class="ml-2 font-medium text-gray-900">2nd Semester</span>
                            </label>
                        </div>
                        @error('semester')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="title" class="block text-sm font-semibold text-gray-700 mb-2">📝 Task Title</label>
                        <input type="text" name="title" id="title" class="block w-full border border-gray-300 rounded-lg px-4 py-3 text-gray-900 bg-white placeholder-gray-500 focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition shadow-sm" value="{{ old('title', $task->title) }}" required>
                        @error('title')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="description" class="block text-sm font-semibold text-gray-700 mb-2">📄 Description</label>
                        <textarea id="description" name="description" rows="5" class="block w-full border border-gray-300 rounded-lg px-4 py-3 text-gray-900 bg-white placeholder-gray-500 focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition shadow-sm">{{ old('description', $task->description) }}</textarea>
                        @error('description')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="due_date" class="block text-sm font-semibold text-gray-700 mb-2">📆 Due Date</label>
                        <input type="date" name="due_date" id="due_date" class="block w-full border border-gray-300 rounded-lg px-4 py-3 text-gray-900 bg-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition shadow-sm" value="{{ old('due_date', optional($task->due_date)->format('Y-m-d')) }}">
                        @error('due_date')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="attachment" class="block text-sm font-semibold text-gray-700 mb-2">📎 Replace Task Attachment (Optional)</label>
                        <input type="file" name="attachment" id="attachment" class="block w-full border border-gray-300 rounded-lg px-4 py-3 text-gray-500 bg-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition shadow-sm file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-600 hover:file:bg-indigo-100 cursor-pointer" accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.jpg,.jpeg,.png,.txt,.zip">
                        <p class="mt-2 text-xs text-gray-500">✓ Accepted: PDF, DOC, DOCX, XLS, XLSX, PPT, PPTX, Images, TXT, ZIP (Max 10MB)</p>
                        @error('attachment')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div class="flex justify-between pt-4 border-t">
                        <a href="{{ route('supervisor.tasks.index') }}" class="px-6 py-3 text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg font-medium transition">← Cancel</a>
                        <button type="submit" class="px-6 py-3 bg-indigo-600 text-white rounded-lg font-medium hover:bg-indigo-700 transition shadow-md">✓ Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const sem1Label = document.getElementById('sem1-label');
            const sem2Label = document.getElementById('sem2-label');
            const sem1 = document.querySelector('input[name="semester"][value="1st"]');
            const sem2 = document.querySelector('input[name="semester"][value="2nd"]');

            function applySelected() {
                if (sem1 && sem1.checked) {
                    sem1Label.classList.add('border-indigo-500', 'bg-indigo-50');
                    sem2Label.classList.remove('border-indigo-500', 'bg-indigo-50');
                } else if (sem2 && sem2.checked) {
                    sem2Label.classList.add('border-indigo-500', 'bg-indigo-50');
                    sem1Label.classList.remove('border-indigo-500', 'bg-indigo-50');
                }
            }

            [sem1, sem2].forEach(r => r && r.addEventListener('change', applySelected));
            applySelected();
        });
    </script>
</x-supervisor-layout>
