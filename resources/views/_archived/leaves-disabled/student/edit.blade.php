<x-student-layout>
    <x-slot name="header">
        <h2 class="text-white">Edit Leave Request</h2>
    </x-slot>

    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm overflow-hidden">
            <div class="px-6 py-4 bg-amber-600">
                <h3 class="text-white font-bold">Update Leave ({{ ucfirst($leave->status) }})</h3>
            </div>

            <form action="{{ route('student.leaves.update', $leave) }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-4">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold mb-1">Leave Type *</label>
                        <select name="type" class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700" required>
                            @foreach (['Sick Leave','Discretionary','Maternity','Exam','Bereavement','No Pay Leave'] as $type)
                                <option value="{{ $type }}" @selected(old('type', $leave->type) === $type)>{{ $type }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold mb-1">Attachment (optional)</label>
                        <input type="file" name="attachment" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx" class="w-full text-sm">
                        @if ($leave->attachment_path)
                            <a href="{{ Storage::url($leave->attachment_path) }}" target="_blank" class="text-xs text-blue-600 font-semibold">Current attachment</a>
                        @endif
                    </div>

                    <div>
                        <label class="block text-sm font-semibold mb-1">Start Date *</label>
                        <input type="date" name="start_date" value="{{ old('start_date', optional($leave->start_date)->format('Y-m-d')) }}" class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700" required>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold mb-1">End Date *</label>
                        <input type="date" name="end_date" value="{{ old('end_date', optional($leave->end_date)->format('Y-m-d')) }}" class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700" required>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold mb-1">Reason / Description *</label>
                    <textarea name="reason" rows="5" class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700" required>{{ old('reason', $leave->reason) }}</textarea>
                </div>

                <div class="flex gap-3">
                    <button type="submit" name="action" value="draft" class="px-4 py-2 rounded-md bg-gray-600 text-white text-sm font-semibold hover:bg-gray-700">Save Draft</button>
                    <button type="submit" name="action" value="submit" onclick="return confirm('Submit this updated leave request?');" class="px-4 py-2 rounded-md bg-indigo-600 text-white text-sm font-semibold hover:bg-indigo-700">Submit Updated Request</button>
                    <a href="{{ route('student.leaves.index') }}" class="px-4 py-2 rounded-md bg-slate-200 text-slate-800 text-sm font-semibold">Back</a>
                </div>
            </form>
        </div>
    </div>
</x-student-layout>
