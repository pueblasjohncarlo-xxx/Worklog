<x-coordinator-layout>
    <x-slot name="header">
        Assignments
    </x-slot>

    <div class="space-y-6">
        <div class="bg-white dark:bg-gray-800 overflow-visible shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900 dark:text-gray-100 space-y-4">
                @if ($errors->any())
                    <div class="text-sm text-red-600 dark:text-red-400">
                        {{ $errors->first() }}
                    </div>
                @endif

                @if (session('status'))
                    <div class="text-sm text-green-600 dark:text-green-400">
                        {{ session('status') }}
                    </div>
                @endif

                <form
                    id="assignmentForm"
                    method="POST"
                    action="{{ route('coordinator.assignments.store') }}"
                    class="space-y-4"
                >
                    @csrf

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div class="space-y-1">
                            <label for="student_ids" class="block text-sm font-medium">
                                Student(s)
                            </label>
                            <select
                                id="student_ids"
                                name="student_ids[]"
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                multiple="multiple"
                                required
                            >
                                @foreach ($groupedStudents as $group => $students)
                                    <optgroup label="{{ $group }}">
                                        @foreach ($students as $student)
                                            <option value="{{ $student->id }}" data-email="{{ $student->email }}">
                                                {{ $student->lastname }}, {{ $student->firstname }}
                                            </option>
                                        @endforeach
                                    </optgroup>
                                @endforeach
                            </select>
                        </div>
                        <div class="space-y-1">
                            <label for="supervisor_id" class="block text-sm font-medium">
                                Supervisor
                            </label>
                            <select
                                id="supervisor_id"
                                name="supervisor_id"
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                required
                            >
                                <option value="">
                                    Select supervisor
                                </option>
                                @foreach ($supervisors as $supervisor)
                                    <option
                                        value="{{ $supervisor->id }}"
                                        @selected(old('supervisor_id') == $supervisor->id)
                                    >
                                        {{ $supervisor->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="space-y-1">
                            <label for="company_id" class="block text-sm font-medium">
                                Company
                            </label>
                            <select
                                id="company_id"
                                name="company_id"
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                required
                            >
                                <option value="">
                                    Select company
                                </option>
                                @foreach ($companies as $company)
                                    <option
                                        value="{{ $company->id }}"
                                        @selected(old('company_id') == $company->id)
                                    >
                                        {{ $company->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="space-y-1">
                            <label for="ojt_adviser_id" class="block text-sm font-medium">
                                OJT Adviser
                            </label>
                            <select
                                id="ojt_adviser_id"
                                name="ojt_adviser_id"
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            >
                                <option value="">
                                    Select OJT adviser (Optional)
                                </option>
                                @foreach ($ojtAdvisers as $adviser)
                                    <option
                                        value="{{ $adviser->id }}"
                                        @selected(old('ojt_adviser_id') == $adviser->id)
                                    >
                                        {{ $adviser->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <label for="start_date" class="block text-sm font-medium">
                                Start date
                            </label>
                            <input
                                id="start_date"
                                name="start_date"
                                type="date"
                                value="{{ old('start_date') }}"
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            >
                        </div>
                        <div class="space-y-1">
                            <label for="end_date" class="block text-sm font-medium">
                                End date
                            </label>
                            <input
                                id="end_date"
                                name="end_date"
                                type="date"
                                value="{{ old('end_date') }}"
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            >
                        </div>
                        
                    </div>

                    <div class="flex items-center justify-end">
                        <button
                            type="button"
                            onclick="confirmAssignment()"
                            class="inline-flex items-center px-4 py-2 rounded-md bg-indigo-600 text-xs font-semibold uppercase tracking-wide text-white hover:bg-indigo-700"
                        >
                            Create assignment
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Confirmation Modal -->
        <div id="confirmationModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-indigo-100 sm:mx-0 sm:h-10 sm:w-10">
                                <svg class="h-6 w-6 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                </svg>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100" id="modal-title">
                                    Review Assignment
                                </h3>
                                <div class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                                    <p class="mb-2">Please double check the assignment details:</p>
                                    <div class="bg-gray-50 dark:bg-gray-700 p-3 rounded-md space-y-2 text-left">
                                        <p><strong>Students Selected:</strong> <span id="confirm-count">0</span></p>
                                        <div id="confirm-students-list" class="text-xs text-gray-500 dark:text-gray-300 max-h-20 overflow-y-auto pl-2 border-l-2 border-indigo-500"></div>
                                        <p><strong>Supervisor:</strong> <span id="confirm-supervisor" class="text-indigo-600 dark:text-indigo-400 font-bold">-</span></p>
                                        <p><strong>OJT Adviser:</strong> <span id="confirm-adviser" class="text-indigo-600 dark:text-indigo-400 font-bold">-</span></p>
                                        <p><strong>Company:</strong> <span id="confirm-company" class="text-indigo-600 dark:text-indigo-400 font-bold">-</span></p>
                                        <p><strong>Duration:</strong> <span id="confirm-duration">-</span></p>
                                    </div>
                                    <p class="mt-3 text-xs italic text-red-500" id="confirm-warning"></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="button" onclick="submitForm()" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm">
                            Confirm & Create
                        </button>
                        <button type="button" onclick="closeModal()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900 dark:text-gray-100">
                <h3 class="font-semibold mb-3">
                    Existing assignments
                </h3>

                @if ($assignments->isEmpty())
                    <p class="text-sm">
                        No assignments yet.
                    </p>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-left text-sm">
                            <thead>
                                        <tr class="border-b border-gray-200 dark:border-gray-700">
                                            <th class="px-3 py-2">Student</th>
                                            <th class="px-3 py-2">Supervisor</th>
                                            <th class="px-3 py-2">OJT Adviser</th>
                                            <th class="px-3 py-2">Company</th>
                                            <th class="px-3 py-2">Start</th>
                                            <th class="px-3 py-2">End</th>
                                            <th class="px-3 py-2">Status</th>
                                            <th class="px-3 py-2">Required Hours</th>
                                            <th class="px-3 py-2 text-right">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($assignments as $assignment)
                                            <tr class="border-b border-gray-100 dark:border-gray-800">
                                                <td class="px-3 py-2 whitespace-nowrap">
                                                    {{ $assignment->student->name }}
                                                </td>
                                                <td class="px-3 py-2 whitespace-nowrap">
                                                    {{ $assignment->supervisor->name }}
                                                </td>
                                                <td class="px-3 py-2 whitespace-nowrap">
                                                    {{ $assignment->ojtAdviser->name ?? '---' }}
                                                </td>
                                                <td class="px-3 py-2 whitespace-nowrap">
                                                    {{ $assignment->company->name }}
                                                </td>
                                        <td class="px-3 py-2 whitespace-nowrap">
                                            {{ optional($assignment->start_date)->format('Y-m-d') }}
                                        </td>
                                        <td class="px-3 py-2 whitespace-nowrap">
                                            {{ optional($assignment->end_date)->format('Y-m-d') }}
                                        </td>
                                        <td class="px-3 py-2 whitespace-nowrap">
                                            {{ ucfirst($assignment->status) }}
                                        </td>
                                        <td class="px-3 py-2 whitespace-nowrap">
                                            <form method="POST" action="{{ route('coordinator.assignments.update-hours', $assignment) }}" class="flex items-center gap-2">
                                                @csrf
                                                @method('PATCH')
                                                <input 
                                                    type="number" 
                                                    name="required_hours" 
                                                    min="1" max="5000"
                                                    value="{{ old('required_hours', $assignment->required_hours ?? 1600) }}"
                                                    class="w-24 rounded-md border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 text-sm"
                                                >
                                                <button type="submit" class="px-3 py-1.5 rounded-md bg-indigo-600 text-white text-xs font-bold hover:bg-indigo-700">
                                                    Save
                                                </button>
                                            </form>
                                        </td>
                                        <td class="px-3 py-2 whitespace-nowrap text-right">
                                            <span class="text-xs text-gray-400">Updated: {{ $assignment->updated_at->diffForHumans() }}</span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
    @push('scripts')
    <script>
        $(document).ready(function() {
            function formatStudent (student) {
                if (!student.id) {
                    return student.text;
                }
                var email = $(student.element).data('email');
                if (!email) {
                    return student.text;
                }
                var $student = $(
                    '<div class="flex flex-col">' +
                        '<span class="font-semibold">' + student.text + '</span>' +
                        '<span class="text-xs text-gray-400">' + email + '</span>' +
                    '</div>'
                );
                return $student;
            }

            $('#student_ids').select2({
                placeholder: "Search by Name or Email...",
                allowClear: true,
                width: '100%',
                closeOnSelect: false,
                templateResult: formatStudent,
                matcher: function(params, data) {
                    // If there are no search terms, return all of the data
                    if ($.trim(params.term) === '') {
                        return data;
                    }

                    // Do not display the item if there is no 'text' label
                    if (typeof data.text === 'undefined') {
                        return null;
                    }

                    // Search by Name
                    if (data.text.toLowerCase().indexOf(params.term.toLowerCase()) > -1) {
                        return data;
                    }

                    // Search by Email (using data attribute)
                    var email = $(data.element).data('email');
                    if (email && email.toLowerCase().indexOf(params.term.toLowerCase()) > -1) {
                        return data;
                    }

                    // Return `null` if the term should not be displayed
                    return null;
                }
            });
        });

        function confirmAssignment() {
            const studentIds = $('#student_ids').val();
            const supervisorId = $('#supervisor_id').val();
            const companyId = $('#company_id').val();
            const startDate = $('#start_date').val();
            const endDate = $('#end_date').val();

            if (!studentIds || studentIds.length === 0 || !supervisorId || !companyId) {
                alert('Please select at least one student, a supervisor, and a company.');
                return;
            }

            // Populate Modal
            $('#confirm-count').text(studentIds.length);
            
            // List students
            let studentList = '';
            $('#student_ids option:selected').each(function() {
                studentList += `<div>• ${$(this).text().trim()}</div>`;
            });
            $('#confirm-students-list').html(studentList);

            $('#confirm-supervisor').text($('#supervisor_id option:selected').text().trim());
            const adviserText = $('#ojt_adviser_id').val() ? $('#ojt_adviser_id option:selected').text().trim() : 'None';
            $('#confirm-adviser').text(adviserText);
            $('#confirm-company').text($('#company_id option:selected').text().trim());
            
            let duration = 'Not specified';
            if (startDate && endDate) {
                duration = `${startDate} to ${endDate}`;
            } else if (startDate) {
                duration = `Starts ${startDate}`;
            }
            $('#confirm-duration').text(duration);

            // Warning if multiple students
            if (studentIds.length > 1) {
                $('#confirm-warning').text(`Note: This will create ${studentIds.length} separate assignments.`);
            } else {
                $('#confirm-warning').text('');
            }

            // Show Modal
            $('#confirmationModal').removeClass('hidden');
        }

        function closeModal() {
            $('#confirmationModal').addClass('hidden');
        }

        function submitForm() {
            $('#assignmentForm').submit();
        }
    </script>
    @endpush
</x-coordinator-layout>
