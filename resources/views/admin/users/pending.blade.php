<x-admin-layout>
    <x-slot name="header">
        Pending Approvals
    </x-slot>

    <div class="space-y-6">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900 dark:text-gray-100">
                <h3 class="font-semibold mb-4 text-lg">Registration Requests</h3>

                @if (session('status'))
                    <div class="mb-4 p-4 bg-green-100 text-green-700 rounded-lg">
                        {{ session('status') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mb-4 p-4 bg-red-100 text-red-700 rounded-lg">
                        {{ $errors->first() }}
                    </div>
                @endif

                @if ($users->isEmpty())
                    <div class="text-center py-8 text-gray-500">
                        No pending approvals found.
                    </div>
                @else
                    <form action="{{ route('admin.users.bulk-action') }}" method="POST" id="bulk-action-form">
                        @csrf
                        <input type="hidden" name="action" id="bulk-action-input" value="">
                        <div class="flex justify-between items-center mb-4">
                            <div class="flex items-center gap-2">
                                <input type="checkbox" id="select-all" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                <label for="select-all" class="text-sm text-gray-600 dark:text-gray-400">Select All</label>
                            </div>
                            <div class="flex gap-2">
                                <button type="submit" name="action" value="approve" class="inline-flex items-center gap-1.5 px-4 py-2 bg-emerald-600 text-white text-xs font-bold uppercase rounded-lg hover:bg-emerald-700 transition-all duration-200 shadow-md">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                    </svg>
                                    Approve Selected
                                </button>
                                <button type="button" id="bulk-reject-btn" class="inline-flex items-center gap-1.5 px-4 py-2 bg-rose-600 text-white text-xs font-bold uppercase rounded-lg hover:bg-rose-700 transition-all duration-200 shadow-md">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                    Reject Selected
                                </button>
                            </div>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-900/50">
                                    <tr>
                                        <th class="px-6 py-3 w-4"></th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Name</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Email</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Role</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Section/Dept</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Requested At</th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach ($users as $user)
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <input type="checkbox" name="user_ids[]" value="{{ $user->id }}" class="user-checkbox rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="font-bold text-gray-900 dark:text-white">{{ $user->name }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                                {{ $user->email }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <x-user-role-badge :role="$user->role" />
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                                {{ $user->section ?? ($user->department ?? 'N/A') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                                {{ $user->updated_at->diffForHumans() }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                <div class="flex justify-end gap-2">
                                                    <button type="button"
                                                       data-form-id="approve-form-{{ $user->id }}"
                                                       class="js-user-approve inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-600 text-white text-xs font-bold uppercase rounded-lg hover:bg-emerald-700 transition-all duration-200 shadow-md">
                                                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                                        </svg>
                                                        Approve
                                                    </button>
                                                    <button type="button"
                                                       data-form-id="reject-form-{{ $user->id }}"
                                                       class="js-user-reject inline-flex items-center gap-1.5 px-3 py-1.5 bg-rose-600 text-white text-xs font-bold uppercase rounded-lg hover:bg-rose-700 transition-all duration-200 shadow-md">
                                                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
                                                        </svg>
                                                        Reject
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </form>

                    <!-- Hidden individual forms to keep existing route compatibility -->
                    @foreach ($users as $user)
                        <form id="approve-form-{{ $user->id }}" action="{{ route('admin.users.approve', $user) }}" method="POST" style="display: none;">@csrf</form>
                        <form id="reject-form-{{ $user->id }}" action="{{ route('admin.users.reject', $user) }}" method="POST" style="display: none;">@csrf @method('DELETE')</form>
                    @endforeach

                    <script>
                        document.addEventListener('DOMContentLoaded', function () {
                            const selectAll = document.getElementById('select-all');
                            const bulkRejectBtn = document.getElementById('bulk-reject-btn');
                            const bulkActionForm = document.getElementById('bulk-action-form');
                            const bulkActionInput = document.getElementById('bulk-action-input');

                            if (selectAll) {
                                selectAll.addEventListener('change', function() {
                                    const checkboxes = document.querySelectorAll('.user-checkbox');
                                    checkboxes.forEach(checkbox => checkbox.checked = this.checked);
                                });
                            }

                            if (bulkRejectBtn && bulkActionForm && bulkActionInput) {
                                bulkRejectBtn.addEventListener('click', function () {
                                    const selected = document.querySelectorAll('.user-checkbox:checked');
                                    if (selected.length === 0) {
                                        alert('Please select at least one user to reject.');
                                        return;
                                    }

                                    const confirmed = confirm('Are you sure you want to reject selected requests?');
                                    if (!confirmed) {
                                        return;
                                    }

                                    console.debug('[PendingApprovals] Bulk reject confirmed for users:', selected.length);
                                    bulkActionInput.value = 'reject';
                                    bulkActionForm.submit();
                                });
                            }

                            document.querySelectorAll('.js-user-approve').forEach(function (button) {
                                button.addEventListener('click', function () {
                                    const form = document.getElementById(button.dataset.formId);
                                    if (!form) {
                                        console.error('[PendingApprovals] Approve form not found:', button.dataset.formId);
                                        return;
                                    }

                                    console.debug('[PendingApprovals] Approve submit:', button.dataset.formId);
                                    form.submit();
                                });
                            });

                            document.querySelectorAll('.js-user-reject').forEach(function (button) {
                                button.addEventListener('click', function () {
                                    const form = document.getElementById(button.dataset.formId);
                                    if (!form) {
                                        console.error('[PendingApprovals] Reject form not found:', button.dataset.formId);
                                        return;
                                    }

                                    const confirmed = confirm('Are you sure you want to reject this request?');
                                    if (!confirmed) {
                                        return;
                                    }

                                    console.debug('[PendingApprovals] Reject submit:', button.dataset.formId);
                                    form.submit();
                                });
                            });
                        });
                    </script>
                @endif
            </div>
        </div>
    </div>
</x-admin-layout>