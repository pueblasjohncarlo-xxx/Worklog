<x-supervisor-layout>
    <x-slot name="header">
        Assign New Task
    </x-slot>

    <div class="max-w-4xl mx-auto py-6">
        <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-100">
            <!-- Header -->
            <div class="bg-gradient-to-r from-purple-600 to-indigo-600 px-6 py-4">
                <h3 class="font-bold text-white text-lg">✓ Create New Task</h3>
                <p class="text-purple-100 text-sm mt-1">Assign a task to your student with detailed instructions</p>
            </div>

            <!-- Form -->
            <div class="p-8">
                <form method="POST" action="{{ route('supervisor.tasks.store') }}" class="space-y-6" enctype="multipart/form-data">
                    @csrf

                    <!-- Student Selection -->
                    <div>
                        <label for="assignment_id" class="block text-sm font-semibold text-gray-700 mb-2">👤 Assign To (Student)</label>
                        <select id="assignment_id" name="assignment_id" class="block w-full border border-gray-300 rounded-lg px-4 py-3 text-gray-900 bg-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition shadow-sm" required>
                            <option value="">-- Select a Student --</option>
                            @forelse($assignments as $assignment)
                                <option value="{{ $assignment->id }}">{{ $assignment->student->name }} ({{ $assignment->company->name ?? 'No Company' }})</option>
                            @empty
                                <option value="" disabled>No students assigned to you</option>
                            @endforelse
                        </select>
                        @error('assignment_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <!-- Semester Selection -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-3">📅 Semester</label>
                        <div class="grid grid-cols-2 gap-3">
                            <label class="flex items-center p-3 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-indigo-400 hover:bg-indigo-50 transition" id="sem1-label">
                                <input type="radio" name="semester" value="1st" class="w-4 h-4 text-indigo-600 rounded" required>
                                <span class="ml-2 font-medium text-gray-900">1st Semester</span>
                            </label>
                            <label class="flex items-center p-3 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-indigo-400 hover:bg-indigo-50 transition" id="sem2-label">
                                <input type="radio" name="semester" value="2nd" class="w-4 h-4 text-indigo-600 rounded" required>
                                <span class="ml-2 font-medium text-gray-900">2nd Semester</span>
                            </label>
                        </div>
                        @error('semester')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <!-- Task Title -->
                    <div>
                        <label for="title" class="block text-sm font-semibold text-gray-700 mb-2">📝 Task Title</label>
                        <input type="text" name="title" id="title" class="block w-full border border-gray-300 rounded-lg px-4 py-3 text-gray-900 bg-white placeholder-gray-500 focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition shadow-sm" placeholder="e.g. Complete System Documentation" value="{{ old('title') }}" required>
                        @error('title')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <!-- Description -->
                    <div>
                        <label for="description" class="block text-sm font-semibold text-gray-700 mb-2">📄 Description</label>
                        <textarea id="description" name="description" rows="5" class="block w-full border border-gray-300 rounded-lg px-4 py-3 text-gray-900 bg-white placeholder-gray-500 focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition shadow-sm" placeholder="Provide detailed instructions for the task..." required>{{ old('description') }}</textarea>
                        @error('description')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <!-- Due Date -->
                    <div>
                        <label for="due_date" class="block text-sm font-semibold text-gray-700 mb-2">📆 Due Date</label>
                        <input type="date" name="due_date" id="due_date" class="block w-full border border-gray-300 rounded-lg px-4 py-3 text-gray-900 bg-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition shadow-sm" value="{{ old('due_date') }}" required>
                        @error('due_date')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <!-- Attachment -->
                    <div>
                        <label for="attachment" class="block text-sm font-semibold text-gray-700 mb-2">📎 Attachment (Optional)</label>
                        <input type="file" name="attachment" id="attachment" class="block w-full border border-gray-300 rounded-lg px-4 py-3 text-gray-500 bg-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition shadow-sm file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-600 hover:file:bg-indigo-100 cursor-pointer" accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.jpg,.jpeg,.png,.txt,.zip">
                        <p class="mt-2 text-xs text-gray-500">✓ Accepted: PDF, DOC, DOCX, XLS, XLSX, PPT, PPTX, Images, TXT, ZIP (Max 10MB)</p>
                        @error('attachment')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <!-- Buttons -->
                    <div class="flex justify-between pt-4 border-t">
                        <a href="{{ route('supervisor.dashboard') }}" class="px-6 py-3 text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg font-medium transition">← Cancel</a>
                        <button type="submit" class="px-6 py-3 bg-indigo-600 text-white rounded-lg font-medium hover:bg-indigo-700 transition shadow-md">✓ Assign Task</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const radios = document.querySelectorAll('input[name="semester"]');
            const sem1Label = document.getElementById('sem1-label');
            const sem2Label = document.getElementById('sem2-label');
            
            radios.forEach(radio => {
                radio.addEventListener('change', (e) => {
                    if (e.target.value === '1st') {
                        sem1Label.classList.add('border-indigo-500', 'bg-indigo-50');
                        sem2Label.classList.remove('border-indigo-500', 'bg-indigo-50');
                    } else {
                        sem2Label.classList.add('border-indigo-500', 'bg-indigo-50');
                        sem1Label.classList.remove('border-indigo-500', 'bg-indigo-50');
                    }
                });
            });
        });
    </script>
</x-supervisor-layout>