<x-app-layout>
    <x-slot name="header">
        <h2 class="text-white">Leave Request</h2>
    </x-slot>

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-6">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Leave Request Form -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm overflow-hidden">
                    <div class="bg-indigo-600 px-6 py-4">
                        <h3 class="text-lg font-bold text-white">Submit Leave Request</h3>
                    </div>

                    <div class="p-6">
                        @if ($errors->any())
                            <div class="mb-6 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg">
                                <p class="text-red-800 dark:text-red-300 font-semibold mb-2">Error:</p>
                                @foreach ($errors->all() as $error)
                                    <p class="text-red-700 dark:text-red-400 text-sm">• {{ $error }}</p>
                                @endforeach
                            </div>
                        @endif

                        @if (session('success'))
                            <div class="mb-6 p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg">
                                <p class="text-green-800 dark:text-green-300">{{ session('success') }}</p>
                            </div>
                        @endif

                        <form action="{{ route('student.leaves.store') }}" method="POST">
                            @csrf

                            @if (!$assignment)
                                <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4">
                                    <p class="text-yellow-800 dark:text-yellow-300 text-sm">
                                        No active assignment found. Please contact your coordinator.
                                    </p>
                                </div>
                            @else
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <!-- Leave Type -->
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                            Leave Type <span class="text-red-500">*</span>
                                        </label>
                                        <select name="type" required class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                                            <option value="">Select Leave Type</option>
                                            <option value="Sick Leave" {{ old('type') == 'Sick Leave' ? 'selected' : '' }}>Sick Leave</option>
                                            <option value="Vacation Leave" {{ old('type') == 'Vacation Leave' ? 'selected' : '' }}>Vacation Leave</option>
                                            <option value="Emergency Leave" {{ old('type') == 'Emergency Leave' ? 'selected' : '' }}>Emergency Leave</option>
                                            <option value="Personal Leave" {{ old('type') == 'Personal Leave' ? 'selected' : '' }}>Personal Leave</option>
                                            <option value="Bereavement Leave" {{ old('type') == 'Bereavement Leave' ? 'selected' : '' }}>Bereavement Leave</option>
                                        </select>
                                        @error('type')
                                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <!-- Start Date -->
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                            Start Date <span class="text-red-500">*</span>
                                        </label>
                                        <input type="date" name="start_date" required value="{{ old('start_date') }}" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                                        @error('start_date')
                                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <!-- End Date -->
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                            End Date <span class="text-red-500">*</span>
                                        </label>
                                        <input type="date" name="end_date" required value="{{ old('end_date') }}" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                                        @error('end_date')
                                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <!-- Date Filed -->
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                            Date Filed
                                        </label>
                                        <input type="date" name="date_filed" value="{{ old('date_filed', now()->format('Y-m-d')) }}" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                                        @error('date_filed')
                                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Company Name -->
                                <div class="mt-6">
                                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                        Company Name
                                    </label>
                                    <input type="text" name="company_name" value="{{ old('company_name', $assignment?->company?->name ?? '') }}" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent" readonly>
                                </div>

                                <!-- Reason -->
                                <div class="mt-6">
                                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                        Reason
                                    </label>
                                    <textarea name="reason" rows="4" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent">{{ old('reason') }}</textarea>
                                    @error('reason')
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Submit Button -->
                                <div class="mt-8 flex gap-4">
                                    <button type="submit" class="px-6 py-2 bg-indigo-600 text-white font-semibold rounded-lg hover:bg-indigo-700 transition-colors">
                                        Submit Leave Request
                                    </button>
                                    <button type="reset" class="px-6 py-2 bg-gray-300 dark:bg-gray-600 text-gray-800 dark:text-white font-semibold rounded-lg hover:bg-gray-400 dark:hover:bg-gray-700 transition-colors">
                                        Clear
                                    </button>
                                </div>
                            @endif
                        </form>
                    </div>
                </div>
            </div>

            <!-- Sidebar - Leave Statistics -->
            <div class="space-y-6">
                <!-- Status Summary -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm overflow-hidden">
                    <div class="bg-blue-600 px-6 py-4">
                        <h3 class="text-lg font-bold text-white">Leave Requests</h3>
                    </div>
                    <div class="p-6 space-y-4">
                        @php
                            $pending = $leaves->where('status', 'pending')->count();
                            $approved = $leaves->where('status', 'approved')->count();
                            $rejected = $leaves->where('status', 'rejected')->count();
                        @endphp
                        <div class="flex items-center justify-between p-3 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg">
                            <span class="text-sm font-semibold text-yellow-800 dark:text-yellow-300">Pending</span>
                            <span class="text-2xl font-bold text-yellow-600">{{ $pending }}</span>
                        </div>
                        <div class="flex items-center justify-between p-3 bg-green-50 dark:bg-green-900/20 rounded-lg">
                            <span class="text-sm font-semibold text-green-800 dark:text-green-300">Approved</span>
                            <span class="text-2xl font-bold text-green-600">{{ $approved }}</span>
                        </div>
                        <div class="flex items-center justify-between p-3 bg-red-50 dark:bg-red-900/20 rounded-lg">
                            <span class="text-sm font-semibold text-red-800 dark:text-red-300">Rejected</span>
                            <span class="text-2xl font-bold text-red-600">{{ $rejected }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Leave History -->
        <div class="mt-8 bg-white dark:bg-gray-800 rounded-lg shadow-sm overflow-hidden">
            <div class="bg-gray-100 dark:bg-gray-700 px-6 py-4 border-b border-gray-200 dark:border-gray-600">
                <h3 class="text-lg font-bold text-gray-800 dark:text-white">Leave History</h3>
            </div>

            @if ($leaves->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-900/50">
                            <tr>
                                <th class="px-6 py-4 font-semibold text-gray-700 dark:text-gray-300">Leave Type</th>
                                <th class="px-6 py-4 font-semibold text-gray-700 dark:text-gray-300">Start Date</th>
                                <th class="px-6 py-4 font-semibold text-gray-700 dark:text-gray-300">End Date</th>
                                <th class="px-6 py-4 font-semibold text-gray-700 dark:text-gray-300">Status</th>
                                <th class="px-6 py-4 font-semibold text-gray-700 dark:text-gray-300">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach ($leaves as $leave)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                    <td class="px-6 py-4 font-semibold text-gray-900 dark:text-white">{{ $leave->type }}</td>
                                    <td class="px-6 py-4 text-gray-600 dark:text-gray-400">{{ $leave->start_date->format('M d, Y') }}</td>
                                    <td class="px-6 py-4 text-gray-600 dark:text-gray-400">{{ $leave->end_date->format('M d, Y') }}</td>
                                    <td class="px-6 py-4">
                                        <span class="px-3 py-1 rounded-full text-xs font-semibold
                                            {{ $leave->status === 'approved' ? 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-300' : '' }}
                                            {{ $leave->status === 'rejected' ? 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-300' : '' }}
                                            {{ $leave->status === 'pending' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-300' : '' }}
                                        ">
                                            {{ ucfirst($leave->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <a href="{{ route('student.leaves.print', $leave) }}" target="_blank" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 font-semibold">
                                            View
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                    {{ $leaves->links() }}
                </div>
            @else
                <div class="p-8 text-center text-gray-500 dark:text-gray-400">
                    <p>No leave requests found. Submit your first leave request above.</p>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
