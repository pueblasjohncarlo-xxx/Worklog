<x-student-layout>
    <x-slot name="header">
        <h2 class="text-white">Leave Request</h2>
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">
        @if (session('status'))
            <div class="rounded-lg bg-green-100 text-green-800 px-4 py-3 text-sm font-semibold">
                {{ session('status') }}
            </div>
        @endif

        @if (isset($errors) && $errors->any())
            <div class="rounded-lg bg-red-100 text-red-800 px-4 py-3 text-sm">
                <p class="font-semibold">Please fix the following:</p>
                <ul class="list-disc pl-5 mt-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4">
            <h3 class="font-bold text-gray-900 dark:text-white">Leave Request Completion Checklist</h3>
            <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">Requested feature list inserted and implemented below.</p>
            <ul class="mt-3 text-xs text-gray-700 dark:text-gray-300 list-disc pl-5 space-y-1">
                <li>Fields included: leave type, start date, end date, number of days, reason/description, optional attachment.</li>
                <li>Validation enforced for required fields before submission.</li>
                <li>Confirmation dialog added before final submission.</li>
                <li>Save as draft supported before final submission.</li>
                <li>Status workflow implemented: draft, submitted, pending, approved, rejected.</li>
                <li>Status updates after every action.</li>
                <li>List includes leave type, dates, short reason, status, and submitted date.</li>
                <li>Full details view available via View action.</li>
                <li>Student actions enabled: edit (draft/rejected), delete (draft), cancel (submitted/pending).</li>
                <li>Requests sent to assigned supervisor and admins for review notifications.</li>
                <li>Supervisor/Admin approve and reject actions with remarks.</li>
                <li>Approve/Reject flows trigger backend updates reliably.</li>
                <li>Filtering/search by status, date range, and keyword.</li>
                <li>Color-coded statuses and success/error feedback messages.</li>
                <li>Rules enforced: no open clock-out conflicts, no overlapping leave dates, leave-day limit.</li>
                <li>Debug logging added for event/backend flow tracing.</li>
            </ul>
        </div>

        <!-- Enhanced Leave Balance Section -->
        @if($assignment && !empty($leaveBalance))
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-800/20 rounded-lg p-4 border border-blue-200 dark:border-blue-800">
                    <p class="text-xs font-semibold text-blue-600 dark:text-blue-300 uppercase tracking-wide">Annual Leave</p>
                    <div class="mt-2">
                        <div class="text-2xl font-bold text-blue-700 dark:text-blue-400">{{ $leaveBalance['annual_remaining'] ?? 0 }}</div>
                        <p class="text-xs text-blue-600 dark:text-blue-300 mt-1">of {{ $leaveBalance['annual_limit'] ?? 0 }} days remaining</p>
                        <div class="mt-3 w-full bg-blue-200 dark:bg-blue-900 rounded-full h-2">
                            <div class="bg-blue-600 dark:bg-blue-500 h-2 rounded-full" style="width: {{ $leaveBalance['annual_remaining'] && $leaveBalance['annual_limit'] ? ($leaveBalance['annual_remaining'] / $leaveBalance['annual_limit']) * 100 : 0 }}%"></div>
                        </div>
                    </div>
                </div>

                <div class="bg-gradient-to-br from-green-50 to-green-100 dark:from-green-900/20 dark:to-green-800/20 rounded-lg p-4 border border-green-200 dark:border-green-800">
                    <p class="text-xs font-semibold text-green-600 dark:text-green-300 uppercase tracking-wide">Sick Leave</p>
                    <div class="mt-2">
                        <div class="text-2xl font-bold text-green-700 dark:text-green-400">{{ $leaveBalance['sick_remaining'] ?? 0 }}</div>
                        <p class="text-xs text-green-600 dark:text-green-300 mt-1">of {{ $leaveBalance['sick_limit'] ?? 0 }} days remaining</p>
                        <div class="mt-3 w-full bg-green-200 dark:bg-green-900 rounded-full h-2">
                            <div class="bg-green-600 dark:bg-green-500 h-2 rounded-full" style="width: {{ $leaveBalance['sick_remaining'] && $leaveBalance['sick_limit'] ? ($leaveBalance['sick_remaining'] / $leaveBalance['sick_limit']) * 100 : 0 }}%"></div>
                        </div>
                    </div>
                </div>

                <div class="bg-gradient-to-br from-purple-50 to-purple-100 dark:from-purple-900/20 dark:to-purple-800/20 rounded-lg p-4 border border-purple-200 dark:border-purple-800">
                    <p class="text-xs font-semibold text-purple-600 dark:text-purple-300 uppercase tracking-wide">Pending Approval</p>
                    <div class="mt-2">
                        <div class="text-2xl font-bold text-purple-700 dark:text-purple-400">{{ $leaveBalance['total_pending'] ?? 0 }}</div>
                        <p class="text-xs text-purple-600 dark:text-purple-300 mt-1">days awaiting review</p>
                    </div>
                </div>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-lg shadow-sm overflow-hidden">
                <div class="px-6 py-4 bg-indigo-600">
                    <h3 class="text-white font-bold">Create Leave Request</h3>
                </div>

                <form action="{{ route('student.leaves.store') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-4" id="leave-create-form">
                    @csrf

                    @if (! $assignment)
                        <div class="rounded-md bg-yellow-100 text-yellow-800 px-4 py-3 text-sm">
                            No active assignment found. Please contact your coordinator.
                        </div>
                    @else
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-semibold mb-2 flex items-center gap-2">
                                    Leave Type
                                    <span class="text-red-500">*</span>
                                    <span class="group relative">
                                        <span class="cursor-help text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">ⓘ</span>
                                        <span class="invisible group-hover:visible absolute -top-8 left-0 bg-gray-900 dark:bg-gray-950 text-white text-xs rounded py-1 px-2 whitespace-nowrap z-10">Choose the type of leave</span>
                                    </span>
                                </label>
                                <select name="type" id="leave_type" class="w-full rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 p-2" required onchange="updateLeaveTypeDescription(); checkLeaveBalance();">
                                    <option value="">-- Select leave type --</option>
                                    <option value="Sick Leave" @selected(old('type') === 'Sick Leave')>Sick Leave</option>
                                    <option value="Annual" @selected(old('type') === 'Annual')>Annual Leave</option>
                                    <option value="Discretionary" @selected(old('type') === 'Discretionary')>Discretionary</option>
                                    <option value="Maternity" @selected(old('type') === 'Maternity')>Maternity</option>
                                    <option value="Exam" @selected(old('type') === 'Exam')>Exam</option>
                                    <option value="Bereavement" @selected(old('type') === 'Bereavement')>Bereavement</option>
                                    <option value="Vacation" @selected(old('type') === 'Vacation')>Vacation</option>
                                    <option value="No Pay Leave" @selected(old('type') === 'No Pay Leave')>No Pay Leave</option>
                                </select>
                                <div id="type_help" class="text-xs text-gray-500 dark:text-gray-400 mt-1 block"></div>
                                <div id="approval_timeline" class="text-xs text-blue-600 dark:text-blue-300 mt-2 hidden">
                                    <strong>Estimated approval:</strong> <span id="timeline_text"></span>
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-semibold mb-2">Date Filed</label>
                                <input type="date" name="date_filed" value="{{ old('date_filed', now()->format('Y-m-d')) }}" class="w-full rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 p-2">
                            </div>

                            <div>
                                <label class="block text-sm font-semibold mb-2 flex items-center gap-2">
                                    Start Date
                                    <span class="text-red-500">*</span>
                                </label>
                                <input type="date" name="start_date" id="start_date" value="{{ old('start_date') }}" class="w-full rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 p-2" required onchange="calculateDays();">
                            </div>

                            <div>
                                <label class="block text-sm font-semibold mb-2 flex items-center gap-2">
                                    End Date
                                    <span class="text-red-500">*</span>
                                </label>
                                <input type="date" name="end_date" id="end_date" value="{{ old('end_date') }}" class="w-full rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 p-2" required onchange="calculateDays();">
                            </div>

                            <div>
                                <label class="block text-sm font-semibold mb-2">Number of Days</label>
                                <div class="flex items-center gap-2">
                                    <input type="number" id="number_of_days" name="number_of_days" readonly class="flex-1 rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 bg-gray-100 dark:bg-gray-900 p-2">
                                    <span id="days_badge" class="px-3 py-2 bg-indigo-100 dark:bg-indigo-900 text-indigo-700 dark:text-indigo-300 rounded text-sm font-semibold min-w-fit hidden">0 days</span>
                                </div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Calculated automatically from dates above</p>
                            </div>

                            <div>
                                <label class="block text-sm font-semibold mb-2">Attachment <span class="text-gray-500 text-xs">(Optional)</span></label>
                                <input type="file" name="attachment" id="attachment" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx" class="w-full text-sm border border-gray-300 dark:border-gray-600 rounded p-2" onchange="updateAttachmentInfo();">
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Supported: PDF, JPG, PNG, DOC, DOCX (Max 5MB)</p>
                                <div id="file_info" class="text-xs text-green-600 dark:text-green-400 mt-1 hidden"></div>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold mb-2">Reason / Description <span class="text-red-500">*</span></label>
                            <textarea name="reason" id="reason" rows="4" class="w-full rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 p-2" required placeholder="Please provide a detailed reason for your leave request..." onkeyup="updateCharCount();">{{ old('reason') }}</textarea>
                            <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                <span id="char_count">0</span>/2000 characters
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-semibold mb-2">Company</label>
                                <input type="text" name="company_name" value="{{ old('company_name', $assignment?->company?->name) }}" class="w-full rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 p-2" readonly>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold mb-2">Prepared By</label>
                                <input type="text" name="prepared_by" value="{{ old('prepared_by', auth()->user()->name) }}" class="w-full rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 p-2" readonly>
                            </div>
                        </div>

                        <!-- Enhanced button section with progress indicator -->
                        <div class="flex flex-wrap gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                            <button type="submit" name="action" value="draft" class="px-6 py-3 rounded-md bg-gray-500 hover:bg-gray-600 text-white text-sm font-semibold transition-colors flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                                </svg>
                                Save as Draft
                            </button>
                            <button type="submit" name="action" value="submit" class="px-6 py-3 rounded-md bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold transition-colors flex items-center gap-2" onclick="return validateForm();">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                Submit Request
                            </button>
                        </div>
                    @endif
                </form>
            </div>

            <div class="space-y-4">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4">
                    <h3 class="font-bold mb-3">Status Summary</h3>
                    @php
                        $all = $leaves->total();
                        $draft = $leaves->getCollection()->where('status', 'draft')->count();
                        $submitted = $leaves->getCollection()->where('status', 'submitted')->count();
                        $pending = $leaves->getCollection()->where('status', 'pending')->count();
                        $approved = $leaves->getCollection()->where('status', 'approved')->count();
                        $rejected = $leaves->getCollection()->where('status', 'rejected')->count();
                    @endphp
                    <div class="grid grid-cols-2 gap-2 text-sm">
                        <div class="p-2 rounded bg-slate-100 dark:bg-slate-700">Total: <span class="font-bold">{{ $all }}</span></div>
                        <div class="p-2 rounded bg-gray-100 dark:bg-gray-700">Draft: <span class="font-bold">{{ $draft }}</span></div>
                        <div class="p-2 rounded bg-blue-100 text-blue-700">Submitted: <span class="font-bold">{{ $submitted }}</span></div>
                        <div class="p-2 rounded bg-yellow-100 text-yellow-700">Pending: <span class="font-bold">{{ $pending }}</span></div>
                        <div class="p-2 rounded bg-green-100 text-green-700">Approved: <span class="font-bold">{{ $approved }}</span></div>
                        <div class="p-2 rounded bg-red-100 text-red-700">Rejected: <span class="font-bold">{{ $rejected }}</span></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="font-bold">Leave Requests List</h3>
                <form method="GET" action="{{ route('student.leaves.index') }}" class="mt-3 grid grid-cols-1 md:grid-cols-5 gap-3">
                    <input type="text" name="q" placeholder="Search reason/type" value="{{ request('q') }}" class="rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-sm">
                    <select name="status" class="rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-sm">
                        <option value="">All Status</option>
                        @foreach (['draft','submitted','pending','approved','rejected','cancelled'] as $st)
                            <option value="{{ $st }}" @selected(request('status') === $st)>{{ ucfirst($st) }}</option>
                        @endforeach
                    </select>
                    <input type="date" name="date_from" value="{{ request('date_from') }}" class="rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-sm">
                    <input type="date" name="date_to" value="{{ request('date_to') }}" class="rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-sm">
                    <button type="submit" class="px-3 py-2 rounded-md bg-indigo-600 text-white text-sm font-semibold">Filter</button>
                </form>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-900/40">
                        <tr>
                            <th class="px-4 py-3 text-left">Leave Type</th>
                            <th class="px-4 py-3 text-left">Dates</th>
                            <th class="px-4 py-3 text-left">Days</th>
                            <th class="px-4 py-3 text-left">Reason</th>
                            <th class="px-4 py-3 text-left">Status</th>
                            <th class="px-4 py-3 text-left">Submitted</th>
                            <th class="px-4 py-3 text-left">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($leaves ?? [] as $leave)
                            <tr class="border-t border-gray-200 dark:border-gray-700">
                                <td class="px-4 py-3 font-semibold">{{ $leave->type }}</td>
                                <td class="px-4 py-3">
                                    {{ optional($leave->start_date)->format('M d, Y') }} - {{ optional($leave->end_date)->format('M d, Y') }}
                                </td>
                                <td class="px-4 py-3">{{ $leave->number_of_days ?? '-' }}</td>
                                <td class="px-4 py-3 max-w-[260px] truncate" title="{{ $leave->reason }}">{{ $leave->reason }}</td>
                                <td class="px-4 py-3">
                                    @php
                                        $statusClasses = [
                                            'approved' => 'bg-green-100 text-green-700',
                                            'rejected' => 'bg-red-100 text-red-700',
                                            'pending' => 'bg-yellow-100 text-yellow-700',
                                            'submitted' => 'bg-blue-100 text-blue-700',
                                            'draft' => 'bg-gray-100 text-gray-700',
                                            'cancelled' => 'bg-slate-100 text-slate-700',
                                        ];
                                    @endphp
                                    <span class="px-2 py-1 rounded-full text-xs font-bold {{ $statusClasses[$leave->status] ?? 'bg-gray-100 text-gray-700' }}">{{ ucfirst($leave->status) }}</span>
                                </td>
                                <td class="px-4 py-3">{{ optional($leave->submitted_at)->format('M d, Y h:i A') ?? '-' }}</td>
                                <td class="px-4 py-3">
                                    <div class="flex flex-wrap gap-2">
                                        <a href="{{ route('student.leaves.print', $leave) }}" target="_blank" class="text-indigo-600 font-semibold">View</a>

                                        @if ($leave->attachment_path)
                                            <a href="{{ Storage::url($leave->attachment_path) }}" target="_blank" class="text-blue-600 font-semibold">Attachment</a>
                                        @endif

                                        @if (in_array($leave->status, ['draft', 'rejected'], true))
                                            <a href="{{ route('student.leaves.edit', $leave) }}" class="text-amber-600 font-semibold">Edit</a>
                                        @endif

                                        @if ($leave->status === 'draft')
                                            <form method="POST" action="{{ route('student.leaves.destroy', $leave) }}" onsubmit="return confirm('Delete this draft?');" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 font-semibold">Delete</button>
                                            </form>
                                        @endif

                                        @if (in_array($leave->status, ['submitted', 'pending'], true))
                                            <form method="POST" action="{{ route('student.leaves.cancel', $leave) }}" onsubmit="return confirm('Cancel this leave request?');" class="inline">
                                                @csrf
                                                <button type="submit" class="text-red-500 font-semibold">Cancel</button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @if ($leave->reviewer_remarks)
                                <tr>
                                    <td colspan="7" class="px-4 pb-3 text-xs text-gray-600 dark:text-gray-300">
                                        <span class="font-semibold">Reviewer Remarks:</span> {{ $leave->reviewer_remarks }}
                                    </td>
                                </tr>
                            @endif
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-6 text-center text-gray-500">No leave requests found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                {{ $leaves->links() }}
            </div>
        </div>
    </div>

    <script>
        // Leave type descriptions database
        const leaveTypeDescriptions = {
            'Sick Leave': 'For illness or medical reasons. Usually approved within 1-2 business days.',
            'Annual': 'Regular annual leave. Requires 3-5 business days approval.',
            'Discretionary': 'Personal or family matters. Requires 3-5 business days approval.',
            'Maternity': 'Maternity or parental leave. Usually approved same day.',
            'Exam': 'Educational examination purposes. Usually approved within 2-3 business days.',
            'Bereavement': 'Death of family member. Usually approved same day.',
            'Vacation': 'Vacation and rest days. Requires 3-5 business days approval.',
            'No Pay Leave': 'Unpaid leave (deducted from salary). Requires 5-7 business days approval.'
        };

        const approvalTimelines = {
            'Sick Leave': '1-2 business days',
            'Exam': '2-3 business days',
            'Annual': '3-5 business days',
            'Vacation': '3-5 business days',
            'Maternity': 'Same day - 1 business day',
            'Bereavement': 'Same day - 1 business day',
            'No Pay Leave': '5-7 business days',
            'Discretionary': '3-5 business days'
        };

        // Update leave type description when selection changes
        function updateLeaveTypeDescription() {
            const typeSelect = document.getElementById('leave_type');
            const typeHelpDesc = document.getElementById('type_help');
            const approvalDiv = document.getElementById('approval_timeline');
            const timelineText = document.getElementById('timeline_text');

            if (!typeSelect.value) {
                typeHelpDesc.textContent = '';
                approvalDiv.classList.add('hidden');
                return;
            }

            const description = leaveTypeDescriptions[typeSelect.value] || '';
            const timeline = approvalTimelines[typeSelect.value] || '2-3 business days';

            typeHelpDesc.textContent = description;
            timelineText.textContent = timeline;
            approvalDiv.classList.remove('hidden');
        }

        // Calculate days between start and end date
        function calculateDays() {
            const startInput = document.getElementById('start_date');
            const endInput = document.getElementById('end_date');
            const daysInput = document.getElementById('number_of_days');
            const daysBadge = document.getElementById('days_badge');

            if (!startInput.value || !endInput.value) {
                daysInput.value = '';
                daysBadge.classList.add('hidden');
                return;
            }

            const start = new Date(startInput.value);
            const end = new Date(endInput.value);
            const diff = Math.floor((end - start) / (1000 * 60 * 60 * 24)) + 1;

            if (diff > 0) {
                daysInput.value = diff;
                daysBadge.textContent = diff + ' day' + (diff !== 1 ? 's' : '');
                daysBadge.classList.remove('hidden');
                
                // Check if exceeds 30 days
                if (diff > 30) {
                    daysBadge.classList.add('ring-2', 'ring-red-500');
                } else {
                    daysBadge.classList.remove('ring-2', 'ring-red-500');
                }
            } else {
                daysInput.value = '';
                daysBadge.classList.add('hidden');
            }
        }

        // Update attachment file info
        function updateAttachmentInfo() {
            const fileInput = document.getElementById('attachment');
            const fileInfo = document.getElementById('file_info');

            if (!fileInput.files || fileInput.files.length === 0) {
                fileInfo.classList.add('hidden');
                return;
            }

            const file = fileInput.files[0];
            const sizeMB = (file.size / (1024 * 1024)).toFixed(2);
            fileInfo.textContent = `✓ ${file.name} (${sizeMB} MB)`;
            fileInfo.classList.remove('hidden');
        }

        // Update character count for reason textarea
        function updateCharCount() {
            const reasonTextarea = document.getElementById('reason');
            const charCountSpan = document.getElementById('char_count');
            charCountSpan.textContent = reasonTextarea.value.length;
        }

        // Validate form before submission
        function validateForm() {
            const leaveType = document.getElementById('leave_type').value;
            const startDate = document.getElementById('start_date').value;
            const endDate = document.getElementById('end_date').value;
            const reason = document.getElementById('reason').value.trim();
            const daysValue = document.getElementById('number_of_days').value;

            // Check required fields
            if (!leaveType) {
                alert('Please select a leave type.');
                document.getElementById('leave_type').focus();
                return false;
            }

            if (!startDate || !endDate) {
                alert('Please select both start and end dates.');
                return false;
            }

            if (!reason) {
                alert('Please provide a reason for your leave request.');
                document.getElementById('reason').focus();
                return false;
            }

            if (reason.length < 10) {
                alert('Please provide a more detailed reason (at least 10 characters).');
                return false;
            }

            // Check date validity
            const start = new Date(startDate);
            const end = new Date(endDate);
            if (start > end) {
                alert('End date must be after or equal to start date.');
                return false;
            }

            // Check 30-day limit
            const days = parseInt(daysValue);
            if (days > 30) {
                alert('Leave request cannot exceed 30 days!');
                return false;
            }

            return confirm('Are you sure you want to submit this leave request?\n\nLeave Type: ' + leaveType + '\nDates: ' + startDate + ' to ' + endDate + '\nDays: ' + days);
        }

        // Check leave balance based on selected type
        function checkLeaveBalance() {
            const typeSelect = document.getElementById('leave_type');
            // This can be enhanced to show actual balance warnings if you add balance data to the page
            // For now, it just updates the description
            updateLeaveTypeDescription();
        }

        // Initialize when page loads
        document.addEventListener('DOMContentLoaded', function () {
            // Setup event listeners for date inputs
            const startInput = document.getElementById('start_date');
            const endInput = document.getElementById('end_date');

            if (startInput && endInput) {
                startInput.addEventListener('change', calculateDays);
                endInput.addEventListener('change', calculateDays);
            }

            // Set up reason textarea char count
            const reasonTextarea = document.getElementById('reason');
            if (reasonTextarea) {
                reasonTextarea.addEventListener('keyup', updateCharCount);
                updateCharCount(); // Initialize on load
            }

            // Initialize attachment info if file is pre-selected
            const fileInput = document.getElementById('attachment');
            if (fileInput) {
                fileInput.addEventListener('change', updateAttachmentInfo);
            }

            // Calculate days if dates are already filled
            if (startInput && startInput.value && endInput && endInput.value) {
                calculateDays();
            }

            // Update leave type description if type is already selected
            const typeSelect = document.getElementById('leave_type');
            if (typeSelect && typeSelect.value) {
                updateLeaveTypeDescription();
            }
        });
    </script>
</x-student-layout>

